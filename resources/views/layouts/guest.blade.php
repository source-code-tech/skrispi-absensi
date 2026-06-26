<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🚨 LOGIKA PENGAMBILAN SETTINGS UNTUK TITLE DAN FAVICON --}}
    @php
        use Illuminate\Support\Facades\Storage;
        
        // Menggunakan fallback jika settings belum dimuat
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? config('app.name', 'E-Absensi');
        $schoolLogoPath = $settings['school_logo'] ?? 'default/favicon.ico'; 
        
        // --- LOGIKA PATH FAVICON/LOGO ---
        $faviconUrl = asset('images/default/favicon.ico'); 
        
        // 🔥 PERBAIKAN: Gunakan path default yang lebih aman jika path DB kosong
        if (!empty($schoolLogoPath) && $schoolLogoPath != 'default/favicon.ico' && Storage::disk('public')->exists($schoolLogoPath)) {
            $faviconUrl = asset('storage/' . $schoolLogoPath);
        }
    @endphp
    
    <title>@yield('title') - {{ $schoolName }}</title>

    {{-- FAVICON DINAMIS --}}
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">

    {{-- Font Tailwind: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    
    {{-- Font Awesome (Tetap dipakai untuk ikon) --}}
    <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    
    {{-- 🔥 TAILWIND CSS (MENGGANTIKAN SEMUA CSS ADMINLTE) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    {{-- AOS ANIMATION --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Custom CSS --}}
    @stack('css')

</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    
    {{-- GLOBAL LOADER --}}
    @include('layouts.partials.loader')

    {{-- YIELD CONTENT DIRECTLY (Full Width Control) --}}
    @yield('content')
    
    {{-- REQUIRED SCRIPTS --}}
    <script src="{{ asset('template/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    {{-- AOS ANIMATION JS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
    
    {{-- Custom JavaScript --}}
    @yield('js')

</body>
</html>