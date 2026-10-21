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
                            ->whereColumn('aca.assessment_id', 'a.id')
                            ->where('cm.student_id', $studentId);
                    });
            })
            ->exists();
    }

    private function ensureAssessmentAccessible(
        int $assessmentId
    ): void {
        abort_unless(
            $this->studentCanAccessAssessment(
                $assessmentId,
                (int) Auth::id()
            ),
            404,
            'Asesmen tidak ditemukan atau tidak ditugaskan ke kelasmu.'
        );
    }

    private function decodeArray($value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Menyamakan aturan identifikasi esai
     * di controller dengan Blade siswa.
     */
    private function isEssayQuestion($question): bool
    {
        $type = strtolower(
            trim((string) ($question->question_type ?? ''))
        );

        $options = $this->decodeArray(
            $question->options ?? null
        );

        return str_contains($type, 'esai')
            || str_contains($type, 'essay')
            || str_contains($type, 'uraian')
            || empty($options);
    }

    /**
     * Mengambil output Dify yang telah tersimpan.
     * Tidak memanggil API Dify.
     */
    private function getStoredDifyOutputs(
        int $assessmentId
    ): array {
        $generation = DB::table('ai_generations')
            ->where('assessment_id', $assessmentId)
            ->where('status', 'success')
            ->orderByDesc('id')
            ->first();

        if (!$generation) {
            return [];
        }

        $result = DB::table('ai_agent_results')
            ->where('generation_id', $generation->id)
            ->where('status', 'success')
            ->orderByDesc('id')
            ->first();

        if (!$result) {
            return [];
        }

        $stored = $this->decodeArray(
            $result->output_data
        );

        return $this->decodeArray(
            $stored['outputs'] ?? []
        );
    }

    /**
     * Mengambil soal Adaptif yang sudah direview guru,
     * atau output awal/revisi Dify.
     */
    private function getAdaptiveSourceQuestions(
        int $assessmentId,
        array $outputs
    ): array {
        $adaptif = [];

        $review = DB::table('assessment_reviews')
            ->where('assessment_id', $assessmentId)
            ->first();

        if ($review) {
            $adaptif = $this->decodeArray(
                $review->adaptif
            );
        }

        if ($adaptif === []) {
            $adaptif = $this->decodeArray(
                $outputs['asesmen_inklusif_revisi']
                ?? $outputs['asesmen_inklusif']
                ?? []
            );
        }

        $questions = $adaptif['soal_inklusif']
            ?? $adaptif['soal_adaptif']
            ?? $adaptif['soal']
            ?? [];

        return is_array($questions)
            ? array_values($questions)
            : [];
    }

    /**
     * Mengubah petunjuk Dify menjadi array teks.
     */
    private function normalizeScaffolding($value): array
    {
        if (is_string($value)) {
            $value = trim($value) !== ''
                ? [$value]
                : [];
        }

        if (!is_array($value)) {
            return [];
        }

        $result = [];

        foreach ($value as $item) {
            if (is_string($item)) {
                if (trim($item) !== '') {
                    $result[] = trim($item);
                }
            } elseif (is_array($item)) {
                $text = $item['teks']
                    ?? $item['petunjuk']
                    ?? $item['langkah']
                    ?? null;

                if (is_string($text) && trim($text) !== '') {
                    $result[] = trim($text);
                }
            }
        }

        return $result;
    }

    /**
     * Menyiapkan gambar dan scaffolding Adaptif
     * menggunakan ID soal database.
     */
    private function getAdaptiveSupport(
        int $assessmentId,
        $questions,
        string $version
    ): array {
        $empty = [
            'scaffolding' => [],
            'images' => [],
        ];

        if ($version !== 'adaptif' || $questions->isEmpty()) {
            return $empty;
        }

        $outputs = $this->getStoredDifyOutputs(
            $assessmentId
        );

        $sourceQuestions = $this->getAdaptiveSourceQuestions(
            $assessmentId,
            $outputs
        );

        $questions = $questions->values();

        // Jangan memasangkan petunjuk ke soal yang salah.
        if (count($sourceQuestions) !== $questions->count()) {
            return $empty;
        }

        $scaffoldingPerQuestion = [];
        $gambarPerQuestion = [];
        $databaseIdPerSourceId = [];

        foreach ($questions as $index => $question) {
            $source = $sourceQuestions[$index] ?? null;

            if (!is_array($source)) {
                continue;
            }

            $petunjuk = $this->normalizeScaffolding(
                $source['dukungan_scaffolding'] ?? []
            );

            if ($petunjuk !== []) {
                $scaffoldingPerQuestion[$question->id] =
                    $petunjuk;
            }

            $sourceId = trim((string) (
                $source['id_soal_asal']
                ?? $source['id_soal']
                ?? $source['id']
                ?? ''
            ));

            if ($sourceId !== '') {
                $databaseIdPerSourceId[$sourceId] =
                    $question->id;
            }
        }

        $gambarRaw = $this->decodeArray(
            $outputs['gambar_asesmen_revisi']
            ?? $outputs['gambar_asesmen']
            ?? []
        );

        $queue = [$gambarRaw];

        while ($queue !== []) {
            $item = array_shift($queue);

            if (!is_array($item)) {
                continue;
            }

            if (
                isset($item['output'])
                && is_array($item['output'])
            ) {
                $queue[] = $item['output'];
                continue;
            }

            if (
                isset($item['gambar_per_soal'])
                && is_array($item['gambar_per_soal'])
            ) {
                $queue[] = $item['gambar_per_soal'];
                continue;
            }

            if (array_is_list($item)) {
                foreach ($item as $nested) {
                    $queue[] = $nested;
                }

                continue;
            }

            $sourceId = trim((string) (
                $item['id_soal_asal'] ?? ''
            ));

            if (
                $sourceId === ''
                || !isset($databaseIdPerSourceId[$sourceId])
            ) {
                continue;
            }

            $url = $item['url']
                ?? $item['gambar_url']
                ?? $item['download_url']
                ?? $item['preview_url']
                ?? null;

            if (
                !is_string($url)
                || !filter_var($url, FILTER_VALIDATE_URL)
            ) {
                continue;
            }

            $scheme = strtolower(
                (string) parse_url($url, PHP_URL_SCHEME)
            );

            if (!in_array($scheme, ['http', 'https'], true)) {
                continue;
            }

            $questionId = $databaseIdPerSourceId[$sourceId];

            $gambarPerQuestion[$questionId][] = [
                'url' => $url,
                'alt' => (string) (
                    $item['teks_alternatif']
                    ?? $item['filename']
                    ?? 'Ilustrasi pendukung soal'
                ),
            ];
        }

        return [
            'scaffolding' => $scaffoldingPerQuestion,
            'images' => $gambarPerQuestion,
        ];
    }

    /**
     * Soal yang dikirim ke siswa.
     * Kunci jawaban tidak disertakan.
     */
    private function getQuestionsForStudent(
        int $assessmentId,
        string $version
    ) {
        return DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessmentId)
            ->whereRaw(
                'LOWER(TRIM(q.version_type)) = ?',
                [$version]
            )
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
            ->get()
            ->map(function ($question) {
                $question->options = $this->decodeArray(
                    $question->options
                );

                return $question;
            });
    }

    /**
     * Halaman petunjuk asesmen.
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

        abort_unless(
            $assessment,
            404,
            'Asesmen tidak ditemukan.'
        );

        $versions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $assessment->id)
            ->distinct()
            ->pluck('q.version_type')
            ->map(
                fn ($value) =>
                    strtolower(trim((string) $value))
            );

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
                'LOWER(TRIM(q.version_type)) IN (?, ?)',
                ['reguler', 'adaptif']
            )
            ->count();

        return view(
            'student.assessment-instruction',
            compact(
                'assessment',
                'hasRegular',
                'hasAdaptive',
                'questionCount'
            )
        );
    }

    /**
     * Mulai pengerjaan siswa.
     */
    public function start(Request $request, $id)
    {
        $this->ensureStudent();
        $this->ensureAssessmentAccessible((int) $id);

        $validated = $request->validate([
            'version_type' => [
                'required',
                'in:reguler,adaptif',
            ],
        ]);

        $version = $validated['version_type'];

        $assessment = DB::table('assessments')
            ->where('id', $id)
            ->where('status', 'Published')
            ->first();

        abort_unless(
            $assessment,
            404,
            'Asesmen tidak ditemukan.'
        );

        $questions = $this->getQuestionsForStudent(
            (int) $assessment->id,
            $version
        );

        abort_if(
            $questions->isEmpty(),
            404,
            'Versi soal ini belum tersedia.'
        );

        $support = $this->getAdaptiveSupport(
            (int) $assessment->id,
            $questions,
            $version
        );

        $scaffoldingPerQuestion = $support['scaffolding'];
        $gambarPerQuestion = $support['images'];

        $studentId = (int) Auth::id();
        $attemptId = session('assessment_attempt_id');
        $attempt = null;

        if ($attemptId) {
            $attempt = DB::table('student_assessments')
                ->where('id', $attemptId)
                ->where('assessment_id', $assessment->id)
                ->where('student_id', $studentId)
                ->where('status', 'in_progress')
                ->first();
        }

        if (!$attempt) {
            $attemptId = DB::table('student_assessments')
                ->insertGetId([
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

            if (
                $savedVersion
                && $savedVersion !== $version
            ) {
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

        return view(
            'student.assessment-working',
            compact(
                'assessment',
                'questions',
                'version',
                'attemptId',
                'savedAnswers',
                'scaffoldingPerQuestion',
                'gambarPerQuestion'
            )
        );
    }

    /**
     * Mengumpulkan jawaban siswa.
     *
     * Esai disimpan sebagai teks untuk koreksi guru.
     * Skor yang mengandung esai masih sementara.
     */
    public function submit(Request $request, $id)
    {
        $this->ensureStudent();

        $validated = $request->validate([
            'attempt_id' => ['required', 'integer'],
            'answers' => ['sometimes', 'array'],
            'answers.*' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $attemptId = (int) $validated['attempt_id'];
        $studentId = (int) Auth::id();

        $attempt = DB::table('student_assessments')
            ->where('id', $attemptId)
            ->where('assessment_id', $id)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        abort_unless(
            $attempt,
            403,
            'Sesi pengerjaan tidak valid.'
        );

        $this->ensureAssessmentAccessible((int) $id);

        $version = session(
            'assessment_version_' . $attemptId
        );

        abort_unless(
            in_array($version, ['reguler', 'adaptif'], true),
            403,
            'Versi soal tidak valid. Silakan buka ulang asesmen.'
        );

        $answers = $validated['answers'] ?? [];

        $questions = DB::table('assessment_questions as aq')
            ->join('questions as q', 'q.id', '=', 'aq.question_id')
            ->where('aq.assessment_id', $id)
            ->whereRaw(
                'LOWER(TRIM(q.version_type)) = ?',
                [$version]
            )
            ->select(
                'q.id',
                'q.question_type',
                'q.options',
                'q.correct_answer',
                'aq.points'
            )
            ->get();

        abort_if(
            $questions->isEmpty(),
            404,
            'Soal tidak ditemukan.'
        );

        // Pastikan jawaban hanya untuk soal paket ini.
        $validIds = $questions
            ->pluck('id')
            ->map(fn ($questionId) => (string) $questionId)
            ->all();

        foreach ($answers as $questionId => $answer) {
            if (!in_array((string) $questionId, $validIds, true)) {
                return back()->withErrors([
                    'answers' =>
                        'Terdapat jawaban yang tidak sesuai dengan versi soal ini.',
                ]);
            }

            if ($answer !== null && !is_string($answer)) {
                return back()->withErrors([
                    'answers' =>
                        'Ada jawaban dengan format yang tidak valid.',
                ]);
            }
        }

        $totalPoints = (float) $questions->sum(
            fn ($question) => (float) $question->points
        );

        $earnedPoints = 0.0;

        // Perbaikan: aturan esai sama dengan Blade.
        $hasEssay = $questions->contains(
            fn ($question) => $this->isEssayQuestion($question)
        );

        DB::transaction(function () use (
            $questions,
            $answers,
            $attemptId,
            $totalPoints,
            &$earnedPoints
        ) {
            foreach ($questions as $question) {
                $answer = $answers[$question->id] ?? null;

                // Perbaikan: soal tanpa opsi diperlakukan
                // sebagai input teks, bukan pilihan ganda.
                $isEssay = $this->isEssayQuestion($question);

                if ($isEssay) {
                    $answer = is_string($answer)
                        ? trim($answer)
                        : null;

                    if ($answer === '') {
                        $answer = null;
                    }

                    // Menunggu koreksi guru.
                    $isCorrect = false;
                    $points = 0.0;
                } else {
                    $options = $this->decodeArray(
                        $question->options
                    );

                    if (
                        $answer !== null
                        && !array_key_exists($answer, $options)
                    ) {
                        $answer = null;
                    }

                    // Logika penilaian pilihan ganda lama.
                    $isCorrect = $answer !== null
                        && trim((string) $answer)
                            === trim(
                                (string) $question->correct_answer
                            );

                    $points = $isCorrect
                        ? (float) $question->points
                        : 0.0;
                }

                $earnedPoints += $points;

                DB::table('student_answers')
                    ->updateOrInsert(
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
                ? round(
                    ($earnedPoints / $totalPoints) * 100,
                    2
                )
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
            ? round(
                ($earnedPoints / $totalPoints) * 100,
                2
            )
            : 0;

        session()->forget([
            'assessment_attempt_id',
            'assessment_version_' . $attemptId,
        ]);

        return view(
            'student.assessment-result',
            compact(
                'score',
                'earnedPoints',
                'totalPoints',
                'hasEssay'
            )
        );
    }

    /**
     * Melanjutkan pengerjaan siswa.
     *
     * Gambar dan scaffolding Adaptif tetap tersedia.
     */
    public function work($id, $attemptId)
    {
        $this->ensureStudent();
        $this->ensureAssessmentAccessible((int) $id);

        $attempt = DB::table('student_assessments as sa')
            ->join(
                'assessments as a',
                'a.id',
                '=',
                'sa.assessment_id'
            )
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

        abort_unless(
            $attempt,
            404,
            'Sesi asesmen tidak ditemukan.'
        );

        $version = session(
            'assessment_version_' . $attempt->attempt_id
        );

        abort_unless(
            in_array($version, ['reguler', 'adaptif'], true),
            403,
            'Versi asesmen tidak ditemukan di sesi ini.'
        );

        $questions = $this->getQuestionsForStudent(
            (int) $attempt->assessment_id,
            $version
        );

        abort_if(
            $questions->isEmpty(),
            404,
            'Soal untuk versi ini belum tersedia.'
        );

        $support = $this->getAdaptiveSupport(
            (int) $attempt->assessment_id,
            $questions,
            $version
        );

        $scaffoldingPerQuestion = $support['scaffolding'];
        $gambarPerQuestion = $support['images'];

        $savedAnswers = DB::table('student_answers')
            ->where(
                'student_assessment_id',
                $attempt->attempt_id
            )
            ->pluck('answer', 'question_id');

        $assessment = (object) [
            'id' => $attempt->assessment_id,
            'nama' => $attempt->nama,
            'mapel' => $attempt->mapel,
            'icon' => $attempt->icon,
            'color' => $attempt->color,
        ];

        $attemptId = $attempt->attempt_id;

        return view(
            'student.assessment-working',
            compact(
                'assessment',
                'questions',
                'version',
                'attemptId',
                'savedAnswers',
                'scaffoldingPerQuestion',
                'gambarPerQuestion'
            )
        );
    }
}
