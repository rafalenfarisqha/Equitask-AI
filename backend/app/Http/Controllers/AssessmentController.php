<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use App\Services\DifyWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    // Dashboard Guru
    public function dashboard()
    {
        $userId = auth()->id();

        $totalKelas = \App\Models\ClassRoom::where(
            'teacher_id',
            $userId
        )->count();

        $totalAsesmen = Assessment::where(
            'user_id',
            $userId
        )->count();

        $totalSiswa = \App\Models\ClassRoom::where(
            'teacher_id',
            $userId
        )
            ->withCount('students')
            ->get()
            ->sum('students_count');

        return view(
            'guru.dashboard',
            compact('totalKelas', 'totalAsesmen', 'totalSiswa')
        );
    }

    // 1. Menampilkan asesmen milik guru
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Assessment::where('user_id', $userId);

        if ($request->filled('status')) {
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
        )->where('status', 'Draft')->count();

        $totalPublished = Assessment::where(
            'user_id',
            $userId
        )->where('status', 'Published')->count();

        return view(
            'guru.assessments.index',
            compact(
                'assessments',
                'totalAsesmen',
                'totalDraft',
                'totalPublished'
            )
        );
    }

    // Menampilkan detail asesmen milik guru yang sedang login.
    public function show(int $id)
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('guru.assessments.show', compact('assessment'));
    }

    // 2. Menampilkan Bank Soal Publik
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
            $query->where('mapel', $request->mapel);
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

    // 3. Membagikan soal ke Bank Soal Publik
    public function shareToPublic($id)
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', auth()->id())
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
            auth()->id(),
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

    // 4. Menyalin soal publik ke asesmen pribadi
    public function gunakanSoal($id)
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

    // 5. Menampilkan form Buat Asesmen
    public function create()
    {
        return view('guru.assessments.create');
    }

    // 6. Membuat asesmen menggunakan Dify AI
    public function store(
        Request $request,
        DifyWorkflowService $dify
    ) {
        set_time_limit(600);
        // Tahap 1: Validasi input
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

        // Tahap 2: Susun input START workflow Dify
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

        // Tahap 3: Jalankan Dify
        try {
            $hasilDify = $dify->generateAsesmen(
                $dataDify,
                $request->file('materi_ajar'),
                (string) auth()->id()
            );

            // Pastikan workflow benar-benar berhasil
            $workflowStatus = strtolower(
                (string) ($hasilDify['status'] ?? '')
            );

            if ($workflowStatus !== 'succeeded') {
                throw new \RuntimeException(
                    'Workflow Dify belum berhasil. Status: ' .
                    ($workflowStatus ?: 'tidak diketahui')
                );
            }

            // Tahap 4: Simpan file materi ajar
            $materiPath = $request->file('materi_ajar')
                ->store('materi-ajar', 'local');

            // Tahap 5: Simpan metadata dan output AI
            // dalam satu transaksi database
            $assessment = DB::transaction(function () use (
                $validated,
                $request,
                $materiPath,
                $dataDify,
                $hasilDify
            ) {
                $assessment = Assessment::create([
                    'user_id' => auth()->id(),
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

                    'jumlah_esai' =>
                        $validated['jumlah_esai'],

                    'c1' => (int) $request->boolean('c1'),
                    'c2' => (int) $request->boolean('c2'),
                    'c3' => (int) $request->boolean('c3'),
                    'c4' => (int) $request->boolean('c4'),
                    'c5' => (int) $request->boolean('c5'),
                    'c6' => (int) $request->boolean('c6'),

                    // Tetap Draft sampai diperiksa guru
                    'status' => 'Draft',
                    'visibility' => 'private',

                    'icon' => 'smart_toy',
                    'color' => '#2563EB',
                ]);

                // Tahap 6: Catat proses generasi AI
                $generation = AIGeneration::create([
                    'teacher_id' => auth()->id(),
                    'assessment_id' => $assessment->id,
                    'status' => 'success',
                    'total_agents' => 1,
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                // Tahap 7: Simpan JSON output akhir Dify
                AIAgentResult::create([
                    'generation_id' => $generation->id,

                    'agent_name' => 'Dify Workflow Final Output',
                    'agent_order' => 1,

                    'input_data' => $dataDify,

                    'output_data' => [
                        'workflow_run_id' =>
                            $hasilDify['workflow_run_id'] ?? null,

                        'workflow_status' =>
                            $hasilDify['status'] ?? null,

                        'outputs' =>
                            $hasilDify['outputs'] ?? [],
                    ],

                    'status' => 'success',
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);

                return $assessment;
            });

            // Tahap 8: Catat informasi workflow ke log
            // tanpa mencatat API key atau isi materi
            Log::info('Workflow Dify berhasil disimpan', [
                'assessment_id' => $assessment->id,
                'workflow_run_id' =>
                    $hasilDify['workflow_run_id'] ?? null,
                'status' => $hasilDify['status'] ?? null,
                'output_keys' => array_keys(
                    $hasilDify['outputs'] ?? []
                ),
            ]);

        } catch (\Throwable $e) {
            Log::error('Gagal membuat asesmen AI', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->except('materi_ajar'))
                ->with(
                    'error',
                    'Gagal membuat asesmen. ' .
                    'Periksa koneksi Dify dan log Laravel.'
                );
        }

        // Tahap 9: Kirim notifikasi ke guru
        try {
            NotificationHelper::send(
                auth()->id(),
                'Asesmen AI Selesai',
                'Asesmen <b>' . e($assessment->nama) .
                '</b> telah dibuat sebagai draft.',
                'ai_generation'
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi asesmen', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('guru.assessments.index')
            ->with(
                'success',
                'Workflow Dify berhasil dijalankan. ' .
                'Asesmen dan hasil AI tersimpan sebagai Draft.'
            );
    }
}
