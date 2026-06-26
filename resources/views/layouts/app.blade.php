<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>E-Absensi Siswa | @yield('title', config('app.name', 'Laravel'))</title>

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/fontawesome-free/css/all.min.css') }}" />
        
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/select2/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- AOS ANIMATION --}}
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        @yield('css')
    </head>
    
    {{-- Mengganti class body agar sesuai dengan layout custom --}}
    <body class="font-sans antialiased bg-gray-100">
        
        {{-- GLOBAL LOADER --}}
        @include('layouts.partials.loader')

        <div class="min-h-screen">
            
            {{-- 1. Sidebar (Fixed, Muncul di md: ke atas) --}}
            {{-- Menggunakan path partials Anda: resources/views/layouts/partials/sidebar.blade.php --}}
            @include('layouts.partials.sidebar') 
            
            {{-- 2. Header (Fixed di atas konten, bergeser 64px di md: ke atas) --}}
            {{-- Menggunakan path partials Anda: resources/views/layouts/partials/header.blade.php --}}
            @include('layouts.partials.header') 

            {{-- 3. CONTENT WRAPPER --}}
            {{-- min-h-screen agar konten memanjang, md:ml-64 untuk menggeser konten dari sidebar --}}
            <div class="min-h-screen md:ml-64 pt-16 transition-all duration-300">
                
                {{-- Content Header (Page Heading) --}}
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="max-w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{-- Page Content (Content Utama) --}}
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
            {{-- @include('layouts.partials.footer') --}} {{-- Jika ada footer --}}
        </div>

        {{-- JQUERY & BOOTSTRAP (Hanya jika dibutuhkan oleh Select2/AdminLTE JS) --}}
        <script src="{{ asset('template/adminlte/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('template/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        {{-- Select2 dan Instascan JS --}}
        <script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>

        {{-- AOS ANIMATION JS --}}
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                once: true,
            });
        </script>

        @yield('js')
    </body>
</html>