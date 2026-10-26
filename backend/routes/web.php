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
use App\Http\Controllers\Siswa\AssessmentController as SiswaAssessmentController;
use App\Http\Controllers\Siswa\NotificationController as SiswaNotificationController;
use App\Http\Controllers\AssessmentPdfController;

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
        |--------------------------------------------------------------------------
        | PROFIL GURU
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | DASHBOARD GURU
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AssessmentController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | ANALYTICS GURU
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/analytics',
            [GuruAnalyticsController::class, 'index']
        )->name('analytics.index');

        /*
        |--------------------------------------------------------------------------
        | BANK SOAL PUBLIK
        |--------------------------------------------------------------------------
        */

        // Daftar Bank Soal Komunitas
        Route::get(
            '/bank-soal',
            [BankSoalController::class, 'index']
        )->name('bank-soal');

        // Detail soal publik (hanya lihat)
        Route::get(
            '/bank-soal/{id}',
            [BankSoalController::class, 'detail']
        )
            ->whereNumber('id')
            ->name('bank-soal.detail');

        // Download PDF dari Bank Soal publik
        Route::get(
            '/bank-soal/{id}/download-pdf',
            [AssessmentPdfController::class, 'downloadPublic']
        )
            ->whereNumber('id')
            ->name('bank-soal.download-pdf');

        /*
        |--------------------------------------------------------------------------
        | DAFTAR ASESMEN GURU
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments',
            [AssessmentController::class, 'index']
        )->name('assessments.index');

        /*
        |--------------------------------------------------------------------------
        | BUAT ASESMEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments/create',
            [AssessmentController::class, 'create']
        )->name('assessments.create');

        /*
        |--------------------------------------------------------------------------
        | GENERATE ASESMEN DIFY
        |--------------------------------------------------------------------------
        |
        | Alur Generate Dify tetap menggunakan store().
        | Fitur duplikat tidak memanggil Dify.
        |
        */

        Route::post(
            '/assessments',
            [AssessmentController::class, 'store']
        )->name('assessments.store');

        /*
        |--------------------------------------------------------------------------
        | DUPLIKAT PAKET ASESMEN
        |--------------------------------------------------------------------------
        |
        | Menyalin paket Reguler dan Adaptif yang sudah
        | tersimpan tanpa menjalankan Generate Dify ulang.
        |
        | Paket hasil duplikasi memiliki ID baru.
        | Nama salinan dapat ditentukan oleh guru.
        | Status awal paket salinan adalah Draft.
        |
        */

        Route::post(
            '/assessments/{id}/duplicate',
            [AssessmentController::class, 'duplicate']
        )
            ->whereNumber('id')
            ->name('assessments.duplicate');

        /*
        |--------------------------------------------------------------------------
        | EDIT JUDUL ASESMEN
        |--------------------------------------------------------------------------
        |
        | Mengubah nama paket tanpa mengubah isi soal,
        | scaffolding, gambar, maupun hasil Dify.
        |
        */

        Route::patch(
            '/assessments/{id}/title',
            [AssessmentController::class, 'updateTitle']
        )
            ->whereNumber('id')
            ->name('assessments.update-title');

        /*
        |--------------------------------------------------------------------------
        | EDIT SOAL REGULER DAN ADAPTIF
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments/{id}/edit',
            [AssessmentReviewController::class, 'edit']
        )
            ->whereNumber('id')
            ->name('assessments.edit');

        Route::put(
            '/assessments/{id}',
            [AssessmentReviewController::class, 'update']
        )
            ->whereNumber('id')
            ->name('assessments.update');

        /*
        |--------------------------------------------------------------------------
        | PUBLIKASI ASESMEN KE KELAS
        |--------------------------------------------------------------------------
        |
        | Berlaku untuk paket asli dan paket hasil duplikasi.
        | Siswa dapat mengakses paket setelah dipublikasikan
        | dan ditugaskan ke kelas yang sesuai.
        |
        */

        Route::post(
            '/assessments/{id}/publish-to-class',
            [AssessmentReviewController::class, 'publishToClass']
        )
            ->whereNumber('id')
            ->name('assessments.publish-to-class');

        /*
        |--------------------------------------------------------------------------
        | BAGIKAN KE BANK SOAL PUBLIK
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/assessments/{id}/share',
            [AssessmentController::class, 'shareToPublic']
        )
            ->whereNumber('id')
            ->name('assessments.share');

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD PDF ASESMEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments/{assessment}/download-pdf',
            [AssessmentPdfController::class, 'download']
        )
            ->whereNumber('assessment')
            ->name('assessments.download-pdf');

        /*
        |--------------------------------------------------------------------------
        | DETAIL ASESMEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments/{id}',
            [AssessmentController::class, 'show']
        )
            ->whereNumber('id')
            ->name('assessments.show');

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI GURU
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [NotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------------------
        | MANAJEMEN KELAS
        |--------------------------------------------------------------------------
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
        )
            ->whereNumber('id')
            ->name('kelas.show');

        Route::post(
            '/kelas/{id}/tambah-siswa',
            [ClassroomController::class, 'addStudent']
        )
            ->whereNumber('id')
            ->name('kelas.addStudent');

        Route::post(
            '/kelas/{id}/import-excel',
            [ClassroomController::class, 'importExcel']
        )
            ->whereNumber('id')
            ->name('kelas.importExcel');
    });

/*
|--------------------------------------------------------------------------
| RUTE SISWA
|--------------------------------------------------------------------------
|
| Fitur lama tetap dipertahankan.
| Fitur baru: melihat kembali hasil asesmen yang
| sudah dikumpulkan tanpa mengerjakan ulang.
|
*/

