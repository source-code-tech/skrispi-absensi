<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response // Menerima string $roles
    {
        // 1. Cek Otentikasi (Auth::check())
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. LOGIKA APPROVAL: Blok user jika belum disetujui (kecuali super_admin)
        if ($user->role !== 'super_admin' && !$user->is_approved) {
            
            // Logout user, invalidate session, dan regenerate token
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Redirect ke halaman login dengan pesan error spesifik
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda belum disetujui oleh Administrator. Mohon tunggu persetujuan.'
            ]);
        }
        
        // 3. LOGIKA ROLE CHECK (Mendukung multiple roles: 'admin|wali_kelas')
        $requiredRoles = explode('|', $roles); // Memecah string 'admin|editor' menjadi array

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        if (!in_array($user->role, $requiredRoles)) {
            // Jika role tidak sesuai, kirim forbidden (403)
            return abort(403, 'Akses ditolak. Peran Anda tidak memiliki izin untuk mengakses halaman ini.'); 
        }

        // Jika semua cek lolos, lanjutkan request
        return $next($request);
    }
}