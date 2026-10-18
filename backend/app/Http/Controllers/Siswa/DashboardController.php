<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pastikan halaman ini hanya digunakan oleh akun siswa.
        abort_unless($user && $user->role === 'siswa', 403);

        // Ambil kelas yang diikuti siswa yang sedang login.
        $classes = DB::table('class_members as cm')
            ->join('classes as c', 'c.id', '=', 'cm.class_id')
            ->where('cm.student_id', $user->id)
            ->select('c.id', 'c.name', 'c.description')
            ->orderBy('c.name')
            ->get();

        $totalKelas = $classes->count();

        // Ambil assessment yang sudah dipublikasikan untuk umum.
        $assessmentQuery = DB::table('assessments')
            ->where('status', 'Published')
            ->where('visibility', 'public');

        $totalAssessment = (clone $assessmentQuery)->count();

        $assessments = (clone $assessmentQuery)
            ->select(
                'id',
                'nama',
                'mapel',
                'icon',
                'color',
                'created_at'
            )
            ->latest('created_at')
            ->limit(3)
            ->get();

        // Buat inisial nama untuk avatar siswa.
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->implode('');

        return view('student.dashboard', compact(
            'user',
            'classes',
            'totalKelas',
            'totalAssessment',
            'assessments',
            'initials'
        ));       
    }

    public function assessments()
{
    $user = Auth::user();

    abort_unless($user && $user->role === 'siswa', 403);

    $assessments = DB::table('assessments')
        ->where('status', 'Published')
        ->where('visibility', 'public')
        ->select('id', 'nama', 'mapel', 'icon', 'color', 'created_at')
        ->latest('created_at')
        ->get();

    return view('student.assessment-list', compact('assessments'));
}
}