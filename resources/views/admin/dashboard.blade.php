@extends('layouts.adminlte')

@section('title', 'Dashboard Super Admin')

@section('content_header')
{{-- CUSTOM HEADER: Sudah menggunakan Indigo, hanya penyesuaian sedikit --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    {{-- Judul Halaman --}}
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-tachometer-alt text-indigo-600 mr-2"></i> 
        <span>Dashboard Super Admin</span>
    </h1>
    
    {{-- Breadcrumb --}}
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li> 
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Dashboard</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- GRADIENT WELCOME HERO --}}
    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 mb-8 shadow-2xl overflow-hidden relative" data-aos="fade-down">
        {{-- Abstract Pattern Overlay --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between text-center sm:text-left">
            <div>
                <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-indigo-100 text-lg opacity-90 max-w-xl">
                    Pantau aktivitas sekolah secara <span class="font-bold text-white">Real-Time</span>. Sistem berjalan optimal.
                </p>
            </div>
            <div class="mt-6 sm:mt-0 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-inner">
                <div class="flex items-center space-x-3 text-white">
                    <div class="text-right">
                        <p class="text-xs text-indigo-200 font-medium uppercase tracking-wider">Jam Server</p>
                        <p class="text-xl font-mono font-bold" id="dashboard-clock">{{ \Carbon\Carbon::now()->format('H:i') }}</p>
                    </div>
                    <i class="fas fa-clock text-3xl opacity-80"></i>
                </div>
            </div>
        </div>
    </div>


    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $stats = [
                ['color' => 'indigo', 'icon' => 'fa-user-graduate', 'label' => 'Total Siswa', 'value' => $totalStudents, 'route' => route('students.index')],
                ['color' => 'cyan',   'icon' => 'fa-chalkboard',    'label' => 'Total Kelas', 'value' => $totalClasses,  'route' => route('classes.index')],
                ['color' => 'emerald','icon' => 'fa-check-circle',  'label' => 'Hadir Hari Ini', 'value' => $attendancePercentage . '%', 'route' => route('report.index')],
                ['color' => 'rose',   'icon' => 'fa-users',         'label' => 'Total User', 'value' => $totalUsers,    'route' => route('admin.users.index')],
            ];
            
            // Handle Pending Users Alert
            if($pendingUsers > 0) {
                 $stats[3] = ['color' => 'orange', 'icon' => 'fa-user-clock', 'label' => 'Menunggu Approval', 'value' => $pendingUsers, 'route' => route('admin.users.index', ['tab' => 'pending'])];
            }
        @endphp

        @foreach($stats as $index => $stat)
            <a href="{{ $stat['route'] }}" class="group relative" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden">
                    {{-- Decorative Blur --}}
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-{{ $stat['color'] }}-50 rounded-full blur-2xl opacity-50 transition-all group-hover:scale-150"></div>
                    
                    <div class="relative z-10 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $stat['label'] }}</p>
                            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-{{ $stat['color'] }}-600 transition-colors">
                                {{ $stat['value'] }}
                            </h3>
                        </div>
                        <div class="bg-{{ $stat['color'] }}-100 p-3 rounded-xl text-{{ $stat['color'] }}-600 group-hover:rotate-12 transition-transform duration-300 shadow-sm">
                            <i class="fas {{ $stat['icon'] }} text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- MAIN CONTENT GRID: Grafik (Lebar) & Timeline (Sempit) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- KOLOM KIRI (LEBAR): Filter & Grafik Analitik --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- FILTER KELAS (Versi dipertegas) --}}
<div class="mb-8">
    <form action="{{ route('admin.dashboard') }}" method="GET" class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between">
        <div class="mb-4 sm:mb-0">
            {{-- Menggunakan ikon fa-chalkboard dan warna text-gray-900 (Hitam Pekat) --}}
            <h4 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider flex items-center">
                <span class="bg-cyan-100 p-2 rounded-lg mr-3 text-cyan-600">
                    <i class="fas fa-chalkboard"></i>
                </span>
                Filter Analitik
            </h4>
            <p class="text-xs text-gray-500 mt-1">Pilih kelas untuk melihat tren spesifik.</p>
        </div>

        {{-- Memberikan background bg-gray-50 dan border lebih gelap agar terlihat --}}
        <div class="w-full sm:w-72 relative">
            <select name="class_code" onchange="this.form.submit()" 
                class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 shadow-sm transition duration-200 hover:bg-white">
                <option value="">Semua Kelas (Global)</option>
                @foreach($classes as $item)
                    <option value="{{ $item->code }}" {{ $classCode == $item->code ? 'selected' : '' }}>
                        Kelas {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

            {{-- 2. GRAFIK BATANG (Versi Stabil Tanpa Bentrok Animasi) --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 flex items-center text-lg">
                <span class="bg-indigo-100 p-2 rounded-lg mr-3 text-indigo-600"><i class="fas fa-chart-bar"></i></span>
                Tren Kehadiran (7 Hari Terakhir)
            </h3>

            {{-- Pastikan pembungkus canvas ini bersih dan berukuran kaku --}}
            <div class="relative" style="height: 380px; min-height: 380px; display: block; width: 100%;"> 
                <canvas id="adminTrendChart"></canvas>
            </div>
        </div>
    </div>


    {{-- KOLOM KANAN (SEMPIT): Live Timeline & Quick Actions --}}
    <div class="lg:col-span-1 space-y-6">
        
        {{-- LIVE TIMELINE --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden" data-aos="fade-left">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-md flex items-center">
                    <span class="bg-indigo-100 p-1.5 rounded-lg mr-2 text-indigo-600"><i class="fas fa-stream text-sm"></i></span>
                    Live Timeline
                </h3>
            </div>
            
            <div class="p-6 max-h-[400px] overflow-y-auto custom-scrollbar">
                @forelse($recentAbsences as $absence)
                    <div class="relative pl-6 pb-6 last:pb-0 border-l-2 border-gray-100 last:border-l-0">
                        <div class="absolute -left-[7px] top-0 bg-white border-2 border-indigo-100 h-3 w-3 rounded-full z-10"></div>
                        <div class="text-xs">
                            <p class="font-bold text-gray-800 truncate">{{ $absence->student->name ?? 'Siswa' }}</p>
                            <p class="text-gray-400 font-mono">{{ $absence->attendance_time->format('H:i') }} • {{ $absence->status }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-xs py-4">Belum ada aktivitas.</p>
                @endforelse
            </div> {{-- ✅ Menutup bagian scroll timeline --}}
        </div> {{-- ✅ Menutup Card Timeline (Sangat Penting! Ini yang tadinya kurang) --}}

        {{-- QUICK ACTIONS (Sekarang dia ada DI LUAR kotak timeline) --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
            <h3 class="font-bold text-sm mb-4 relative z-10">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3 relative z-10">
                <a href="{{ route('admin.absensi.scan') }}" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition border border-white/5">
                    <i class="fas fa-qrcode text-lg mb-1 text-indigo-400"></i>
                    <p class="text-[10px] font-semibold">Scan</p>
                </a>
                <a href="{{ route('report.index') }}" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition border border-white/5">
                    <i class="fas fa-file-export text-lg mb-1 text-emerald-400"></i>
                    <p class="text-[10px] font-semibold">Laporan</p>
                </a>
            </div>
        </div>
    </div> {{-- ✅ Menutup kolom lg:col-span-1 --}}
</div> {{-- ✅ Menutup baris grid utama --}}

@stop

@section('js')
{{-- Load Chart.js dari CDN resmi --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.onload = function () {
    const canvasTrend = document.getElementById('adminTrendChart');
    
    if (canvasTrend) {
        const ctx = canvasTrend.getContext('2d');
        
        try {
            // Fungsi pengubah {} (Object) menjadi [] (Array) murni
            function pastikanArray(data) {
                if (!data) return [];
                return Array.isArray(data) ? data : Object.values(data);
            }

            let dataTanggal = pastikanArray({!! json_encode($grafikTanggal ?? []) !!});
            let dataHadir = pastikanArray({!! json_encode($grafikHadir ?? []) !!});
            let dataTerlambat = pastikanArray({!! json_encode($grafikTerlambat ?? []) !!});
            let dataIzinSakit = pastikanArray({!! json_encode($grafikIzinSakit ?? []) !!});
            let dataAlpha = pastikanArray({!! json_encode($grafikAlpha ?? []) !!});

            if (window.myAttendanceChart) {
                window.myAttendanceChart.destroy();
            }

            // Pengaturan Kelengkungan Sudut Atas Batang Saja (Bawah Tetap Rata)
            const borderStyle = {
                topLeft: 6,
                topRight: 6,
                bottomLeft: 0,
                bottomRight: 0
            };

            window.myAttendanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataTanggal,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: dataHadir,
                            backgroundColor: '#10B981', // Emerald 500
                            borderRadius: borderStyle,
                            maxBarThickness: 16, // Biar ukuran batang stabil & rapi
                        },
                        {
                            label: 'Terlambat',
                            data: dataTerlambat,
                            backgroundColor: '#F59E0B', // Amber 500
                            borderRadius: borderStyle,
                            maxBarThickness: 16,
                        },
                        {
                            label: 'Izin/Sakit',
                            data: dataIzinSakit,
                            backgroundColor: '#3B82F6', // Blue 500
                            borderRadius: borderStyle,
                            maxBarThickness: 16,
                        },
                        {
                            label: 'Alpha',
                            data: dataAlpha,
                            backgroundColor: '#EF4444', // Red 500
                            borderRadius: borderStyle,
                            maxBarThickness: 16,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        // Desain Legenda Atas
                        legend: {
                            position: 'top',
                            align: 'center', // Merapat ke kanan atas biar estetik
                            labels: {
                                usePointStyle: true, // Mengubah kotak kaku jadi lingkaran kecil
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 20,
                                font: {
                                    family: "'Plus Jakarta Sans', 'Inter', sans-serif",
                                    size: 12,
                                    weight: '600'
                                },
                                color: '#64748B' // Slate 500
                            }
                        },
                        // Desain Kotak Detail saat di-Hover (Tooltip)
                        tooltip: {
                            backgroundColor: '#0F172A', // Slate 900 (Mewah)
                            padding: 12,
                            cornerRadius: 12, // Melengkung halus
                            usePointStyle: true,
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 13,
                                weight: '700'
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12
                            }
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false }, // Sembunyikan garis vertikal biar clean
                            ticks: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#94A3B8' // Slate 400
                            }
                        },
                        y: { 
                            beginAtZero: true, 
                            grid: {
                                color: '#F1F5F9', // Garis horizontal super tipis & lembut
                                drawBorder: false
                            },
                            ticks: { 
                                stepSize: 1, 
                                precision: 0,
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11
                                },
                                color: '#94A3B8'
                            } 
                        }
                    }
                }
            });
            
            console.log("Grafik versi premium sukses digambar!");
        } catch (error) {
            console.error("Gagal memoles grafik:", error);
        }
    }
};
</script>
@stop