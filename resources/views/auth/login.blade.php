@extends('layouts.guest') 

@section('title', 'Login - ' . ($globalSettings['school_name'] ?? 'Sistem Absensi'))

@php
    use Illuminate\Support\Facades\Storage;

    $settings = $globalSettings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;

    $defaultLogo = asset('images/default_logo.png');
    $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
@endphp

@section('content')

{{-- ===================================================== --}}
{{-- VARIABEL WARNA & FONT — konsisten dengan landing page  --}}
{{-- ===================================================== --}}
<style>
    .login-page {
        --bg: #FAFAF7;
        --surface: #FFFFFF;
        --ink: #14171C;
        --muted: #696E76;
        --accent: #2954E5;
        --accent-dark: #1B3AA8;
        --line: #E6E4DB;

        font-family: 'Inter', sans-serif;
        background-color: var(--bg);
        color: var(--ink);
        min-height: 100vh;
    }
    .login-page .font-display { font-family: 'Space Grotesk', sans-serif; }
    .login-page .font-mono { font-family: 'IBM Plex Mono', monospace; }

    @keyframes blink-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }
    .login-page .live-dot { animation: blink-slow 2.4s ease-in-out infinite; }

    .login-page .input-line {
        background-color: var(--surface);
        border: 1px solid var(--line);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .login-page .input-line:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(41, 84, 229, 0.10);
    }

    .login-page .role-tab-btn {
        font-family: 'Inter', sans-serif;
        transition: all .15s ease;
    }
</style>

<div class="login-page flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-[420px]">

        {{-- Logo + Nama Sekolah (kecil, di atas card, sama gaya seperti navbar landing) --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="bg-white border border-[var(--line)] p-1.5 rounded-md">
                <img src="{{ $finalLogo }}" class="w-7 h-7 object-contain rounded-sm" alt="Logo">
            </div>
            <div class="text-left">
                <span class="block text-base font-display font-bold text-[var(--ink)] tracking-tight leading-none">{{ $schoolName }}</span>
                <span class="text-[9px] font-mono text-[var(--muted)] tracking-[0.18em] uppercase">Sistem Absensi Digital</span>
            </div>
        </div>

        {{-- Badge status, konsisten dengan hero landing page --}}
        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center gap-2 border border-[var(--line)] bg-white px-3 py-1.5 rounded-md">
                <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)] live-dot"></span>
                <span class="font-mono text-[10px] tracking-[0.16em] uppercase text-[var(--muted)]">Portal Masuk &middot; {{ $schoolName }}</span>
            </div>
        </div>

        {{-- CARD LOGIN --}}
        <div class="bg-white border border-[var(--line)] rounded-xl shadow-[0_1px_2px_rgba(20,23,28,0.04)] p-7 sm:p-9">

            <div class="mb-6 text-center">
                <h1 class="font-display text-2xl font-bold text-[var(--ink)] tracking-tight">Masuk ke Akun</h1>
                <p class="text-sm text-[var(--muted)] mt-1.5">Pilih peran Anda dan masuk dengan akun terdaftar</p>
            </div>

            {{-- TAB PEMILIHAN ROLE --}}
            <div class="mb-6 grid grid-cols-3 gap-1.5 bg-[var(--bg)] p-1.5 rounded-lg border border-[var(--line)]" role="tablist">
                <button type="button"
                    id="tab-super_admin"
                    onclick="switchLoginTab('super_admin')"
                    role="tab"
                    aria-selected="true"
                    class="role-tab-btn py-2.5 px-2 rounded-md text-xs font-bold bg-[var(--accent)] text-white">
                    Admin
                </button>
                <button type="button"
                    id="tab-wali_kelas"
                    onclick="switchLoginTab('wali_kelas')"
                    role="tab"
                    aria-selected="false"
                    class="role-tab-btn py-2.5 px-2 rounded-md text-xs font-bold text-[var(--muted)] hover:text-[var(--ink)]">
                    Wali Kelas
                </button>
                <button type="button"
                    id="tab-orang_tua"
                    onclick="switchLoginTab('orang_tua')"
                    role="tab"
                    aria-selected="false"
                    class="role-tab-btn py-2.5 px-2 rounded-md text-xs font-bold text-[var(--muted)] hover:text-[var(--ink)]">
                    Orang Tua
                </button>
            </div>

            {{-- Alerts --}}
            @if (session('status') || session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-md text-emerald-700 text-sm flex items-start gap-2">
                    <i class="fas fa-check-circle mt-0.5"></i> <span>{{ session('status') ?? session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 px-4 py-3 rounded-md text-red-700 text-sm flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5"></i> <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- FORM LOGIN --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf

                <input type="hidden" name="role" id="role-input" value="{{ old('role', 'super_admin') }}">

                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-[var(--muted)] -mb-1">
                    Login sebagai <span class="font-bold text-[var(--accent)]" id="role-helper-label">Admin</span>
                </p>

                {{-- Email --}}
                <div class="w-full">
                    <label for="email" class="block text-xs font-bold text-[var(--ink)] mb-2">Alamat Email</label>
                    <div class="input-line rounded-md flex items-center px-4">
                        <i class="fas fa-envelope text-[var(--muted)] text-sm mr-3"></i>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                </div>

                {{-- Password --}}
                <div class="w-full">
                    <label for="password" class="block text-xs font-bold text-[var(--ink)] mb-2">Kata Sandi</label>
                    <div class="input-line rounded-md flex items-center px-4">
                        <i class="fas fa-lock text-[var(--muted)] text-sm mr-3"></i>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                            placeholder="••••••••">
                    </div>
                </div>

                {{-- Ingat saya & Lupa Password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember-me" name="remember" type="checkbox" class="w-4 h-4 rounded border-[var(--line)] text-[var(--accent)] focus:ring-[var(--accent)]">
                        <span class="text-sm text-[var(--muted)]">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)]">Lupa password?</a>
                    @endif
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" id="loginBtn"
                    class="w-full bg-[var(--accent)] hover:bg-[var(--accent-dark)] text-white font-bold py-3.5 rounded-md transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk ke Sistem
                </button>

                <p class="text-center text-sm text-[var(--muted)]">
                    Belum memiliki akun?
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="font-bold text-[var(--ink)] hover:text-[var(--accent)]">Daftar sekarang</a>
                    @endif
                </p>
            </form>
        </div>

        {{-- Kembali ke beranda --}}
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-[0.14em] text-[var(--muted)] hover:text-[var(--ink)] transition-colors">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const roleLabels = {
        'super_admin': 'Admin',
        'wali_kelas': 'Wali Kelas',
        'orang_tua': 'Orang Tua',
    };

    function switchLoginTab(role) {
        document.querySelectorAll('.role-tab-btn').forEach(function (btn) {
            btn.classList.remove('bg-[var(--accent)]', 'text-white');
            btn.classList.add('text-[var(--muted)]');
        });

        const activeBtn = document.getElementById('tab-' + role);
        if (activeBtn) {
            activeBtn.classList.add('bg-[var(--accent)]', 'text-white');
            activeBtn.classList.remove('text-[var(--muted)]');
            document.querySelectorAll('.role-tab-btn').forEach(b => b.setAttribute('aria-selected', 'false'));
            activeBtn.setAttribute('aria-selected', 'true');
        }

        const roleInput = document.getElementById('role-input');
        if (roleInput) roleInput.value = role;

        const helperLabel = document.getElementById('role-helper-label');
        if (helperLabel) helperLabel.textContent = roleLabels[role] || role;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const oldRole = document.getElementById('role-input').value || 'super_admin';
        switchLoginTab(oldRole);
    });

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });
</script>
@endsection