<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAssessmentTrackController extends Controller
{
    public function update(
        Request $request,
        int $classId,
        int $studentId
    ) {
        $guru = $request->user();

        abort_unless(
            $guru && in_array($guru->role, ['guru', 'teacher'], true),
            403
        );

        $validated = $request->validate([
            'assessment_track' => [
                'required',
                'in:reguler,adaptif',
            ],
        ]);

        // Guru hanya dapat mengatur kelas miliknya.
        $kelas = DB::table('classes')
            ->where('id', $classId)
            ->where('teacher_id', $guru->id)
            ->first();

        abort_unless($kelas, 404, 'Kelas tidak ditemukan.');

        // Siswa harus merupakan anggota kelas ini.
        $anggota = DB::table('class_members')
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->first();

        abort_unless(
            $anggota,
            404,
            'Siswa bukan anggota kelas ini.'
        );

        DB::table('class_members')
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->update([
                'assessment_track' =>
                    $validated['assessment_track'],
            ]);

        return back()->with(
            'success',
            'Jalur asesmen siswa berhasil diperbarui.'
        );
    }
}
