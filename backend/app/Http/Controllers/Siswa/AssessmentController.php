<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AKSES SISWA
    |--------------------------------------------------------------------------
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

    private function studentCanAccessAssessment(
        int $assessmentId,
        int $studentId
    ): bool {
        return DB::table('assessments as a')
            ->where('a.id', $assessmentId)
            ->where('a.status', 'Published')
            ->where(function ($query) use ($studentId) {
                $query
                    ->where('a.visibility', 'public')
                    ->orWhereExists(function ($subquery) use ($studentId) {
                        $subquery
                            ->selectRaw('1')
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
                            ->where(
                                'cm.student_id',
                                $studentId
                            );
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

    /*
    |--------------------------------------------------------------------------
    | HELPER DATA
    |--------------------------------------------------------------------------
    */

    private function decodeArray($value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Soal esai dikenali dari jenis soal
     * atau dari opsi jawaban yang kosong.
     *
     * Aturan ini sama dengan Blade pengerjaan siswa.
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

    /*
    |--------------------------------------------------------------------------
    | DATA DIFY YANG SUDAH TERSIMPAN
    |--------------------------------------------------------------------------
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
                $text = trim($item);

                if ($text !== '') {
                    $result[] = $text;
                }

                continue;
            }

            if (is_array($item)) {
                $text = $item['teks']
                    ?? $item['petunjuk']
                    ?? $item['langkah']
                    ?? null;

                if (
                    is_string($text)
                    && trim($text) !== ''
                ) {
                    $result[] = trim($text);
                }
            }
        }

        return $result;
    }

    /**
     * Menyiapkan gambar dan dukungan scaffolding
     * untuk soal Adaptif.
     *
     * Tidak memanggil API Dify kembali.
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

        if (
            $version !== 'adaptif'
            || $questions->isEmpty()
        ) {
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

        /*
         * Hindari memasangkan gambar atau petunjuk
         * ke soal yang tidak sesuai.
         */
        if (
            count($sourceQuestions)
            !== $questions->count()
        ) {
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

        /*
         * Ambil gambar dari output Dify.
         */
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

            /*
             * Format output Dify dapat bertingkat.
             */
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
                (string) parse_url(
                    $url,
                    PHP_URL_SCHEME
                )
            );

            if (
                !in_array(
                    $scheme,
                    ['http', 'https'],
                    true
                )
            ) {
                continue;
            }

            $questionId =
                $databaseIdPerSourceId[$sourceId];

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

    /*
    |--------------------------------------------------------------------------
    | SOAL YANG BOLEH DILIHAT SISWA
    |--------------------------------------------------------------------------
    */

    private function getQuestionsForStudent(
        int $assessmentId,
        string $version
    ) {
        return DB::table('assessment_questions as aq')
            ->join(
                'questions as q',
                'q.id',
                '=',
                'aq.question_id'
            )
            ->where(
                'aq.assessment_id',
                $assessmentId
            )
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

    /*
    |--------------------------------------------------------------------------
    | HALAMAN PETUNJUK
    |--------------------------------------------------------------------------
    */

    public function instructions($id)
    {
        $this->ensureStudent();
        $this->ensureAssessmentAccessible((int) $id);

        $assessment = DB::table('assessments as a')
            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                'a.user_id'
            )
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
            ->join(
                'questions as q',
                'q.id',
                '=',
                'aq.question_id'
            )
            ->where(
                'aq.assessment_id',
                $assessment->id
            )
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
            ->join(
                'questions as q',
                'q.id',
                '=',
                'aq.question_id'
            )
            ->where(
                'aq.assessment_id',
                $assessment->id
            )
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

    /*
    |--------------------------------------------------------------------------
    | MULAI PENGERJAAN - PERBAIKAN DUPLICATE KEY
    |--------------------------------------------------------------------------
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

        /*
         * PENTING:
         *
         * Periksa database terlebih dahulu.
         * Jangan langsung membuat pengerjaan baru
         * hanya karena session tidak ditemukan.
         */
        $studentId = (int) Auth::id();

        $attempt = DB::table('student_assessments')
            ->where(
                'assessment_id',
                $assessment->id
            )
            ->where(
                'student_id',
                $studentId
            )
            ->first();

        /*
         * SUDAH SELESAI:
         * Arahkan siswa ke hasil pengerjaan lama.
         */
        if (
            $attempt
            && in_array(
                strtolower((string) $attempt->status),
                ['submitted', 'completed'],
                true
            )
        ) {
            session()->forget([
                'assessment_attempt_id',
                'assessment_version_' . $attempt->id,
            ]);

            return redirect()->route(
                'siswa.assessment.result',
                ['attemptId' => $attempt->id]
            );
        }

        /*
         * Pastikan versi soal tersedia.
         */
        $questions = $this->getQuestionsForStudent(
            (int) $assessment->id,
            $version
        );

        abort_if(
            $questions->isEmpty(),
            404,
            'Versi soal ini belum tersedia.'
        );

        /*
         * Ambil gambar dan scaffolding Adaptif.
         */
        $support = $this->getAdaptiveSupport(
            (int) $assessment->id,
            $questions,
            $version
        );

        $scaffoldingPerQuestion =
            $support['scaffolding'];

        $gambarPerQuestion =
            $support['images'];

        /*
         * MASIH DIKERJAKAN:
         * Gunakan attempt lama.
         */
        if ($attempt) {
            abort_unless(
                $attempt->status === 'in_progress',
                409,
                'Status pengerjaan belum dapat dilanjutkan.'
            );

            $attemptId = $attempt->id;

            $savedVersion = session(
                'assessment_version_' . $attemptId
            );

            if (
                $savedVersion
                && $savedVersion !== $version
            ) {
                return redirect()
                    ->route(
                        'siswa.assessment.instructions',
                        $id
                    )
                    ->with(
                        'error',
                        'Selesaikan versi yang sedang dikerjakan terlebih dahulu.'
                    );
            }

        } else {

            /*
             * BELUM PERNAH DIKERJAKAN:
             * Baru buat pengerjaan baru.
             */
            $attemptId = DB::table('student_assessments')
                ->insertGetId([
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        /*
         * Simpan sesi dan versi yang dipilih.
         */
        session([
            'assessment_attempt_id' => $attemptId,
            'assessment_version_' . $attemptId => $version,
        ]);

        /*
         * Ambil jawaban yang sudah tersimpan,
         * jika pengerjaan dilanjutkan.
         */
        $savedAnswers = DB::table('student_answers')
            ->where(
                'student_assessment_id',
                $attemptId
            )
            ->pluck(
                'answer',
                'question_id'
            );

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

    /*
    |--------------------------------------------------------------------------
    | HELPER PENILAIAN PILIHAN GANDA
    |--------------------------------------------------------------------------
    */

    private function normalizeAnswer($value): string
    {
        return mb_strtoupper(
            trim((string) $value),
            'UTF-8'
        );
    }

    /**
     * Mendukung format jawaban:
     * - key opsi 0, 1, 2, 3
     * - label A, B, C, D
     * - teks opsi
     */
    private function checkMultipleChoiceAnswer(
        string $answer,
        array $options,
        $correctAnswer
    ): bool {
        if (!array_key_exists($answer, $options)) {
            return false;
        }

        $selectedOption = $options[$answer];

        $selectedData = is_array($selectedOption)
            ? $selectedOption
            : [];

        $selectedLabel = $selectedData['label']
            ?? (
                ctype_digit($answer)
                    ? chr(65 + (int) $answer)
                    : $answer
            );

        $selectedText = $selectedData['teks']
            ?? $selectedData['text']
            ?? (
                is_scalar($selectedOption)
                    ? (string) $selectedOption
                    : ''
            );

        $correct = $this->normalizeAnswer(
            $correctAnswer
        );

        if ($correct === '') {
            return false;
        }

        $accepted = [
            $this->normalizeAnswer($answer),
            $this->normalizeAnswer($selectedLabel),
            $this->normalizeAnswer($selectedText),
        ];

        return in_array(
            $correct,
            $accepted,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS DAN KALIMAT MOTIVASI SISWA
    |--------------------------------------------------------------------------
    */

    private function makeStudentFeedback(
        ?float $nilaiPG
    ): array {
        if ($nilaiPG === null) {
            return [
                'kategori' => 'Menunggu Penilaian',
                'pesan' =>
                    'Terima kasih sudah menyelesaikan asesmen! ' .
                    'Jawaban esaimu akan diperiksa oleh guru. ' .
                    'Tetap semangat belajar dan terus berkembang, ya!',
            ];
        }

        if ($nilaiPG >= 85) {
            return [
                'kategori' => 'Sangat Baik',
                'pesan' =>
                    'Hebat! Pemahamanmu sudah sangat baik. ' .
                    'Pertahankan semangat belajarmu dan terus ' .
                    'tantang dirimu dengan hal-hal baru!',
            ];
        }

        if ($nilaiPG >= 70) {
            return [
                'kategori' => 'Baik',
                'pesan' =>
                    'Bagus! Kamu sudah memahami banyak materi. ' .
                    'Yuk, pelajari kembali beberapa bagian yang ' .
                    'masih sulit agar hasilmu semakin meningkat!',
            ];
        }

        return [
            'kategori' => 'Terus Berkembang',
            'pesan' =>
                'Terima kasih sudah berusaha! Setiap latihan ' .
                'adalah kesempatan untuk berkembang. ' .
                'Coba pelajari kembali materi dan terus berlatih, ' .
                'ya. Kemajuan bisa dimulai dari langkah kecil!',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | KUMPULKAN JAWABAN SISWA
    |--------------------------------------------------------------------------
    */

    public function submit(Request $request, $id)
    {
        $this->ensureStudent();

        $validated = $request->validate([
            'attempt_id' => [
                'required',
                'integer',
            ],
            'answers' => [
                'sometimes',
                'array',
            ],
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
            ->first();

        abort_unless(
            $attempt,
            403,
            'Sesi pengerjaan tidak valid.'
        );

        /*
         * Jika pengerjaan sudah dikumpulkan,
         * jangan mengirim atau menilai ulang.
         */
        if (
            in_array(
                (string) $attempt->status,
                ['submitted', 'completed'],
                true
            )
        ) {
            return redirect()->route(
                'siswa.assessment.result',
                ['attemptId' => $attemptId]
            );
        }

        abort_unless(
            $attempt->status === 'in_progress',
            403,
            'Sesi pengerjaan tidak aktif.'
        );

        $this->ensureAssessmentAccessible((int) $id);

        $version = session(
            'assessment_version_' . $attemptId
        );

        abort_unless(
            in_array(
                $version,
                ['reguler', 'adaptif'],
                true
            ),
            403,
            'Versi soal tidak valid.'
        );

        $answers = $validated['answers'] ?? [];

        $questions = DB::table('assessment_questions as aq')
            ->join(
                'questions as q',
                'q.id',
                '=',
                'aq.question_id'
            )
            ->where(
                'aq.assessment_id',
                $id
            )
            ->whereRaw(
                'LOWER(TRIM(q.version_type)) = ?',
                [$version]
            )
            ->orderBy('aq.question_order')
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

        /*
         * Validasi ID soal.
         */
        $validIds = $questions
            ->pluck('id')
            ->map(
                fn ($questionId) =>
                    (string) $questionId
            )
            ->all();

        foreach ($answers as $questionId => $answer) {
            if (
                !in_array(
                    (string) $questionId,
                    $validIds,
                    true
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'answers' =>
                            'Ada jawaban yang tidak sesuai dengan asesmen ini.',
                    ]);
            }

            if (
                $answer !== null
                && !is_string($answer)
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'answers' =>
                            'Format jawaban tidak valid.',
                    ]);
            }
        }

        /*
         * Statistik pilihan ganda.
         */
        $benar = 0;
        $salah = 0;
        $belumDijawab = 0;
        $jumlahPG = 0;

        $poinPG = 0.0;
        $poinPGMaksimal = 0.0;

        /*
         * Statistik esai.
         */
        $jumlahEsai = 0;
        $esaiTerisi = 0;

        $totalPoints = (float) $questions->sum(
            fn ($question) =>
                (float) $question->points
        );

        DB::transaction(function () use (
            $questions,
            $answers,
            $attemptId,
            $totalPoints,
            &$benar,
            &$salah,
            &$belumDijawab,
            &$jumlahPG,
            &$jumlahEsai,
            &$esaiTerisi,
            &$poinPG,
            &$poinPGMaksimal
        ) {
            foreach ($questions as $question) {
                $answer = $answers[$question->id] ?? null;

                $isEssay = $this->isEssayQuestion(
                    $question
                );

                $bobot = (float) $question->points;

                $isCorrect = false;
                $points = 0.0;

                if ($isEssay) {
                    /*
                     * Esai belum dinilai guru.
                     */
                    $jumlahEsai++;

                    $answer = is_string($answer)
                        ? trim($answer)
                        : null;

                    if ($answer === '') {
                        $answer = null;
                    }

                    if ($answer !== null) {
                        $esaiTerisi++;
                    }

                    /*
                     * Nilai teknis sementara.
                     * Bukan berarti esainya salah.
                     */
                    $isCorrect = false;
                    $points = 0.0;

                } else {
                    /*
                     * Pilihan ganda.
                     */
                    $jumlahPG++;

                    $poinPGMaksimal += $bobot;

                    $options = $this->decodeArray(
                        $question->options
                    );

                    $answer = is_string($answer)
                        ? trim($answer)
                        : null;

                    if (
                        $answer !== null
                        && !array_key_exists(
                            $answer,
                            $options
                        )
                    ) {
                        $answer = null;
                    }

                    if (
                        $answer === null
                        || $answer === ''
                    ) {
                        $belumDijawab++;

                    } else {
                        $isCorrect =
                            $this->checkMultipleChoiceAnswer(
                                $answer,
                                $options,
                                $question->correct_answer
                            );

                        if ($isCorrect) {
                            $benar++;
                            $points = $bobot;
                            $poinPG += $points;
                        } else {
                            $salah++;
                        }
                    }
                }

                DB::table('student_answers')
                    ->updateOrInsert(
                        [
                            'student_assessment_id' =>
                                $attemptId,
                            'question_id' =>
                                $question->id,
                        ],
                        [
                            'answer' => $answer,
                            'is_correct' => $isCorrect,
                            'points_obtained' => $points,
                            'answered_at' => now(),
                        ]
                    );
            }

            /*
             * Skor database masih sementara
             * apabila terdapat soal esai.
             */
            $scoreSementara = $totalPoints > 0
                ? round(
                    ($poinPG / $totalPoints) * 100,
                    2
                )
                : 0;

            DB::table('student_assessments')
                ->where('id', $attemptId)
                ->where('status', 'in_progress')
                ->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'score' => $scoreSementara,
                    'updated_at' => now(),
                ]);
        });

        /*
         * Nilai dari soal pilihan ganda saja.
         */
        $nilaiPG = $poinPGMaksimal > 0
            ? round(
                ($poinPG / $poinPGMaksimal) * 100,
                2
            )
            : null;

        $hasEssay = $jumlahEsai > 0;

        $esaiBelumTerisi =
            $jumlahEsai - $esaiTerisi;

        $feedback = $this->makeStudentFeedback(
            $nilaiPG
        );

        $kategoriHasil = $feedback['kategori'];
        $kalimatHasil = $feedback['pesan'];

        $catatanEsai = $hasEssay
            ? 'Hasil pilihan ganda telah diperiksa otomatis. ' .
              'Jawaban esai masih menunggu koreksi guru, ' .
              'sehingga nilai keseluruhan belum final.'
            : 'Seluruh soal pilihan ganda telah diperiksa otomatis.';

        /*
         * Variabel kompatibilitas halaman hasil.
         */
        $earnedPoints = $poinPG;

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
                'hasEssay',
                'nilaiPG',
                'benar',
                'salah',
                'belumDijawab',
                'jumlahPG',
                'jumlahEsai',
                'esaiTerisi',
                'esaiBelumTerisi',
                'kategoriHasil',
                'kalimatHasil',
                'catatanEsai'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LANJUTKAN PENGERJAAN
    |--------------------------------------------------------------------------
    */

    public function work($id, $attemptId = null)
    {
        $this->ensureStudent();

        $this->ensureAssessmentAccessible(
            (int) $id
        );

        /*
         * Route lama tidak selalu membawa
         * attemptId sebagai parameter.
         */
        $attemptId = $attemptId
            ?? session('assessment_attempt_id');

        /*
         * Jika sesi hilang, cari pengerjaan
         * yang masih aktif di database.
         */
        if (!$attemptId) {
            $existingAttempt = DB::table(
                'student_assessments'
            )
                ->where('assessment_id', $id)
                ->where(
                    'student_id',
                    (int) Auth::id()
                )
                ->first();

            if (
                $existingAttempt
                && in_array(
                    $existingAttempt->status,
                    ['submitted', 'completed'],
                    true
                )
            ) {
                return redirect()->route(
                    'siswa.assessment.result',
                    ['attemptId' => $existingAttempt->id]
                );
            }

            $attemptId = $existingAttempt->id ?? null;
        }

        abort_unless(
            $attemptId,
            404,
            'Sesi pengerjaan tidak ditemukan.'
        );

        $attempt = DB::table('student_assessments as sa')
            ->join(
                'assessments as a',
                'a.id',
                '=',
                'sa.assessment_id'
            )
            ->where('sa.id', $attemptId)
            ->where('sa.assessment_id', $id)
            ->where(
                'sa.student_id',
                Auth::id()
            )
            ->where('a.status', 'Published')
            ->select(
                'sa.id as attempt_id',
                'sa.assessment_id',
                'sa.status',
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

        /*
         * Jika sudah dikumpulkan,
         * buka halaman hasil.
         */
        if (
            in_array(
                $attempt->status,
                ['submitted', 'completed'],
                true
            )
        ) {
            return redirect()->route(
                'siswa.assessment.result',
                ['attemptId' => $attempt->attempt_id]
            );
        }

        abort_unless(
            $attempt->status === 'in_progress',
            403,
            'Sesi pengerjaan tidak aktif.'
        );

        $version = session(
            'assessment_version_' .
            $attempt->attempt_id
        );

        abort_unless(
            in_array(
                $version,
                ['reguler', 'adaptif'],
                true
            ),
            403,
            'Versi asesmen tidak ditemukan di sesi ini. Silakan pilih versi melalui halaman petunjuk.'
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

        $scaffoldingPerQuestion =
            $support['scaffolding'];

        $gambarPerQuestion =
            $support['images'];

        $savedAnswers = DB::table('student_answers')
            ->where(
                'student_assessment_id',
                $attempt->attempt_id
            )
            ->pluck(
                'answer',
                'question_id'
            );

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

    /*
    |--------------------------------------------------------------------------
    | LIHAT KEMBALI HASIL DARI RIWAYAT SISWA
    |--------------------------------------------------------------------------
    */

    public function result(int $attemptId)
    {
        $this->ensureStudent();

        /*
         * Hanya pemilik pengerjaan
         * yang boleh melihat hasilnya.
         */
        $attempt = DB::table('student_assessments as sa')
            ->join(
                'assessments as a',
                'a.id',
                '=',
                'sa.assessment_id'
            )
            ->where(
                'sa.id',
                $attemptId
            )
            ->where(
                'sa.student_id',
                Auth::id()
            )
            ->whereIn(
                'sa.status',
                ['submitted', 'completed']
            )
            ->select(
                'sa.id',
                'sa.assessment_id',
                'sa.status',
                'sa.score',
                'a.nama',
                'a.mapel'
            )
            ->first();

        abort_unless(
            $attempt,
            404,
            'Hasil pengerjaan tidak ditemukan.'
        );

        /*
         * Ambil seluruh jawaban siswa
         * yang sudah tersimpan di database.
         */
        $rows = DB::table('student_answers as ans')
            ->join(
                'questions as q',
                'q.id',
                '=',
                'ans.question_id'
            )
            ->join(
                'assessment_questions as aq',
                'aq.question_id',
                '=',
                'q.id'
            )
            ->where(
                'ans.student_assessment_id',
                $attempt->id
            )
            ->where(
                'aq.assessment_id',
                $attempt->assessment_id
            )
            ->orderBy('aq.question_order')
            ->select(
                'q.id',
                'q.question_type',
                'q.options',
                'q.correct_answer',
                'aq.points',
                'ans.answer'
            )
            ->get();

        abort_if(
            $rows->isEmpty(),
            404,
            'Data jawaban pengerjaan ini belum ditemukan.'
        );

        /*
         * Statistik pilihan ganda.
         */
        $benar = 0;
        $salah = 0;
        $belumDijawab = 0;
        $jumlahPG = 0;

        $poinPG = 0.0;
        $poinPGMaksimal = 0.0;

        /*
         * Statistik esai.
         */
        $jumlahEsai = 0;
        $esaiTerisi = 0;

        $totalPoints = 0.0;

        foreach ($rows as $row) {
            $bobot = (float) $row->points;

            $totalPoints += $bobot;

            $jawaban = is_string($row->answer)
                ? trim($row->answer)
                : null;

            if ($this->isEssayQuestion($row)) {
                /*
                 * Esai belum dikoreksi guru.
                 * Tidak dihitung sebagai salah.
                 */
                $jumlahEsai++;

                if (
                    $jawaban !== null
                    && $jawaban !== ''
                ) {
                    $esaiTerisi++;
                }

                continue;
            }

            /*
             * Hitung hasil pilihan ganda.
             */
            $jumlahPG++;
            $poinPGMaksimal += $bobot;

            $options = $this->decodeArray(
                $row->options
            );

            if (
                $jawaban === null
                || $jawaban === ''
                || !array_key_exists(
                    $jawaban,
                    $options
                )
            ) {
                $belumDijawab++;
                continue;
            }

            $isCorrect = $this->checkMultipleChoiceAnswer(
                $jawaban,
                $options,
                $row->correct_answer
            );

            if ($isCorrect) {
                $benar++;
                $poinPG += $bobot;
            } else {
                $salah++;
            }
        }

        /*
         * Nilai pilihan ganda dari data tersimpan.
         */
        $nilaiPG = $poinPGMaksimal > 0
            ? round(
                ($poinPG / $poinPGMaksimal) * 100,
                2
            )
            : null;

        $hasEssay = $jumlahEsai > 0;

        $esaiBelumTerisi =
            $jumlahEsai - $esaiTerisi;

        /*
         * Umpan balik dari backend.
         */
        $feedback = $this->makeStudentFeedback(
            $nilaiPG
        );

        $kategoriHasil = $feedback['kategori'];
        $kalimatHasil = $feedback['pesan'];

        $catatanEsai = $hasEssay
            ? 'Jawaban pilihan ganda telah diperiksa otomatis. ' .
              'Jawaban esai masih menunggu koreksi guru, ' .
              'sehingga nilai keseluruhan belum final.'
            : 'Seluruh soal pilihan ganda telah diperiksa otomatis.';

        $earnedPoints = $poinPG;

        /*
         * Kompatibilitas dengan halaman hasil lama.
         */
        $score = $totalPoints > 0
            ? round(
                ($earnedPoints / $totalPoints) * 100,
                2
            )
            : 0;

        return view(
            'student.assessment-result',
            compact(
                'score',
                'earnedPoints',
                'totalPoints',
                'hasEssay',
                'nilaiPG',
                'benar',
                'salah',
                'belumDijawab',
                'jumlahPG',
                'jumlahEsai',
                'esaiTerisi',
                'esaiBelumTerisi',
                'kategoriHasil',
                'kalimatHasil',
                'catatanEsai'
            )
        );
    }
}
