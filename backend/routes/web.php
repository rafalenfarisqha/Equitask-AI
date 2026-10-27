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
use App\Http\Controllers\AssessmentAnalyticsController;

use App\Http\Controllers\Guru\StudentAssessmentTrackController;
use App\Http\Controllers\Guru\EssayReviewController;

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\StudentPageController;
use App\Http\Controllers\Siswa\AssessmentController as SiswaAssessmentController;
use App\Http\Controllers\Siswa\NotificationController as SiswaNotificationController;

/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
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
| ROUTE GURU
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

        Route::get(
            '/bank-soal',
            [BankSoalController::class, 'index']
        )->name('bank-soal');

        Route::post(
            '/bank-soal/{id}/gunakan',
            [BankSoalController::class, 'gunakanSoal']
        )
            ->whereNumber('id')
            ->name('bank-soal.gunakan');

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
        */

        Route::post(
            '/assessments',
            [AssessmentController::class, 'store']
        )->name('assessments.store');

        /*
        |--------------------------------------------------------------------------
        | DUPLIKAT PAKET ASESMEN
        |--------------------------------------------------------------------------
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
        | ANALITIK AI GURU PER ASESMEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessments/{assessmentId}/analitik-ai',
            [AssessmentAnalyticsController::class, 'guru']
        )
            ->whereNumber('assessmentId')
            ->name('assessments.analitik-ai');

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

        /*
        |--------------------------------------------------------------------------
        | PENANDAAN SISWA ADAPTIF OLEH GURU
        |--------------------------------------------------------------------------
        |
        | Semua siswa default Reguler.
        | Guru hanya mencentang siswa Adaptif.
        |
        | Data disimpan pada:
        | class_members.assessment_track
        |
        */

        // Tugaskan asesmen Published yang sudah ada ke kelas ini.
        Route::post(
            '/kelas/{id}/assign-assessment',
            [ClassroomController::class, 'assignAssessment']
        )
            ->whereNumber('id')
            ->name('kelas.assign-assessment');

        Route::patch(
            '/kelas/{classId}/anggota/{studentId}/assessment-track',
            [StudentAssessmentTrackController::class, 'update']
        )
            ->whereNumber('classId')
            ->whereNumber('studentId')
            ->name('kelas.anggota.assessment-track');

        /*
        |--------------------------------------------------------------------------
        | ESSAY REVIEW GURU
        |--------------------------------------------------------------------------
        |
        | Guru melihat jawaban esai dan mengoreksi skor.
        |
        */

        Route::get(
            '/essay-review/{attemptId}',
            [EssayReviewController::class, 'show']
        )
            ->whereNumber('attemptId')
            ->name('essay-review.show');

        Route::put(
            '/essay-review/{attemptId}/{questionId}',
            [EssayReviewController::class, 'update']
        )
            ->whereNumber('attemptId')
            ->whereNumber('questionId')
            ->name('essay-review.update');
    });

/*
|--------------------------------------------------------------------------
| ROUTE SISWA
|--------------------------------------------------------------------------
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
        |
        | Hanya asesmen yang ditugaskan ke kelas siswa,
        | sesuai filter DashboardController.
        |
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
        | Profil Reguler/Adaptif ditentukan oleh guru.
        | Siswa tidak memilih versi soal.
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
        |
        | Controller memilih versi soal secara otomatis
        | berdasarkan class_members.assessment_track.
        |
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
        | Controller menyimpan jawaban.
        | Essay Grading Dify ditangani oleh service.
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
        | HASIL ASESMEN SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assessment/pengerjaan/{attemptId}/hasil',
            [SiswaAssessmentController::class, 'result']
        )
            ->whereNumber('attemptId')
            ->name('assessment.result');

        /*
        |--------------------------------------------------------------------------
        | ANALITIK AI HASIL SISWA
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/hasil/{id}/analitik-ai',
            [AssessmentAnalyticsController::class, 'siswa']
        )
            ->whereNumber('id')
            ->name('hasil.analitik-ai');

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
        | GABUNG KELAS MELALUI LINK
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kelas/{id}/join',
            [ClassroomController::class, 'joinViaLink']
        )
            ->whereNumber('id')
            ->name('kelas.join');
    });
