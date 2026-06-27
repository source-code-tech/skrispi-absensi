@extends('layouts.adminlte')

@section('title', 'Laporan Absensi')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-file-alt text-purple-600 mr-3"></i>
            Laporan Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Filter dan unduh laporan kehadiran siswa.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 lg:gap-6">
        
        {{-- CARD KIRI: FORM FILTER (3 Kolom) --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-filter mr-2 text-purple-600"></i> Filter Laporan
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('report.generate') }}" method="GET" id="filterForm" class="space-y-6">
                        @php
                            $classes = $classes ?? []; 
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                            
                            $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                            $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                        @endphp
        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Filter Kelas --}}
                            <div>
                                <label for="class_code" class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas</label>
                                <div class="relative">
                                    <select name="class_code" id="class_code" class="w-full select2-form-control">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->code }}" {{ old('class_code') == $class->code ? 'selected' : '' }}>
                                                {{ $class->code }} (Tingkat {{ $class->grade }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('class_code') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Dari Tanggal --}}
                            <div>
                                <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal <span class="text-red-500">*</span></label>
                                <div class="relative">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="start_date" id="start_date" 
                                        class="pl-10 {{ $errors->has('start_date') ? $inputErrorClass : $inputClass }}" 
                                        value="{{ old('start_date', $currentMonthStart) }}" 
                                        required>
                                </div>
                                @error('start_date') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
        
                            {{-- Sampai Tanggal --}}
                            <div>
                                <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-400"></i>
                                    </div>
                                    <input type="date" name="end_date" id="end_date" 
                                        class="pl-10 {{ $errors->has('end_date') ? $inputErrorClass : $inputClass }}" 
                                        value="{{ old('end_date', $currentDate) }}" 
                                        required>
                                </div>
                                @error('end_date') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    
                        {{-- ganti bagian div tombol yang ada di bawah form --}}
                        <div class="pt-6 border-t border-gray-100 mt-6 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 justify-end items-center">
                            
                            {{-- Tombol Tampilkan Laporan --}}
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg 
                                        text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                                        focus:ring-4 focus:ring-purple-500/50 transition duration-150 transform hover:-translate-y-0.5"
                                    id="submitFilterBtn">
                                <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                            </button>

                            {{-- Tombol Unduh Rekap Excel --}}
                            <button type="button" id="btnRekapExcel"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg 
                                        text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 
                                        focus:ring-4 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5">
                                <i class="fas fa-file-excel mr-2"></i> Unduh Rekap Absensi
                            </button>

                        </div>
                     </form>
                </div>
            </div>
        </div>

        {{-- CARD KANAN: PANDUAN (1 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
             <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-info-circle mr-2 text-indigo-500"></i> Panduan</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    <div class="space-y-3">
                        <div class="flex items-start">
                             <div class="bg-purple-100 p-2 rounded-lg text-purple-600 mr-3 flex-shrink-0">
                                <i class="fas fa-filter"></i>
                             </div>
                            <div>
                                <strong class="block text-gray-800">Filter Data</strong>
                                <span class="text-xs">Pilih kelas dan rentang tanggal untuk melihat data spesifik.</span>
                            </div>
                        </div>
                        <div class="flex items-start">
                             <div class="bg-green-100 p-2 rounded-lg text-green-600 mr-3 flex-shrink-0">
                                <i class="fas fa-file-excel"></i>
                             </div>
                            <div>
                                <strong class="block text-gray-800">Export Excel</strong>
                                <span class="text-xs">Unduh rekap absensi dalam format .xlsx untuk diolah lebih lanjut.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
       // Initialize Select2 with Bootstrap 4 theme
        $('.select2-form-control').select2({ theme: 'bootstrap4' });
        
        
    });

    $('#btnRekapExcel').on('click', function () {
    const startDate = $('#start_date').val();
    const endDate   = $('#end_date').val();
    const classId   = $('#class_code').val();

    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Filter Belum Lengkap',
            text: 'Harap isi tanggal awal dan tanggal akhir terlebih dahulu.',
            confirmButtonColor: '#7c3aed',
        });
        return;
    }

    const params = new URLSearchParams({
        start_date: startDate,
        end_date:   endDate,
        ...(classId && { class_code: classId }),
    });

    window.location.href = '{{ route("report.export.excel-rekap") }}?' + params.toString();
});
</script>
@stop

@section('css')
<style>
/* CSS Override total biar Select2 rapi & panah presisi di dalam kotak */
.select2-container--bootstrap4 .select2-selection--single {
    height: 50px !important; 
    border: 1px solid #e5e7eb !important; 
    border-radius: 0.75rem !important; 
    background-color: #f9fafb !important; 
    position: relative !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 48px !important; 
    padding-left: 1rem !important;
    color: #1f2937 !important;
}

/* 🌟 KUNCI PERBAIKAN: Memaksa panah dropdown masuk & center di dalam kotak 🌟 */
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 50px !important;      /* Samakan tinggi dengan box biar seimbang */
    top: 0 !important;             /* Reset posisi dari atas biar gak melayang keluar */
    right: 12px !important;        /* Kasih space/jarak aman dari dinding kanan kotak */
    position: absolute !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--bootstrap4 .select2-selection--single:focus {
    border-color: #a855f7 !important; 
    box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1) !important;
}
</style>
@stop