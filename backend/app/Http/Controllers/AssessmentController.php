<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    // Dashboard Guru
    public function dashboard()
    {
        $userId = Auth::id();

        $totalKelas = ClassRoom::where('teacher_id', $userId)->count();
        $totalAsesmen = Assessment::where('user_id', $userId)->count();
        $totalSiswa = ClassRoom::where('teacher_id', $userId)
            ->withCount('students')
            ->get()
            ->sum('students_count');

        return view('guru.dashboard', compact('totalKelas', 'totalAsesmen', 'totalSiswa'));
    }

    // 1. Menampilkan Asesmen Milik Guru Sendiri
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Assessment::where('user_id', $userId);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('mapel', 'like', '%' . $search . '%');
            });
        }

        $assessments = $query->latest()->get();

        $totalAsesmen = Assessment::where('user_id', $userId)->count();
        $totalDraft = Assessment::where('user_id', $userId)->where('status', 'Draft')->count();
        $totalPublished = Assessment::where('user_id', $userId)->where('status', 'Published')->count();

        return view('guru.assessments.index', compact('assessments', 'totalAsesmen', 'totalDraft', 'totalPublished'));
    }

    // 2. Menampilkan Detail Asesmen
    public function show(int $id)
    {
        $assessment = Assessment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('guru.assessments.show', compact('assessment'));
    }

    // 3. Menampilkan Bank Soal Publik
    public function bankSoal(Request $request)
    {
        $query = Assessment::where('visibility', 'public')
                           ->where('status', 'Published')
                           ->latest();

        if ($request->has('mapel') && $request->mapel !== '' && $request->mapel !== 'Semua') {
            $query->where('mapel', $request->mapel);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('mapel', 'like', '%' . $search . '%');
            });
        }

        $publicAssessments = $query->get();

        return view('guru.bank-soal', compact('publicAssessments'));
    }

    // 4. Aksi Guru untuk Membagikan Soal ke Bank Soal Publik
    public function shareToPublic(int $id)
    {
        $assessment = Assessment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $assessment->update(['visibility' => 'public']);

        NotificationHelper::send(
            (int) Auth::id(),
            'Bank Soal Publik',
            'Soal <b>' . $assessment->nama . '</b> telah berhasil dibagikan ke koleksi Bank Soal Publik.',
            'bank_soal'
        );

        return back()->with('success', 'Soal berhasil dibagikan ke Bank Soal Publik!');
    }

    // 5. Aksi Guru untuk Menyalin Soal dari Bank Soal
    public function gunakanSoal(int $id)
    {
        $source = Assessment::findOrFail($id);

        $clone = $source->replicate();
        $clone->user_id = Auth::id();
        $clone->visibility = 'private';
        $clone->status = 'Draft';
        $clone->nama = $source->nama . ' (Salinan)';
        $clone->save();

        return redirect()->route('guru.assessments.index')->with('success', 'Soal berhasil disalin ke daftar asesmen Anda!');
    }

    // 6. Create Asesmen (View Form)
    public function create()
    {
        return view('guru.assessments.create');
    }

    // 7. Store Asesmen & Integrasi Dify Workflow
    public function store(Request $request)
    {
        // Naikkan batas waktu eksekusi PHP khusus fungsi ini menjadi 5 menit (300 detik)
        set_time_limit(300);
        ini_set('max_execution_time', '300');

        $request->validate([
            'nama' => 'required|string|max:255',
            'mata_pelajaran' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
            'tujuan_pembelajaran' => 'required|string',
            'materi_ajar' => 'required|file|mimes:pdf,txt,doc,docx,ppt,pptx|max:10240', // Maks 10MB
            'versi_asesmen' => 'required|string',
            'jumlah_pilihan_ganda' => 'required|integer|min:0',
            'jumlah_esai' => 'required|integer|min:0',
            'status' => 'required|in:Published,Draft',
            'visibility' => 'required|in:private,public',
        ]);

        $apiKey = env('DIFY_API_KEY');
        $difyUploadUrl = 'https://api.dify.ai/v1/files/upload';
        $difyWorkflowUrl = env('DIFY_API_URL', 'https://api.dify.ai/v1') . '/workflows/run';

        $uploadedFileId = null;

        try {
            // 1. UPLOAD FILE KE SERVER DIFY
            $file = $request->file('materi_ajar');

            $uploadResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post($difyUploadUrl, [
                'user' => 'guru-' . Auth::id(),
            ]);

            if ($uploadResponse->successful()) {
                $uploadData = $uploadResponse->json();
                $uploadedFileId = $uploadData['id'] ?? null;
            } else {
                Log::error('Gagal upload file ke Dify: ' . $uploadResponse->body());
            }
        } catch (\Exception $e) {
            Log::error('Exception saat upload ke Dify: ' . $e->getMessage());
        }

        // 2. SIMPAN INFORMASI ASESMEN KE DATABASE LOKAL
        $localPath = $request->file('materi_ajar')->store('materi_ajar', 'public');

        $assessment = Assessment::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'mapel' => $request->mata_pelajaran,
            'kelas' => $request->kelas,
            'tujuan_pembelajaran' => $request->tujuan_pembelajaran,
            'materi_ajar' => $localPath,
            'versi_asesmen' => $request->versi_asesmen,
            'jumlah_pilihan_ganda' => $request->jumlah_pilihan_ganda,
            'jumlah_esai' => $request->jumlah_esai,
            'c1' => $request->has('c1') ? 1 : 0,
            'c2' => $request->has('c2') ? 1 : 0,
            'c3' => $request->has('c3') ? 1 : 0,
            'c4' => $request->has('c4') ? 1 : 0,
            'c5' => $request->has('c5') ? 1 : 0,
            'c6' => $request->has('c6') ? 1 : 0,
            'status' => $request->status,
            'visibility' => $request->visibility,
            'icon' => 'smart_toy',
            'color' => '#2563EB',
        ]);

        // 3. JALANKAN WORKFLOW DIFY & SIMPAN OUTPUTNYA
        try {
            $filePayload = $uploadedFileId ? [
                'type' => 'document',
                'transfer_method' => 'local_file',
                'upload_file_id' => $uploadedFileId,
                'name' => $request->file('materi_ajar')->getClientOriginalName(),
            ] : null;

            $response = Http::timeout(180)->withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($difyWorkflowUrl, [
                'inputs' => [
                    'mata_pelajaran'       => $request->mata_pelajaran,
                    'kelas'                => $request->kelas,
                    'tujuan_pembelajaran'  => $request->tujuan_pembelajaran,
                    'materi_ajar'          => $filePayload,
                    'versi_asesmen'        => $request->versi_asesmen,
                    'jumlah_pilihan_ganda' => (int) $request->jumlah_pilihan_ganda,
                    'jumlah_esai'          => (int) $request->jumlah_esai,
                    'c1'                   => $request->has('c1') ? true : false,
                    'c2'                   => $request->has('c2') ? true : false,
                    'c3'                   => $request->has('c3') ? true : false,
                    'c4'                   => $request->has('c4') ? true : false,
                    'c5'                   => $request->has('c5') ? true : false,
                    'c6'                   => $request->has('c6') ? true : false,
                ],
                'response_mode' => 'blocking',
                'user' => 'guru-' . Auth::id(),
            ]);

            if ($response->successful()) {
                $difyResult = $response->json();

                $outputs = $difyResult['data']['outputs'] ?? [];
                $aiOutputs = $outputs['result'] ?? $outputs['text'] ?? $outputs['output'] ?? (is_array($outputs) ? reset($outputs) : null);

                if ($aiOutputs) {
                    $assessment->update([
                        'description' => is_array($aiOutputs) ? json_encode($aiOutputs) : $aiOutputs,
                    ]);
                } else {
                    Log::warning('Respons Dify diterima tetapi kunci outputs tidak cocok: ' . json_encode($difyResult));
                }

                NotificationHelper::send(
                    (int) Auth::id(),
                    'Generasi AI Selesai',
                    'Multi-agent Dify AI berhasil memproses materi ajar dan menyusun soal untuk asesmen <b>' . $assessment->nama . '</b>.',
                    'ai_generation'
                );
            } else {
                Log::error('Dify Workflow Error Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan Dify Workflow: ' . $e->getMessage());
        }

        return redirect()->route('guru.assessments.index')->with('success', 'Asesmen berhasil dibuat dan disimpan!');
    }
}
