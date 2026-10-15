<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Helpers\NotificationHelper;
use App\Models\User;

class AssessmentController extends Controller
{
    public function dashboard()
    {
    $guru = auth()->user();

    // 1. Hitung data asli dari database
    $totalAssessments = Assessment::where('user_id', $guru->id)->count();
    $publishedCount = Assessment::where('user_id', $guru->id)->where('status', 'Published')->count();
    $publicCount = Assessment::where('visibility', 'public')->count();

    // 2. Ambil 4 asesmen terbaru buatan guru ini
    $recentAssessments = Assessment::where('user_id', $guru->id)->latest()->take(4)->get();

    // Data kelas & siswa (bisa disesuaikan nanti jika tabel kelas sudah dibuat, sementara menggunakan angka atau relasi)
    $totalKelas = 3;
    $totalSiswa = 84;

    return view('guru.dashboard', compact(
        'guru',
        'totalAssessments',
        'publishedCount',
        'publicCount',
        'recentAssessments',
        'totalKelas',
        'totalSiswa'
    ));
    }

    // 1. Menampilkan Asesmen Milik Guru Sendiri (Privat / Daftar Buatan Sendiri)
    public function index()
    {
        $assessments = Assessment::where('user_id', auth()->id())->latest()->get();
        return view('assessments.index', compact('assessments'));
    }

    // 2. Menampilkan Bank Soal Publik (Komunitas Guru dari Sekolah Lain)
    public function bankSoal(Request $request)
    {
        $query = Assessment::where('visibility', 'public')->latest();

        // Fitur filter berdasarkan mata pelajaran
        if ($request->has('mapel') && $request->mapel !== 'Semua') {
            $query->where('mapel', $request->mapel);
        }

        $publicAssessments = $query->get();

        return view('guru.bank-soal', compact('publicAssessments'));
    }

    // 3. Aksi Guru untuk Membagikan Soal Pribadi ke Bank Soal Publik
    public function shareToPublic($id)
    {
        $assessment = Assessment::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Ubah visibilitas menjadi public
        $assessment->update(['visibility' => 'public']);

        // --> KIRIM NOTIFIKASI KE DATABASE <--
        NotificationHelper::send(
            auth()->id(),
            'Bank Soal Publik',
            'Soal <b>' . $assessment->title . '</b> telah berhasil dibagikan ke koleksi Bank Soal Publik.',
            'bank_soal'
        );

        return back()->with('success', 'Soal berhasil dibagikan ke Bank Soal Publik!');
    }

    public function store(Request $request)
    {
        // Validasi input asesmen...
        $request->validate([
            'title' => 'required|string|max:255',
            'mapel' => 'required|string',
        ]);

        // Simpan asesmen ke database
        $assessment = Assessment::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'mapel' => $request->mapel,
            'status' => 'Published',
            'visibility' => 'private',
        ]);

        // 1. Kirim notifikasi ke Guru (Pembuat Asesmen)
        NotificationHelper::send(
            auth()->id(),
            'Asesmen Berhasil Dibuat',
            'Multi-agent AI berhasil menyusun asesmen <b>' . $assessment->title . '</b>.',
            'ai_generation'
        );

        // 2. Kirim notifikasi ke Semua Siswa (Opsional / Jika ada fitur kelas)
        $siswaList = User::where('role', 'siswa')->get();
        foreach ($siswaList as $siswa) {
            NotificationHelper::send(
                $siswa->id,
                'Asesmen Baru Tersedia',
                'Guru <b>' . auth()->user()->name . '</b> menambahkan asesmen baru: <i>' . $assessment->title . '</i>.',
                'assessment'
            );
        }

        return redirect()->route('assessments.index')->with('success', 'Asesmen berhasil dibuat!');
    }
}
