<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Siswa\NotificationController as SiswaNotificationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\StudentPageController;
use App\Http\Controllers\BankSoalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');


/*
|--------------------------------------------------------------------------
| RUTE UNTUK GURU (FINAL & BERSIH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {

    // Profil Guru
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::post('/profil/update', [ProfileController::class, 'update'])->name('profil.update');

    // Dashboard Guru
    Route::get('/dashboard', [AssessmentController::class, 'dashboard'])->name('dashboard');

    // Bank Soal Publik & Fitur Salin Soal
    Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('bank-soal');
    Route::post('/bank-soal/{id}/gunakan', [BankSoalController::class, 'gunakanSoal'])->name('bank-soal.gunakan');

    // Asesmen Pribadi Guru
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::post('/assessments/{id}/share', [AssessmentController::class, 'shareToPublic'])->name('assessments.share');

    // Notifikasi Guru
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi');

    // --- MANAJEMEN KELAS (FINAL & TERPUSAT) ---
    Route::get('/kelas', [ClassroomController::class, 'index'])->name('kelas');
    Route::post('/kelas', [ClassroomController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id}', [ClassroomController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/{id}/tambah-siswa', [ClassroomController::class, 'addStudent'])->name('kelas.addStudent');
    // Import siswa via Excel/CSV
    Route::post('/kelas/{id}/import-excel', [ClassroomController::class, 'importExcel'])->name('kelas.importExcel');
});


/*
|--------------------------------------------------------------------------
| RUTE UNTUK SISWA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/daftar-assessment', [DashboardController::class, 'assessments'])->name('assessment');
    Route::get('/riwayat', [StudentPageController::class, 'riwayat'])->name('riwayat');
    // Rute untuk menampilkan halaman simulasi assessment
    /* Route::get('/assessment-demo/{id}', function ($id) {
        abort_unless(in_array((int) $id, [1, 2, 3], true), 404);
        return view('student.assessment');
    })->name('assessment-demo'); */

Route::get('/profil', [StudentPageController::class, 'profil'])
    ->name('profil');

    // Rute Notifikasi Siswa
    Route::get('/notifikasi', [SiswaNotificationController::class, 'index'])
        ->name('notifikasi');
    });

    // Siswa bergabung ke kelas via link
    Route::get('/kelas/{id}/join', [ClassroomController::class, 'joinViaLink'])->name('kelas.join');
