<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda mendaftarkan rute-rute web untuk aplikasi Anda.
|
*/

// 1. Rute Halaman Awal (Splash/Root) langsung diarahkan ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Rute Autentikasi (Login)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


/*
|--------------------------------------------------------------------------
| RUTE UNTUK GURU
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->group(function () {

    Route::get('/dashboard', function () {
        return view('guru.dashboard'); // Akan mencari file resources/views/guru/dashboard.blade.php
    })->name('dashboard');

    Route::get('/kelas', function () {
        return view('guru.kelas');
    })->name('kelas');

    Route::get('/kelas/{id}', function ($id) {
        // $id akan berisi '7a', '7b', dll sesuai URL yang diklik
        // Nantinya, Anda akan melakukan query database di sini (misal: Kelas::find($id))

        return view('guru.detail-kelas', [
            'id_kelas' => $id // Mengirim variabel ID ke Blade
        ]);
    })->name('detail-kelas');

    Route::get('/bank-soal', function () {
        return view('guru.bank-soal');
    })->name('bank-soal');

    Route::get('/profil', function () {
        return view('guru.profil');
    })->name('profil');

});


/*
|--------------------------------------------------------------------------
| RUTE UNTUK SISWA
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->name('siswa.')->group(function () {

    Route::get('/dashboard', function () {
        return view('siswa.dashboard'); // Akan mencari file resources/views/siswa/dashboard.blade.php
    })->name('dashboard');

    Route::get('/daftar-assessment', function () {
        return view('siswa.daftar-assessment');
    })->name('assessment');

    Route::get('/riwayat', function () {
        return view('siswa.riwayat');
    })->name('riwayat');

    Route::get('/profil', function () {
        return view('siswa.profil');
    })->name('profil');

});
