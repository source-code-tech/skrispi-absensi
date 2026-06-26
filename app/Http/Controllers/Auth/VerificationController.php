<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
// Hapus: use Illuminate\Foundation\Auth\VerifiesEmails; 
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified; // Digunakan saat verifikasi sukses
use Illuminate\Routing\Controller as BaseController; // Digunakan di VerificationController bawaan

// Kita akan mengimplementasikan logic secara manual, meniru trait VerifiesEmails

class VerificationController extends Controller
{
    // Menggunakan base class Controller
    
    /**
     * Tampilkan form verifikasi email (verification.notice).
     */
    public function show(Request $request)
    {
        // Panggil view default yang menampilkan tautan verifikasi
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('auth.verify-email'); // Sesuaikan nama view jika berbeda
    }

    /**
     * Tangani permintaan untuk mengirim ulang email verifikasi.
     * Route: verification.send
     */
    public function resend(Request $request): RedirectResponse
    {
        // Pastikan pengguna memiliki contract MustVerifyEmail
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        // Kirim ulang notifikasi
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Tautan verifikasi baru telah dikirim ke alamat email Anda.');
    }

    /**
     * Tangani permintaan verifikasi (verification.verify).
     */
    public function verify(Request $request)
    {
        // Cek apakah user sudah login dan memverifikasi email
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                        ? new Response('', 204)
                        : redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        
        // --- LOGIKA KUSTOM ANDA DIJALANKAN DI SINI ---
        
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Jika user belum disetujui Admin
        if (!$user->is_approved) {
            Auth::logout(); // Logout user
            session()->flash('status', 'Email Anda berhasil diverifikasi! Silakan masuk kembali dan tunggu persetujuan Administrator.');
            return redirect(route('login'));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }
    
    /**
     * Tentukan ke mana harus dialihkan setelah verifikasi email.
     */
    protected function redirectPath()
    {
        if (Auth::check() && Auth::user()->is_approved) {
             return '/dashboard'; // Ganti dengan path dashboard default Anda yang benar
        }
        return route('login');
    }

    
}