<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Query asesmen yang boleh dilihat siswa:
     * 1. Asesmen publik yang sudah dipublikasikan.
     * 2. Asesmen privat yang ditugaskan ke kelas siswa.
     */
    private function accessibleAssessments(int $studentId)
    {
        return DB::table('assessments as a')
            ->where('a.status', 'Published')
            ->where(function ($query) use ($studentId) {
                $query->where('a.visibility', 'public')
                    ->orWhereExists(function ($subquery) use ($studentId) {
                        $subquery->selectRaw('1')
                            ->from('assessment_class_assignments as aca')
                            ->join(
                                'class_members as cm',
                                'cm.class_id',
                                '=',
                                'aca.class_id'
                            )
                            ->whereColumn(
                                'aca.assessment_id',
                                'a.id'
                            )
                            ->where('cm.student_id', $studentId);
                    });
            });
    }

    /**
     * Dashboard utama siswa.
     */
    public function index()
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array($user->role, ['siswa', 'student'], true),
            403,
            'Halaman ini hanya untuk siswa.'
        );

        $classes = DB::table('class_members as cm')
            ->join('classes as c', 'c.id', '=', 'cm.class_id')
            ->where('cm.student_id', $user->id)
            ->select('c.id', 'c.name', 'c.description')
            ->orderBy('c.name')
            ->get();

        $totalKelas = $classes->count();

        $assessmentQuery = $this->accessibleAssessments($user->id);

        $totalAssessment = (clone $assessmentQuery)->count();

        $assessments = (clone $assessmentQuery)
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.icon',
                'a.color',
                'a.created_at'
            )
            ->latest('a.created_at')
            ->limit(3)
            ->get();

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

    /**
     * Daftar asesmen siswa.
     */
    public function assessments()
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array($user->role, ['siswa', 'student'], true),
            403,
            'Halaman ini hanya untuk siswa.'
        );

        $assessments = $this->accessibleAssessments($user->id)
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.icon',
                'a.color',
                'a.created_at'
            )
            ->latest('a.created_at')
            ->get();

        return view('student.assessment-list', compact('assessments'));
    }
}
