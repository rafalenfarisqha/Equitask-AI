<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar: ambil assessment yang visibility-nya public dan status Published
        $query = Assessment::where('visibility', 'public')
                           ->where('status', 'Published');

        // Filter berdasarkan Mata Pelajaran (mapel) dari chip filter
        if ($request->has('mapel') && $request->mapel != '') {
            $query->where('mapel', $request->mapel);
        }

        // Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('mapel', 'like', '%' . $search . '%');
            });
        }

        $publicAssessments = $query->latest()->get();

        return view('guru.bank-soal', compact('publicAssessments'));
    }

    // Fungsi untuk tombol "Gunakan Soal Ini" (menyalin ke akun guru yang login)
    public function gunakanSoal($id)
    {
        $source = Assessment::findOrFail($id);

        $clone = $source->replicate();
        $clone->user_id = Auth::id(); // Milik guru yang sedang login
        $clone->visibility = 'private'; // Jadikan private di akun pribadi
        $clone->status = 'Draft';
        $clone->nama = $source->nama . ' (Salinan)';
        $clone->save();

        return redirect()->route('guru.assessments.index')->with('success', 'Soal berhasil disalin ke daftar asesmen Anda!');
    }
}
