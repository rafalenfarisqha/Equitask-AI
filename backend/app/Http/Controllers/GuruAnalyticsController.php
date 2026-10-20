<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuruAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // Hanya kelas milik guru yang login.
        $classes = DB::table('classes')
            ->where('teacher_id', $teacherId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->input('class_id')
            : (int) ($classes->first()->id ?? 0);

        $selectedClass = $classes->first(
            fn ($class) => (int) $class->id === $selectedClassId
        );

        abort_if(
            $selectedClassId !== 0 && !$selectedClass,
            403,
            'Kelas bukan milik guru ini.'
        );

        $emptyStats = [
            'jumlah_siswa' => 0,
            'jumlah_asesmen' => 0,
            'total_tugas' => 0,
            'selesai' => 0,
            'dinilai' => 0,
            'rata_rata' => null,
            'tertinggi' => null,
            'terendah' => null,
            'penyelesaian' => null,
        ];

        $stats = $emptyStats;
        $assessmentOptions = collect();
        $assessmentStats = collect();
        $studentStats = collect();
        $detailRows = collect();
        $selectedAssessmentId = 0;

        if ($selectedClass) {

            // Ambil seluruh anggota kelas, tanpa data disabilitas.
            $studentIds = DB::table('class_members')
                ->where('class_id', $selectedClassId)
                ->distinct()
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->values();

            // Baca nama siswa dari tabel users jika tersedia.
            $nameColumn = null;

            if (Schema::hasTable('users')) {
                foreach (['name', 'nama', 'full_name'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $nameColumn = $column;
                        break;
                    }
                }
            }

            $studentNames = collect();

            if (
                $studentIds->isNotEmpty()
                && $nameColumn !== null
            ) {
                $studentNames = DB::table('users')
                    ->whereIn('id', $studentIds->all())
                    ->pluck($nameColumn, 'id');
            }

            // Hanya asesmen yang benar-benar ditugaskan ke kelas.
            $assessmentOptions = DB::table(
                'assessment_class_assignments as aca'
            )
                ->join(
                    'assessments as a',
                    'a.id',
                    '=',
                    'aca.assessment_id'
                )
                ->where('aca.class_id', $selectedClassId)
                ->where('a.user_id', $teacherId)
                ->where('a.status', 'Published')
                ->select('a.id', 'a.nama', 'a.mapel')
                ->distinct()
                ->orderBy('a.nama')
                ->get();

            $allowedAssessmentIds = $assessmentOptions
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            $selectedAssessmentId = $request->filled('assessment_id')
                ? (int) $request->input('assessment_id')
                : 0;

            if (
                $selectedAssessmentId !== 0
                && !$allowedAssessmentIds->contains(
                    $selectedAssessmentId
                )
            ) {
                abort(403, 'Asesmen tidak ditugaskan ke kelas ini.');
            }

            // Filter asesmen untuk statistik yang sedang ditampilkan.
            $filteredAssessments = $selectedAssessmentId !== 0
                ? $assessmentOptions->filter(
                    fn ($assessment) =>
                        (int) $assessment->id === $selectedAssessmentId
                )->values()
                : $assessmentOptions;

            $filteredAssessmentIds = $filteredAssessments
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            // Ambil pengerjaan siswa asli dari PostgreSQL.
            $attempts = collect();

            if (
                $studentIds->isNotEmpty()
                && $filteredAssessmentIds->isNotEmpty()
            ) {
                $attempts = DB::table('student_assessments')
                    ->whereIn('student_id', $studentIds->all())
                    ->whereIn(
                        'assessment_id',
                        $filteredAssessmentIds->all()
                    )
                    ->orderByDesc('id')
                    ->get();
            }

            /*
             * Satu siswa + satu asesmen = satu hasil.
             *
             * Ambil pengerjaan terakhir yang sudah dikumpulkan.
             * Jika belum ada pengumpulan, ambil pengerjaan
             * terakhir untuk mengetahui statusnya.
             */
            $attemptsByPair = $attempts
                ->groupBy(
                    fn ($attempt) =>
                        $attempt->student_id . ':' .
                        $attempt->assessment_id
                )
                ->map(function ($group) {
                    return $group->first(
                        fn ($attempt) =>
                            $this->isSubmitted($attempt)
                    ) ?? $group->first();
                });

            $rows = collect();

            foreach ($filteredAssessments as $assessment) {
                foreach ($studentIds as $studentId) {
                    $key = $studentId . ':' . $assessment->id;

                    $attempt = $attemptsByPair->get($key);

                    $submitted = $attempt
                        ? $this->isSubmitted($attempt)
                        : false;

                    $score = (
                        $submitted
                        && $attempt !== null
                        && is_numeric($attempt->score)
                    )
                        ? (float) $attempt->score
                        : null;

                    $rows->push([
                        'student_id' => $studentId,
                        'student_name' => (string) (
                            $studentNames->get($studentId)
                            ?: 'Siswa #' . $studentId
                        ),
                        'assessment_id' => (int) $assessment->id,
                        'assessment_name' => $assessment->nama,
                        'mapel' => $assessment->mapel,
                        'status' => $submitted
                            ? ($score !== null
                                ? 'Dinilai'
                                : 'Menunggu nilai')
                            : ($attempt
                                ? 'Sedang dikerjakan'
                                : 'Belum dikerjakan'),
                        'submitted' => $submitted,
                        'score' => $score,
                        'submitted_at' => $attempt->submitted_at
                            ?? null,
                    ]);
                }
            }

            $detailRows = $rows->sortBy([
                ['student_name', 'asc'],
                ['assessment_name', 'asc'],
            ])->values();

            $scoredRows = $rows->filter(
                fn ($row) => $row['score'] !== null
            );

            $totalTasks = $rows->count();

            $submittedCount = $rows->filter(
                fn ($row) => $row['submitted']
            )->count();

            $stats = [
                'jumlah_siswa' => $studentIds->count(),
                'jumlah_asesmen' => $filteredAssessments->count(),
                'total_tugas' => $totalTasks,
                'selesai' => $submittedCount,
                'dinilai' => $scoredRows->count(),
                'rata_rata' => $scoredRows->isNotEmpty()
                    ? round($scoredRows->avg('score'), 2)
                    : null,
                'tertinggi' => $scoredRows->isNotEmpty()
                    ? $scoredRows->max('score')
                    : null,
                'terendah' => $scoredRows->isNotEmpty()
                    ? $scoredRows->min('score')
                    : null,
                'penyelesaian' => $totalTasks > 0
                    ? round(
                        $submittedCount / $totalTasks * 100,
                        1
                    )
                    : null,
            ];

            // Rata-rata asli untuk setiap asesmen.
            $assessmentStats = $filteredAssessments->map(
                function ($assessment) use ($rows) {
                    $items = $rows->where(
                        'assessment_id',
                        (int) $assessment->id
                    );

                    $scored = $items->filter(
                        fn ($row) => $row['score'] !== null
                    );

                    return [
                        'id' => (int) $assessment->id,
                        'nama' => $assessment->nama,
                        'mapel' => $assessment->mapel,
                        'average' => $scored->isNotEmpty()
                            ? round($scored->avg('score'), 2)
                            : null,
                        'submitted' => $items->filter(
                            fn ($row) => $row['submitted']
                        )->count(),
                        'total' => $items->count(),
                        'graded' => $scored->count(),
                    ];
                }
            )->values();

            // Rekap hasil individual siswa.
            $studentStats = $studentIds->map(
                function ($studentId) use (
                    $rows,
                    $studentNames
                ) {
                    $items = $rows->where(
                        'student_id',
                        $studentId
                    );

                    $scored = $items->filter(
                        fn ($row) => $row['score'] !== null
                    );

                    return [
                        'id' => $studentId,
                        'name' => (string) (
                            $studentNames->get($studentId)
                            ?: 'Siswa #' . $studentId
                        ),
                        'average' => $scored->isNotEmpty()
                            ? round($scored->avg('score'), 2)
                            : null,
                        'submitted' => $items->filter(
                            fn ($row) => $row['submitted']
                        )->count(),
                        'graded' => $scored->count(),
                        'total' => $items->count(),
                    ];
                }
            )->sortBy('name')->values();
        }

        return view('guru.analytics.index', compact(
            'classes',
            'selectedClass',
            'selectedClassId',
            'selectedAssessmentId',
            'assessmentOptions',
            'stats',
            'assessmentStats',
            'studentStats',
            'detailRows'
        ));
    }

    /**
     * Deteksi apakah pengerjaan sudah dikumpulkan.
     */
    private function isSubmitted(object $attempt): bool
    {
        if (!empty($attempt->submitted_at)) {
            return true;
        }

        return in_array(
            strtolower(trim((string) ($attempt->status ?? ''))),
            [
                'submitted',
                'completed',
                'finished',
                'graded',
                'dinilai',
                'selesai',
            ],
            true
        );
    }
}
