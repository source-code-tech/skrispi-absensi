<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>E-Absensi Siswa | @yield('title', config('app.name', 'Laravel'))</title>

        {{-- 🔥 LOGIKA PENGAMBILAN LOGO SEKOLAH UNTUK FAVICON (LOGIKA TETAP SAMA) --}}
        @php
            use App\Models\Setting;
            // Ambil pengaturan logo
            $settingLogo = Setting::where('key', 'school_logo')->first();
            // Tentukan path: gunakan storage jika ada, jika tidak, gunakan path default (public/favicon.ico)
            $faviconPath = $settingLogo && $settingLogo->value ? asset('storage/' . $settingLogo->value) : asset('favicon.ico');
        @endphp
        {{-- 🔥 Terapkan path logo ke link favicon --}}
        <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon" />
        <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon" />

        {{-- Fonts: Mengganti Inter dengan sans-serif standar untuk konsistensi modern --}}
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        
        {{-- Font Awesome --}}
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/fontawesome-free/css/all.min.css') }}" />
        
        {{-- Select2 (Jika masih digunakan) --}}
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/select2/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />

        {{-- Scripts Tailwind (Wajib menggunakan @vite) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- AOS ANIMATION --}}
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        @yield('css')
    </head>
    
    <body class="font-inter antialiased bg-gray-100 text-gray-800 h-full">
        
        {{-- GLOBAL LOADER --}}
        @include('layouts.partials.loader')

        <div class="flex h-full">
            
            {{-- 1. Sidebar --}}
            {{-- Menggunakan Z-index yang lebih tinggi dan posisi tetap untuk tampilan modern --}}
            @include('layouts.partials.sidebar') 

            {{-- Mobile Sidebar Overlay (Backdrop) --}}
            <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-30 hidden opacity-0 transition-opacity duration-300 md:hidden backdrop-blur-sm"></div> 

            {{-- Main Content Area --}}
            <div class="flex flex-col flex-1">
                
                {{-- 2. Header --}}
                @include('layouts.partials.header') 

                {{-- 3. CONTENT WRAPPER --}}
                {{-- Penyesuaian padding dan margin untuk mengimbangi header/sidebar --}}
                <div class="flex-1 overflow-y-auto pt-16 md:ml-64 transition-all duration-300 ease-in-out">
                    
                    {{-- Content Header --}}
                    {{-- Memberi batas bawah yang lebih menonjol dan padding yang konsisten --}}
                    <header class="bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-20">
                        <div class="max-w-full mx-auto py-3 px-4 sm:px-6 lg:px-8">
                            @yield('content_header') 
                        </div>
                    </header>

                    {{-- Page Content --}}
                    <main class="flex-1 p-4 sm:p-6 lg:p-8">
                        <div class="container-fluid">
                            {{-- Menerapkan padding atas agar tidak terlalu mepet ke header --}}
                            @yield('content') 
                        </div>
                    </main>
                    
                    {{-- 4. Footer --}}
                    @include('layouts.partials.footer') 
                </div>
            </div>
        </div>

        {{-- JQUERY & BOOTSTRAP JS (Tidak ada perubahan pada logika) --}}
        <script src="{{ asset('template/adminlte/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('template/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        {{-- Select2 dan Instascan JS --}}
        <script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- AOS ANIMATION JS --}}
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                once: true,
            });
        </script>

        @yield('js')
        
        {{-- SKRIP GLOBAL (Sidebar Toggle, Dropdown, Polling, dan Waktu Server) (Logika Tetap Sama) --}}
        <script>
            // SWEETALERT GLOBAL NOTIFICATION
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-indigo-600 rounded-xl px-4 py-2'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-red-600 rounded-xl px-4 py-2'
                    }
                });
            @endif

            // Logika DOMContentLoaded untuk Toggle dan Dropdown
            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('main-sidebar');
                const toggleBtn = document.getElementById('sidebar-toggle-btn');
                const profileDropdownBtn = document.querySelector('.profile-dropdown-btn');
                const profileDropdownContent = document.getElementById('profile-dropdown-content');
                const notificationDropdownBtn = document.querySelector('.notification-dropdown-btn');
                const notificationDropdownContent = document.getElementById('notification-list');

                // 1. Sidebar Toggle Logic
                if (sidebar && toggleBtn) {
                    toggleBtn.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent immediate closing
                        sidebar.classList.toggle('-translate-x-full');
                        
                        // Toggle Overlay
                        const overlay = document.getElementById('sidebar-overlay');
                        if (overlay) {
                            if (!sidebar.classList.contains('-translate-x-full')) {
                                // Sidebar is OPEN
                                overlay.classList.remove('hidden');
                                setTimeout(() => overlay.classList.remove('opacity-0'), 10); // Fade in
                            } else {
                                // Sidebar is CLOSED
                                overlay.classList.add('opacity-0');
                                setTimeout(() => overlay.classList.add('hidden'), 300); // Wait for fade out
                            }
                        }
                    });
                }

                // 2. Overlay Click Logic (Close Sidebar)
                const overlay = document.getElementById('sidebar-overlay');
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.add('-translate-x-full'); // Close sidebar
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    });
                }
                
                // 3. Dropdown Toggle Logic
                if (profileDropdownBtn && profileDropdownContent) {
                    profileDropdownBtn.addEventListener('click', function(e) { e.stopPropagation(); profileDropdownContent.classList.toggle('hidden'); });
                }
                if (notificationDropdownBtn && notificationDropdownContent) {
                    notificationDropdownBtn.addEventListener('click', function(e) { e.stopPropagation(); notificationDropdownContent.classList.toggle('hidden'); });
                }
                
                // 4. Close all dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    // Note: Sidebar close logic is now handled by the overlay for mobile
                    
                    if (profileDropdownContent && !profileDropdownBtn.contains(e.target) && !profileDropdownContent.contains(e.target)) {
                        profileDropdownContent.classList.add('hidden');
                    }
                    if (notificationDropdownContent && !notificationDropdownBtn.contains(e.target) && !notificationDropdownContent.contains(e.target)) {
                        notificationDropdownContent.classList.add('hidden');
                    }
                });

                // 5. Polling Notifikasi (Menggunakan JQuery)
                function fetchNotifications() {
                    if (typeof $ === 'undefined') return; // Guard clause if jQuery not loaded
                    
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{ route("notifications.latest") }}',
                        method: 'GET',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(response) {
                            const listContainer = $('#dynamic-notifications');
                            const headerCount = $('#dropdown-header-count');
                            const badge = $('#notification-badge');
                            listContainer.empty();
                            const count = response.count;
                            headerCount.text(count + ' Notifikasi Terbaru');
                            if (count > 0) {
                                badge.show();
                                let html = '';
                                response.notifications.forEach(function(notif) {
                                    html += `<a href="${notif.url || '{{ route("report.index") }}'}" class="flex items-center px-4 py-3 text-sm text-gray-700 transition duration-150 ease-in-out border-b border-gray-100 hover:bg-indigo-50/50">
                                                <i class="${notif.icon || 'fas fa-bell'} text-indigo-500 mr-3 w-5 text-center"></i> 
                                                <div class="flex-1 overflow-hidden">
                                                    <p class="font-semibold truncate leading-snug">${notif.title}</p>
                                                    <span class="text-xs text-gray-500 block mt-1">${notif.time}</span>
                                                </div>
                                            </a>`;
                                });
                                listContainer.append(html);
                            } else {
                                badge.hide();
                                listContainer.append('<div class="px-4 py-4 text-center text-gray-500 text-sm">Tidak ada notifikasi baru.</div>');
                            }
                        }
                    });
                }

                // Jalankan polling
                fetchNotifications(); 
                setInterval(fetchNotifications, 10000); 
            });
            
            // SKRIP KHUSUS WAKTU SERVER (Logika Tetap Sama)
            let serverTimeElement = document.getElementById('live-server-time'); 
            if (serverTimeElement) {
                let initialTimeStr = serverTimeElement.textContent;
                let [hours, minutes, seconds] = initialTimeStr.split(':').map(Number);
                
                let serverDate = new Date();
                serverDate.setHours(hours);
                serverDate.setMinutes(minutes);
                serverDate.setSeconds(seconds);
                
                function updateServerTime() {
                    serverDate.setSeconds(serverDate.getSeconds() + 1);
                    const h = String(serverDate.getHours()).padStart(2, '0');
                    const m = String(serverDate.getMinutes()).padStart(2, '0');
                    const s = String(serverDate.getSeconds()).padStart(2, '0');
                    serverTimeElement.textContent = `${h}:${m}:${s}`;
                }
                setInterval(updateServerTime, 1000);
            }
        </script>
    </body>
</html>