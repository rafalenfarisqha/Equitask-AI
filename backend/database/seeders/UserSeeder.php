<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun Guru
        DB::table('users')->insert([
            'name' => 'Ibu Guru Contoh',
            'email' => 'guru@equitask.ai',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat akun Siswa
        $studentId = DB::table('users')->insertGetId([
            'name' => 'Siswa Inklusif',
            'email' => 'siswa@equitask.ai',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Menambahkan profil pendukung untuk siswa
        DB::table('student_profiles')->insert([
            'user_id' => $studentId,
            'disability_type' => 'Disleksia / Hambatan Membaca',
            'support_needs' => 'Membutuhkan format teks dengan Easy Read dan spasi luas',
            'learning_notes' => 'Perlu waktu pengerjaan yang lebih fleksibel',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
