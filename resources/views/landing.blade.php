<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- LOGIKA PENGAMBILAN SETTINGS --}}
    @php
        use Illuminate\Support\Facades\Storage;

        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
        $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
        $schoolLogoPath = $settings['school_logo'] ?? null;

        $defaultLogo = asset('images/default_logo.png');
        $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
    @endphp

    <title>{{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $finalLogo }}">

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Styles & Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --bg: #FAFAF7;
            --surface: #FFFFFF;
            --ink: #14171C;
            --muted: #696E76;
            --accent: #2954E5;
            --accent-dark: #1B3AA8;
            --amber: #C7861A;
            --line: #E6E4DB;
        }

        body{
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--ink);
        }
        .font-display{ font-family: 'Space Grotesk', sans-serif; }
        .font-mono{ font-family: 'IBM Plex Mono', monospace; }

        .scrolled-nav{
            background-color: rgba(250, 250, 247, 0.92);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid var(--line);
        }

        @keyframes blink-slow{
            0%, 100% { opacity: 1; }
            50% { opacity: .35; }
        }
        .live-dot{ animation: blink-slow 2.4s ease-in-out infinite; }

        @keyframes rise-in{
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .log-row{ animation: rise-in .5s ease-out both; }
        .log-row:nth-child(1){ animation-delay: .05s; }
        .log-row:nth-child(2){ animation-delay: .15s; }
        .log-row:nth-child(3){ animation-delay: .25s; }
        .log-row:nth-child(4){ animation-delay: .35s; }

        .underline-active{ box-shadow: inset 0 -2px 0 0 var(--accent); }
    </style>
</head>

<body class="antialiased text-[var(--ink)]"
     x-data="{ scrolled: false, activeSection: 'home' }" @scroll.window="scrolled = (window.pageYOffset > 20)">

{{-- LOADER --}}
@include('layouts.partials.loader')

{{-- NAVBAR --}}
<nav x-data="{ mobileMenuOpen: false }"
     :class="{ 'scrolled-nav py-3': scrolled, 'py-5 bg-[var(--bg)]': !scrolled, 'bg-white shadow-sm': mobileMenuOpen }"
     class="fixed top-0 w-full z-50 transition-all duration-200"
     x-init="
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) activeSection = entry.target.id;
            });
        }, { threshold: 0.5 });
        document.querySelectorAll('section[id]').forEach(section => observer.observe(section));
     ">
    <div class="container mx-auto px-6 lg:px-12">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <a href="#" @click="activeSection = 'home'" class="flex items-center gap-3 group z-50">
                <div class="bg-white border border-[var(--line)] p-1.5 rounded-md">
                    <img src="{{ $finalLogo }}" class="w-7 h-7 object-contain rounded-sm">
                </div>
                <div>
                    <span class="block text-lg font-display font-bold text-[var(--ink)] tracking-tight leading-none">{{ $schoolName }}</span>
                    <span class="text-[9px] font-mono text-[var(--muted)] tracking-[0.18em] uppercase">Sistem Absensi Digital</span>
                </div>
            </a>

            {{-- Tombol Hamburger untuk HP --}}
            <div class="md:hidden flex items-center z-50">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-[var(--ink)] p-2">
                    <i class="fas text-xl" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>

            {{-- Desktop Menu --}}
            <div class="ml-auto hidden md:flex items-center space-x-1">
                <a href="#" @click="activeSection = 'home'"
                   :class="activeSection === 'home' ? 'text-[var(--ink)] underline-active' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                   class="px-4 py-2 text-sm font-semibold transition-all duration-150">
                    Beranda
                </a>

                <a href="#tentang" @click="activeSection = 'tentang'"
                   :class="activeSection === 'tentang' ? 'text-[var(--ink)] underline-active' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                   class="px-4 py-2 text-sm font-semibold transition-all duration-150">
                    Tentang Kami
                </a>

                <a href="#fitur" @click="activeSection = 'fitur'"
                   :class="activeSection === 'fitur' ? 'text-[var(--ink)] underline-active' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                   class="px-4 py-2 text-sm font-semibold transition-all duration-150">
                    Fitur
                </a>

                <div class="h-6 w-px bg-[var(--line)] mx-3"></div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-[var(--ink)] rounded-md hover:bg-[var(--accent)] transition-colors">
                        <i class="fas fa-grid-2 mr-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-[var(--accent)] rounded-md hover:bg-[var(--accent-dark)] transition-colors">
                        Masuk
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden absolute top-full left-0 w-full bg-white border-t border-[var(--line)] flex flex-col py-4 px-6 space-y-1 shadow-lg"
                 style="display: none;">

                <a href="#" @click="activeSection = 'home'; mobileMenuOpen = false"
                   :class="activeSection === 'home' ? 'text-[var(--accent)] bg-[var(--bg)]' : 'text-[var(--ink)]'"
                   class="px-4 py-3 rounded-md font-semibold flex items-center gap-3">
                   <i class="fas fa-house w-5 text-center text-sm"></i> Beranda
                </a>

                <a href="#tentang" @click="activeSection = 'tentang'; mobileMenuOpen = false"
                   :class="activeSection === 'tentang' ? 'text-[var(--accent)] bg-[var(--bg)]' : 'text-[var(--ink)]'"
                   class="px-4 py-3 rounded-md font-semibold flex items-center gap-3">
                   <i class="fas fa-layer-group w-5 text-center text-sm"></i> Tentang Kami
                </a>

                <a href="#fitur" @click="activeSection = 'fitur'; mobileMenuOpen = false"
                   :class="activeSection === 'fitur' ? 'text-[var(--accent)] bg-[var(--bg)]' : 'text-[var(--ink)]'"
                   class="px-4 py-3 rounded-md font-semibold flex items-center gap-3">
                   <i class="fas fa-rocket w-5 text-center text-sm"></i> Fitur
                </a>

                <div class="h-px bg-[var(--line)] my-3"></div>

                @auth
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center py-3.5 text-sm font-bold text-white bg-[var(--ink)] rounded-md">
                        <i class="fas fa-grid-2 mr-2"></i> Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-3.5 text-sm font-bold text-white bg-[var(--accent)] rounded-md">
                        Masuk Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section id="home" class="relative pt-32 pb-20 lg:pt-40 lg:pb-28">
    <div class="container mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

            {{-- KOLOM TEKS --}}
            <div>
                <div class="inline-flex items-center gap-2 border border-[var(--line)] bg-white px-3 py-1.5 rounded-md mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)] live-dot"></span>
                    <span class="font-mono text-[10px] tracking-[0.16em] uppercase text-[var(--muted)]">Sistem Aktif &middot; {{ $schoolName }}</span>
                </div>

                <h1 class="font-display text-4xl lg:text-[3.4rem] font-bold text-[var(--ink)] mb-6 leading-[1.1] tracking-tight">
                    Absensi siswa,<br>
                    <span class="relative inline-block">
                        <span class="relative z-10">tercatat real-time.</span>
                        <span class="absolute left-0 bottom-1 w-full h-3 bg-[var(--accent)]/15"></span>
                    </span>
                </h1>

                <p class="text-base lg:text-lg text-[var(--muted)] mb-10 max-w-xl leading-relaxed">
                    Siswa scan QR, orang tua langsung menerima notifikasi WhatsApp, dan wali kelas tidak perlu lagi merekap kehadiran secara manual.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-12">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-[var(--accent)] text-white rounded-lg font-bold hover:bg-[var(--accent-dark)] transition-colors">
                        <i class="fas fa-qrcode mr-2.5"></i> Mulai Presensi
                    </a>
                    <a href="#fitur" class="inline-flex items-center justify-center px-7 py-3.5 border border-[var(--ink)]/15 text-[var(--ink)] rounded-lg font-bold hover:border-[var(--ink)] transition-colors">
                        Pelajari Fitur
                    </a>
                </div>

            </div>

           {{-- PANEL QR CODE --}}
