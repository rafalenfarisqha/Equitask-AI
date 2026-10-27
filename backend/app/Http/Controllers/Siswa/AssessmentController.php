<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\DifyAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Hanya asesmen Published yang benar-benar ditugaskan
     * ke salah satu kelas siswa. Visibilitas public saja
     * tidak berarti otomatis menjadi tugas siswa.
     */
    private function studentCanAccessAssessment(
        int $assessmentId,
        int $studentId
    ): bool {
        return DB::table('assessments as a')
            ->where('a.id', $assessmentId)
            ->where('a.status', 'Published')
            ->whereExists(function ($query) use ($studentId) {
                $query->selectRaw('1')
                    ->from('assessment_class_assignments as aca')
                    ->join('class_members as cm', 'cm.class_id', '=', 'aca.class_id')
                    ->whereColumn('aca.assessment_id', 'a.id')
                    ->where('cm.student_id', $studentId);
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

    /**
     * Jalur asesmen ditentukan oleh guru pada anggota kelas.
     * Tanpa penugasan kelas, akses ditolak.
     * Konflik profil pada beberapa kelas harus diselesaikan guru.
     */
    private function getStudentAssessmentTrack(int $assessmentId, int $studentId): string
    {
        $tracks = DB::table('assessment_class_assignments as aca')
            ->join('class_members as cm', 'cm.class_id', '=', 'aca.class_id')
            ->where('aca.assessment_id', $assessmentId)
            ->where('cm.student_id', $studentId)
            ->distinct()
            ->pluck('cm.assessment_track')
            ->map(fn ($track) => strtolower(trim((string) ($track ?? 'reguler'))))
            ->unique()
            ->values();

        abort_if(
            $tracks->count() > 1,
            409,
            'Profil asesmen di beberapa kelas berbeda. Hubungi guru kelas.'
        );

        abort_if($tracks->isEmpty(), 404, 'Asesmen tidak ditugaskan ke kelasmu.');

        $track = $tracks->first();

        abort_unless(
            in_array($track, ['reguler', 'adaptif'], true),
            409,
            'Profil asesmen belum valid. Hubungi guru kelas.'
        );

        return $track;
    }

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

        $version = $this->getStudentAssessmentTrack(
            (int) $assessment->id,
            (int) Auth::id()
        );

        // Variabel kompatibilitas Blade lama: hanya satu yang true.
        $hasRegular = $version === 'reguler';
        $hasAdaptive = $version === 'adaptif';

        $questionCount = $this->getQuestionsForStudent(
            (int) $assessment->id,
            $version
        )->count();

        abort_if(
            $questionCount === 0,
            404,
            'Soal sesuai profilmu belum tersedia. Hubungi guru.'
        );

        return view(
            'student.assessment-instruction',
            compact(
                'assessment',
                'hasRegular',
                'hasAdaptive',
                'questionCount',
                'version'
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

        // Tidak menerima pilihan versi soal dari siswa.
        $version = $this->getStudentAssessmentTrack(
            (int) $id,
            (int) Auth::id()
        );

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
                    $isCorrect = null;
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

        // Setelah submit, baca ulang hasil dari database dan tampilkan
        // nilai + Analitik AI dalam halaman yang sama.
        return redirect()->route(
            'siswa.assessment.result',
            ['attemptId' => $attemptId]
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

        if (!in_array($version, ['reguler', 'adaptif'], true)) {
            $version = $this->getStudentAssessmentTrack(
                (int) $attempt->assessment_id,
                (int) Auth::id()
            );
            session([
                'assessment_attempt_id' => $attempt->attempt_id,
                'assessment_version_' . $attempt->attempt_id => $version,
            ]);
        }

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

    public function result(
        int $attemptId,
        DifyAnalyticsService $dify
    )
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
                'ans.answer',
                'ans.points_obtained',
                'ans.is_correct'
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
        $esaiBelumDinilai = 0;
        $poinEsai = 0.0;

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

                // Nilai esai yang sudah disimpan AI/guru harus
                // tetap terbaca pada halaman hasil siswa.
                if ($row->is_correct === null) {
                    $esaiBelumDinilai++;
                } else {
                    $poinEsai += max(0.0, (float) $row->points_obtained);
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
            ? ($esaiBelumDinilai > 0
                ? 'Jawaban pilihan ganda telah diperiksa. ' .
                  'Sebagian jawaban esai masih menunggu penilaian; ' .
                  'nilai keseluruhan belum final.'
                : 'Seluruh jawaban termasuk esai telah mendapatkan skor.')
            : 'Seluruh soal pilihan ganda telah diperiksa otomatis.';

        $earnedPoints = $poinPG + $poinEsai;

        /*
         * Kompatibilitas dengan halaman hasil lama.
         */
        $score = $totalPoints > 0
            ? round(
                ($earnedPoints / $totalPoints) * 100,
                2
            )
            : 0;

        /*
         * Analitik Dify: dijalankan untuk asesmen yang telah dikumpulkan,
         * termasuk jika terdapat esai yang belum diberi skor.
         *
         * Penting: analisis naratif BUKAN penilaian esai otomatis.
         * Angka nilai akhir tidak boleh diperlakukan sebagai final
         * sampai seluruh skor esai benar-benar tersedia di database.
         */
        $analisis = null;

        $questionIds = $rows->pluck('id')->all();
        $metadataSoal = DB::table('questions')
            ->whereIn('id', $questionIds)
            ->get(['id', 'question_text', 'materi', 'topik'])
            ->keyBy('id');

        $rekapMateri = [];
        $rekapTopik = [];
        $esaiUntukAnalitik = [];

        foreach ($rows as $row) {
            $metadata = $metadataSoal->get($row->id);
            $namaMateri = trim((string) ($metadata->materi ?? ''));
            $namaTopik = trim((string) ($metadata->topik ?? ''));
            $bobot = max(0.0, (float) $row->points);
            $isEssay = $this->isEssayQuestion($row);
            $jawabanSiswa = trim((string) ($row->answer ?? ''));

            if ($isEssay) {
                $esaiUntukAnalitik[] = [
                    'question_id' => (int) $row->id,
                    'pertanyaan' => (string) ($metadata->question_text ?? ''),
                    'jawaban_siswa' => $jawabanSiswa,
                    'materi' => $namaMateri,
                    'topik' => $namaTopik,
                    'skor_maksimum' => $bobot,
                    'status_penilaian' => $row->is_correct === null ? 'belum_dinilai' : 'sudah_dinilai',
                ];
                continue;
            }

            $opsi = $this->decodeArray($row->options);
            $tepat = $jawabanSiswa !== ''
                && array_key_exists($jawabanSiswa, $opsi)
                && $this->checkMultipleChoiceAnswer(
                    $jawabanSiswa,
                    $opsi,
                    $row->correct_answer
                );

            if ($namaMateri !== '') {
                if (!isset($rekapMateri[$namaMateri])) {
                    $rekapMateri[$namaMateri] = [
                        'nama' => $namaMateri, 'benar' => 0, 'salah' => 0,
                        'skor_diperoleh' => 0, 'skor_maksimum_dinilai' => 0,
                    ];
                }
                $rekapMateri[$namaMateri][$tepat ? 'benar' : 'salah']++;
                $rekapMateri[$namaMateri]['skor_diperoleh'] += $tepat ? $bobot : 0;
                $rekapMateri[$namaMateri]['skor_maksimum_dinilai'] += $bobot;
            }
            if ($namaTopik !== '') {
                if (!isset($rekapTopik[$namaTopik])) {
                    $rekapTopik[$namaTopik] = [
                        'nama' => $namaTopik, 'benar' => 0, 'salah' => 0,
                        'skor_diperoleh' => 0, 'skor_maksimum_dinilai' => 0,
                    ];
                }
                $rekapTopik[$namaTopik][$tepat ? 'benar' : 'salah']++;
                $rekapTopik[$namaTopik]['skor_diperoleh'] += $tepat ? $bobot : 0;
                $rekapTopik[$namaTopik]['skor_maksimum_dinilai'] += $bobot;
            }
        }

        $formatRekap = static function (array $rekap): array {
            foreach ($rekap as &$item) {
                $maksimum = $item['skor_maksimum_dinilai'];
                $item['ketepatan'] = $maksimum > 0
                    ? round($item['skor_diperoleh'] / $maksimum * 100, 2)
                    : null;
            }
            unset($item);
            return array_values($rekap);
        };

        $laporan = [
            'jenis' => 'siswa',
            'hasil' => [
                'mapel' => $attempt->mapel,
                'score' => $esaiBelumDinilai > 0 ? null : $score,
                'nilai_pg' => $nilaiPG,
                'penilaian_lengkap' => $esaiBelumDinilai === 0,
                'benar' => $benar,
                'salah' => $salah,
                'belum_dijawab' => $belumDijawab,
                'belum_dinilai' => $esaiBelumDinilai,
                'jumlah_jawaban' => $rows->count(),
                'materi' => $formatRekap($rekapMateri),
                'topik' => $formatRekap($rekapTopik),
                'esai' => $esaiUntukAnalitik,
            ],
        ];

        $cacheKey = 'equitask:analitik:siswa:' . $attemptId . ':'
            . hash('sha256', json_encode(
                $laporan,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ));

        $analisis = Cache::get($cacheKey);
        if (!is_array($analisis)) {
            $analisis = $dify->analisis('siswa', $laporan);
            if (is_array($analisis)) {
                Cache::put($cacheKey, $analisis, now()->addHours(6));
            }
        }

        return view(
            'student.assessment-result',
            compact(
                'attemptId',
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
                'catatanEsai',
                'analisis'
            )
        );
    }
}
