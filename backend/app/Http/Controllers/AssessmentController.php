<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use App\Models\ClassRoom;
use App\Services\DifyWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    /**
     * Dashboard guru.
     */
    public function dashboard()
    {
        $userId = Auth::id();

        $totalKelas = ClassRoom::where(
            'teacher_id',
            $userId
        )->count();

        $totalAsesmen = Assessment::where(
            'user_id',
            $userId
        )->count();

        $totalSiswa = ClassRoom::where(
            'teacher_id',
            $userId
        )
            ->withCount('students')
            ->get()
            ->sum('students_count');

        return view('guru.dashboard', compact(
            'totalKelas',
            'totalAsesmen',
            'totalSiswa'
        ));
    }

    /**
     * Daftar asesmen milik guru.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Assessment::where('user_id', $userId);

        if (
            $request->filled('status') &&
            $request->status !== 'Semua'
        ) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                )->orWhere(
                    'mapel',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        $assessments = $query->latest()->get();

        $totalAsesmen = Assessment::where(
            'user_id',
            $userId
        )->count();

        $totalDraft = Assessment::where(
            'user_id',
            $userId
        )
            ->where('status', 'Draft')
            ->count();

        $totalPublished = Assessment::where(
            'user_id',
            $userId
        )
            ->where('status', 'Published')
            ->count();

        return view('guru.assessments.index', compact(
            'assessments',
            'totalAsesmen',
            'totalDraft',
            'totalPublished'
        ));
    }

    /**
     * Detail asesmen guru.
     *
     * Membaca hasil AI, review guru, dan gambar
     * dari database tanpa Generate Dify ulang.
     */
    public function show(int $id)
    {
        $userId = Auth::id();

        $assessment = Assessment::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $generation = AIGeneration::where(
            'assessment_id',
            $assessment->id
        )
            ->where('teacher_id', $userId)
            ->where('status', 'success')
            ->latest('id')
            ->first();

        $outputs = [];

        if ($generation) {
            $result = AIAgentResult::where(
                'generation_id',
                $generation->id
            )
                ->where('status', 'success')
                ->latest('id')
                ->first();

            if ($result) {
                $stored = $result->output_data;

                if (is_string($stored)) {
                    $stored = json_decode($stored, true);
                }

                if (is_array($stored)) {
                    $outputs = $stored['outputs'] ?? [];
                }

                if (is_string($outputs)) {
                    $outputs = json_decode($outputs, true);
                }

                if (!is_array($outputs)) {
                    $outputs = [];
                }
            }
        }

        $decodeOutput = static function ($value): array {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            return is_array($value) ? $value : [];
        };

        $reguler = $decodeOutput(
            $outputs['asesmen_reguler_revisi']
            ?? $outputs['asesmen_reguler']
            ?? []
        );

        $adaptif = $decodeOutput(
            $outputs['asesmen_inklusif_revisi']
            ?? $outputs['asesmen_inklusif']
            ?? []
        );

        $evaluasi = $decodeOutput(
            $outputs['evaluasi_teks']
            ?? $outputs['evaluasi_kesetaraan']
            ?? []
        );

        // Utamakan revisi guru jika sudah disimpan.
        $review = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($review) {
            $reviewReguler = $decodeOutput(
                $review->reguler
            );

            $reviewAdaptif = $decodeOutput(
                $review->adaptif
            );

            if ($reviewReguler !== []) {
                $reguler = $reviewReguler;
            }

            if ($reviewAdaptif !== []) {
                $adaptif = $reviewAdaptif;
            }
        }

        $soalReguler = $reguler['soal'] ?? [];

        $soalAdaptif = $adaptif['soal_inklusif']
            ?? $adaptif['soal_adaptif']
            ?? $adaptif['soal']
            ?? [];

        if (!is_array($soalReguler)) {
            $soalReguler = [];
        }

        if (!is_array($soalAdaptif)) {
            $soalAdaptif = [];
        }

        /*
         * Gambar Adaptif dari Dify.
         * Tetap menggunakan ID soal asal agar
         * gambar tidak tertukar.
         */
        $gambarRaw = $decodeOutput(
            $outputs['gambar_asesmen_revisi']
            ?? $outputs['gambar_asesmen']
            ?? []
        );

        $gambarAsesmen = [];
        $gambarPerSoal = [];
        $queue = [$gambarRaw];

        while ($queue !== []) {
            $item = array_shift($queue);

            if (!is_array($item)) {
                continue;
            }

            if (
                isset($item['output']) &&
                is_array($item['output'])
            ) {
                $queue[] = $item['output'];
                continue;
            }

            if (
                isset($item['gambar_per_soal']) &&
                is_array($item['gambar_per_soal'])
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

            $url = $item['url']
                ?? $item['gambar_url']
                ?? $item['download_url']
                ?? $item['preview_url']
                ?? null;

            if (
                !is_string($url) ||
                !filter_var($url, FILTER_VALIDATE_URL)
            ) {
                continue;
            }

            $scheme = strtolower(
                (string) parse_url(
                    $url,
                    PHP_URL_SCHEME
                )
            );

            if (!in_array(
                $scheme,
                ['https', 'http'],
                true
            )) {
                continue;
            }

            $idSoal = trim(
                (string) ($item['id_soal_asal'] ?? '')
            );

            $gambar = [
                'id_soal_asal' => $idSoal,
                'id_visual' => $item['id_visual'] ?? null,
                'url' => $url,
                'filename' => $item['filename']
                    ?? 'Ilustrasi asesmen',
                'teks_alternatif' =>
                    $item['teks_alternatif'] ?? null,
                'type' => $item['type'] ?? 'image',
            ];

            $gambarAsesmen[] = $gambar;

            if ($idSoal !== '') {
                if (!isset($gambarPerSoal[$idSoal])) {
                    $gambarPerSoal[$idSoal] = [];
                }

                $gambarPerSoal[$idSoal][] = $gambar;
            }
        }

        $statusKesetaraan = $outputs['status_kesetaraan']
            ?? $outputs['status_akhir']
            ?? null;

        $hasilValidasi = $outputs['hasil_validasi_revisi']
            ?? $outputs['hasil_validasi']
            ?? null;

        $jumlahPerluRevisi =
            $outputs['jumlah_soal_perlu_revisi_cabang']
            ?? $outputs['jumlah_soal_perlu_revisi']
            ?? null;

        $jumlahSoalDiperiksa =
            $outputs['jumlah_soal_diperiksa']
            ?? null;

        $statusValidasiGambar =
            $outputs['status_validasi_gambar']
            ?? null;

        return view('guru.assessments.show', [
            'assessment' => $assessment,
            'soalReguler' => array_values($soalReguler),
            'soalAdaptif' => array_values($soalAdaptif),
            'catatanReguler' => $reguler['catatan'] ?? null,
            'catatanAdaptif' => $adaptif['catatan'] ?? null,
            'statusKesetaraan' => $statusKesetaraan,
            'hasilValidasi' => $hasilValidasi,
            'jumlahPerluRevisi' => $jumlahPerluRevisi,
            'jumlahSoalDiperiksa' => $jumlahSoalDiperiksa,
            'statusValidasiGambar' => $statusValidasiGambar,
            'evaluasi' => $evaluasi,
            'gambarAsesmen' => $gambarAsesmen,
            'gambarPerSoal' => $gambarPerSoal,
            'outputs' => $outputs,
        ]);
    }

    /**
     * Menampilkan Bank Soal Publik.
     */
    public function bankSoal(Request $request)
    {
        $query = Assessment::where(
            'visibility',
            'public'
        )
            ->where('status', 'Published')
            ->latest();

        if (
            $request->filled('mapel') &&
            $request->mapel !== 'Semua'
        ) {
            $query->where(
                'mapel',
                $request->mapel
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                )->orWhere(
                    'mapel',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        $publicAssessments = $query->get();

        return view(
            'guru.bank-soal',
            compact('publicAssessments')
        );
    }

    /**
     * Membagikan asesmen ke Bank Soal Publik.
     */
    public function shareToPublic(int $id)
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($assessment->status !== 'Published') {
            return back()->with(
                'error',
                'Asesmen harus dipublikasikan terlebih dahulu.'
            );
        }

        $assessment->update([
            'visibility' => 'public',
        ]);

        NotificationHelper::send(
            Auth::id(),
            'Bank Soal Publik',
            'Soal <b>' . e($assessment->nama) .
            '</b> telah dibagikan ke Bank Soal Publik.',
            'bank_soal'
        );

        return back()->with(
            'success',
            'Soal berhasil dibagikan ke Bank Soal Publik!'
        );
    }

    /**
     * Menyalin soal publik ke daftar asesmen guru.
     *
     * Method lama tetap dipertahankan.
     */
    public function gunakanSoal(int $id)
    {
        $source = Assessment::where(
            'visibility',
            'public'
        )
            ->where('status', 'Published')
            ->findOrFail($id);

        $clone = $source->replicate();

        $clone->user_id = Auth::id();
        $clone->visibility = 'private';
        $clone->status = 'Draft';
        $clone->nama = $source->nama . ' (Salinan)';

        $clone->save();

        return redirect()
            ->route('guru.assessments.index')
            ->with(
                'success',
                'Soal berhasil disalin ke daftar asesmen Anda!'
            );
    }

    /**
     * Menampilkan halaman Buat Asesmen.
     */
    public function create()
    {
        return view('guru.assessments.create');
    }

    /**
     * Generate asesmen menggunakan Dify.
     *
     * Bagian ini tetap menjalankan alur asli.
     * Duplikasi paket TIDAK memanggil method ini.
     */
    public function store(
        Request $request,
        DifyWorkflowService $dify
    ) {
        set_time_limit(3600);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'mata_pelajaran' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
            'tujuan_pembelajaran' => 'required|string',
            'materi_ajar' => 'required|file|mimes:pdf|max:10240',
            'versi_asesmen' => 'required|string',
            'jumlah_pilihan_ganda' => 'required|integer|min:0',
            'jumlah_esai' => 'required|integer|min:0',
            'c1' => 'sometimes|boolean',
            'c2' => 'sometimes|boolean',
            'c3' => 'sometimes|boolean',
            'c4' => 'sometimes|boolean',
            'c5' => 'sometimes|boolean',
            'c6' => 'sometimes|boolean',
            'status' => 'required|in:Published,Draft',
            'visibility' => 'required|in:private,public',
        ]);

        $dataDify = [
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'kelas' => $validated['kelas'],
            'tujuan_pembelajaran' =>
                $validated['tujuan_pembelajaran'],
            'versi_asesmen' => $validated['versi_asesmen'],
            'jumlah_pilihan_ganda' =>
                (int) $validated['jumlah_pilihan_ganda'],
            'jumlah_esai' =>
                (int) $validated['jumlah_esai'],
            'c1' => $request->boolean('c1'),
            'c2' => $request->boolean('c2'),
            'c3' => $request->boolean('c3'),
            'c4' => $request->boolean('c4'),
            'c5' => $request->boolean('c5'),
            'c6' => $request->boolean('c6'),
        ];

        $materiPath = null;
        $gambarPaths = [];
        $sudahTersimpanDiDatabase = false;

        try {
            // 1. Jalankan workflow Dify.
            $hasilDify = $dify->generateAsesmen(
                $dataDify,
                $request->file('materi_ajar'),
                (string) Auth::id()
            );

            if (
                strtolower(
                    (string) ($hasilDify['status'] ?? '')
                ) !== 'succeeded'
            ) {
                throw new \RuntimeException(
                    'Workflow Dify tidak berhasil. Status: ' .
                    ($hasilDify['status'] ?? 'tidak diketahui')
                );
            }

            $outputs = $hasilDify['outputs'] ?? [];

            if (is_string($outputs)) {
                $outputs = json_decode($outputs, true) ?? [];
            }

            if (!is_array($outputs)) {
                throw new \RuntimeException(
                    'Format output Dify tidak valid.'
                );
            }

            // 2. Ambil soal Reguler dan Adaptif.
            $decodeArray = static function ($value): array {
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }

                return is_array($value) ? $value : [];
            };

            $reguler = $decodeArray(
                $outputs['asesmen_reguler_revisi']
                ?? $outputs['asesmen_reguler']
                ?? []
            );

            $adaptif = $decodeArray(
                $outputs['asesmen_inklusif_revisi']
                ?? $outputs['asesmen_inklusif']
                ?? []
            );

            $soalReguler = $reguler['soal'] ?? [];

            $soalAdaptif = $adaptif['soal_inklusif']
                ?? $adaptif['soal_adaptif']
                ?? $adaptif['soal']
                ?? [];

            if (
                !is_array($soalReguler) ||
                !is_array($soalAdaptif)
            ) {
                throw new \RuntimeException(
                    'Format daftar soal dari Dify tidak valid.'
                );
            }

            if (
                count($soalReguler) +
                count($soalAdaptif) === 0
            ) {
                throw new \RuntimeException(
                    'Dify berhasil dijalankan, tetapi daftar soal ' .
                    'tidak ditemukan. Periksa struktur outputs Dify.'
                );
            }

            // 3. Unduh dan simpan file gambar AKTUAL saat URL Dify masih valid.
            // Kegagalan salah satu gambar membatalkan pembuatan asesmen,
            // sehingga tidak ada soal adaptif yang tersimpan tanpa gambar.
            $gambarRaw = $outputs['gambar_asesmen_revisi']
                ?? $outputs['gambar_asesmen']
                ?? [];
            $daftarGambar = $this->normalisasiGambarDify($gambarRaw);

            $idSoalAdaptif = [];
            foreach ($soalAdaptif as $soal) {
                if (!is_array($soal)) {
                    continue;
                }
                $id = trim((string) ($soal['id_soal_asal'] ?? $soal['id'] ?? ''));
                if ($id === '') {
                    throw new \RuntimeException('ID soal adaptif kosong.');
                }
                $idSoalAdaptif[$id] = true;
            }

            $gambarLokal = [];
            foreach ($daftarGambar as $gambar) {
                $id = trim((string) ($gambar['id_soal_asal'] ?? ''));
                if (!isset($idSoalAdaptif[$id])) {
                    continue;
                }
                $tersimpan = $this->unduhGambarDify($gambar);
                $gambarPaths[] = $tersimpan['path_gambar'];
                $gambarLokal[] = $tersimpan;
            }

            foreach (array_keys($idSoalAdaptif) as $id) {
                $jumlah = count(array_filter(
                    $gambarLokal,
                    static fn (array $g): bool => $g['id_soal_asal'] === $id
                ));
                if ($jumlah < 1) {
                    throw new \RuntimeException(
                        "Soal adaptif {$id} belum mempunyai gambar aktual yang tersimpan."
                    );
                }
            }

            // URL pada hasil yang DISIMPAN adalah URL Laravel, bukan Dify.
            // Bentuk array ini juga dapat dibaca oleh method show() lama.
            $outputs['gambar_asesmen'] = $gambarLokal;
            $outputs['gambar_asesmen_revisi'] = $gambarLokal;

            // 4. Simpan materi ajar.
            $materiPath = $request->file('materi_ajar')
                ->store('materi-ajar', 'local');

            // 4. Simpan asesmen dan data soal.
            $assessment = DB::transaction(function () use (
                $validated,
                $request,
                $materiPath,
                $dataDify,
                $hasilDify,
                $outputs,
                $soalReguler,
                $soalAdaptif
            ) {
                $assessment = Assessment::create([
                    'user_id' => Auth::id(),
                    'nama' => $validated['nama'],
                    'mapel' => $validated['mata_pelajaran'],
                    'kelas' => $validated['kelas'],
                    'tujuan_pembelajaran' =>
                        $validated['tujuan_pembelajaran'],
                    'materi_ajar' => $materiPath,
                    'versi_asesmen' =>
                        $validated['versi_asesmen'],
                    'jumlah_pilihan_ganda' =>
                        $validated['jumlah_pilihan_ganda'],
                    'jumlah_esai' => $validated['jumlah_esai'],
                    'c1' => (int) $request->boolean('c1'),
                    'c2' => (int) $request->boolean('c2'),
                    'c3' => (int) $request->boolean('c3'),
                    'c4' => (int) $request->boolean('c4'),
                    'c5' => (int) $request->boolean('c5'),
                    'c6' => (int) $request->boolean('c6'),
                    'status' => 'Draft',
                    'visibility' => 'private',
                    'icon' => 'smart_toy',
                    'color' => '#2563EB',
                ]);

                $generation = AIGeneration::create([
                    'teacher_id' => Auth::id(),
                    'assessment_id' => $assessment->id,
                    'status' => 'success',
                    'total_agents' => 1,
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                AIAgentResult::create([
                    'generation_id' => $generation->id,
                    'agent_name' =>
                        'Dify Workflow Final Output',
                    'agent_order' => 1,
                    'input_data' => $dataDify,
                    'output_data' => [
                        'workflow_run_id' =>
                            $hasilDify['workflow_run_id'] ?? null,
                        'workflow_status' =>
                            $hasilDify['status'] ?? null,
                        'outputs' => $outputs,
                    ],
                    'status' => 'success',
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                // Ambil nilai dari beberapa kemungkinan key Dify.
                $ambilNilai = static function (
                    array $soal,
                    array $keys,
                    $default = null
                ) {
                    foreach ($keys as $key) {
                        if (
                            array_key_exists($key, $soal) &&
                            $soal[$key] !== null &&
                            $soal[$key] !== ''
                        ) {
                            return $soal[$key];
                        }
                    }

                    return $default;
                };

                $simpanSoal = function (
                    array $daftarSoal,
                    string $versi
                ) use ($assessment, $ambilNilai) {
                    $nomor = 0;

                    foreach ($daftarSoal as $soal) {
                        if (!is_array($soal)) {
                            continue;
                        }

                        $teks = $ambilNilai($soal, [
                            'question_text',
                            'pertanyaan',
                            'teks_soal',
                            'soal',
                            'question',
                        ]);

                        if (
                            !is_string($teks) ||
                            trim($teks) === ''
                        ) {
                            continue;
                        }

                        $opsi = $ambilNilai($soal, [
                            'options',
                            'opsi',
                            'pilihan_jawaban',
                            'pilihan',
                        ]);

                        if (is_string($opsi)) {
                            $decoded = json_decode($opsi, true);

                            $opsi = is_array($decoded)
                                ? $decoded
                                : [$opsi];
                        }

                        $jenis = strtolower(
                            (string) $ambilNilai($soal, [
                                'jenis',
                                'question_type',
                                'jenis_soal',
                                'tipe_soal',
                                'tipe',
                                'type',
                            ], 'pilihan_ganda')
                        );

                        $jenis = (
                            str_contains($jenis, 'esai') ||
                            str_contains($jenis, 'uraian') ||
                            str_contains($jenis, 'essay')
                        )
                            ? 'essay'
                            : 'pilihan_ganda';

                        $jawaban = $ambilNilai($soal, [
                            'correct_answer',
                            'jawaban_benar',
                            'kunci_jawaban',
                            'jawaban',
                            'answer',
                        ]);

                        if (is_array($jawaban)) {
                            $jawaban = json_encode(
                                $jawaban,
                                JSON_UNESCAPED_UNICODE
                            );
                        }

                        // Metadata analitik hanya berasal dari field Dify
                        // yang benar-benar tersedia pada masing-masing soal.
                        // assessment.materi_ajar adalah path PDF, bukan nama materi.
                        $normalisasiMetadata = static function ($value): ?string {
                            if (!is_string($value) && !is_numeric($value)) {
                                return null;
                            }

                            $value = trim((string) $value);

                            return $value === ''
                                ? null
                                : mb_substr($value, 0, 255);
                        };

                        $materiSoal = $normalisasiMetadata(
                            $ambilNilai($soal, [
                                'materi',
                                'nama_materi',
                                'materi_pembelajaran',
                                'material',
                            ])
                        );

                        $topikSoal = $normalisasiMetadata(
                            $ambilNilai($soal, [
                                'topik',
                                'subtopik',
                                'sub_topik',
                                'topic',
                                'kompetensi',
                            ])
                        );

                        // Topik yang tersedia dapat menjadi label materi;
                        // jangan menebak subtopik dari teks pertanyaan.
                        if ($materiSoal === null && $topikSoal !== null) {
                            $materiSoal = $topikSoal;
                        }

                        $now = now();

                        $questionId = DB::table('questions')
                            ->insertGetId([
                                'teacher_id' => Auth::id(),
                                'material_id' => null,
                                'question_text' => trim($teks),
                                'question_type' => $jenis,
                                'options' => $opsi === null
                                    ? null
                                    : json_encode(
                                        $opsi,
                                        JSON_UNESCAPED_UNICODE
                                    ),
                                'correct_answer' => $jawaban === null
                                    ? null
                                    : (string) $jawaban,
                                'bloom_level' =>
                                    (string) $ambilNilai($soal, [
                                        'bloom_level',
                                        'level_kognitif',
                                        'level_bloom',
                                        'taksonomi_bloom',
                                    ], 'C1'),
                                'version_type' => $versi,
                                'explanation' =>
                                    $ambilNilai($soal, [
                                        'explanation',
                                        'pembahasan',
                                        'penjelasan',
                                    ]),
                                'materi' => $materiSoal,
                                'topik' => $topikSoal,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                        DB::table('assessment_questions')->insert([
                            'assessment_id' => $assessment->id,
                            'question_id' => $questionId,
                            'question_order' => ++$nomor,
                            'points' => (float) $ambilNilai(
                                $soal,
                                ['skor_maksimum', 'points', 'poin', 'bobot'],
                                1
                            ),
                        ]);
                    }

                    return $nomor;
                };

                $jumlahReguler = $simpanSoal(
                    $soalReguler,
                    'reguler'
                );

                $jumlahAdaptif = $simpanSoal(
                    $soalAdaptif,
                    'adaptif'
                );

                if (
                    $jumlahReguler +
                    $jumlahAdaptif === 0
                ) {
                    throw new \RuntimeException(
                        'Output Dify ditemukan, tetapi format ' .
                        'teks soal tidak dikenali.'
                    );
                }

                Log::info('Soal asesmen berhasil disimpan', [
                    'assessment_id' => $assessment->id,
                    'reguler' => $jumlahReguler,
                    'adaptif' => $jumlahAdaptif,
                ]);

                return $assessment;
            });

            $sudahTersimpanDiDatabase = true;

            NotificationHelper::send(
                Auth::id(),
                'Asesmen AI Selesai',
                'Asesmen <b>' . e($assessment->nama) .
                '</b> berhasil dibuat sebagai draft.',
                'ai_generation'
            );

            return redirect()
                ->route('guru.assessments.index')
                ->with(
                    'success',
                    'Asesmen dan soal berhasil disimpan sebagai Draft.'
                );

        } catch (\Throwable $e) {
            // Hanya bersihkan jika transaksi database BELUM berhasil.
            if (!$sudahTersimpanDiDatabase) {
                foreach ($gambarPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
                if ($materiPath !== null) {
                    Storage::disk('local')->delete($materiPath);
                }
            }

            Log::error('Gagal membuat asesmen AI', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->except('materi_ajar'))
                ->with(
                    'error',
                    'Gagal menyimpan asesmen: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * DUPLIKAT PAKET ASESMEN
     *
     * Menyalin paket tanpa memanggil Dify.
     * Menyalin semua relasi soal, review guru,
     * dan data hasil AI yang tersimpan.
     *
     * Paket baru memiliki assessment_id berbeda
     * dan dimulai dengan status Draft.
     */
    public function duplicate(Request $request, int $id)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $source = Assessment::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        try {
            $clone = DB::transaction(function () use (
                $source,
                $validated
            ) {
                // Ambil semua soal dari paket sumber.
                $relations = DB::table(
                    'assessment_questions'
                )
                    ->where(
                        'assessment_id',
                        $source->id
                    )
                    ->orderBy('question_order')
                    ->get();

                if ($relations->isEmpty()) {
                    throw new \RuntimeException(
                        'Paket asli belum memiliki soal.'
                    );
                }

                // 1. Salin paket asesmen.
                $clone = $source->replicate();

                $clone->user_id = Auth::id();
                $clone->nama = trim($validated['nama']);
                $clone->status = 'Draft';
                $clone->visibility = 'private';

                $clone->save();

                // 2. Salin seluruh relasi soal.
                //
                // question_id tetap sama supaya isi
                // paket salinan identik dengan paket asli.
                foreach ($relations as $relation) {
                    DB::table('assessment_questions')->insert([
                        'assessment_id' => $clone->id,
                        'question_id' => $relation->question_id,
                        'question_order' =>
                            $relation->question_order,
                        'points' => $relation->points,
                    ]);
                }

                // 3. Salin hasil review guru.
                //
                // Menjaga data Reguler dan Adaptif,
                // termasuk dukungan scaffolding.
                $review = DB::table('assessment_reviews')
                    ->where(
                        'assessment_id',
                        $source->id
                    )
                    ->first();

                if ($review) {
                    $reviewData = (array) $review;

                    unset($reviewData['id']);

                    $reviewData['assessment_id'] =
                        $clone->id;

                    if (
                        array_key_exists(
                            'created_at',
                            $reviewData
                        )
                    ) {
                        $reviewData['created_at'] = now();
                    }

                    if (
                        array_key_exists(
                            'updated_at',
                            $reviewData
                        )
                    ) {
                        $reviewData['updated_at'] = now();
                    }

                    DB::table('assessment_reviews')
                        ->insert($reviewData);
                }

                // 4. Salin hasil Generate AI yang tersimpan.
                //
                // Tidak ada pemanggilan Dify di sini.
                // Data gambar/scaffolding disalin dari DB.
                $generation = AIGeneration::where(
                    'assessment_id',
                    $source->id
                )
                    ->where('status', 'success')
                    ->latest('id')
                    ->first();

                if ($generation) {
                    $newGeneration =
                        $generation->replicate();

                    $newGeneration->assessment_id =
                        $clone->id;

                    $newGeneration->teacher_id =
                        Auth::id();

                    $newGeneration->save();

                    $results = AIAgentResult::where(
                        'generation_id',
                        $generation->id
                    )->get();

                    foreach ($results as $result) {
                        $newResult = $result->replicate();

                        $newResult->generation_id =
                            $newGeneration->id;

                        $newResult->save();
                    }
                }

                return $clone;
            });

            return redirect()
                ->route(
                    'guru.assessments.show',
                    $clone->id
                )
                ->with(
                    'success',
                    'Paket berhasil diduplikasi sebagai Draft. ' .
                    'Periksa isi paket, kemudian publikasikan ke kelas.'
                );

        } catch (\Throwable $e) {
            Log::error('Duplikasi asesmen gagal', [
                'assessment_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Duplikasi gagal: ' . $e->getMessage()
            );
        }
    }

    /**
     * EDIT NAMA/JUDUL ASESMEN
     *
     * Mengubah judul tanpa mengubah soal,
     * hasil Dify, atau data pengerjaan siswa.
     */
    public function updateTitle(
        Request $request,
        int $id
    ) {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $assessment = Assessment::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $assessment->update([
            'nama' => trim($validated['nama']),
        ]);

        return redirect()
            ->route('guru.assessments.index')
            ->with(
                'success',
                'Judul paket asesmen berhasil diperbarui.'
            );
    }

    /**
     * Normalisasi output gambar dari Dify Iteration/Code node.
     * Mendukung JSON Array, JSON String, serta wrapper output/gambar_per_soal.
     */
    private function normalisasiGambarDify(mixed $raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $hasil = [];
        $queue = [$raw];

        while ($queue !== []) {
            $item = array_shift($queue);
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (!is_array($item)) {
                continue;
            }

            // Daftar item (termasuk output Iteration).
            if (array_is_list($item)) {
                foreach ($item as $child) {
                    $queue[] = $child;
                }
                continue;
            }

            foreach (['output', 'gambar_per_soal', 'gambar_asesmen', 'files'] as $key) {
                if (array_key_exists($key, $item)) {
                    $queue[] = $item[$key];
                }
            }

            $url = $item['url'] ?? $item['gambar_url']
                ?? $item['download_url'] ?? $item['preview_url'] ?? null;

            if (!is_string($url) || $url === '') {
                continue;
            }

            $idSoal = trim((string) ($item['id_soal_asal'] ?? ''));
            if ($idSoal === '') {
                continue;
            }

            $hasil[] = [
                'id_soal_asal' => $idSoal,
                'id_visual' => (string) ($item['id_visual'] ?? ''),
                'url' => $url,
                'filename' => (string) ($item['filename'] ?? ''),
                'reference' => $item['reference'] ?? null,
                'teks_alternatif' => $item['teks_alternatif'] ?? null,
            ];
        }

        return $hasil;
    }

    /**
     * Unduh gambar Dify dan simpan permanen ke disk public Laravel.
     * Jangan terima URL dari input pengguna; hanya dari hasil workflow.
     */
    private function unduhGambarDify(array $gambar): array
    {
        $url = $gambar['url'];
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));

        // Tambahkan host lain hanya bila server Dify milik sendiri
        // memang menggunakan domain penyimpanan yang berbeda.
        $hostDiizinkan = ['upload.dify.ai'];

        if (
            !is_array($parts) ||
            strtolower((string) ($parts['scheme'] ?? '')) !== 'https' ||
            !in_array($host, $hostDiizinkan, true) ||
            isset($parts['user']) || isset($parts['pass']) ||
            isset($parts['port'])
        ) {
            throw new \RuntimeException('Host URL gambar Dify tidak diizinkan.');
        }

        // Hindari redirect menuju alamat yang tidak diizinkan.
        // Timeout lebih longgar untuk file gambar Dify yang lambat.
        // Retry hanya untuk gangguan koneksi, HTTP 429, atau HTTP 5xx.
        // HTTP 403 tidak diulang karena URL bertanda tangan mungkin kedaluwarsa.
        $response = Http::connectTimeout(15)
            ->timeout(120)
            ->withOptions(['allow_redirects' => false])
            ->retry(3, 2000, function ($exception) {
                if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                    return true;
                }

                if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                    $status = $exception->response->status();
                    return $status === 429 || $status >= 500;
                }

                return false;
            }, throw: false)
            ->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Unduh gambar Dify gagal (HTTP ' . $response->status() .
                ') untuk ' . $gambar['id_soal_asal'] .
                '. Jalankan ulang generator jika URL sudah kedaluwarsa.'
            );
        }

        $bytes = $response->body();
        if ($bytes === '' || strlen($bytes) > 10 * 1024 * 1024) {
            throw new \RuntimeException('Gambar Dify kosong atau melebihi 10 MB.');
        }

        // Cek tipe gambar dari isi file, bukan ekstensi URL saja.
        $imageInfo = @getimagesizefromstring($bytes);
        $mime = $imageInfo['mime'] ?? null;
        $extensions = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mime])) {
            throw new \RuntimeException('Dify mengembalikan file bukan gambar valid.');
        }

        $path = 'asesmen-gambar/' . Str::uuid() . '.' . $extensions[$mime];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        if (!$disk->put($path, $bytes)) {
            throw new \RuntimeException('Tidak berhasil menyimpan gambar ke storage.');
        }

        $urlPermanen = $disk->url($path);

        return [
            'id_soal_asal' => $gambar['id_soal_asal'],
            'id_visual' => $gambar['id_visual'],
            'url' => $urlPermanen,
            'gambar_url' => $urlPermanen,
            'path_gambar' => $path,
            'filename' => basename($path),
            'reference' => $gambar['reference'],
            'teks_alternatif' => $gambar['teks_alternatif'],
            'tersimpan' => true,
            'type' => 'image',
        ];
    }

}
