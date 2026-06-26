{{-- resources/views/layouts/partials/sidebar.blade.php (UPDATED UI) --}}

@php
    // --- Logika PHP (TIDAK BERUBAH) ---
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;

    $user = Auth::user();
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;
    $scanIconColor = 'text-red-500'; // Warna ikon Scan tetap

    // Penanganan Active State (TIDAK BERUBAH)
    $isAbsensiAdminActive = Request::is('admin/absensi/scan-kelas');
    $isManajemenDataActive =
        Request::is('admin/classes*') ||
        Request::is('admin/students*') ||
        Request::is('admin/teachers*') ||
        Request::is('admin/parents*') ||
        Request::is('admin/users*');
    $isAbsensiWaliKelasActive = Request::is('walikelas/absensi*');

    function isActive($path)
    {
        return Request::is($path);
    }

    // Class Tailwind untuk Styling (PEMBARUAN WARNA)
    // Mengganti blue-600 dengan indigo-600 untuk estetika yang lebih modern
    $activeClass = 'bg-indigo-600 text-white font-semibold shadow-md';
    $defaultClass = 'text-gray-300 hover:bg-gray-700/70 hover:text-white'; // Hover lebih menonjol
    // Menggunakan warna indigo yang lebih terang untuk sub-menu
    $activeSubClass = 'bg-indigo-700/50 text-white font-medium';
    $defaultSubClass = 'text-gray-400 hover:bg-gray-700/50 hover:text-white';
@endphp

