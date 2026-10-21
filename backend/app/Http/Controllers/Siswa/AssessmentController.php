<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Memastikan pengguna yang login adalah siswa.
     */
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
     * Memastikan asesmen sudah dipublikasikan dan boleh diakses siswa.
     *
     * Asesmen dapat diakses jika:
     * 1. Bersifat publik; atau
     * 2. Ditugaskan ke kelas yang diikuti siswa.
     */
    private function studentCanAccessAssessment(
        int $assessmentId,
        int $studentId
    ): bool {
        return DB::table('assessments as a')
            ->where('a.id', $assessmentId)
            ->where('a.status', 'Published')
            ->where(function ($query) use ($studentId) {
                $query->where('a.visibility', 'public')
                    ->orWhereExists(function ($subquery) use ($studentId) {
                        $subquery->selectRaw('1')
                            ->from('assessment_class_assignments as aca')
                            ->join(
                                'class_members as cm',
                                'cm.class_id',
                                '=',
                                'aca.class_id'
                            )
                            ->whereColumn(
                                'aca.assessment_id',
                                'a.id'
                            )
                            ->where('cm.student_id', $studentId);
                    });
            })
            ->exists();
    }

    /**
     * Memastikan asesmen bisa diakses oleh siswa yang login.
     */
    private function ensureAssessmentAccessible(int $assessmentId): void
    {
        abort_unless(
            $this->studentCanAccessAssessment(
                $assessmentId,
                (int) Auth::id()
            ),
            404,
            'Asesmen tidak ditemukan atau tidak ditugaskan ke kelasmu.'
        );
    }

    /**
     * Petunjuk dan pemilihan versi soal.
     */
    public function instructions($id)
    {
        $this->ensureStudent();
        $this->ensureAssessmentAccessible((int) $id);

        $assessment = DB::table('assessments as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.id', $id)
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
            ->map(fn ($version) => strtolower(trim((string) $version)));

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
            ->whereRaw(
                "LOWER(TRIM(q.version_type)) IN (?, ?)",
                ['reguler', 'adaptif']
            )
            ->count();

        return view('student.assessment-instruction', compact(
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
        $this->ensureAssessmentAccessible((int) $id);

        $validated = $request->validate([
            'version_type' => ['required', 'in:reguler,adaptif'],
        ]);

        $version = $validated['version_type'];

        $assessment = DB::table('assessments')
            ->where('id', $id)
            ->where('status', 'Published')
            ->first();

        abort_unless($assessment, 404, 'Asesmen tidak ditemukan.');

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessment->id)
            ->whereRaw('LOWER(TRIM(q.version_type)) = ?', [$version])
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

        abort_if(
            $questions->isEmpty(),
            404,
            'Versi soal ini belum tersedia.'
        );

        // Jangan mengirim kunci jawaban ke browser.
        $questions = $questions->map(function ($question) {
            $options = $question->options;

            if (is_string($options)) {
                $options = json_decode($options, true) ?? [];
            }

            $question->options = is_array($options) ? $options : [];

            return $question;
        });

        $studentId = (int) Auth::id();
        $attemptId = session('assessment_attempt_id');
        $attempt = null;

        // Cari sesi sebelumnya yang belum selesai.
        if ($attemptId) {
            $attempt = DB::table('student_assessments')
                ->where('id', $attemptId)
                ->where('assessment_id', $assessment->id)
                ->where('student_id', $studentId)
                ->where('status', 'in_progress')
                ->first();
        }

        // Buat sesi baru jika belum ada sesi yang dapat dilanjutkan.
        if (!$attempt) {
            $attemptId = DB::table('student_assessments')->insertGetId([
                'assessment_id' => $assessment->id,
                'student_id' => $studentId,
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
            $savedVersion = session(
                'assessment_version_' . $attemptId
            );

            if ($savedVersion && $savedVersion !== $version) {
                return redirect()
                    ->route('siswa.assessment.instructions', $id)
                    ->with(
                        'error',
                        'Selesaikan versi yang sedang dikerjakan terlebih dahulu.'
                    );
            }

            session([
                'assessment_version_' . $attemptId => $version,
            ]);
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

        $validated = $request->validate([
            'attempt_id' => ['required', 'integer'],
            'answers' => ['sometimes', 'array'],
            'answers.*' => ['nullable', 'string', 'max:5000'],
        ]);

        $attemptId = (int) $validated['attempt_id'];
        $studentId = (int) Auth::id();

        $attempt = DB::table('student_assessments')
            ->where('id', $attemptId)
            ->where('assessment_id', $id)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        abort_unless($attempt, 403, 'Sesi pengerjaan tidak valid.');

        $this->ensureAssessmentAccessible((int) $id);

        $version = session('assessment_version_' . $attemptId);

        abort_unless(
            in_array($version, ['reguler', 'adaptif'], true),
            403,
            'Versi soal tidak valid. Silakan buka ulang asesmen.'
        );

        $answers = $validated['answers'] ?? [];

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $id)
            ->whereRaw('LOWER(TRIM(q.version_type)) = ?', [$version])
            ->select(
                'q.id',
                'q.options',
                'q.correct_answer',
                'aq.points'
            )
            ->get();

        abort_if($questions->isEmpty(), 404, 'Soal tidak ditemukan.');

        $validIds = $questions
            ->pluck('id')
            ->map(fn ($questionId) => (string) $questionId)
            ->all();

        foreach ($answers as $questionId => $answer) {
            if (!in_array((string) $questionId, $validIds, true)) {
                return back()->withErrors([
                    'answers' => 'Terdapat jawaban yang tidak sesuai dengan versi soal ini.',
                ]);
            }

            if ($answer !== null && !is_string($answer)) {
                return back()->withErrors([
                    'answers' => 'Ada jawaban dengan format yang tidak valid.',
                ]);
            }
        }

        $totalPoints = (float) $questions->sum(
            fn ($question) => (float) $question->points
        );

        $earnedPoints = 0.0;

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

                $options = is_array($options) ? $options : [];

                // Jawaban wajib sesuai dengan salah satu opsi.
                if (
                    $answer !== null
                    && !array_key_exists($answer, $options)
                ) {
                    $answer = null;
                }

                $isCorrect = $answer !== null
                    && trim((string) $answer)
                        === trim((string) $question->correct_answer);

                $points = $isCorrect
                    ? (float) $question->points
                    : 0.0;

                $earnedPoints += $points;

                DB::table('student_answers')->updateOrInsert(
                    [
                        'student_assessment_id' => $attemptId,
                        'question_id' => $question->id,
                    ],
                    [
                        'answer' => $answer,
                        'is_correct' => $isCorrect,
                        'points_obtained' => $points,
                        'answered_at' => now(),
                    ]
                );
            }

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
        });

        $score = $totalPoints > 0
            ? round(($earnedPoints / $totalPoints) * 100, 2)
            : 0;

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
     * Membuka kembali sesi asesmen yang belum selesai.
     */
    public function work($id, $attemptId)
    {
        $this->ensureStudent();
        $this->ensureAssessmentAccessible((int) $id);

        $attempt = DB::table('student_assessments as sa')
            ->join('assessments as a', 'a.id', '=', 'sa.assessment_id')
            ->where('sa.id', $attemptId)
            ->where('sa.assessment_id', $id)
            ->where('sa.student_id', Auth::id())
            ->where('sa.status', 'in_progress')
            ->where('a.status', 'Published')
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

        $version = session(
            'assessment_version_' . $attempt->attempt_id
        );

        abort_unless(
            in_array($version, ['reguler', 'adaptif'], true),
            403,
            'Versi asesmen tidak ditemukan di sesi ini.'
        );

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $attempt->assessment_id)
            ->whereRaw('LOWER(TRIM(q.version_type)) = ?', [$version])
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

        abort_if(
            $questions->isEmpty(),
            404,
            'Soal untuk versi ini belum tersedia.'
        );

        $questions = $questions->map(function ($question) {
            $options = $question->options;

            if (is_string($options)) {
                $options = json_decode($options, true) ?? [];
            }

            $question->options = is_array($options) ? $options : [];

            return $question;
        });

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