<div class="lg:pt-2">
    <div class="bg-white border border-[var(--line)] rounded-xl overflow-hidden shadow-[0_1px_2px_rgba(20,23,28,0.04)]">

        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--line)]">
            <span class="font-mono text-[11px] uppercase tracking-widest text-[var(--muted)]">
                QR Code Presensi
            </span>

            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)] live-dot"></span>
                <span class="font-mono text-[10px] text-[var(--muted)]">
                    AKTIF
                </span>
            </span>
        </div>

        <div class="p-8 flex flex-col items-center justify-center">

            <img
                src="{{ asset('images/fitur-qr.png') }}"
                alt="QR Code"
                class="w-64 floating">

            <h3 class="mt-6 text-xl font-bold text-center">
                Scan QR untuk Presensi
            </h3>

            <p class="text-center text-[var(--muted)] mt-2 max-w-sm">
                Siswa cukup melakukan scan QR Code untuk mencatat kehadiran secara cepat, akurat, dan real-time.
            </p>

            <div class="flex gap-3 mt-6 flex-wrap justify-center">

                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                    QR Code
                </span>

                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                    Real-time
                </span>

                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-medium">
                    WhatsApp
                </span>

            </div>

        </div>

    </div>
</div>
</section>

{{-- SECTION: TENTANG KAMI --}}
<section id="tentang" class="relative py-20 lg:py-28 border-t border-[var(--line)]">
    <div class="container mx-auto px-6 lg:px-12">
        <div class="max-w-2xl mb-16">
            <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-[var(--accent)]">Mengenal Kami</span>
            <h2 class="font-display text-3xl lg:text-4xl font-bold text-[var(--ink)] mt-3 tracking-tight leading-tight">
                Lebih dekat dengan sistem e-absensi
            </h2>
        </div>

        {{-- SLIDER TENTANG KAMI --}}
        <div x-data="{ aboutActive: 0, total: 3 }" x-init="setInterval(() => { aboutActive = (aboutActive + 1) % total }, 5000)" class="relative max-w-6xl">
            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-700 ease-out" :style="`transform: translateX(-${aboutActive * 100}%)`" style="width: 100%;">

                    {{-- Slide 1 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                            <div class="rounded-xl overflow-hidden border border-[var(--line)] bg-white">
                                <img src="{{ asset('images/misi-digitalisasi.png') }}" class="w-full aspect-video object-cover" alt="Visi">
                            </div>
                            <div>
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Misi Digitalisasi</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Kami hadir untuk membawa perubahan nyata dalam manajemen sekolah melalui teknologi tepat guna bagi seluruh civitas akademika di {{ $schoolName }}.</p>
                                <div class="flex items-center gap-3 p-4 border border-[var(--line)] rounded-lg bg-white">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] flex-shrink-0">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[var(--ink)] text-sm">Inovasi berkelanjutan</h4>
                                        <p class="text-xs text-[var(--muted)]">Update teknologi berkala untuk sekolah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Slide 2 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                            <div class="rounded-xl overflow-hidden border border-[var(--line)] bg-white lg:order-2">
                                <img src="{{ asset('images/protect-data.png') }}" class="w-full aspect-video object-cover" alt="Keamanan">
                            </div>
                            <div class="lg:order-1">
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Prioritas Keamanan Data</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Sistem database yang aman menjamin tidak ada manipulasi data absensi, menjaga integritas informasi di sekolah.</p>
                                <div class="flex items-center gap-3 p-4 border border-[var(--line)] rounded-lg bg-white">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] flex-shrink-0">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[var(--ink)] text-sm">Data terproteksi</h4>
                                        <p class="text-xs text-[var(--muted)]">Privasi adalah prioritas utama kami.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Slide 3 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                            <div class="rounded-xl overflow-hidden border border-[var(--line)] bg-white">
                                <img src="{{ asset('images/parent.png') }}" class="w-full aspect-video object-cover" alt="Kemitraan">
                            </div>
                            <div>
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Membangun Kepercayaan</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Transparansi laporan real-time memperkuat sinergi antara sekolah dan wali murid demi kesuksesan siswa.</p>
                                <div class="flex items-center gap-3 p-4 border border-[var(--line)] rounded-lg bg-white">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] flex-shrink-0">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[var(--ink)] text-sm">Komunitas solid</h4>
                                        <p class="text-xs text-[var(--muted)]">Sinergi sekolah dan orang tua.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Navigasi Slider --}}
            <div class="flex justify-between items-center mt-10">
                <div class="flex gap-2">
                    <template x-for="i in [0, 1, 2]" :key="i">
                        <div @click="aboutActive = i" :class="aboutActive === i ? 'w-8 bg-[var(--accent)]' : 'w-4 bg-[var(--line)]'" class="h-1.5 rounded-full cursor-pointer transition-all duration-300"></div>
                    </template>
                </div>
                <div class="flex gap-2">
                    <button @click="aboutActive = aboutActive === 0 ? 2 : aboutActive - 1" class="w-9 h-9 rounded-md border border-[var(--line)] flex items-center justify-center text-[var(--ink)] hover:border-[var(--ink)] transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </button>
                    <button @click="aboutActive = aboutActive === 2 ? 0 : aboutActive + 1" class="w-9 h-9 rounded-md border border-[var(--line)] flex items-center justify-center text-[var(--ink)] hover:border-[var(--ink)] transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SECTION: FITUR --}}
