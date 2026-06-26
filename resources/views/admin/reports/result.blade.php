@extends('layouts.adminlte')

@section('title', 'Hasil Laporan Absensi')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
            Hasil Laporan
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('report.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Laporan</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Hasil</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- CARD HEADER --}}
        <div class="p-6 border-b border-gray-100 bg-purple-50/30 flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <div>
                 <h3 class="text-lg font-bold text-gray-800">
                    Laporan Absensi 
                    @if($class)
                        Kelas <span class="text-purple-600">{{ $class->name }}</span>
                    @else
                        <span class="text-purple-600">Semua Kelas</span>
                    @endif
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="far fa-calendar-alt mr-1"></i> Periode: 
                    <span class="font-medium text-gray-700">{{ $startDate->format('d/m/Y') }}</span> s/d <span class="font-medium text-gray-700">{{ $endDate->format('d/m/Y') }}</span>
                </p>
            </div>
            
            {{-- Tombol Aksi (Export) --}}
            <div class="flex space-x-3 mt-4 lg:mt-0">
                
            
                
                {{-- EXPORT PDF --}}
                <form action="{{ route('report.export.pdf') }}" method="GET" class="inline-flex" target="_blank">
                    <input type="hidden" name="class_code" value="{{ request('class_code') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-red-500 hover:bg-red-600 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </form>

                {{-- EXPORT EXCEL --}}
                <form action="{{ route('report.export.excel') }}" method="GET" class="inline-flex">
                    <input type="hidden" name="class_code" value="{{ request('class_code') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-green-500 hover:bg-green-600 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </form>

                
                <a href="{{ route('report.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <div class="p-0"> {{-- Padding 0 untuk table full width --}}
            @if($absences->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-block p-4 rounded-full bg-indigo-50 text-indigo-500 mb-4">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tidak Ada Data Ditemukan</h3>
                    <p class="text-gray-500 mt-2">Tidak ada data absensi yang sesuai dengan filter periode dan kelas yang Anda pilih.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($absences as $absence)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800">{{ $absence->student->name ?? 'Siswa Dihapus' }}</div>
                                    <div class="text-xs text-gray-500">NIS: {{ $absence->student->nis ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $absence->student->class->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $absence->attendance_time->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $absence->attendance_time->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusStyles = [
                                            'Hadir'     => 'bg-green-100 text-green-700',
                                            'Terlambat' => 'bg-amber-100 text-amber-700',
                                            'Alpha'     => 'bg-red-100 text-red-700',
                                            'Absen'     => 'bg-red-100 text-red-700',
                                            'Izin'      => 'bg-blue-100 text-blue-700',
                                            'Sakit'     => 'bg-cyan-100 text-cyan-700',
                                        ];
                                        $style = $statusStyles[$absence->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $style }}">
                                        {{ $absence->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($absence->status == 'Terlambat')
                                        <span class="text-amber-600 font-medium"><i class="fas fa-clock mr-1"></i> {{ $absence->late_duration }} menit</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@stop