<aside id="main-sidebar"
    class="fixed top-0 left-0 z-50 h-full w-64 bg-gray-900 
           transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-[4px_0_24px_rgba(0,0,0,0.1)] border-r border-gray-800">

    {{-- 1. BRAND LINK (Logo & Nama Sekolah) --}}
    {{-- Padding vertikal disesuaikan menjadi p-4 py-3 untuk tampilan yang lebih ramping --}}
    <a href="{{ route('dashboard') }}" class="flex items-center p-4 py-3 border-b border-gray-700">

        {{-- LOGO / ICON --}}
        @if ($schoolLogoPath)
            {{-- Menggunakan h-8 w-8, ditambah bg-white dan p-1 agar PNG gelap terlihat jelas --}}
            <img src="{{ asset('storage/' . $schoolLogoPath) }}" alt="{{ $schoolName }}"
                class="h-8 w-8 rounded-full bg-white p-1 object-contain mr-3 border-2 border-indigo-500/50">
        @else
            {{-- Menggunakan warna indigo pada ikon default --}}
            <i
                class="fas fa-clipboard-check text-white h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center mr-3 text-base"></i>
        @endif

        {{-- TEXT (Nama Sekolah) --}}
        <div class="flex flex-col overflow-hidden leading-snug">
            {{-- Ukuran teks nama sekolah sedikit dikecilkan dan lebih bold --}}
            <span class="text-white text-sm font-extrabold whitespace-nowrap overflow-ellipsis">
                {{ Str::limit($schoolName, 20) }}
            </span>
            <small class="text-gray-400 text-xs font-light mt-0">E-Absensi Digital</small>
        </div>
    </a>

    {{-- 2. NAVIGASI MENU UTAMA --}}
    {{-- Menyesuaikan p-2 menjadi p-4 dan menambah space-y-2 untuk jarak antar menu --}}
    <div class="overflow-y-auto h-[calc(100vh-62px)] p-4">
        <nav>
            <ul class="space-y-2">

                @if ($user)
                    {{-- AREA SUPER ADMIN --}}
                    @if ($user->isSuperAdmin())
                        {{-- Header Menu: Penyesuaian padding dan warna teks --}}
                        <li
                            class="px-2 pt-3 pb-1 text-xs font-semibold text-indigo-400 uppercase border-t border-gray-700 mt-2 flex justify-between items-center">
                            <span>ADMINISTRASI PUSAT</span>
                            <i class="fas fa-cogs text-indigo-500/70"></i>
                        </li>

                        {{-- Dashboard --}}
                        <li>
                            {{-- Menggunakan p-2.5 dan rounded-md untuk tampilan yang lebih modern --}}
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('admin/dashboard') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>

                        {{-- DROPDOWN MANAJEMEN DATA --}}
                        <li class="relative">
                            {{-- Menggunakan p-2.5 dan rounded-md --}}
                            <button
                                onclick="document.getElementById('submenu-manajemen-data').classList.toggle('hidden');"
                                class="w-full flex items-center p-2.5 rounded-md transition duration-150 {{ $isManajemenDataActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-database w-5 h-5 mr-3"></i>
                                <span class="flex-1 text-sm whitespace-nowrap text-left">Manajemen Data</span>
                                <i
                                    class="fas fa-angle-left right ml-auto transition-transform duration-200 {{ $isManajemenDataActive ? '-rotate-90' : 'rotate-0' }}"></i>
                            </button>

                            {{-- Submenu: Menggunakan padding kiri yang lebih kecil (pl-2) dan kelas defaultSubClass --}}
                            <ul id="submenu-manajemen-data"
                                class="pl-2 pt-1 space-y-1 {{ $isManajemenDataActive ? 'block' : 'hidden' }}">
                                @foreach ([
        'classes.index' => ['route' => 'classes.index', 'text' => 'Data Kelas'],
        'students.index' => ['route' => 'students.index', 'text' => 'Data Siswa'],
        'teachers.index' => ['route' => 'teachers.index', 'text' => 'Data Wali Kelas'],
        'parents.index' => ['route' => 'parents.index', 'text' => 'Data Orang Tua'],
        'admin.users.index' => ['route' => 'admin.users.index', 'text' => 'Manajemen Pengguna'],
    ] as $routeKey => $item)
                                    <li>
                                        <a href="{{ route($routeKey) }}"
                                            class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                    {{ Request::is('admin/' . str_replace('.index', '*', Str::after($routeKey, 'admin.'))) ? $activeSubClass : $defaultSubClass }}">
                                            <i class="far fa-dot-circle text-xs w-3 h-3 mr-3 opacity-75"></i>
                                            <span class="ml-1">{{ $item['text'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                                {{-- MANAJEMEN PELAJARAN --}}
                                <li>
                                    <a href="{{ route('admin.subjects.index') }}"
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('admin/subjects*') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-book-open text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Mata Pelajaran</span>
                                    </a>
                                </li>
                                {{-- MANAJEMEN JADWAL --}}
                                <li>
                                    <a href="{{ route('admin.schedules.index') }}"
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('admin/schedules*') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="far fa-calendar-alt text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Atur Jadwal</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- HEADER: LAPORAN & PENGATURAN --}}
                        <li
                            class="px-2 pt-3 pb-1 text-xs font-semibold text-indigo-400 uppercase border-t border-gray-700 mt-2 flex justify-between items-center">
                            <span>LAPORAN & PENGATURAN</span>
                            <i class="fas fa-chart-line text-indigo-500/70"></i>
                        </li>

                        {{-- Laporan Absensi & Pengaturan Umum --}}
                        @foreach ([
        'report.index' => ['icon' => 'fas fa-chart-line', 'text' => 'Laporan Absensi', 'route_path' => 'admin/report*'],
        'settings.index' => ['icon' => 'fas fa-cog', 'text' => 'Pengaturan Umum', 'route_path' => 'admin/settings*'],
    ] as $routeKey => $item)
                            <li>
                                <a href="{{ route($routeKey) }}"
                                    class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive($item['route_path']) ? $activeClass : $defaultClass }}">
                                    <i class="{{ $item['icon'] }} w-5 h-5 mr-3"></i>
                                    <span class="text-sm">{{ $item['text'] }}</span>
                                </a>
                            </li>
                        @endforeach

                        {{-- Kelola Pengumuman --}}
                        <li>
                            <a href="{{ route('announcements.index') }}"
                            class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('admin/announcements*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-bullhorn w-5 h-5 mr-3"></i>
                                <span class="text-sm">Kelola Pengumuman</span>
                            </a>
                        </li>

                        {{-- Hari Libur --}}
                        <li>
                            <a href="{{ route('admin.holidays.index') }}"
                            class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('admin/holidays*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-calendar-times w-5 h-5 mr-3"></i>
                                <span class="text-sm">Hari Libur</span>
                            </a>
                        </li>

                        {{-- HEADER: OPERASI UTAMA (Absensi) --}}
                        <li
                            class="px-2 pt-3 pb-1 text-xs font-semibold text-indigo-400 uppercase border-t border-gray-700 mt-2 flex justify-between items-center">
                            <span>OPERASI UTAMA</span>
                            <i class="fas fa-camera text-indigo-500/70"></i>
                        </li>

                        {{-- Absensi QR Scan --}}
                        <li>
                            <a href="{{ route('admin.absensi.scan') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ $isAbsensiAdminActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-qrcode w-5 h-5 mr-3 {{ $scanIconColor }}"></i>
                                <span class="text-sm">Absensi QR Scan</span>
                            </a>
                        </li>

                        {{-- AREA WALI KELAS --}}
                    @elseif($user->isWaliKelas())
                        <li
                            class="px-2 pt-3 pb-1 text-xs font-semibold text-indigo-400 uppercase border-t border-gray-700 mt-2 flex justify-between items-center">
                            <span>AREA WALI KELAS</span>
                            <i class="fas fa-user-tie text-indigo-500/70"></i>
                            </li>
                        
                        {{-- Dashboard Kelas --}}
                        <li>
                            <a href="{{ route('walikelas.dashboard') }}"
                                
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('walikelas/dashboard') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-home w-5 h-5 mr-3"></i>
                                <span class="text-sm">Dashboard Kelas</span>
                                </a>
                            </li>
                        
                        {{-- Data Siswa Kelas yang Diampu --}}
                        <li>
                            <a href="{{ route('walikelas.students.index') }}"
                                
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('walikelas/students*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-users w-5 h-5 mr-3"></i>
                                <span class="text-sm">Data Siswa Kelas</span>
                                </a>
                            </li>
                        
                       {{-- 🆕 PERMINTAAN IZIN (Tambahan baru di Wali Kelas) --}}
                            <li>
                                <a href="{{ route('walikelas.izin.index') }}"
                                    class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('walikelas/izin*') ? $activeClass : $defaultClass }}">
                                    <i class="fas fa-envelope-open-text w-5 h-5 mr-3"></i>
                                    <span class="text-sm">Permintaan Izin</span>

                                    {{-- 🚩 TAMBAHKAN KODE INI DI SINI --}}
                                    @if(isset($pendingRequestsCount) && $pendingRequestsCount > 0)
                                        <span class="ml-auto bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                            {{ $pendingRequestsCount }}
                                        </span>
                                    @endif
                                    {{-- -------------------------- --}}
                                </a>
                            </li>
                        
                        {{-- Menu Absensi (Dropdown) --}}
                        <li class="relative">
                            <button
                                onclick="document.getElementById('submenu-walikelas-absensi').classList.toggle('hidden');"
                                
                                class="w-full flex items-center p-2.5 rounded-md transition duration-150 {{ $isAbsensiWaliKelasActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                                <span
                                    class="flex-1 text-sm whitespace-nowrap text-left">Absensi Kelas</span>
                                <i
                                    class="fas fa-angle-left right ml-auto transition-transform duration-200 {{ $isAbsensiWaliKelasActive ? '-rotate-90' : 'rotate-0' }}"></i>
                                </button>
                            {{-- SUBMENU --}}
                            <ul id="submenu-walikelas-absensi"
                                class="pl-2 pt-1 space-y-1 {{ $isAbsensiWaliKelasActive ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('walikelas.absensi.scan') }}"
                                        
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('walikelas/absensi/scan') ? $activeSubClass : $defaultSubClass }}">
                                        <i
                                            class="fas fa-qrcode text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Scan
                                            Masuk/Pulang</span>
                                        </a>
                                    </li>
                                <li>
                                    <a
                                        href="{{ route('walikelas.absensi.manual.index') }}"
                                        
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('walikelas/absensi/manual*') ? $activeSubClass : $defaultSubClass }}">
                                        <i
                                            class="fas fa-edit text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Manual &
                                            Koreksi</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        
                        {{-- Riwayat & Laporan (Dropdown) --}}
                        <li class="relative">
                            <button
                                onclick="document.getElementById('submenu-walikelas-report').classList.toggle('hidden');"
                                
                                class="w-full flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('walikelas/report*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                                <span
                                    class="flex-1 text-sm whitespace-nowrap text-left">Riwayat & Laporan</span>
                                <i
                                    class="fas fa-angle-left right ml-auto transition-transform duration-200 {{ isActive('walikelas/report*') ? '-rotate-90' : 'rotate-0' }}"></i>
                                </button>
                            
                            {{-- SUB-MENU --}}
                            <ul id="submenu-walikelas-report"
                                class="pl-2 pt-1 space-y-1 {{ isActive('walikelas/report*') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('walikelas.report.index') }}"
                                        
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('walikelas/report') && !isActive('walikelas/report/monthly-recap') ? $activeSubClass : $defaultSubClass }}">
                                        <i
                                            class="fas fa-list-alt text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Laporan
                                            Harian</span>
                                        </a>
                                    </li>
                                <li>
                                    <a
                                        href="{{ route('walikelas.report.monthly_recap') }}"
                                        
                                        class="flex items-center p-2 rounded-md transition duration-150 text-sm 
                                                {{ isActive('walikelas/report/monthly-recap') ? $activeSubClass : $defaultSubClass }}">
                                        <i
                                            class="fas fa-calendar-alt text-xs w-3 h-3 mr-3 opacity-75"></i>
                                        <span class="ml-1">Rekap Absensi
                                            Bulanan</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        {{-- AREA ORANG TUA --}}
                    @elseif($user->isOrangTua())
                        <li
                            class="px-2 pt-3 pb-1 text-xs font-semibold text-indigo-400 uppercase border-t border-gray-700 mt-2 flex justify-between items-center">
                            <span>AREA ORANG TUA</span>
                            <i class="fas fa-users text-indigo-500/70"></i>
                        </li>

                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('orangtua.dashboard') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('orangtua/dashboard') && !isActive('orangtua/report*') && !isActive('orangtua/izin*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>

                        {{-- Riwayat Absensi Anak (Tabel) --}}
                        <li>
                            <a href="{{ route('orangtua.report.index') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('orangtua/report*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-list-alt w-5 h-5 mr-3"></i>
                                <span class="text-sm">Riwayat Absensi Anak</span>
                            </a>
                        </li>

                        {{-- Pengajuan Izin Online --}}
                        <li>
                            <a href="{{ route('orangtua.izin.index') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('orangtua/izin*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-file-medical-alt w-5 h-5 mr-3"></i>
                                <span class="text-sm">Pengajuan Izin/Sakit</span>
                            </a>
                        </li>

                        {{-- Jadwal Pelajaran (Baru) --}}
                        <li>
                            <a href="{{ route('orangtua.jadwal.index') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('orangtua/jadwal*') ? $activeClass : $defaultClass }}">
                                <i class="far fa-calendar-alt w-5 h-5 mr-3"></i>
                                <span class="text-sm">Jadwal Pelajaran</span>
                            </a>
                        </li>

                        {{-- Edit Profil --}}
                        <li class="border-t border-gray-700 pt-2 mt-2">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center p-2.5 rounded-md transition duration-150 {{ isActive('profile') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-user w-5 h-5 mr-3"></i>
                                <span class="text-sm">Edit Profil</span>
                            </a>
                        </li>
                    @endif {{-- Tutup blok Role --}}

                @endif {{-- Tutup @if ($user) --}}
            </ul>
        </nav>
    </div>
</aside>
