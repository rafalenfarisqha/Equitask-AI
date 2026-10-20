<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    private function ensureStudent(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array($user->role, ['student', 'siswa'], true),
            403,
            'Halaman ini hanya untuk siswa.'
        );
    }

    /**
     * Petunjuk dan pemilihan versi soal.
     */
    public function instructions($id)
    {
        $this->ensureStudent();

        $assessment = DB::table('assessments as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.id', $id)
            ->where('a.status', 'Published')
            ->where('a.visibility', 'public')
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.icon',
                'a.color',
                'u.name as teacher_name'
            )
            ->first();

        abort_unless($assessment, 404, 'Asesmen tidak ditemukan.');

        $versions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessment->id)
            ->select('q.version_type')
            ->distinct()
            ->pluck('version_type')
            ->map(fn ($v) => strtolower(trim((string) $v)));

        $hasRegular = $versions->contains('reguler');
        $hasAdaptive = $versions->contains('adaptif');

        abort_unless(
            $hasRegular || $hasAdaptive,
            404,
            'Soal untuk asesmen ini belum tersedia.'
        );

        $questionCount = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessment->id)
            ->whereIn('q.version_type', ['reguler', 'adaptif'])
            ->count();

        return view('student.assessment-instructions', compact(
            'assessment',
            'hasRegular',
            'hasAdaptive',
            'questionCount'
        ));
    }

    /**
     * Memuat soal berdasarkan versi yang dipilih.
     */
    public function start(Request $request, $id)
    {
        $this->ensureStudent();

        $validated = $request->validate([
            'version_type' => ['required', 'in:reguler,adaptif'],
        ]);

        $version = $validated['version_type'];

        $assessment = DB::table('assessments')
            ->where('id', $id)
            ->where('status', 'Published')
            ->where('visibility', 'public')
            ->first();

        abort_unless($assessment, 404, 'Asesmen tidak ditemukan.');

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessment->id)
            ->whereRaw('LOWER(q.version_type) = ?', [$version])
            ->orderBy('aq.question_order')
            ->select(
                'q.id',
                'q.question_text',
                'q.question_type',
                'q.options',
                'q.explanation',
                'aq.question_order',
                'aq.points'
            )
            ->get();

        abort_if($questions->isEmpty(), 404, 'Versi soal ini belum tersedia.');

        // Jangan mengirim correct_answer ke browser.
        $questions = $questions->map(function ($question) {
            $options = $question->options;

            if (is_string($options)) {
                $options = json_decode($options, true) ?? [];
            }

            $question->options = $options;
            return $question;
        });

        $attemptId = session('assessment_attempt_id');

        // Lanjutkan attempt yang belum selesai untuk asesmen + versi yang sama.
        $attempt = null;

        if ($attemptId) {
            $attempt = DB::table('student_assessments')
                ->where('id', $attemptId)
                ->where('assessment_id', $assessment->id)
                ->where('student_id', Auth::id())
                ->where('status', 'in_progress')
                ->first();
        }

        if (!$attempt) {
            $attemptId = DB::table('student_assessments')->insertGetId([
                'assessment_id' => $assessment->id,
                'student_id' => Auth::id(),
                'status' => 'in_progress',
                'started_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            session([
                'assessment_attempt_id' => $attemptId,
                'assessment_version_' . $attemptId => $version,
            ]);
        } else {
            // Pastikan versi tidak berubah saat melanjutkan attempt.
            $savedVersion = session('assessment_version_' . $attemptId);

            if ($savedVersion && $savedVersion !== $version) {
                return redirect()
                    ->route('siswa.assessment.instructions', $id)
                    ->with('error', 'Selesaikan versi yang sedang dikerjakan terlebih dahulu.');
            }

            session(['assessment_version_' . $attemptId => $version]);
        }

        $savedAnswers = DB::table('student_answers')
            ->where('student_assessment_id', $attemptId)
            ->pluck('answer', 'question_id');

        return view('student.assessment-working', compact(
            'assessment',
            'questions',
            'version',
            'attemptId',
            'savedAnswers'
        ));
    }

    /**
     * Memeriksa jawaban dan menyimpan hasil pengerjaan.
     */
    public function submit(Request $request, $id)
    {
        $this->ensureStudent();

        $attemptId = $request->input('attempt_id');

        $attempt = DB::table('student_assessments')
            ->where('id', $attemptId)
            ->where('assessment_id', $id)
            ->where('student_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        abort_unless($attempt, 403, 'Sesi pengerjaan tidak valid.');

        $version = session('assessment_version_' . $attemptId);

        abort_unless(
            in_array($version, ['reguler', 'adaptif'], true),
            403,
            'Versi soal tidak valid. Silakan buka ulang asesmen.'
        );

        $answers = $request->input('answers', []);

        if (!is_array($answers)) {
            return back()->withErrors([
                'answers' => 'Format jawaban tidak valid.',
            ]);
        }

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $id)
            ->whereRaw('LOWER(q.version_type) = ?', [$version])
            ->select(
                'q.id',
                'q.options',
                'q.correct_answer',
                'aq.points'
            )
            ->get();

        abort_if($questions->isEmpty(), 404, 'Soal tidak ditemukan.');

        $validIds = $questions->pluck('id')->map(fn ($id) => (string) $id)->all();

        foreach ($answers as $questionId => $answer) {
            if (!in_array((string) $questionId, $validIds, true)) {
                return back()->withErrors([
                    'answers' => 'Terdapat jawaban yang tidak sesuai dengan versi soal ini.',
                ]);
            }

            if (!is_string($answer) || mb_strlen($answer) > 5000) {
                return back()->withErrors([
                    'answers' => 'Ada jawaban dengan format yang tidak valid.',
                ]);
            }
        }

        $totalPoints = (float) $questions->sum(
            fn ($question) => (float) $question->points
        );

        $earnedPoints = 0;

        DB::transaction(function () use (
            $questions,
            $answers,
            $attemptId,
            &$earnedPoints
        ) {
            foreach ($questions as $question) {
                $answer = $answers[$question->id] ?? null;

                $options = $question->options;
                if (is_string($options)) {
                    $options = json_decode($options, true) ?? [];
                }

                // Abaikan jawaban yang bukan salah satu pilihan yang tersedia.
                if ($answer !== null && is_array($options)
                    && !array_key_exists($answer, $options)) {
                    $answer = null;
                }

                $isCorrect = $answer !== null
                    && trim((string) $answer) === trim((string) $question->correct_answer);

                $points = $isCorrect ? (float) $question->points : 0;

                $earnedPoints += $points;

                DB::table('student_answers')->updateOrInsert(
                    [
                        'student_assessment_id' => $attemptId,
                        'question_id' => $question->id,
                    ],
                    [
                        'answer' => $answer,
                        'is_correct' => $answer === null ? false : $isCorrect,
                        'points_obtained' => $points,
                        'answered_at' => now(),
                    ]
                );
            }
        });

        $score = $totalPoints > 0
            ? round(($earnedPoints / $totalPoints) * 100, 2)
            : 0;

        DB::table('student_assessments')
            ->where('id', $attemptId)
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'score' => $score,
                'updated_at' => now(),
            ]);

        session()->forget([
            'assessment_attempt_id',
            'assessment_version_' . $attemptId,
        ]);

        return view('student.assessment-result', compact(
            'score',
            'earnedPoints',
            'totalPoints'
        ));
    }

    /**
 * Menampilkan kembali asesmen yang sedang dikerjakan siswa.
 */
