<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\User;

class ClassroomController extends Controller
{
    public function index()
    {
        $classes = Classroom::where('teacher_id', auth()->id())->get();
        return view('guru.kelas', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        ClassRoom::create([
            'teacher_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('guru.kelas')->with('success', 'Kelas baru berhasil dibuat!');
    }

    // Membuka detail kelas (Daftar siswa & link join)
    public function show($id)
    {
        // Ubah $id menjadi integer (angka) agar sesuai dengan tipe data di database PostgreSQL
        $classroom = Classroom::with('students')->find((int) $id);

        // Jika kelas sama sekali tidak ada di database
        if (!$classroom) {
            return redirect()->route('guru.kelas')->with('error', 'Kelas dengan ID tersebut tidak ditemukan di database.');
        }

        // Cek apakah kelas ini milik guru yang sedang login
        if ($classroom->teacher_id !== auth()->id()) {
            return redirect()->route('guru.kelas')->with('error', 'Anda tidak memiliki hak akses ke kelas ini.');
        }

        return view('guru.detail-kelas', compact('classroom'));
    }

    public function addStudent(Request $request, $id)
    {
        $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $classroom = Classroom::where('teacher_id', auth()->id())->findOrFail($id);

        $student = User::where('nisn', $request->nisn)->where('role', 'siswa')->first();

        if (!$student) {
            return back()->withErrors(['nisn' => 'Siswa dengan NISN tersebut tidak ditemukan.']);
        }

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return back()->withErrors(['nisn' => 'Siswa tersebut sudah tergabung di dalam kelas ini.']);
        }

        $classroom->students()->attach($student->id, ['joined_at' => now()]);

        return redirect()->route('guru.kelas.show', $id)->with('success', 'Siswa berhasil ditambahkan ke kelas!');
    }

    public function importExcel(Request $request, $id)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
        ]);

        $classroom = ClassRoom::where('teacher_id', auth()->id())->findOrFail($id);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Membaca file CSV / Excel yang di-upload
        $data = array_map('str_getcsv', file($path));

        $successCount = 0;
        $notFoundCount = 0;

        foreach ($data as $index => $row) {
            // Lewati baris pertama jika berupa header (misal: kolom "nisn" atau "NISN")
            if ($index === 0 && !is_numeric($row[0])) {
                continue;
            }

            $nisn = trim($row[0] ?? ''); // Asumsi kolom pertama adalah NISN

            if (empty($nisn)) continue;

            // Cari siswa berdasarkan NISN dan role 'siswa'
            $student = User::where('nisn', $nisn)->where('role', 'siswa')->first();

            if ($student) {
                // Cek apakah belum terdaftar di kelas ini
                if (!$classroom->students()->where('student_id', $student->id)->exists()) {
                    $classroom->students()->attach($student->id, ['joined_at' => now()]);
                    $successCount++;
                }
            } else {
                experimental: $notFoundCount++;
            }
        }

        return redirect()->route('guru.kelas.show', $id)
            ->with('success', "Berhasil mengimpor $successCount siswa ke dalam kelas!");
    }

    // Siswa bergabung lewat Link
    public function joinViaLink($id)
    {
        $classroom = ClassRoom::findOrFail((int) $id);
        $user = auth()->user();

        // Validasi: pastikan yang login adalah siswa
        if ($user->role !== 'siswa') {
            return redirect()->route('guru.dashboard')->with('error', 'Hanya akun siswa yang dapat bergabung ke kelas.');
        }

        // Cek apakah siswa sudah terdaftar di kelas ini
        if (!$classroom->students()->where('student_id', $user->id)->exists()) {
            $classroom->students()->attach($user->id, ['joined_at' => now()]);
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Anda berhasil bergabung ke kelas ' . $classroom->name);
    }
}
