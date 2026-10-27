<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class EssayReviewController extends Controller
{
    /**
     * Memastikan hanya guru yang boleh
     * mengakses penilaian esai.
     */
    private function ensureTeacher(Request $request): void
    {
        $user = $request->user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['guru', 'teacher'],
                true
            ),
            403,
            'Halaman ini hanya untuk guru.'
        );
    }

    /**
     * Mengambil pengerjaan siswa.
     * Guru hanya dapat meninjau asesmen miliknya.
     */
    private function findAttempt(int $attemptId)
    {
        $attempt = DB::table('student_assessments as sa')
            ->join(
                'assessments as a',
                'a.id',
                '=',
                'sa.assessment_id'
            )
            ->join(
                'users as u',
                'u.id',
                '=',
                'sa.student_id'
            )
            ->where('sa.id', $attemptId)
            ->where('a.user_id', Auth::id())
            ->whereIn(
                'sa.status',
                ['submitted', 'completed']
            )
            ->select(
                'sa.id',
                'sa.assessment_id',
                'sa.student_id',
                'sa.status',
                'sa.score',
                'a.nama as assessment_name',
                'a.mapel',
                'u.name as student_name'
            )
            ->first();

        abort_unless(
            $attempt,
            404,
            'Pengerjaan siswa tidak ditemukan.'
        );

        return $attempt;
    }

    /**
     * Mengenali jenis soal esai.
     */
    private function isEssay($question): bool
    {
        $type = strtolower(
            trim((string) ($question->question_type ?? ''))
        );

        $options = $question->options ?? [];

        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        return str_contains($type, 'esai')
            || str_contains($type, 'essay')
            || str_contains($type, 'uraian')
            || empty($options);
    }

    /**
     * Mengambil jawaban siswa beserta bobot
     * dan informasi penilaian.
     */
    private function getAnswerRows(
        int $attemptId,
        int $assessmentId
    ) {
        $query = DB::table('student_answers as ans')
            ->join(
                'assessment_questions as aq',
                'aq.question_id',
                '=',
                'ans.question_id'
            )
            ->join(
                'questions as q',
                'q.id',
                '=',
                'ans.question_id'
            )
            ->where(
                'ans.student_assessment_id',
                $attemptId
            )
            ->where(
                'aq.assessment_id',
                $assessmentId
            )
            ->orderBy('aq.question_order')
            ->select(
                'q.id as question_id',
                'q.question_text',
                'q.question_type',
                'q.options',
                'q.correct_answer',
                'aq.points as maximum_points',
                'ans.answer',
                'ans.points_obtained',
                'ans.is_correct'
            );

        // Kolom tambahan dari migration AI Grading.
        foreach ([
            'grading_status',
            'grading_source',
            'ai_feedback',
            'teacher_feedback',
        ] as $column) {
            if (Schema::hasColumn('student_answers', $column)) {
                $query->addSelect('ans.' . $column);
            }
        }

        return $query->get();
    }

    /**
     * Menampilkan halaman pemeriksaan esai.
     */
    public function show(
        Request $request,
        int $attemptId
    ) {
        $this->ensureTeacher($request);

        $attempt = $this->findAttempt($attemptId);

        $allAnswers = $this->getAnswerRows(
            $attemptId,
            (int) $attempt->assessment_id
        );

        $essays = $allAnswers
            ->filter(
                fn ($row) => $this->isEssay($row)
            )
            ->values();

        return view(
            'guru.essay-review',
            compact('attempt', 'essays')
        );
    }

    /**
     * Menyimpan nilai esai dari guru.
     */
    public function update(
        Request $request,
        int $attemptId,
        int $questionId
    ) {
        $this->ensureTeacher($request);

        $attempt = $this->findAttempt($attemptId);

        $question = $this->getAnswerRows(
            $attemptId,
            (int) $attempt->assessment_id
        )->firstWhere('question_id', $questionId);

        abort_unless(
            $question && $this->isEssay($question),
            404,
            'Jawaban esai tidak ditemukan.'
        );

        $validated = $request->validate([
            'score' => [
                'required',
                'numeric',
                'min:0',
            ],
            'feedback' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $score = (float) $validated['score'];
        $maximum = (float) $question->maximum_points;

        if (!is_finite($score) || $score > $maximum) {
            throw ValidationException::withMessages([
                'score' => 'Skor tidak boleh melebihi '
                    . $maximum . ' poin.',
            ]);
        }

        DB::transaction(function () use (
            $attempt,
            $attemptId,
            $questionId,
            $score,
            $maximum,
            $validated
        ) {
            $update = [
                'points_obtained' => round($score, 2),
                'is_correct' => $maximum > 0
                    && $score >= $maximum,
            ];

            // Catat sumber penilaian jika kolom tersedia.
            if (Schema::hasColumn(
                'student_answers',
                'grading_source'
            )) {
                $update['grading_source'] = 'teacher';
            }

            // Simpan status penilaian.
            // Sesuaikan nilai ini jika migration
            // memakai enum atau constraint berbeda.
            if (Schema::hasColumn(
                'student_answers',
                'grading_status'
            )) {
                $update['grading_status'] = 'graded';
            }

            // Simpan catatan guru pada kolom terpisah.
            // Jangan menimpa feedback AI.
            if (Schema::hasColumn(
                'student_answers',
                'teacher_feedback'
            )) {
                $update['teacher_feedback'] =
                    $validated['feedback'] ?? null;
            }

            if (Schema::hasColumn(
                'student_answers',
                'graded_at'
            )) {
                $update['graded_at'] = now();
            }

            DB::table('student_answers')
                ->where(
                    'student_assessment_id',
                    $attemptId
                )
                ->where(
                    'question_id',
                    $questionId
                )
                ->update($update);

            /**
             * Hitung ulang nilai berdasarkan
             * soal yang benar-benar dikerjakan.
             *
             * Tidak menjumlahkan kedua versi
             * Reguler dan Adaptif sekaligus.
             */
            $totals = DB::table('student_answers as ans')
                ->join(
                    'assessment_questions as aq',
                    'aq.question_id',
                    '=',
                    'ans.question_id'
                )
                ->where(
                    'ans.student_assessment_id',
                    $attemptId
                )
                ->where(
                    'aq.assessment_id',
                    $attempt->assessment_id
                )
                ->selectRaw(
                    'COALESCE(SUM(ans.points_obtained), 0) as earned,
                     COALESCE(SUM(aq.points), 0) as maximum'
                )
                ->first();

            $earned = (float) ($totals->earned ?? 0);
            $total = (float) ($totals->maximum ?? 0);

            $finalScore = $total > 0
                ? round(($earned / $total) * 100, 2)
                : 0;

            DB::table('student_assessments')
                ->where('id', $attemptId)
                ->update([
                    'score' => $finalScore,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route(
                'guru.essay-review.show',
                ['attemptId' => $attemptId]
            )
            ->with(
                'success',
                'Nilai esai berhasil disimpan.'
            );
    }
}
