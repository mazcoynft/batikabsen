<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\IzinController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\DokumenController;

// CSRF Token refresh route for WebView apps
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');

// Frontend routes
Route::get('/', [FrontendController::class, 'showLogin'])->name('frontend.login');
Route::post('/login', [FrontendController::class, 'login'])->name('frontend.login.post');
Route::get('/login', [FrontendController::class, 'showLogin'])->name('login');

Route::middleware('auth:frontend')->group(function () {
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('frontend.dashboard');
    Route::get('/history', [FrontendController::class, 'history'])->name('frontend.history');
    Route::get('/profile', [FrontendController::class, 'profile'])->name('frontend.profile');
    Route::post('/profile/avatar', [FrontendController::class, 'updateAvatar'])->name('frontend.profile.avatar.update');
    Route::post('/logout', [FrontendController::class, 'logout'])->name('frontend.logout');

    // Absen
    Route::get('/absen', [FrontendController::class, 'absen'])->name('frontend.absen');
    Route::post('/absen/store', [FrontendController::class, 'storeAbsen'])
        ->middleware('webview.csrf')
        ->name('frontend.absen.store');
    Route::get('/get-jam-kerja', [FrontendController::class, 'getJamKerja'])->name('frontend.get-jam-kerja');
    Route::get('/check-absen-status', [FrontendController::class, 'checkAbsenStatus'])->name('frontend.check-absen-status');

    // Izin Page
    Route::get('/izin', [IzinController::class, 'index'])->name('frontend.izin.index');
    Route::post('/izin/store', [IzinController::class, 'store'])->name('frontend.izin.store');
    Route::post('/izin/{id}/cancel', [IzinController::class, 'cancel'])->name('frontend.izin.cancel');
    Route::get('/izin/get-cuti-details/{id}', [IzinController::class, 'getCutiDetails'])->name('frontend.izin.getCutiDetails');
    
    // Lembur Page
    Route::get('/lembur', [LemburController::class, 'index'])->name('frontend.lembur.index');
    Route::post('/lembur/store', [LemburController::class, 'store'])->name('frontend.lembur.store');
    
    // Piket Page
    Route::get('/piket', [PiketController::class, 'index'])->name('frontend.piket.index');
    Route::post('/piket/store', [PiketController::class, 'store'])->name('frontend.piket.store');
    
    // Dokumen Page (Slip Gaji & Dokumen)
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('frontend.dokumen.index');
    Route::post('/dokumen/{id}/mark-read', [DokumenController::class, 'markAsRead'])->name('frontend.dokumen.markRead');
});

// require __DIR__ . '/auth.php';
