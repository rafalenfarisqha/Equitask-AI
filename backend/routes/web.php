<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentReviewController;
use App\Http\Controllers\GuruAnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\BankSoalController;

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\StudentPageController;
use App\Http\Controllers\Siswa\NotificationController as SiswaNotificationController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTENTIKASI
|--------------------------------------------------------------------------
*/

Route::get(
    '/login',
    [AuthController::class, 'showLoginForm']
)->name('login');

Route::post(
    '/login',
    [AuthController::class, 'login']
);

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post(
    '/register',
    [AuthController::class, 'register']
)->name('register.store');

/*
|--------------------------------------------------------------------------
| RUTE GURU
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        /*
        |--------------------------------------------------------------
        | Profil Guru
        |--------------------------------------------------------------
        */

        Route::get(
            '/profil',
            [ProfileController::class, 'show']
        )->name('profil');

        Route::get(
            '/profil/edit',
            [ProfileController::class, 'edit']
        )->name('profil.edit');

        Route::post(
            '/profil/update',
            [ProfileController::class, 'update']
        )->name('profil.update');

        /*
        |--------------------------------------------------------------
        | Dashboard Guru
        |--------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AssessmentController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------
        | Analytics Guru
        |--------------------------------------------------------------
        | Mengambil nilai asli dari student_assessments.
        | Menampilkan rata-rata kelas dan hasil setiap siswa.
        */

        Route::get(
            '/analytics',
            [GuruAnalyticsController::class, 'index']
        )->name('analytics.index');

        /*
        |--------------------------------------------------------------
        | Bank Soal Publik
        |--------------------------------------------------------------
        */

        Route::get(
            '/bank-soal',
            [BankSoalController::class, 'index']
        )->name('bank-soal');

        Route::post(
            '/bank-soal/{id}/gunakan',
            [BankSoalController::class, 'gunakanSoal']
        )->name('bank-soal.gunakan');

        /*
        |--------------------------------------------------------------
        | Daftar dan Pembuatan Asesmen
        |--------------------------------------------------------------
        */

        Route::get(
            '/assessments',
            [AssessmentController::class, 'index']
        )->name('assessments.index');

        Route::get(
            '/assessments/create',
            [AssessmentController::class, 'create']
        )->name('assessments.create');

        /*
        | Generate Dify
        | Tetap menggunakan AssessmentController::store.
        */

        Route::post(
            '/assessments',
            [AssessmentController::class, 'store']
        )->name('assessments.store');

        /*
        |--------------------------------------------------------------
        | Edit Soal Reguler dan Adaptif
        |--------------------------------------------------------------
        */

        // Menampilkan formulir edit soal.
        Route::get(
            '/assessments/{id}/edit',
            [AssessmentReviewController::class, 'edit']
        )->whereNumber('id')
         ->name('assessments.edit');

        // Menyimpan revisi guru ke PostgreSQL.
        Route::put(
            '/assessments/{id}',
            [AssessmentReviewController::class, 'update']
        )->whereNumber('id')
         ->name('assessments.update');

        /*
        |--------------------------------------------------------------
        | Publikasikan Satu Paket Asesmen ke Kelas
        |--------------------------------------------------------------
        */

        Route::post(
            '/assessments/{id}/publish-to-class',
            [AssessmentReviewController::class, 'publishToClass']
        )->whereNumber('id')
         ->name('assessments.publish-to-class');

        /*
        |--------------------------------------------------------------
        | Berbagi ke Bank Soal Publik
        |--------------------------------------------------------------
        */

        Route::post(
            '/assessments/{id}/share',
            [AssessmentController::class, 'shareToPublic']
        )->whereNumber('id')
         ->name('assessments.share');

        /*
        |--------------------------------------------------------------
        | Detail Asesmen
        |--------------------------------------------------------------
        */

        Route::get(
            '/assessments/{id}',
            [AssessmentController::class, 'show']
        )->whereNumber('id')
         ->name('assessments.show');

        /*
        |--------------------------------------------------------------
        | Notifikasi Guru
        |--------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [NotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------
        | Manajemen Kelas
        |--------------------------------------------------------------
        */

        Route::get(
            '/kelas',
            [ClassroomController::class, 'index']
        )->name('kelas');

        Route::post(
            '/kelas',
            [ClassroomController::class, 'store']
        )->name('kelas.store');

        Route::get(
            '/kelas/{id}',
            [ClassroomController::class, 'show']
        )->whereNumber('id')
         ->name('kelas.show');

        Route::post(
            '/kelas/{id}/tambah-siswa',
            [ClassroomController::class, 'addStudent']
        )->whereNumber('id')
         ->name('kelas.addStudent');

        Route::post(
            '/kelas/{id}/import-excel',
            [ClassroomController::class, 'importExcel']
        )->whereNumber('id')
         ->name('kelas.importExcel');
    });

/*
|--------------------------------------------------------------------------
| RUTE SISWA
|--------------------------------------------------------------------------
| Seluruh bagian siswa dipertahankan.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        /*
        |--------------------------------------------------------------
        | Dashboard Siswa
        |--------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------
        | Daftar Asesmen Siswa
        |--------------------------------------------------------------
        */

        Route::get(
            '/daftar-assessment',
            [DashboardController::class, 'assessments']
        )->name('assessment');

        /*
        |--------------------------------------------------------------
        | Riwayat Siswa
        |--------------------------------------------------------------
        */

        Route::get(
            '/riwayat',
            [StudentPageController::class, 'riwayat']
        )->name('riwayat');

        /*
        |--------------------------------------------------------------
        | Profil Siswa
        |--------------------------------------------------------------
        */

        Route::get(
            '/profil',
            [StudentPageController::class, 'profil']
        )->name('profil');

        /*
        |--------------------------------------------------------------
        | Notifikasi Siswa
        |--------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [SiswaNotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------
        | Join Kelas
        |--------------------------------------------------------------
        */

        Route::get(
            '/kelas/{id}/join',
            [ClassroomController::class, 'joinViaLink']
        )->whereNumber('id')
         ->name('kelas.join');
    });
