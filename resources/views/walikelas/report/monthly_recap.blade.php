@extends('layouts.adminlte')

@section('title', 'Rekap Absensi Bulanan')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Rekap Absensi Bulanan</h2>
            <p class="text-sm text-gray-500 mt-1">
                Kelas: <span class="font-bold text-indigo-600">{{ $class->name }}</span> 
                • Periode: <span class="font-bold text-gray-800">{{ $currentMonth }}</span>
            </p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Rekap Bulanan</span>
        </nav>
    </div>

    {{-- CARD FILTER & TABLE --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        
        {{-- Toolbar Row --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            
            {{-- Filter Form --}}
            <form action="{{ route('walikelas.report.monthly_recap') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select name="month" class="pl-4 pr-10 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-gray-700 bg-white shadow-sm appearance-none cursor-pointer hover:bg-gray-50 transition" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonthNum == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="relative">
                    @php
                        $currentYear = \Carbon\Carbon::now()->year;
                        $selectedYear = request('year', $currentYear);
                    @endphp
                    <select name="year" class="pl-4 pr-10 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-gray-700 bg-white shadow-sm appearance-none cursor-pointer hover:bg-gray-50 transition" onchange="this.form.submit()">
                        @for($y = $currentYear - 2; $y <= $currentYear + 3; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>
            </form>

            {{-- Export Button --}}
            <a href="{{ route('walikelas.report.monthly_recap.export', ['month' => $currentMonthNum, 'year' => $selectedYear]) }}" 
               class="inline-flex items-center px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-0.5"
               target="_blank">
                <i class="fas fa-file-excel mr-2"></i> Download Excel
            </a>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto relative">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-800 text-white sticky top-0 z-20 shadow-md">
                    <tr>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider sticky left-0 z-30 bg-gray-800 border-r border-gray-700 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.3)]">Nama Siswa</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider sticky left-[200px] z-30 bg-gray-800 border-r border-gray-700 min-w-[120px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.3)]">NISN</th>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $date = \Carbon\Carbon::createFromDate($selectedYear, $currentMonthNum, $i);
                                $isWeekend = $date->isWeekend();
                                $bgHeader = $isWeekend ? 'bg-gray-700 text-red-300' : '';
                            @endphp
                            <th class="p-2 text-center text-xs font-bold w-10 border-r border-gray-700 {{ $bgHeader }}">
                                {{ $i }}
                                <div class="text-[9px] opacity-60 font-normal mt-0.5">{{ $date->minDayName }}</div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($recapData as $studentId => $data)
                        <tr class="hover:bg-gray-50 transition duration-150 group">
                            <td class="p-3 sticky left-0 z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 font-bold text-gray-800 text-sm shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                {{ $data['name'] }}
                            </td>
                            <td class="p-3 sticky left-[200px] z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 text-gray-600 text-sm font-medium shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                {{ $data['nisn'] ?? '-' }}
                            </td>
                            
                            @for($i = 1; $i <= $daysInMonth; $i++)
                                @php
                                    $status = $data['status_by_day'][$i] ?? 'N/A';
                                    $date = \Carbon\Carbon::createFromDate($selectedYear, $currentMonthNum, $i);
                                    $isWeekend = $date->isWeekend();

                                    $cellStyle = match($status) {
                                        'Hadir'     => 'text-green-500 text-2xl leading-none',
                                        'Terlambat' => 'bg-amber-100 text-amber-700 font-bold',
                                        'Sakit'     => 'bg-cyan-100 text-cyan-700 font-bold',
                                        'Izin'      => 'bg-blue-100 text-blue-700 font-bold',
                                        'Alpha'     => 'bg-red-100 text-red-700 font-bold',
                                        default     => $isWeekend ? 'bg-gray-50' : ''
                                    };

                                    $displayChar = match($status) {
                                        'Hadir'     => '•',
                                        'Terlambat' => 'T',
                                        'Sakit'     => 'S',
                                        'Izin'      => 'I',
                                        'Alpha'     => 'A',
                                        default     => ''
                                    };
                                @endphp
                                <td class="p-1 text-center border-r border-gray-50 {{ $isWeekend ? 'bg-gray-50' : '' }}">
                                    @if($status != 'N/A')
                                        <div class="w-8 h-8 flex items-center justify-center rounded-lg mx-auto text-xs {{ $cellStyle }}">
                                            {{ $displayChar }}
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="p-8 text-center text-gray-500 italic">
                                Tidak ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- LEGEND --}}
        <div class="bg-gray-50 border-t border-gray-200 p-4">
            <div class="flex flex-wrap gap-4 justify-center text-xs font-bold text-gray-600">
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Hadir (•)</div>
                <div class="flex items-center"><span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mr-2">T</span> Terlambat</div>
                <div class="flex items-center"><span class="w-6 h-6 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center mr-2">S</span> Sakit</div>
                <div class="flex items-center"><span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mr-2">I</span> Izin</div>
                <div class="flex items-center"><span class="w-6 h-6 rounded-lg bg-red-100 text-red-700 flex items-center justify-center mr-2">A</span> Alpha</div>
            </div>
        </div>
    </div>
</div>
@stop