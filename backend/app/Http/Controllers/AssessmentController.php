<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // Dashboard Guru
    public function dashboard()
    {
        $userId = auth()->id();

        $totalKelas = \App\Models\ClassRoom::where('teacher_id', $userId)->count();
        $totalAsesmen = Assessment::where('user_id', $userId)->count();
        $totalSiswa = \App\Models\ClassRoom::where('teacher_id', $userId)
            ->withCount('students')
            ->get()
            ->sum('students_count');

        return view('guru.dashboard', compact('totalKelas', 'totalAsesmen', 'totalSiswa'));
    }

    // 1. Menampilkan Asesmen Milik Guru Sendiri (Privat / Daftar Buatan Sendiri)
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Assessment::where('user_id', $userId);

        // Filter berdasarkan status (Draft / Published) jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Fitur pencarian berdasarkan nama atau mapel
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('mapel', 'like', '%' . $search . '%');
            });
        }

        $assessments = $query->latest()->get();

        // Hitung statistik untuk kartu di atas
        $totalAsesmen = Assessment::where('user_id', $userId)->count();
        $totalDraft = Assessment::where('user_id', $userId)->where('status', 'Draft')->count();
        $totalPublished = Assessment::where('user_id', $userId)->where('status', 'Published')->count();

        // Sesuaikan path view dengan folder foldermu (misal: guru.assessments.index)
        return view('guru.assessments.index', compact('assessments', 'totalAsesmen', 'totalDraft', 'totalPublished'));
    }

    // 2. Menampilkan Bank Soal Publik (Komunitas Guru dari Sekolah Lain)
    public function bankSoal(Request $request)
    {
        $query = Assessment::where('visibility', 'public')
                           ->where('status', 'Published')
                           ->latest();

        // Fitur filter berdasarkan mata pelajaran
        if ($request->has('mapel') && $request->mapel !== '' && $request->mapel !== 'Semua') {
            $query->where('mapel', $request->mapel);
        }

        // Fitur pencarian di bank soal
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

    // 3. Aksi Guru untuk Membagikan Soal Pribadi ke Bank Soal Publik
    public function shareToPublic($id)
    {
        $assessment = Assessment::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $assessment->update(['visibility' => 'public']);

        NotificationHelper::send(
            auth()->id(),
            'Bank Soal Publik',
            'Soal <b>' . $assessment->nama . '</b> telah berhasil dibagikan ke koleksi Bank Soal Publik.',
            'bank_soal'
        );

        return back()->with('success', 'Soal berhasil dibagikan ke Bank Soal Publik!');
    }

    // 4. Aksi Guru untuk Menyalin Soal dari Bank Soal ke Asesmen Pribadi
    public function gunakanSoal($id)
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

    // 5. Create Asesmen
    public function create()
    {
        return view('guru.assessments.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'nama' => 'required|string|max:255',
        'mata_pelajaran' => 'required|string|max:255',
        'kelas' => 'required|string|max:100',
        'tujuan_pembelajaran' => 'required|string',
        'materi_ajar' => 'required|string',
        'versi_asesmen' => 'required|string',
        'jumlah_pilihan_ganda' => 'required|integer|min:0',
        'jumlah_esai' => 'required|integer|min:0',
        'status' => 'required|in:Published,Draft',
        'visibility' => 'required|in:private,public',
    ]);

    Assessment::create([
        'user_id' => auth()->id(),
        'nama' => $request->nama,
        'mapel' => $request->mata_pelajaran,
        'kelas' => $request->kelas,
        'tujuan_pembelajaran' => $request->tujuan_pembelajaran,
        'materi_ajar' => $request->materi_ajar,
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

        // 1. Kirim notifikasi ke Guru (Pembuat Asesmen)
        NotificationHelper::send(
            auth()->id(),
            'Asesmen Berhasil Dibuat',
            'Multi-agent AI berhasil menyusun asesmen <b>' . $assessment->nama . '</b>.',
            'ai_generation'
        );

        // 2. Kirim notifikasi ke Semua Siswa
        $siswaList = User::where('role', 'siswa')->get();
        foreach ($siswaList as $siswa) {
            NotificationHelper::send(
                $siswa->id,
                'Asesmen Baru Tersedia',
                'Guru <b>' . auth()->user()->name . '</b> menambahkan asesmen baru: <i>' . $assessment->nama . '</i>.',
                'assessment'
            );
        }

        return redirect()->route('guru.assessments.index')->with('success', 'Asesmen berhasil dibuat dan disimpan!');
    }
}
