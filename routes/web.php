<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CounselorConsultationController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\MoodCheckinController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| Hanya dapat diakses pengguna yang belum login.
*/

Route::middleware('guest')->group(function () {

    // Registrasi.
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    // Login.
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Hanya dapat diakses pengguna yang sudah login.
*/

Route::middleware('auth')->group(function () {

    // Dashboard Pencerita.
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    // Logout.
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Journaling
    Route::get('/journals', [JournalController::class, 'index'])
        ->name('journals.index');

    Route::get('/journals/create', [JournalController::class, 'create'])
        ->name('journals.create');

    Route::post('/journals', [JournalController::class, 'store'])
        ->name('journals.store');

    Route::get('/journals/{journal}', [JournalController::class, 'show'])
        ->whereNumber('journal')
        ->name('journals.show');

    Route::get('/journals/{journal}/edit', [JournalController::class, 'edit'])
        ->whereNumber('journal')
        ->name('journals.edit');

    Route::put('/journals/{journal}', [JournalController::class, 'update'])
        ->whereNumber('journal')
        ->name('journals.update');

    Route::delete('/journals/{journal}', [JournalController::class, 'destroy'])
        ->whereNumber('journal')
        ->name('journals.destroy');

    // Mood Check-in
    Route::post('/mood-checkins', [MoodCheckinController::class, 'store'])
        ->name('mood-checkins.store');

    // Direktori Konselor
    Route::get('/counselors', [CounselorController::class, 'index'])
        ->name('counselors.index');

    // Detail Profil Konselor
    Route::get('/counselors/{counselor}', [CounselorController::class, 'show'])
        ->whereNumber('counselor')
        ->name('counselors.show');


    // Pengajuan Konsultasi
    Route::get(
        '/counselors/{counselor}/consultations/create',
        [ConsultationController::class, 'create']
    )
        ->whereNumber('counselor')
        ->name('consultations.create');

    Route::post(
        '/counselors/{counselor}/consultations',
        [ConsultationController::class, 'store']
    )
        ->whereNumber('counselor')
        ->name('consultations.store');

    // Riwayat Konsultasi Pencerita
    Route::get('/consultations', [ConsultationController::class, 'index'])
        ->name('consultations.index');

    // Detail Konsultasi
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])
        ->whereNumber('consultation')
        ->name('consultations.show');

    /*
    |--------------------------------------------------------------------------
    | Counselor Routes
    |--------------------------------------------------------------------------
    | Halaman dan tindakan khusus Konselor Sebaya.
    */

    Route::prefix('counselor')
        ->name('counselor.')
        ->group(function () {

            // Dashboard / Inbox Konselor.
            Route::get('/consultations', [
                CounselorConsultationController::class,
                'index'
            ])->name('consultations.index');

            // Detail permintaan konsultasi.
            Route::get('/consultations/{consultation}', [
                CounselorConsultationController::class,
                'show'
            ])
                ->whereNumber('consultation')
                ->name('consultations.show');
            // Mencoba ulang pembuatan ringkasan Hermes.
            Route::patch('/consultations/{consultation}/brief/retry', [
                CounselorConsultationController::class,
                'retryBrief'
            ])
                ->whereNumber('consultation')
                ->name('consultations.brief.retry');

            // Terima permintaan.
            Route::patch('/consultations/{consultation}/accept', [
                CounselorConsultationController::class,
                'accept'
            ])
                ->whereNumber('consultation')
                ->name('consultations.accept');

            // Tolak permintaan.
            Route::patch('/consultations/{consultation}/reject', [
                CounselorConsultationController::class,
                'reject'
            ])
                ->whereNumber('consultation')
                ->name('consultations.reject');

            // Menetapkan atau memperbarui jadwal final.
            Route::patch('/consultations/{consultation}/schedule', [
                CounselorConsultationController::class,
                'schedule'
            ])
                ->whereNumber('consultation')
                ->name('consultations.schedule');

            // Menandai konsultasi selesai.
            Route::patch('/consultations/{consultation}/complete', [
                CounselorConsultationController::class,
                'complete'
            ])
                ->whereNumber('consultation')
                ->name('consultations.complete');
        });

});