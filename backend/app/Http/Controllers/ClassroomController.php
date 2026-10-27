<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    /**
     * Memastikan pengguna adalah guru.
     */
    private function ensureTeacher(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['guru', 'teacher'],
                true
            ),
            403,
            'Halaman ini hanya untuk guru.'
        );
    }

    /**
     * Mengambil kelas milik guru yang sedang login.
     */
    private function ownedClassroom(int $id): Classroom
    {
        $this->ensureTeacher();

        return Classroom::query()
            ->where('teacher_id', Auth::id())
            ->findOrFail($id);
    }

    /**
     * Memeriksa keanggotaan siswa di kelas.
     */
    private function studentAlreadyJoined(
        int $classId,
        int $studentId
    ): bool {
        return DB::table('class_members')
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Menambahkan anggota baru.
     * Profil awal selalu Reguler.
     */
    private function attachRegularStudent(
        Classroom $classroom,
        int $studentId
    ): void {
        $classroom->students()->attach($studentId, [
            'joined_at' => now(),
            'assessment_track' => 'reguler',
        ]);
    }

    /**
     * Daftar kelas guru.
     */
    public function index()
    {
        $this->ensureTeacher();

        $classes = Classroom::query()
            ->where('teacher_id', Auth::id())
            ->orderByDesc('id')
            ->get();

        return view(
            'guru.kelas',
            compact('classes')
        );
    }

    /**
     * Membuat kelas baru.
     */
    public function store(Request $request)
    {
        $this->ensureTeacher();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Classroom::create([
            'teacher_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('guru.kelas')
            ->with(
                'success',
                'Kelas baru berhasil dibuat!'
            );
    }

    /**
     * DETAIL KELAS
     *
     * Memuat:
     * - Daftar siswa
     * - Profil Reguler / Adaptif
     * - Assessment yang telah ditugaskan
     * - Assessment Published yang bisa ditugaskan
     */
    public function show(int $id)
    {
        $classroom = $this->ownedClassroom($id);

        $classroom->load('students');

        /*
         * Ambil profil siswa dari class_members.
         */
        $tracks = DB::table('class_members')
            ->where('class_id', $classroom->id)
            ->pluck(
                'assessment_track',
                'student_id'
            );

        foreach ($classroom->students as $student) {
            $track = strtolower(trim(
                (string) (
                    $tracks[$student->id] ?? 'reguler'
                )
            ));

            $student->assessment_track =
                $track === 'adaptif'
                    ? 'adaptif'
                    : 'reguler';
        }

        /*
         * Assessment yang telah ditugaskan
         * ke kelas ini.
         */
        $classAssessments = DB::table(
            'assessment_class_assignments as aca'
        )
            ->join(
                'assessments as a',
                'a.id',
                '=',
                'aca.assessment_id'
            )
            ->where(
                'aca.class_id',
                $classroom->id
            )
            ->where(
                'a.user_id',
                Auth::id()
            )
            ->where(
                'a.status',
                'Published'
            )
            ->select(
                'a.id',
                'a.nama',
                'a.mapel',
                'a.status',
                'a.created_at'
            )
            ->distinct()
            ->orderByDesc('a.created_at')
            ->get();

        /*
         * Assessment Published milik guru
         * yang belum ditugaskan ke kelas ini.
         */
        $availableAssessments = DB::table(
            'assessments as a'
        )
            ->where(
                'a.user_id',
                Auth::id()
            )
            ->where(
                'a.status',
                'Published'
            )
            ->whereNotExists(
                function ($query) use ($classroom) {
                    $query
                        ->selectRaw('1')
                        ->from(
                            'assessment_class_assignments as aca'
                        )
                        ->whereColumn(
                            'aca.assessment_id',
                            'a.id'
                        )
                        ->where(
                            'aca.class_id',
                            $classroom->id
                        );
                }
            )
            ->select(
                'a.id',
                'a.nama',
                'a.mapel'
            )
            ->orderBy('a.nama')
            ->get();

        return view(
            'guru.detail-kelas',
            compact(
                'classroom',
                'classAssessments',
                'availableAssessments'
            )
        );
    }

    /**
     * TAMBAH SISWA VIA NISN
     *
     * Siswa baru otomatis Reguler.
     */
    public function addStudent(
        Request $request,
        int $id
    ) {
        $classroom = $this->ownedClassroom($id);

        $validated = $request->validate([
            'nisn' => [
                'required',
                'string',
                'max:30',
            ],
        ]);

        $student = User::query()
            ->where(
                'nisn',
                trim($validated['nisn'])
            )
            ->where(
                'role',
                'siswa'
            )
            ->first();

        if (!$student) {
            return back()->withErrors([
                'nisn' =>
                    'Siswa dengan NISN tersebut tidak ditemukan.',
            ]);
        }

        if ($this->studentAlreadyJoined(
            (int) $classroom->id,
            (int) $student->id
        )) {
            return back()->withErrors([
                'nisn' =>
                    'Siswa tersebut sudah menjadi anggota kelas.',
            ]);
        }

        $this->attachRegularStudent(
            $classroom,
            (int) $student->id
        );

        return redirect()
            ->route(
                'guru.kelas.show',
                $classroom->id
            )
            ->with(
                'success',
                'Siswa berhasil ditambahkan dengan profil Reguler.'
            );
    }

    /**
     * IMPORT SISWA VIA CSV / TXT
     *
     * Kolom pertama = NISN.
     * Anggota baru otomatis Reguler.
     */
    public function importExcel(
        Request $request,
        int $id
    ) {
        $classroom = $this->ownedClassroom($id);

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:2048',
            ],
        ]);

        $file = $request->file('file');

        $handle = fopen(
            $file->getRealPath(),
            'r'
        );

        if ($handle === false) {
            return back()->withErrors([
                'file' => 'File tidak dapat dibaca.',
            ]);
        }

        $successCount = 0;
        $notFoundCount = 0;
        $skippedCount = 0;
        $rowIndex = 0;

        try {
            while (
                ($row = fgetcsv($handle)) !== false
            ) {
                $nisn = trim(
                    (string) ($row[0] ?? '')
                );

                /*
                 * Hilangkan BOM UTF-8.
                 */
                if ($rowIndex === 0) {
                    $nisn = preg_replace(
                        '/^\xEF\xBB\xBF/',
                        '',
                        $nisn
                    ) ?? $nisn;

                    /*
                     * Abaikan header "NISN".
                     */
                    if (strtolower($nisn) === 'nisn') {
                        $rowIndex++;
                        continue;
                    }
                }

                $rowIndex++;

                if ($nisn === '') {
                    continue;
                }

                $student = User::query()
                    ->where('nisn', $nisn)
                    ->where('role', 'siswa')
                    ->first();

                if (!$student) {
                    $notFoundCount++;
                    continue;
                }

                if ($this->studentAlreadyJoined(
                    (int) $classroom->id,
                    (int) $student->id
                )) {
                    $skippedCount++;
                    continue;
                }

                $this->attachRegularStudent(
                    $classroom,
                    (int) $student->id
                );

                $successCount++;
            }
        } finally {
            fclose($handle);
        }

        return redirect()
            ->route(
                'guru.kelas.show',
                $classroom->id
            )
            ->with(
                'success',
                "Import selesai. "
                . "{$successCount} siswa ditambahkan, "
                . "{$notFoundCount} tidak ditemukan, "
                . "{$skippedCount} sudah menjadi anggota."
            );
    }

    /**
     * TUGASKAN ASSESSMENT KE KELAS
     *
     * Memilih paket Published yang sudah ada.
     * Tidak perlu generate Dify lagi.
     */
    public function assignAssessment(
        Request $request,
        int $id
    ) {
        $classroom = $this->ownedClassroom($id);

        $validated = $request->validate([
            'assessment_id' => [
                'required',
                'integer',
                'exists:assessments,id',
            ],
        ]);

        $assessmentId = (int) $validated[
            'assessment_id'
        ];

        /*
         * Assessment harus:
         * - milik guru ini
         * - berstatus Published
         */
        $assessment = DB::table('assessments')
            ->where('id', $assessmentId)
            ->where('user_id', Auth::id())
            ->where('status', 'Published')
            ->first();

        if (!$assessment) {
            return back()->withErrors([
                'assessment_id' =>
                    'Assessment tidak tersedia, belum diterbitkan, '
                    . 'atau bukan milik guru ini.',
            ]);
        }

        /*
         * Jangan membuat penugasan ganda.
         */
        $alreadyAssigned = DB::table(
            'assessment_class_assignments'
        )
            ->where(
                'class_id',
                $classroom->id
            )
            ->where(
                'assessment_id',
                $assessmentId
            )
            ->exists();

        if ($alreadyAssigned) {
            return redirect()
                ->route(
                    'guru.kelas.show',
                    $classroom->id
                )
                ->with(
                    'success',
                    'Assessment ini sudah ditugaskan ke kelas.'
                );
        }

        /*
         * Simpan penugasan baru.
         */
        DB::table(
            'assessment_class_assignments'
        )->insert([
            'class_id' => $classroom->id,
            'assessment_id' => $assessmentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route(
                'guru.kelas.show',
                $classroom->id
            )
            ->with(
                'success',
                'Assessment berhasil ditugaskan ke kelas '
                . $classroom->name . '.'
            );
    }

    /**
     * SISWA BERGABUNG MELALUI LINK
     *
     * Jika siswa belum terdaftar,
     * otomatis menggunakan profil Reguler.
     *
     * Profil siswa lama tidak diubah.
     */
    public function joinViaLink(int $id)
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->role === 'siswa',
            403,
            'Hanya siswa yang dapat bergabung ke kelas.'
        );

        $classroom = Classroom::findOrFail($id);

        if (!$this->studentAlreadyJoined(
            (int) $classroom->id,
            (int) $user->id
        )) {
            $this->attachRegularStudent(
                $classroom,
                (int) $user->id
            );
        }

        return redirect()
            ->route('siswa.dashboard')
            ->with(
                'success',
                'Berhasil bergabung ke kelas '
                . $classroom->name
            );
    }
}
