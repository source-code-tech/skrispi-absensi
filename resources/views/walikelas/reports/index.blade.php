@extends('layouts.adminlte')

@section('title', 'Filter Laporan Absensi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Laporan Absensi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelas: <span class="font-bold text-indigo-600">{{ $class->name }}</span> (Tingkat {{ $class->grade }})</p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Filter Laporan</span>
        </nav>
    </div>

    {{-- CARD FILTER --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-filter text-indigo-500 mr-2"></i> Filter Periode
            </h3>
        </div>
        
        <div class="p-8">
            <div class="bg-indigo-50 rounded-2xl p-6 mb-8 flex items-start space-x-4 border border-indigo-100">
                <div class="bg-white p-3 rounded-xl shadow-sm text-indigo-600">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-indigo-900">Pilih Rentang Waktu</h4>
                    <p class="text-sm text-indigo-700 mt-1">
                        Sistem akan menampilkan rekap kehadiran seluruh siswa di kelas <b>{{ $class->name }}</b> sesuai periode yang Anda tentukan.
                    </p>
                </div>
            </div>

            <form action="{{ route('walikelas.report.generate') }}" method="GET" id="reportFilterForm" class="space-y-8">
                
                @php
                    $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                    {{-- Connector Line (Desktop) --}}
                    <div class="hidden md:block absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-0">
                        <i class="fas fa-arrow-right text-gray-300 text-2xl"></i>
                    </div>

                    {{-- Tanggal Awal --}}
                    <div class="relative z-10">
                        <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="date" name="start_date" id="start_date" 
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" 
                                    value="{{ old('start_date', $currentMonthStart) }}" 
                                    required>
                        </div>
                    </div>

                    {{-- Sampai Tanggal --}}
                    <div class="relative z-10">
                        <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" name="end_date" id="end_date" 
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" 
                                    value="{{ old('end_date', $currentDate) }}" 
                                    required>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                    <a href="{{ route('walikelas.dashboard') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" id="submitFilterBtn" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1 flex items-center">
                        <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Background Decor --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#reportFilterForm').on('submit', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            if (startDate && endDate) {
                $('#submitFilterBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat Data...');
            }
        });
    });
</script>
@stop