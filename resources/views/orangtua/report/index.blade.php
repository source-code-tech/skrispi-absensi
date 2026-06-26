@extends('layouts.adminlte')

@section('title', 'Riwayat Absensi Lengkap')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-file-alt text-purple-600 mr-3"></i>
            Riwayat Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Laporan kehadiran putra/putri Anda dalam 30 hari terakhir.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
                {{-- CARD HEADER --}}
                <div class="p-6 border-b border-gray-100 bg-purple-50/30 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history mr-2 text-purple-600"></i> Data Kehadiran
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Berikut adalah data kehadiran yang tercatat dalam 30 hari terakhir.
                        </p>
                    </div>
                    
                    {{-- Tombol Export --}}
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('orangtua.report.export', ['format' => 'excel']) }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-green-500 hover:bg-green-600 transition duration-150 transform hover:-translate-y-0.5">
                            <i class="fas fa-file-excel mr-2"></i> Export Excel
                        </a>
                        <a href="{{ route('orangtua.report.export', ['format' => 'pdf']) }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-red-500 hover:bg-red-600 transition duration-150 transform hover:-translate-y-0.5">
                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                        </a>
                    </div>
                </div>
        
        <div class="p-0">
            @if($absences->isEmpty())
                <div class="p-12 text-center text-gray-400">
                     <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-calendar-times fa-2x text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum Ada Data</h3>
                    <p class="mt-1">Tidak ada riwayat absensi dalam 30 hari terakhir.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hari</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pulang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                           @php
    $grouped = $absences->groupBy(fn($a) => $a->student->name ?? 'N/A');
@endphp

@foreach($grouped as $studentName => $studentAbsences)
    {{-- Header per Anak --}}
    <tr class="bg-indigo-50/50">
        <td colspan="6" class="px-6 py-3">
            <div class="flex items-center">
                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                    <i class="fas fa-user-graduate text-indigo-600 text-xs"></i>
                </div>
                <div>
                    <span class="text-sm font-bold text-indigo-700">{{ $studentName }}</span>
                    <span class="text-xs text-indigo-400 ml-2">
                        {{ $studentAbsences->first()->student->class?->name ?? '' }}
                    </span>
                </div>
                <span class="ml-auto text-xs font-semibold text-indigo-400 bg-indigo-100 px-2 py-1 rounded-full">
                    {{ $studentAbsences->count() }} record
                </span>
            </div>
        </td>
    </tr>

    {{-- Data Absensi per Anak --}}
    @foreach($studentAbsences as $absence)
    <tr class="hover:bg-gray-50/50 transition duration-150">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-600 pl-10">
                {{ $absence->attendance_time->translatedFormat('l') }}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
            {{ $absence->attendance_time->translatedFormat('d F Y') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
            <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                {{ $absence->attendance_time->format('H:i') }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
            @if($absence->checkout_time)
                <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                    {{ $absence->checkout_time->format('H:i') }}
                </span>
            @else
                <span class="text-gray-300">-</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @php
                $statusStyles = [
                    'Hadir'     => 'bg-green-100 text-green-700',
                    'Terlambat' => 'bg-amber-100 text-amber-700',
                    'Izin'      => 'bg-blue-100 text-blue-700',
                    'Sakit'     => 'bg-cyan-100 text-cyan-700',
                    'Alpha'     => 'bg-red-100 text-red-700',
                ];
                $style = $statusStyles[$absence->status] ?? 'bg-gray-100 text-gray-600';
                if($absence->checkout_time) $style = 'bg-teal-100 text-teal-700';
            @endphp
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $style }}">
                {{ $absence->status }}{{ $absence->checkout_time ? ' (Selesai)' : '' }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <a href="{{ route('orangtua.absensi.show_detail', $absence->id) }}" 
               class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition duration-150">
                <i class="fas fa-chevron-right"></i>
            </a>
        </td>
    </tr>
    @endforeach
@endforeach
                        </tbody>
                    </table>
                </div>
                
               
            @endif
        </div>
    </div>
@stop

@section('js')
<script>
    // Auto-dismiss alerts (jika ada)
    setTimeout(function() {
         $('.alert').fadeOut(400);
    }, 5000);
</script>
@stop