{{-- resources/views/layouts/partials/header.blade.php (UPDATED UI) --}}

@php
    // --- Logika PHP (TIDAK BERUBAH) ---
    $user = Auth::user();
    $userName = $user->name ?? 'User';
    $userRole = Str::upper($user->role ?? 'USER');
@endphp

<header class="fixed top-0 left-0 md:left-64 right-0 z-40 
             bg-white/90 backdrop-blur-md border-b border-gray-200/50 shadow-sm 
             transition-all duration-300 ease-in-out">
    {{-- Glassmorphism applied: bg-white/90 + backdrop-blur-md --}}
    <nav class="flex items-center justify-between h-[4rem] px-4 sm:px-6 lg:px-8">
        
        {{-- Sisi Kiri: Toggle Sidebar (Mobile) & Navigasi Dasar --}}
        <div class="flex items-center space-x-4">
            
            {{-- Tombol Toggle Sidebar (HANYA terlihat di Mobile/Tablet) --}}
            {{-- Menggunakan warna indigo untuk fokus --}}
            <button id="sidebar-toggle-btn" 
                    class="p-2 text-gray-500 hover:text-indigo-600 md:hidden focus:outline-none focus:ring-2 focus:ring-indigo-500/80 rounded-lg" 
                    aria-label="Toggle Menu">
                <i class="fas fa-bars text-xl"></i>
            </button>

            {{-- Navigasi Dasar (Dashboard) --}}
            {{-- Menggunakan indigo-100/30 dan indigo-700 untuk active state --}}
            <a href="{{ route('dashboard') }}" 
               class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition duration-150 
               {{-- Class default/konstan --}}
               text-gray-600 hover:bg-gray-100/50 
               
               {{-- Class bersyarat (override jika aktif) --}}
               @if(Request::is('*/dashboard')) 
                   bg-indigo-100/50 text-indigo-700 font-semibold shadow-sm
               @endif">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            
            {{-- 💡 TAMBAHAN: Waktu Server Langsung (Di samping link dashboard di Desktop) --}}
            <span class="hidden lg:inline-flex items-center text-sm font-mono text-gray-500 bg-gray-50 px-3 py-1 rounded-full border border-gray-200 shadow-inner">
                <i class="fas fa-clock mr-2 text-indigo-500"></i>
                <span id="live-server-time">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span> 
            </span>

        </div>

        {{-- Sisi Kanan: Notifikasi & Profil --}}
        <div class="flex items-center space-x-3 sm:space-x-4">
            
            {{-- Dropdown Notifikasi --}}
            <div class="relative" id="notification-dropdown">
                {{-- Menggunakan warna indigo untuk fokus & hover --}}
                <button class="notification-dropdown-btn p-2 text-gray-600 hover:text-indigo-600 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500/80" 
                        aria-expanded="false" 
                        title="Notifikasi">
                    <i class="far fa-bell text-xl"></i>
                    {{-- Menggunakan shadow yang lebih halus pada badge --}}
                    <span class="absolute top-1 right-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white shadow-md" 
                          id="notification-badge" 
                          style="display: none;">
                    </span>
                    <span class="sr-only" id="notification-count-text">0 notifikasi</span>
                </button>
                
                {{-- Dropdown Content --}}
                {{-- Menggunakan z-index lebih tinggi (z-50) dan shadow yang lebih dalam (shadow-2xl) --}}
                <div class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 hidden z-50 transform origin-top-right animate-fade-in-down"
                     id="notification-list">
                    
                    {{-- Header Notifikasi --}}
                    <div class="px-4 py-3 text-sm font-bold text-gray-700 border-b border-gray-100">
                        <i class="fas fa-bell text-indigo-500 mr-2"></i> <span id="dropdown-header-count">Memuat Notifikasi...</span>
                    </div>
                    
                    <div class="py-1 max-h-80 overflow-y-auto" id="dynamic-notifications">
                        {{-- KONTEN NOTIFIKASI DYNAMIC --}}
                    </div>
                    
                    {{-- Link Lihat Semua --}}
                    <a href="{{ route('report.index') }}" 
                       class="block w-full text-center px-4 py-2 text-sm text-indigo-600 font-semibold hover:bg-indigo-50/50 border-t border-gray-100 rounded-b-xl">
                        Lihat Semua Laporan <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
            
            {{-- Dropdown Profil & Logout --}}
            <div class="relative">
                {{-- Menggunakan warna indigo untuk hover --}}
                <button class="profile-dropdown-btn flex items-center p-2 text-gray-600 hover:text-indigo-800 rounded-lg transition duration-150 focus:outline-none" 
                        aria-expanded="false">
                    {{-- Menambahkan Avatar Default (Jika Anda memiliki gambar profil, bisa ditambahkan di sini) --}}
                    <i class="far fa-user w-5 h-5 mr-1 text-indigo-600"></i>
                    <span class="hidden sm:inline text-sm font-semibold text-gray-700">{{ $userName }}</span>
                    <i class="fas fa-chevron-down text-xs ml-2 opacity-70"></i>
                </button>
                
                {{-- Dropdown Content --}}
                <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 hidden z-50 transform origin-top-right animate-fade-in-down" 
                     id="profile-dropdown-content">
                    
                    <div class="px-4 py-3 text-sm text-gray-700 border-b border-gray-100 rounded-t-xl">
                        Login sebagai: <strong class="text-indigo-600 font-bold">{{ $userRole }}</strong>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150">
                        <i class="fas fa-user-edit mr-3 w-4 text-indigo-500"></i> Kelola Profil
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    
                    {{-- Logout dengan hover yang lebih jelas --}}
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 font-semibold hover:bg-red-50 rounded-b-xl transition duration-150" 
                       onclick="event.preventDefault(); document.getElementById('logout-form-header-tailwind').submit();">
                        <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
                    </a>
                    
                    <form id="logout-form-header-tailwind" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </nav>
</header>