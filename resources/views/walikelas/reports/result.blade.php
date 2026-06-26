@extends('layouts.adminlte')

@section('title', 'Laporan Absensi: ' . $class->name)

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Hasil Laporan</h2>
            <p class="text-sm text-gray-500 mt-1">
                Periode: <span class="font-bold text-gray-800">{{ $startDate->format('d/m/Y') }}</span> s/d <span class="font-bold text-gray-800">{{ $endDate->format('d/m/Y') }}</span>
            </p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.report.index') }}" class="text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-arrow-left mr-1"></i> Filter Ulang
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Hasil</span>
        </nav>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- TOOLBAR --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <h3 class="font-bold text-gray-800 flex items-center">
                <span class="w-2 h-6 bg-indigo-500 rounded-full mr-3"></span>
                Data Absensi Kelas {{ $class->name }}
            </h3>
            
            <div class="flex space-x-3">
                {{-- EXPORT EXCEL --}}
               <form action="{{ route('walikelas.report.export.excel') }}" method="GET" target="_blank">
                    <input type="hidden" name="class_code" value="{{ $class->code }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-0.5">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </form>
                
                {{-- EXPORT PDF --}}
               <form action="{{ route('walikelas.report.export.pdf') }}" method="GET" target="_blank">
                    <input type="hidden" name="class_code" value="{{ $class->code }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-500/20 transition transform hover:-translate-y-0.5">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </form>
            </div>
        </div>
        
        <div class="p-0">
            @if($absences->isEmpty())
                <div class="flex flex-col items-center justify-center py-16">
                    <div class="bg-indigo-50 w-20 h-20 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-clipboard-check text-indigo-300 text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800">Tidak Ada Data</h4>
                    <p class="text-gray-500 mt-2">Belum ada aktivitas absensi pada periode ini.</p>
                </div>
            @else
                <div class="overflow-x-auto"> 
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-center w-12">#</th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Jam Masuk</th> 
                                <th class="px-6 py-4">Jam Pulang</th> 
                                <th class="px-6 py-4">Status</th> 
                                <th class="px-6 py-4 text-center">Ket.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($absences as $absence)
                            @php
                                $statusBadge = match($absence->status) {
                                    'Hadir' => 'bg-green-100 text-green-700 border-green-200',
                                    'Terlambat' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Sakit' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                                    'Izin' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Alpha' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150 group">
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-400 group-hover:text-indigo-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $absence->student->name }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                    {{ $absence->attendance_time->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-700">
                                    @if(in_array($absence->status, ['Sakit', 'Izin', 'Alpha']))
                                        <span class="text-gray-400">-</span>
                                    @else
                                        {{ $absence->attendance_time ? $absence->attendance_time->format('H:i') : '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-700">
                                    @if(in_array($absence->status, ['Sakit', 'Izin', 'Alpha']))
                                        <span class="text-gray-400">-</span>
                                    @elseif($absence->checkout_time)
                                        {{ $absence->checkout_time->format('H:i') }}
                                    @else
                                        <span class="text-gray-400 text-xs italic">Belum</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-lg border {{ $statusBadge }}">
                                        {{ $absence->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    @if ($absence->status == 'Terlambat')
                                        <span class="text-amber-600 font-bold text-xs">+{{ $absence->late_duration }}m</span>
                                    @elseif($absence->notes)
                                        <span class="text-xs truncate max-w-[100px] inline-block" title="{{ $absence->notes }}">{{ $absence->notes }}</span>
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
</div>
@stop

@section('css')
<style>
/* Custom overrides if necessary */
</style>
@stop