<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Memastikan pengguna yang login adalah siswa.
     */
    private function authenticatedStudent()
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['siswa', 'student'],
                true
            ),
            403,
            'Halaman ini hanya untuk siswa.'
        );

        return $user;
    }

    /**
     * Mengambil asesmen yang ditugaskan
     * ke kelas tempat siswa terdaftar.
     *
     * Aturan:
     * - Harus berstatus Published.
     * - Harus ditugaskan ke kelas siswa.
     * - Asesmen public TIDAK otomatis tampil.
     * - Tidak menampilkan asesmen dari kelas lain.
     *
     * exists() mencegah data asesmen ganda.
     */
    private function accessibleAssessments(int $studentId)
    {
        return DB::table('assessments as a')
            ->where('a.status', 'Published')
            ->whereExists(function ($query) use ($studentId) {
                $query
                    ->selectRaw('1')
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
                    ->where(
                        'cm.student_id',
                        $studentId
                    );
            });
    }

    /**
     * Dashboard utama siswa.
     */
    public function index()
    {
        $user = $this->authenticatedStudent();

        /*
         * Ambil daftar kelas yang diikuti siswa.
         */
        $classes = DB::table('class_members as cm')
            ->join(
                'classes as c',
                'c.id',
                '=',
                'cm.class_id'
            )
            ->where(
                'cm.student_id',
                $user->id
            )
            ->select(
                'c.id',
                'c.name',
                'c.description'
            )
            ->orderBy('c.name')
            ->get();

        $totalKelas = $classes->count();

        /*
         * Jumlah asesmen yang benar-benar ditugaskan.
         */
        $assessmentQuery = $this->accessibleAssessments(
            (int) $user->id
        );

        $totalAssessment = (clone $assessmentQuery)->count();

        /*
         * Tampilkan maksimal 3 asesmen
         * terbaru di dashboard.
         */
        $assessments = (clone $assessmentQuery)
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.icon',
                'a.color',
                'a.created_at'
            )
            ->orderByDesc('a.created_at')
            ->limit(3)
            ->get();

        /*
         * Inisial nama untuk avatar dashboard.
         */
        $initials = collect(
            explode(' ', trim((string) $user->name))
        )
            ->filter()
            ->take(2)
            ->map(
                fn ($word) => mb_substr(
                    $word,
                    0,
                    1
                )
            )
            ->implode('');

        return view(
            'student.dashboard',
            compact(
                'user',
                'classes',
                'totalKelas',
                'totalAssessment',
                'assessments',
                'initials'
            )
        );
    }

    /**
     * Halaman daftar asesmen siswa.
     */
    public function assessments()
    {
        $user = $this->authenticatedStudent();

        /*
         * Hanya tampilkan asesmen
         * yang ditugaskan ke kelas siswa.
         */
        $assessments = $this->accessibleAssessments(
            (int) $user->id
        )
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.icon',
                'a.color',
                'a.created_at'
            )
            ->orderByDesc('a.created_at')
            ->get();

        return view(
            'student.assessment-list',
            compact('assessments')
        );
    }
}
