<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// --- Imports Controller Kustom ---
use App\Http\Controllers\Auth\CustomRegisteredUserController; 
use App\Http\Controllers\Auth\VerificationController; // ✅ Controller Verifikasi Kustom
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
// --- Imports Controller Kustom ---


// =======================================================
// RUTE AKSES TAMU (GUEST)
// =======================================================
Route::middleware('guest')->group(function () {
    
    // ✅ 1. REGISTRASI KUSTOM (MENGGANTI Rute Bawaan)
    Route::get('register', [CustomRegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [CustomRegisteredUserController::class, 'store']);


    // 2. LOGIN
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // 3. LUPA PASSWORD
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// =======================================================
// RUTE AKSES TEROTENTIKASI (AUTH)
// =======================================================
Route::middleware('auth')->group(function () {
    
    // ✅ 4. VERIFIKASI EMAIL KUSTOM (MENGGANTI Rute Bawaan)
    Route::get('verify-email', [VerificationController::class, 'show']) // Menampilkan notice
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify']) // Menangani klik link
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [VerificationController::class, 'resend']) // Mengirim ulang email
        ->middleware('throttle:6,1')
        ->name('verification.send');


    // 5. PASSWORD LAINNYA
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // 6. LOGOUT
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});