<section id="fitur" class="relative py-20 lg:py-28 border-t border-[var(--line)]">
    <div class="container mx-auto px-6 lg:px-12">
        <div class="max-w-2xl mb-16">
            <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-[var(--accent)]">Keunggulan Sistem</span>
            <h2 class="font-display text-3xl lg:text-4xl font-bold text-[var(--ink)] mt-3 tracking-tight leading-tight">
                Fitur utama e-absensi siswa
            </h2>
            <p class="text-[var(--muted)] leading-relaxed mt-4">
                Dirancang untuk kebutuhan sekolah, dengan fokus pada kemudahan penggunaan dan kecepatan akses data.
            </p>
        </div>

        {{-- SLIDER FITUR --}}
        <div x-data="{ active: 0, loop() { setInterval(() => { this.active = this.active === 2 ? 0 : this.active + 1 }, 5000) } }" x-init="loop()" class="relative">
            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-700 ease-out" :style="`transform: translateX(-${active * 100}%)`" style="width: 100%;">

                    {{-- Fitur 1 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="bg-white border border-[var(--line)] rounded-xl p-8 lg:p-12 grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <span class="font-mono text-[10px] uppercase tracking-widest text-[var(--accent)]">Modul Scan</span>
                                <div class="w-12 h-12 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] mt-4 mb-6">
                                    <i class="fas fa-qrcode text-lg"></i>
                                </div>
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Presensi cepat via QR code</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Siswa cukup memindai kartu pelajar di depan perangkat sekolah. Proses tercatat dalam hitungan detik.</p>
                                <ul class="space-y-2 text-[var(--ink)] text-sm font-medium">
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Akurasi waktu real-time</li>
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Mencegah titip absen</li>
                                </ul>
                            </div>
                            <div class="rounded-lg border border-[var(--line)] bg-[var(--bg)] p-6 flex justify-center">
                                <img src="{{ asset('images/fitur-qr.png') }}" class="w-full max-w-xs" alt="QR Code Feature">
                            </div>
                        </div>
                    </div>

                    {{-- Fitur 2 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="bg-white border border-[var(--line)] rounded-xl p-8 lg:p-12 grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <span class="font-mono text-[10px] uppercase tracking-widest text-[var(--accent)]">Modul Notifikasi</span>
                                <div class="w-12 h-12 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] mt-4 mb-6">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </div>
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Notifikasi WhatsApp orang tua</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Setiap siswa yang absen otomatis mengirimkan pesan ke WA orang tua sebagai laporan kehadiran.</p>
                                <ul class="space-y-2 text-[var(--ink)] text-sm font-medium">
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Monitoring jarak jauh</li>
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Laporan langsung tanpa jeda</li>
                                </ul>
                            </div>
                            <div class="rounded-lg border border-[var(--line)] bg-[var(--bg)] p-6 flex justify-center">
                                <img src="{{ asset('images/fitur-notif.png') }}" class="w-full max-w-xs" alt="WhatsApp Notification">
                            </div>
                        </div>
                    </div>

                    {{-- Fitur 3 --}}
                    <div class="w-full flex-shrink-0">
                        <div class="bg-white border border-[var(--line)] rounded-xl p-8 lg:p-12 grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <span class="font-mono text-[10px] uppercase tracking-widest text-[var(--accent)]">Modul Laporan</span>
                                <div class="w-12 h-12 flex items-center justify-center rounded-md border border-[var(--accent)]/30 text-[var(--accent)] mt-4 mb-6">
                                    <i class="fas fa-chart-line text-lg"></i>
                                </div>
                                <h3 class="font-display text-2xl font-bold text-[var(--ink)] mb-4">Rekap laporan otomatis</h3>
                                <p class="text-[var(--muted)] leading-relaxed mb-6">Admin dan guru tidak perlu merekap manual. Semua data tersimpan rapi dan bisa diunduh PDF/Excel.</p>
                                <ul class="space-y-2 text-[var(--ink)] text-sm font-medium">
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Hemat waktu administrasi</li>
                                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--accent)] text-xs"></i> Data akurat tanpa manipulasi</li>
                                </ul>
                            </div>
                            <div class="rounded-lg border border-[var(--line)] bg-[var(--bg)] p-6 flex justify-center">
                                <img src="{{ asset('images/fitur-rekap.png') }}" class="w-full max-w-xs" alt="Reporting Feature">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Navigasi Fitur --}}
            <div class="flex justify-center items-center gap-4 mt-10">
                <button @click="active = active === 0 ? 2 : active - 1" class="w-11 h-11 rounded-md border border-[var(--line)] flex items-center justify-center text-[var(--ink)] hover:border-[var(--ink)] transition-colors">
                    <i class="fas fa-arrow-left text-sm"></i>
                </button>
                <div class="flex gap-2">
                    <template x-for="i in [0, 1, 2]" :key="i">
                        <button @click="active = i" :class="active === i ? 'w-8 bg-[var(--accent)]' : 'w-4 bg-[var(--line)]'" class="h-1.5 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
                <button @click="active = active === 2 ? 0 : active + 1" class="w-11 h-11 rounded-md border border-[var(--line)] flex items-center justify-center text-[var(--ink)] hover:border-[var(--ink)] transition-colors">
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="bg-[var(--ink)] py-14 text-white">
    <div class="container mx-auto px-6 lg:px-12">
        <div class="grid md:grid-cols-2 gap-8 items-start">
            <div>
                <h3 class="font-display text-xl font-bold mb-1">{{ $schoolName }}</h3>
                <p class="text-white/50 text-sm mb-5 font-mono uppercase tracking-wide text-[10px]">Sistem Absensi Digital</p>

                <div class="space-y-2 text-sm text-white/60">
                    @if(!empty($settings['school_address']))
                        <p><i class="fas fa-map-marker-alt w-5 text-[var(--accent)]"></i> {{ $settings['school_address'] }}</p>
                    @endif
                    @if(!empty($settings['school_phone']))
                        <p><i class="fas fa-phone w-5 text-[var(--accent)]"></i> {{ $settings['school_phone'] }}</p>
                    @endif
                    @if(!empty($settings['school_email']))
                        <p><i class="fas fa-envelope w-5 text-[var(--accent)]"></i> {{ $settings['school_email'] }}</p>
                    @endif
                </div>
            </div>

            <div class="flex md:justify-end gap-4">
                @if(!empty($settings['social_facebook']))
                    <a href="{{ $settings['social_facebook'] }}" target="_blank" class="w-10 h-10 rounded-md border border-white/15 flex items-center justify-center hover:border-white/40 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                @endif

                @if(!empty($settings['social_instagram']))
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" class="w-10 h-10 rounded-md border border-white/15 flex items-center justify-center hover:border-white/40 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 text-center text-white/40 text-[10px] font-mono uppercase tracking-widest">
            &copy; {{ date('Y') }} {{ $schoolName }} &mdash; Sistem Absensi QR Code
        </div>
    </div>
</footer>
</body>
</html>