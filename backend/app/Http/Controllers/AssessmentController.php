<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    

    // 1. Menampilkan Asesmen Milik Guru Sendiri (Privat / Daftar Buatan Sendiri)

    
    public function index()
    {
        $assessments = Assessment::where('user_id', auth()->id())
            ->latest()
            ->get();

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
    // 4. Create Asesmen
    public function create()
    {
        return view('assessments.create');
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
