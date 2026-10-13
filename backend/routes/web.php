
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Rute Halaman Awal
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Rute Autentikasi (Login dan Register)
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Menampilkan halaman register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Memproses formulir pendaftaran
Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');


/*
|--------------------------------------------------------------------------
| RUTE UNTUK GURU
|--------------------------------------------------------------------------
*/

Route::prefix('guru')->name('guru.')->group(function () {

    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->middleware('auth')->name('dashboard');
    
    Route::get('/kelas', function () {
        return view('guru.kelas');
    })->name('kelas');

    Route::get('/kelas/{id}', function ($id) {
        return view('guru.detail-kelas', [
            'id_kelas' => $id
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
        return view('siswa.dashboard');
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
