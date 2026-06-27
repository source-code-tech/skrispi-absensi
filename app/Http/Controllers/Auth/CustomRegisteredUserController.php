<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class CustomRegisteredUserController extends Controller
{
    /**
     * Tampilkan view registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle proses registrasi akun baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class.',username'], // NIP atau Username
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'],
            'role' => ['required', 'string', Rule::in(['siswa', 'wali_kelas', 'orang_tua'])], 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, 
            'is_approved' => false,
        ]);

        // ✅ AUTO-CREATE PROFILE: Membuat data profil kosong agar user muncul di menu Admin
        if ($user->role === 'wali_kelas') {
            \App\Models\HomeroomTeacher::create([
                'nip' => $user->username,
                'user_username' => $user->username,
                'class_code' => null, // admin yang set
                'scan_token' => \Illuminate\Support\Str::random(32),
            ]);
        } elseif ($user->role === 'orang_tua') {
            \App\Models\ParentModel::create([
                'user_username' => $user->username,
                'name' => $user->name, 
            ]);
        }

        // 🛑 PENTING: Event Registered yang memicu pengiriman email DIHAPUS
        // event(new Registered($user)); // HAPUS BARIS INI!

        // ✅ REDIRECT FINAL: Langsung ke login dengan pesan sukses
        return redirect()->route('login')->with(
            'success', 
            'Akun Berhasil Dibuat! Tunggu Administrator menyetujui akun Anda untuk bisa Masuk.'
        );
    }
}