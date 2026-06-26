@extends('layouts.adminlte')

@section('title', 'Dashboard Orang Tua')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-home text-purple-600 mr-3"></i>
            Dashboard
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Ringkasan kehadiran dan aktivitas anak Anda.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Dashboard</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="space-y-8">
        
        @if(!$parentRecord)
            {{-- KASUS: AKUN BELUM TERHUBUNG --}}
            <div class="bg-white rounded-3xl shadow-xl border border-red-100 overflow-hidden">
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-50 text-red-500 mb-6">
                         <i class="fas fa-user-slash fa-4x"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Akun Belum Terhubung</h3>
                    <p class="text-gray-500 max-w-lg mx-auto mb-6">
                        Mohon hubungi <span class="font-bold text-gray-800">Admin Sekolah</span> atau <span class="font-bold text-gray-800">Wali Kelas</span> untuk menghubungkan akun Anda dengan data siswa.
                    </p>
                    <a href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 transition duration-150 shadow-lg hover:shadow-xl">
                        <i class="fab fa-whatsapp mr-2"></i> Hubungi Admin
                    </a>
                </div>
            </div>
        @else
            
            {{-- WELCOME BANNER --}}
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl shadow-xl overflow-hidden relative mb-8">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
                
                <div class="p-8 flex flex-col md:flex-row items-center justify-between relative z-10">
                    <div class="text-white mb-6 md:mb-0">
                        <h2 class="text-2xl font-bold mb-2">Selamat Datang, Bapak/Ibu {{ $parentRecord->name }}!</h2>
                        <p class="text-indigo-100 opacity-90">Pantau kehadiran putra/putri Anda secara realtime melalui dashboard ini.</p>
                        
                        <div class="mt-6 flex flex-wrap gap-2">
                             @foreach($parentRecord->students as $student)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-sm font-medium backdrop-blur-sm border border-white/10">
                                    <i class="fas fa-user-graduate mr-2 text-yellow-300"></i> {{ $student->name }} ({{ $student->class->name ?? 'N/A' }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                         <img src="{{ asset('images/parent-illustration.svg') }}" onerror="this.src='https://illustrations.popsy.co/amber/working-from-anywhere.svg'" class="h-32 w-auto drop-shadow-lg" alt="Illustration">
                    </div>
                </div>
            </div>

            {{-- 💡 WIDGET WALI KELAS --}}
            @if($parentRecord->students->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($parentRecord->students as $student)
                    @if($student->class && $student->class->homeroomTeacher && $student->class->homeroomTeacher->user)
                        <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100 flex items-center space-x-4 hover:shadow-xl transition duration-300">
                             {{-- Avatar Wali Kelas --}}
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full bg-indigo-100 border-2 border-indigo-50 flex items-center justify-center text-indigo-500 overflow-hidden">
                                     @if($student->class->homeroomTeacher->user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $student->class->homeroomTeacher->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                     @else
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                     @endif
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-indigo-500 uppercase tracking-wide mb-1">
                                    Wali Kelas {{ $student->class->name }}
                                </p>
                                <h5 class="text-lg font-bold text-gray-900 truncate">
                                    {{ $student->class->homeroomTeacher->user->name }}
                                </h5>
                                <p class="text-sm text-gray-500 truncate mb-2">
                                    {{ $student->name }}
                                </p>
                                
                                {{-- Tombol WA --}}
                                @if($student->class->homeroomTeacher->user->phone_number ?? $student->class->homeroomTeacher->user->no_hp)
                                    @php
                                        // Normalisasi nomor HP (08xx -> 628xx)
                                        $phone = $student->class->homeroomTeacher->user->phone_number ?? $student->class->homeroomTeacher->user->no_hp;
                                        if(substr($phone, 0, 1) == '0') {
                                            $phone = '62' . substr($phone, 1);
                                        }
                                        $message = "Assalamu'alaikum, saya orang tua dari " . $student->name . " kelas " . $student->class->name . "...";
                                    @endphp
                                    <a href="https://wa.me/{{ $phone }}?text={{ urlencode($message) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-lg hover:bg-green-100 transition">
                                        <i class="fab fa-whatsapp mr-2 text-lg"></i> Hubungi Guru
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">No. HP tidak tersedia</span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- 💡 BAGIAN PENGUMUMAN --}}
            @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-amber-100 text-amber-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-bullhorn"></i>
                        </span>
                        Pengumuman Terbaru
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($announcements as $announcement)
                            <div class="bg-white rounded-2xl p-5 shadow-lg border-l-4 border-amber-400 relative overflow-hidden group hover:shadow-xl transition duration-300">
                                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition duration-300">
                                    <i class="fas fa-bullhorn fa-3x text-amber-500"></i>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                                            {{ $announcement->created_at->diffForHumans() }}
                                        </span>
                                        @if($announcement->target_type == 'class')
                                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">Kls {{ $announcement->class->name ?? '' }}</span>
                                        @endif
                                    </div>
                                    <h5 class="text-lg font-bold text-gray-900 mb-2">{{ $announcement->title }}</h5>
                                    <p class="text-sm text-gray-600 leading-relaxed mb-3 line-clamp-2">
                                        {{ $announcement->content }}
                                    </p>
                                    <button onclick="Swal.fire({ title: '{{ $announcement->title }}', html: '{{ str_replace(PHP_EOL, '<br>', addslashes($announcement->content)) }}', icon: 'info', confirmButtonText: 'Tutup' })" 
                                            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center">
                                        Baca Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- 💡 EMPTY STATE PENGUMUMAN --}}
                <div class="mb-8 p-6 bg-white rounded-3xl shadow-sm border border-gray-100 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-24 h-24 bg-amber-50 rounded-full blur-xl opacity-50"></div>
                     <div class="relative z-10 flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold">Belum Ada Pengumuman</h4>
                        <p class="text-sm text-gray-500 max-w-sm">Saat ini belum ada informasi atau pengumuman baru dari sekolah.</p>
                    </div>
                </div>
            @endif

            {{-- BAGIAN 1: STATISTIK CARD --}}
            <div>
                 <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    Statistik Kehadiran
                </h4>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @php
                        $statCards = [
                            ['label' => 'Terlambat', 'count' => $totalSIA['Terlambat'] ?? 0, 'icon' => 'fas fa-clock', 'color' => 'from-amber-400 to-orange-500', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                            ['label' => 'Sakit', 'count' => $totalSIA['Sakit'] ?? 0, 'icon' => 'fas fa-procedures', 'color' => 'from-blue-400 to-cyan-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                            ['label' => 'Izin', 'count' => $totalSIA['Izin'] ?? 0, 'icon' => 'fas fa-envelope-open-text', 'color' => 'from-indigo-400 to-purple-500', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                            ['label' => 'Alpha', 'count' => $totalSIA['Alpha'] ?? 0, 'icon' => 'fas fa-times-circle', 'color' => 'from-red-400 to-rose-500', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
                        ];
                    @endphp
                    
                    @foreach($statCards as $card)
                        <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100 hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                                    <h4 class="text-3xl font-extrabold text-gray-800 mt-1 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r {{ $card['color'] }} transition-colors duration-300">
                                        {{ $card['count'] }}
                                    </h4>
                                </div>
                                <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center text-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    <i class="{{ $card['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- BAGIAN 2: RIWAYAT ABSENSI --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-indigo-500"></i> Riwayat Absensi (30 Hari)
                    </h3>
                    <a href="{{ route('orangtua.report.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition duration-150">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pulang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($absences as $absence)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800">{{ $absence->student->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $absence->student->class->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    {{ $absence->attendance_time->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $absence->attendance_time->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    @if($absence->checkout_time)
                                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $absence->checkout_time->format('H:i') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusStyles = [
                                            'Hadir' => 'bg-green-100 text-green-700',
                                            'Terlambat' => 'bg-amber-100 text-amber-700',
                                            'Absen' => 'bg-red-100 text-red-700',
                                            'Izin' => 'bg-blue-100 text-blue-700',
                                            'Sakit' => 'bg-purple-100 text-purple-700',
                                        ];
                                        $style = $statusStyles[$absence->status] ?? 'bg-gray-100 text-gray-600';
                                        
                                        // Override jika sudah pulang
                                        if($absence->checkout_time) {
                                            $style = 'bg-teal-100 text-teal-700';
                                        }
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $style }}">
                                        {{ $absence->status }}{{ $absence->checkout_time ? ' (Selesai)' : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('orangtua.absensi.show_detail', $absence->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition duration-150" title="Lihat Detail">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="far fa-calendar-times fa-3x mb-3"></i>
                                        <span class="text-sm font-medium">Belum ada riwayat absensi dalam 30 hari terakhir.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        @endif
    </div>
@stop

@section('css')
<style>
/* Gradient Text Utility */
.bg-clip-text { -webkit-background-clip: text; background-clip: text; }
</style>
@stop

@section('js')
<script>
    // Script Auto-Dismiss Alert (jika ada)
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);
    });
</script>
@stop