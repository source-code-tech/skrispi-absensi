@extends('layouts.guest') 

@section('title', 'Daftar - ' . ($globalSettings['school_name'] ?? 'Sistem Absensi'))

@php
    use Illuminate\Support\Facades\Storage;

    $settings = $globalSettings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;

    $defaultLogo = asset('images/default_logo.png');
    $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
@endphp

@section('content')

<style>
    .auth-page {
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
    .auth-page .font-display { font-family: 'Space Grotesk', sans-serif; }
    .auth-page .font-mono { font-family: 'IBM Plex Mono', monospace; }

    @keyframes blink-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }
    .auth-page .live-dot { animation: blink-slow 2.4s ease-in-out infinite; }

    .auth-page .input-line {
        background-color: var(--surface);
        border: 1px solid var(--line);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .auth-page .input-line:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(41, 84, 229, 0.10);
    }
    .auth-page .input-line.has-error {
        border-color: #DC2626;
    }

    .auth-page select.role-select {
        appearance: none;
        -webkit-appearance: none;
    }
</style>

<div class="auth-page flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-[440px]">

        {{-- Logo + Nama Sekolah --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="bg-white border border-[var(--line)] p-1.5 rounded-md">
                <img src="{{ $finalLogo }}" class="w-7 h-7 object-contain rounded-sm" alt="Logo">
            </div>
            <div class="text-left">
                <span class="block text-base font-display font-bold text-[var(--ink)] tracking-tight leading-none">{{ $schoolName }}</span>
                <span class="text-[9px] font-mono text-[var(--muted)] tracking-[0.18em] uppercase">Sistem Absensi Digital</span>
            </div>
        </div>

        {{-- Badge status --}}
        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center gap-2 border border-[var(--line)] bg-white px-3 py-1.5 rounded-md">
                <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)] live-dot"></span>
                <span class="font-mono text-[10px] tracking-[0.16em] uppercase text-[var(--muted)]">Pendaftaran Akun &middot; {{ $schoolName }}</span>
            </div>
        </div>

        {{-- CARD REGISTRASI --}}
        <div class="bg-white border border-[var(--line)] rounded-xl shadow-[0_1px_2px_rgba(20,23,28,0.04)] p-7 sm:p-9">

            <div class="mb-6 text-center">
                <h1 class="font-display text-2xl font-bold text-[var(--ink)] tracking-tight">Buat Akun Baru</h1>
                <p class="text-sm text-[var(--muted)] mt-1.5">Lengkapi formulir di bawah ini dengan benar</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 px-4 py-3 rounded-md text-red-700 text-sm flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5"></i> <span>Mohon periksa kembali inputan Anda.</span>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5" id="registerForm">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-[var(--ink)] mb-2">Nama Lengkap</label>
                    <div class="input-line rounded-md flex items-center px-4 {{ $errors->has('name') ? 'has-error' : '' }}">
                        <i class="fas fa-user text-[var(--muted)] text-sm mr-3"></i>
                        <input id="name" name="name" type="text" autocomplete="name" required autofocus
                            class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                            placeholder="Contoh: Budi Santoso" value="{{ old('name') }}">
                    </div>
                    @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-[var(--ink)] mb-2">Alamat Email</label>
                    <div class="input-line rounded-md flex items-center px-4 {{ $errors->has('email') ? 'has-error' : '' }}">
                        <i class="fas fa-envelope text-[var(--muted)] text-sm mr-3"></i>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                    @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Mendaftar Sebagai --}}
                <div>
                    <label for="role" class="block text-xs font-bold text-[var(--ink)] mb-2">Mendaftar Sebagai</label>
                    <div class="input-line rounded-md flex items-center px-4 relative {{ $errors->has('role') ? 'has-error' : '' }}">
                        <i class="fas fa-id-badge text-[var(--muted)] text-sm mr-3"></i>
                        <select id="role" name="role" required
                            class="role-select w-full bg-transparent py-3 text-sm text-[var(--ink)] focus:outline-none cursor-pointer">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih Peran...</option>
                            <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                            <option value="orang_tua" {{ old('role') == 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                        </select>
                        <i class="fas fa-chevron-down text-[var(--muted)] text-xs"></i>
                    </div>
                    @error('role') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Password & Konfirmasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-[var(--ink)] mb-2">Kata Sandi</label>
                        <div class="input-line rounded-md flex items-center px-4 {{ $errors->has('password') ? 'has-error' : '' }}">
                            <i class="fas fa-lock text-[var(--muted)] text-sm mr-3"></i>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                                placeholder="••••••••">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[var(--ink)] mb-2">Ulangi Sandi</label>
                        <div class="input-line rounded-md flex items-center px-4">
                            <i class="fas fa-lock text-[var(--muted)] text-sm mr-3"></i>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="w-full bg-transparent py-3 text-sm text-[var(--ink)] placeholder-[var(--muted)]/60 focus:outline-none"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>
                @error('password') <p class="-mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                {{-- Tombol Daftar --}}
                <button type="submit" id="registerBtn"
                    class="w-full bg-[var(--accent)] hover:bg-[var(--accent-dark)] text-white font-bold py-3.5 rounded-md transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>

                <p class="text-center text-sm text-[var(--muted)]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-bold text-[var(--ink)] hover:text-[var(--accent)]">Masuk di sini</a>
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
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('registerBtn');
        if (this.checkValidity()) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    });
</script>
@endsection