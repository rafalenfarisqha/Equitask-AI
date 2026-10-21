
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
        | PROFIL GURU
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
        | DASHBOARD GURU
        |--------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AssessmentController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------
        | ANALYTICS GURU
        |--------------------------------------------------------------
        */

        Route::get(
            '/analytics',
            [GuruAnalyticsController::class, 'index']
        )->name('analytics.index');

        /*
        |--------------------------------------------------------------
        | BANK SOAL PUBLIK
        |--------------------------------------------------------------
        */

        Route::get(
            '/bank-soal',
            [BankSoalController::class, 'index']
        )->name('bank-soal');

        Route::post(
            '/bank-soal/{id}/gunakan',
            [BankSoalController::class, 'gunakanSoal']
        )->whereNumber('id')
         ->name('bank-soal.gunakan');

        /*
        |--------------------------------------------------------------
        | DAFTAR DAN PEMBUATAN ASESMEN
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
        | Generate asesmen menggunakan Dify.
        | Tetap menggunakan method store() yang sudah ada.
        */

        Route::post(
            '/assessments',
            [AssessmentController::class, 'store']
        )->name('assessments.store');

        /*
        |--------------------------------------------------------------
        | DUPLIKASI PAKET ASESMEN
        |--------------------------------------------------------------
        |
        | Menyalin paket asesmen yang sudah tersedia
        | tanpa menjalankan Dify kembali.
        |
        | Paket salinan mempunyai assessment_id baru,
        | tetapi mempertahankan soal Reguler dan Adaptif
        | dari paket asli.
        |
        | Nama salinan dapat dikirim melalui form dengan
        | input bernama "nama".
        |
        | Status awal salinan: Draft.
        |
        */

        Route::post(
            '/assessments/{id}/duplicate',
            [AssessmentController::class, 'duplicate']
        )->whereNumber('id')
         ->name('assessments.duplicate');

        /*
        |--------------------------------------------------------------
        | EDIT JUDUL / NAMA PAKET ASESMEN
        |--------------------------------------------------------------
        |
        | Guru bisa mengganti judul paket salinan
        | tanpa mengubah isi soal yang sudah tersimpan.
        |
        | Form menggunakan method PATCH
        | dan field input bernama "nama".
        |
        */

        Route::patch(
            '/assessments/{id}/title',
            [AssessmentController::class, 'updateTitle']
        )->whereNumber('id')
         ->name('assessments.update-title');

        /*
        |--------------------------------------------------------------
        | EDIT SOAL REGULER DAN ADAPTIF
        |--------------------------------------------------------------
        */

        Route::get(
            '/assessments/{id}/edit',
            [AssessmentReviewController::class, 'edit']
        )->whereNumber('id')
         ->name('assessments.edit');

        Route::put(
            '/assessments/{id}',
            [AssessmentReviewController::class, 'update']
        )->whereNumber('id')
         ->name('assessments.update');

        /*
        |--------------------------------------------------------------
        | PUBLIKASIKAN PAKET ASESMEN KE KELAS
        |--------------------------------------------------------------
        |
        | Route ini berlaku untuk paket asli maupun salinan.
        |
        | Guru dapat memilih kelas tujuan, dan siswa kelas
        | tersebut dapat melihat asesmen setelah publikasi
        | berhasil dilakukan.
        |
        | Tidak perlu membuat route publikasi baru.
        |
        */

        Route::post(
            '/assessments/{id}/publish-to-class',
            [AssessmentReviewController::class, 'publishToClass']
        )->whereNumber('id')
         ->name('assessments.publish-to-class');

        /*
        |--------------------------------------------------------------
        | BERBAGI KE BANK SOAL PUBLIK
        |--------------------------------------------------------------
        */

        Route::post(
            '/assessments/{id}/share',
            [AssessmentController::class, 'shareToPublic']
        )->whereNumber('id')
         ->name('assessments.share');

        /*
        |--------------------------------------------------------------
        | DETAIL ASESMEN
        |--------------------------------------------------------------
        |
        | Ditempatkan setelah route khusus di atas.
        |
        */

        Route::get(
            '/assessments/{id}',
            [AssessmentController::class, 'show']
        )->whereNumber('id')
         ->name('assessments.show');

        /*
        |--------------------------------------------------------------
        | NOTIFIKASI GURU
        |--------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [NotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------
        | MANAJEMEN KELAS
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
|
| Seluruh alur siswa tetap dipertahankan.
|
| Paket asli dan paket hasil duplikasi nantinya
| akan mempunyai assessment_id masing-masing.
|
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        /*
        |--------------------------------------------------------------
        | DASHBOARD DAN DAFTAR ASESMEN SISWA
        |--------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/daftar-assessment',
            [DashboardController::class, 'assessments']
        )->name('assessment');

        /*
        |--------------------------------------------------------------
        | PETUNJUK DAN PEMILIHAN VERSI ASESMEN
        |--------------------------------------------------------------
        */

        Route::get(
            '/assessment/{id}/petunjuk',
            [\App\Http\Controllers\Siswa\AssessmentController::class, 'instructions']
        )->whereNumber('id')
         ->name('assessment.instructions');

        /*
        |--------------------------------------------------------------
        | MULAI PENGERJAAN
        |--------------------------------------------------------------
        */

        Route::get(
            '/assessment/{id}/mulai',
            [\App\Http\Controllers\Siswa\AssessmentController::class, 'start']
        )->whereNumber('id')
         ->name('assessment.start');

        /*
        |--------------------------------------------------------------
        | LANJUTKAN PENGERJAAN
        |--------------------------------------------------------------
        |
        | Mempertahankan URL dan nama route yang sudah
        | digunakan sebelumnya agar tidak mengubah
        | integrasi milik anggota tim.
        |
        */

        Route::get(
            '/assessment/{id}/kerjakan',
            [\App\Http\Controllers\Siswa\AssessmentController::class, 'work']
        )->whereNumber('id')
         ->name('assessment.work');

        /*
        |--------------------------------------------------------------
        | KUMPULKAN JAWABAN SISWA
        |--------------------------------------------------------------
        */

        Route::post(
            '/assessment/{id}/kumpulkan',
            [\App\Http\Controllers\Siswa\AssessmentController::class, 'submit']
        )->whereNumber('id')
         ->name('assessment.submit');

        /*
        |--------------------------------------------------------------
        | RIWAYAT DAN PROFIL SISWA
        |--------------------------------------------------------------
        */

        Route::get(
            '/riwayat',
            [StudentPageController::class, 'riwayat']
        )->name('riwayat');

        Route::get(
            '/profil',
            [StudentPageController::class, 'profil']
        )->name('profil');

        /*
        |--------------------------------------------------------------
        | NOTIFIKASI SISWA
        |--------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [SiswaNotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------
        | GABUNG KELAS
        |--------------------------------------------------------------
        */

        Route::get(
            '/kelas/{id}/join',
            [ClassroomController::class, 'joinViaLink']
        )->whereNumber('id')
         ->name('kelas.join');

    });