public function work($id, $attemptId)
{
    $this->ensureStudent();

    $userId = Auth::id();

    // Pastikan attempt milik siswa yang sedang login.
    $attempt = DB::table('student_assessments as sa')
        ->join('assessments as a', 'a.id', '=', 'sa.assessment_id')
        ->where('sa.id', $attemptId)
        ->where('sa.assessment_id', $id)
        ->where('sa.student_id', $userId)
        ->where('sa.status', 'in_progress')
        ->where('a.status', 'Published')
        ->where('a.visibility', 'public')
        ->select(
            'sa.id as attempt_id',
            'sa.assessment_id',
            'a.nama',
            'a.mapel',
            'a.icon',
            'a.color'
        )
        ->first();

    abort_unless($attempt, 404, 'Sesi asesmen tidak ditemukan.');

    // Ambil versi yang dipilih ketika asesmen dimulai.
    $version = session('assessment_version_' . $attempt->attempt_id);

    abort_unless(
        in_array($version, ['reguler', 'adaptif'], true),
        403,
        'Versi asesmen tidak ditemukan di sesi ini.'
    );

    // Ambil soal sesuai asesmen dan versi yang dipilih.
    $questions = DB::table('assessment_questions as aq')
        ->join('questions as q', 'q.id', '=', 'aq.question_id')
        ->where('aq.assessment_id', $attempt->assessment_id)
        ->whereRaw('LOWER(q.version_type) = ?', [$version])
        ->orderBy('aq.question_order')
        ->select(
            'q.id',
            'q.question_text',
            'q.question_type',
            'q.options',
            'aq.question_order',
            'aq.points'
        )
        ->get();

    abort_if($questions->isEmpty(), 404, 'Soal untuk versi ini belum tersedia.');

    // Pastikan options bisa dibaca oleh Blade.
    $questions = $questions->map(function ($question) {
        $options = $question->options;

        if (is_string($options)) {
            $options = json_decode($options, true) ?? [];
        }

        $question->options = $options;

        return $question;
    });

    // Muat jawaban yang sudah tersimpan.
    $savedAnswers = DB::table('student_answers')
        ->where('student_assessment_id', $attempt->attempt_id)
        ->pluck('answer', 'question_id');

    $assessment = (object) [
        'id' => $attempt->assessment_id,
        'nama' => $attempt->nama,
        'mapel' => $attempt->mapel,
        'icon' => $attempt->icon,
        'color' => $attempt->color,
    ];

    $attemptId = $attempt->attempt_id;

    return view('student.assessment-working', compact(
        'assessment',
        'questions',
        'version',
        'attemptId',
        'savedAnswers'
    ));
}
}
