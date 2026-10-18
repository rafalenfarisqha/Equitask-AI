<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentPageController extends Controller
{
    /**
     * Menampilkan riwayat pengerjaan assessment siswa.
     */
    public function riwayat()
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'siswa', 403);

        $riwayat = collect();

        // Cegah error jika tabel pengerjaan belum tersedia.
        if (
            Schema::hasTable('student_assessments') &&
            Schema::hasTable('assessments')
        ) {
            $riwayat = DB::table('student_assessments as sa')
                ->leftJoin(
                    'assessments as a',
                    'a.id',
                    '=',
                    'sa.assessment_id'
                )
                ->where('sa.student_id', $user->id)
                ->select(
                    'sa.id',
                    'sa.status',
                    'sa.started_at',
                    'sa.submitted_at',
                    'sa.score',
                    'a.nama',
                    'a.mapel'
                )
                ->orderByRaw('sa.submitted_at DESC NULLS LAST')
                ->orderByDesc('sa.started_at')
                ->get();
        }

        $totalRiwayat = $riwayat->count();

        $selesai = $riwayat->filter(function ($item) {
            return strtolower($item->status ?? '') === 'submitted'
                || strtolower($item->status ?? '') === 'completed';
        })->count();

        return view('student.riwayat', compact(
            'user',
            'riwayat',
            'totalRiwayat',
            'selesai'
        ));
    }

    /**
     * Menampilkan profil siswa yang sedang login.
     */
    public function profil()
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'siswa', 403);

        $initials = collect(
            preg_split('/\s+/', trim($user->name))
        )
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->implode('');

        return view('student.profil', compact('user', 'initials'));
    }
}