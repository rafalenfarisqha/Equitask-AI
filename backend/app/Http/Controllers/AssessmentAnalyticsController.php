<?php

namespace App\Http\Controllers;

use App\Services\DifyAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssessmentAnalyticsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ANALITIK SISWA
    |--------------------------------------------------------------------------
    |
    | Hanya siswa pemilik pengerjaan yang dapat melihat hasil.
    | Hanya pengerjaan yang sudah dikumpulkan yang dianalisis.
    |
    */

    public function siswa(
        Request $request,
        int $id,
        DifyAnalyticsService $dify
    ) {
        $userId = $request->user()->id;

        $pengerjaan = DB::table('student_assessments')
            ->where('id', $id)
            ->where('student_id', $userId)
            ->first();

        abort_unless($pengerjaan, 404);

        abort_unless(
            $pengerjaan->submitted_at !== null,
            403,
            'Asesmen belum dikumpulkan.'
        );

        $assessment = DB::table('assessments')
            ->where('id', $pengerjaan->assessment_id)
            ->first();

        abort_unless($assessment, 404);

        $jawaban = $this->jawabanTerbaru(
            collect([$pengerjaan->id])
        );

        $statistik = $this->hitungJawaban($jawaban);

        $materi = $this->analisisMateri($jawaban);
        $topik = $this->analisisTopik($jawaban);
        $esaiUntukDify = $this->dataEsaiUntukDify($jawaban);
        $penilaianLengkap = $pengerjaan->score !== null
            && $statistik['belum_dinilai'] === 0;

        $laporan = [
            'jenis' => 'siswa',

            'hasil' => [
                'mapel' => $assessment->mapel,

                'score' => $pengerjaan->score,

                'benar' => $statistik['benar'],

                'salah' => $statistik['salah'],

                'belum_dinilai' => $statistik['belum_dinilai'],

                'jumlah_jawaban' => $jawaban->count(),

                'penilaian_lengkap' => $penilaianLengkap,
                'status_nilai' => $penilaianLengkap ? 'final' : 'sementara',
                'catatan_nilai' => $penilaianLengkap
                    ? 'Semua jawaban telah dinilai.'
                    : 'Ada jawaban yang belum dinilai. Jangan mengarang skor akhir.',
                'materi' => $materi,
                'topik' => $topik,
                // Dify dapat menggunakan data ini jika workflow-nya mendukung penilaian esai.
                'esai_belum_dinilai' => $esaiUntukDify,
            ],
        ];

        // Analitik Dify boleh berjalan setelah jawaban dikumpulkan,
        // meskipun koreksi esai belum selesai. Hasil tersebut bersifat sementara.
        // Controller ini tidak menulis skor esai ke database.
        $analisis = $jawaban->isNotEmpty()
            ? $this->analisisDenganCache($dify, 'siswa', $laporan)
            : null;

        return view('analytics.siswa', [
            'assessment' => $assessment,

            'pengerjaan' => $pengerjaan,

            'benar' => $statistik['benar'],

            'salah' => $statistik['salah'],

            'belumDinilai' => $statistik['belum_dinilai'],

            'analisis' => $analisis,
            'penilaianLengkap' => $penilaianLengkap,
            'topik' => $topik,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALITIK GURU
    |--------------------------------------------------------------------------
    |
    | Guru hanya dapat melihat asesmen miliknya.
    | Peserta dihitung dari kelas yang ditugaskan
    | pada asesmen tersebut.
    |
    */

    public function guru(
        Request $request,
        int $assessmentId,
        DifyAnalyticsService $dify
    ) {
        $guruId = $request->user()->id;

        $assessment = DB::table('assessments')
            ->where('id', $assessmentId)
            ->where('user_id', $guruId)
            ->first();

        abort_unless($assessment, 404);

        $classIds = DB::table(
            'assessment_class_assignments'
        )
            ->join(
                'classes',
                'classes.id',
                '=',
                'assessment_class_assignments.class_id'
            )
            ->where(
                'assessment_class_assignments.assessment_id',
                $assessmentId
            )
            ->where('classes.teacher_id', $guruId)
            ->pluck('classes.id');

        $studentIds = DB::table('class_members')
            ->whereIn('class_id', $classIds)
            ->distinct()
            ->pluck('student_id');

        $jumlahSiswa = $studentIds->count();

        /*
         * Hitung pengerjaan yang SUDAH dikumpulkan.
         * Ambil pengerjaan terkumpul terbaru per siswa.
         *
         * Draft baru tidak boleh menggantikan
         * pengerjaan terkumpul sebelumnya.
         */
        $pengerjaan = DB::table('student_assessments')
            ->where('assessment_id', $assessmentId)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get()
            ->unique('student_id')
            ->values();

        $selesai = $pengerjaan->count();

        $jawaban = $this->jawabanTerbaru(
            $pengerjaan->pluck('id')
        );

        /*
         * Identifikasi pengerjaan dengan jawaban
         * yang masih menunggu penilaian.
         */
        $attemptBelumDinilai = $jawaban
            ->filter(
                fn ($item) => $item->is_correct === null
            )
            ->pluck('student_assessment_id')
            ->unique();

        /*
         * Nilai dianggap final jika score tersedia
         * dan tidak ada jawaban yang belum dinilai.
         */
        $dinilaiPenuh = $pengerjaan->filter(
            fn ($item) =>
                $item->score !== null
                && ! $attemptBelumDinilai->contains($item->id)
        );

        $jumlahDinilai = $dinilaiPenuh->count();

        $rataRata = $dinilaiPenuh->isNotEmpty()
            ? round(
                (float) $dinilaiPenuh->avg('score'),
                2
            )
            : null;

        $tertinggi = $dinilaiPenuh->isNotEmpty()
            ? $dinilaiPenuh->max('score')
            : null;

        $terendah = $dinilaiPenuh->isNotEmpty()
            ? $dinilaiPenuh->min('score')
            : null;

        $penyelesaian = $jumlahSiswa > 0
            ? round(
                $selesai / $jumlahSiswa * 100,
                2
            )
            : 0;

        /*
         * Statistik butir soal hanya dihitung
         * dari jawaban yang telah dinilai.
         */
        $jawabanDinilai = $jawaban->filter(
            fn ($item) =>
                $item->is_correct !== null
                && $dinilaiPenuh->contains(
                    'id',
                    $item->student_assessment_id
                )
        );

        $materi = $this->analisisMateri(
            $jawabanDinilai
        );
        $topik = $this->analisisTopik($jawabanDinilai);

        $soalSeringSalah = $jawabanDinilai
            ->filter(
                fn ($item) =>
                    (int) $item->is_correct === 0
            )
            ->groupBy('question_id')
            ->map(
                fn ($items, $questionId) => [
                    'question_id' => (int) $questionId,
                    'jumlah_salah' => $items->count(),
                ]
            )
            ->sortByDesc('jumlah_salah')
            ->take(5)
            ->values()
            ->all();

        $statistikJawaban = $this->hitungJawaban(
            $jawabanDinilai
        );

        $laporan = [
            'jenis' => 'kelas',

            'hasil' => [
                'mapel' => $assessment->mapel,

                'jumlah_siswa' => $jumlahSiswa,

                'selesai' => $selesai,

                'dinilai' => $jumlahDinilai,

                'belum_tercatat_selesai' =>
                    max(0, $jumlahSiswa - $selesai),

                'selesai_belum_dinilai_penuh' =>
                    max(0, $selesai - $jumlahDinilai),

                'rata_rata' => $rataRata,

                'tertinggi' => $tertinggi,

                'terendah' => $terendah,

                'penyelesaian' => $penyelesaian,

                'benar' => $statistikJawaban['benar'],

                'salah' => $statistikJawaban['salah'],

                'materi' => $materi,
                'topik' => $topik,
                'status_nilai' => $selesai === $jumlahDinilai ? 'final' : 'sebagian',

                'soal_sering_salah' => $soalSeringSalah,
            ],
        ];

        /*
         * Jika belum ada pengerjaan dengan
         * penilaian lengkap, jangan mengarang
         * kesimpulan hasil belajar kelas.
         */
        $analisis = $selesai > 0
            ? $this->analisisDenganCache(
                $dify,
                'guru',
                $laporan
            )
            : null;

        return view('analytics.guru', [
            'assessment' => $assessment,

            'jumlahSiswa' => $jumlahSiswa,

            'selesai' => $selesai,

            'dinilai' => $jumlahDinilai,

            'rataRata' => $rataRata,

            'analisis' => $analisis,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL JAWABAN TERBARU
    |--------------------------------------------------------------------------
    |
    | Jika satu question_id memiliki beberapa
    | student_answers dalam pengerjaan yang sama,
    | hanya baris terbaru yang dihitung.
    |
    */

    private function jawabanTerbaru(
        Collection $attemptIds
    ): Collection {
        if ($attemptIds->isEmpty()) {
            return collect();
        }

        return DB::table('student_answers')
            ->whereIn(
                'student_assessment_id',
                $attemptIds->all()
            )
            ->orderByDesc('id')
            ->get()
            ->unique(
                fn ($item) =>
                    $item->student_assessment_id
                    . ':'
                    . $item->question_id
            )
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG BENAR, SALAH, BELUM DINILAI
    |--------------------------------------------------------------------------
    */

    private function hitungJawaban(
        Collection $jawaban
    ): array {
        return [
            'benar' => $jawaban->filter(
                fn ($item) =>
                    $item->is_correct !== null
                    && (int) $item->is_correct === 1
            )->count(),

            'salah' => $jawaban->filter(
                fn ($item) =>
                    $item->is_correct !== null
                    && (int) $item->is_correct === 0
            )->count(),

            'belum_dinilai' => $jawaban->filter(
                fn ($item) =>
                    $item->is_correct === null
            )->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ANALISIS PER MATERI
    |--------------------------------------------------------------------------
    |
    | Pemetaan materi hanya digunakan jika:
    | - tabel questions tersedia,
    | - terdapat kolom materi/topik yang dikenali.
    |
    | Tidak membuat nama topik fiktif.
    |
    */

    private function analisisMateri(
        Collection $jawaban
    ): array {
        if (
            $jawaban->isEmpty()
            || ! Schema::hasTable('questions')
        ) {
            return [];
        }

        $pilihanKolom = [
            'materi',
            'topik',
            'topic',
            'kompetensi',
            'bab',
        ];

        $kolomMateri = null;

        foreach ($pilihanKolom as $kolom) {
            if (
                Schema::hasColumn('questions', $kolom)
            ) {
                $kolomMateri = $kolom;
                break;
            }
        }

        if ($kolomMateri === null) {
            return [];
        }

        $jawabanIds = $jawaban
            ->pluck('id')
            ->all();

        $data = DB::table('student_answers')
            ->join(
                'questions',
                'questions.id',
                '=',
                'student_answers.question_id'
            )
            ->whereIn(
                'student_answers.id',
                $jawabanIds
            )
            ->select(
                'student_answers.is_correct',
                "questions.{$kolomMateri} as nama_materi"
            )
            ->get();

        return $data
            ->filter(
                fn ($item) =>
                    $item->nama_materi !== null
                    && is_scalar($item->nama_materi)
                    && trim(
                        (string) $item->nama_materi
                    ) !== ''
            )
            ->groupBy('nama_materi')
            ->map(
                function ($items, $nama) {
                    $benar = $items->filter(
                        fn ($item) =>
                            $item->is_correct !== null
                            && (int) $item->is_correct === 1
                    )->count();

                    $salah = $items->filter(
                        fn ($item) =>
                            $item->is_correct !== null
                            && (int) $item->is_correct === 0
                    )->count();

                    $dinilai = $benar + $salah;

                    return [
                        'nama' => (string) $nama,
                        'benar' => $benar,
                        'salah' => $salah,
                        'belum_dinilai' =>
                            $items->count() - $dinilai,
                        'ketepatan' => $dinilai > 0
                            ? round(
                                $benar / $dinilai * 100,
                                2
                            )
                            : null,
                    ];
                }
            )
            ->values()
            ->all();
    }

    /**
     * Ringkasan topik terpisah dari materi, tanpa mengarang kategori.
     * Hanya jawaban yang sudah dinilai masuk ke persentase ketepatan.
     */
    private function analisisTopik(Collection $jawaban): array
    {
        if ($jawaban->isEmpty() || !Schema::hasColumn('questions', 'topik')) {
            return [];
        }

        $data = DB::table('student_answers')
            ->join('questions', 'questions.id', '=', 'student_answers.question_id')
            ->whereIn('student_answers.id', $jawaban->pluck('id')->all())
            ->select('student_answers.is_correct', 'questions.materi', 'questions.topik')
            ->get()
            ->filter(fn ($item) => is_string($item->topik)
                && trim($item->topik) !== '');

        return $data->groupBy(fn ($item) => ($item->materi ?? '') . '|' . $item->topik)
            ->map(function ($items) {
                $pertama = $items->first();
                $benar = $items->filter(fn ($item) => $item->is_correct !== null
                    && (int) $item->is_correct === 1)->count();
                $salah = $items->filter(fn ($item) => $item->is_correct !== null
                    && (int) $item->is_correct === 0)->count();
                $dinilai = $benar + $salah;

                return [
                    'materi' => $pertama->materi,
                    'topik' => $pertama->topik,
                    'benar' => $benar,
                    'salah' => $salah,
                    'belum_dinilai' => $items->count() - $dinilai,
                    'ketepatan' => $dinilai > 0
                        ? round($benar / $dinilai * 100, 2)
                        : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Konteks esai untuk workflow Dify, bukan skor hasil penilaian.
     * Nama siswa dan identitas pribadi tidak dikirim.
     */
    private function dataEsaiUntukDify(Collection $jawaban): array
    {
        if ($jawaban->isEmpty()) {
            return [];
        }

        $data = DB::table('student_answers')
            ->join('questions', 'questions.id', '=', 'student_answers.question_id')
            ->whereIn('student_answers.id', $jawaban->pluck('id')->all())
            ->whereIn('questions.question_type', ['essay', 'esai'])
            ->select('student_answers.*', 'questions.question_text',
                'questions.correct_answer', 'questions.materi', 'questions.topik')
            ->get();

        // Nama kolom jawaban bisa berbeda antar proyek.
        // Hanya gunakan kolom yang benar-benar tersedia pada record.
        return $data->filter(fn ($item) => $item->is_correct === null)
            ->map(function ($item) {
                $jawabanSiswa = null;
                foreach (['answer_text', 'answer', 'student_answer', 'jawaban', 'response'] as $key) {
                    if (isset($item->{$key}) && is_scalar($item->{$key})) {
                        $jawabanSiswa = (string) $item->{$key};
                        break;
                    }
                }
                return [
                    'question_id' => (int) $item->question_id,
                    'pertanyaan' => $item->question_text,
                    'jawaban_siswa' => $jawabanSiswa,
                    'kunci_jawaban' => $item->correct_answer,
                    'materi' => $item->materi,
                    'topik' => $item->topik,
                    'status_penilaian' => 'belum_dinilai',
                ];
            })->values()->all();
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE HASIL DIFY
    |--------------------------------------------------------------------------
    |
    | Laporan yang sama tidak diproses ulang
    | selama 6 jam.
    |
    | Jika nilai atau jawaban berubah,
    | hash laporan juga akan berubah.
    |
    | Hanya hasil Dify yang berhasil disimpan.
    |
    */

    private function analisisDenganCache(
        DifyAnalyticsService $dify,
        string $jenis,
        array $laporan
    ): ?array {
        $json = json_encode(
            $laporan,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        $key = 'equitask:analytics:'
            . hash(
                'sha256',
                $jenis . ':' . $json
            );

        $tersimpan = Cache::get($key);

        if (is_array($tersimpan)) {
            return $tersimpan;
        }

        $hasil = $dify->analisis(
            $jenis,
            $laporan
        );

        if (is_array($hasil)) {
            Cache::put(
                $key,
                $hasil,
                now()->addHours(6)
            );

            return $hasil;
        }

        return null;
    }
}