Route::middleware(['auth'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | DAFTAR ASESMEN SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/daftar-assessment',
            [DashboardController::class, 'assessments']
        )->name('assessment');

        /*
        |--------------------------------------------------------------------------
        | PETUNJUK ASESMEN
        |--------------------------------------------------------------------------
        |
        | Siswa memilih versi Reguler atau Adaptif.
        |
        */

        Route::get(
            '/assessment/{id}/petunjuk',
            [SiswaAssessmentController::class, 'instructions']
        )
            ->whereNumber('id')
            ->name('assessment.instructions');

        /*
        |--------------------------------------------------------------------------
        | MULAI ASESMEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessment/{id}/mulai',
            [SiswaAssessmentController::class, 'start']
        )
            ->whereNumber('id')
            ->name('assessment.start');

        /*
        |--------------------------------------------------------------------------
        | LANJUTKAN PENGERJAAN
        |--------------------------------------------------------------------------
        |
        | Mempertahankan URL lama supaya tidak mengganggu
        | fitur yang sudah digunakan oleh anggota tim.
        |
        */

        Route::get(
            '/assessment/{id}/kerjakan',
            [SiswaAssessmentController::class, 'work']
        )
            ->whereNumber('id')
            ->name('assessment.work');

        /*
        |--------------------------------------------------------------------------
        | KUMPULKAN JAWABAN
        |--------------------------------------------------------------------------
        |
        | Backend menghitung jawaban pilihan ganda,
        | menyimpan esai, dan menyiapkan pesan motivasi.
        |
        */

        Route::post(
            '/assessment/{id}/kumpulkan',
            [SiswaAssessmentController::class, 'submit']
        )
            ->whereNumber('id')
            ->name('assessment.submit');

        /*
        |--------------------------------------------------------------------------
        | LIHAT HASIL ASESMEN SISWA
        |--------------------------------------------------------------------------
        |
        | ROUTE BARU.
        |
        | Membaca hasil pengerjaan yang sudah tersimpan.
        | Tidak membuat attempt baru.
        | Tidak mengubah jawaban siswa.
        |
        | Hanya pemilik attempt yang boleh melihat hasil.
        | Validasi pemilik dilakukan di method result().
        |
        | Contoh URL:
        | /siswa/assessment/pengerjaan/6/hasil
        |
        */

        Route::get(
            '/assessment/pengerjaan/{attemptId}/hasil',
            [SiswaAssessmentController::class, 'result']
        )
            ->whereNumber('attemptId')
            ->name('assessment.result');

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/riwayat',
            [StudentPageController::class, 'riwayat']
        )->name('riwayat');

        /*
        |--------------------------------------------------------------------------
        | PROFIL SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profil',
            [StudentPageController::class, 'profil']
        )->name('profil');

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [SiswaNotificationController::class, 'index']
        )->name('notifikasi');

        /*
        |--------------------------------------------------------------------------
        | GABUNG KELAS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kelas/{id}/join',
            [ClassroomController::class, 'joinViaLink']
        )
            ->whereNumber('id')
            ->name('kelas.join');
    });
