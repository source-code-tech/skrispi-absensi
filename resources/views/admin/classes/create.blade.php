@extends('layouts.adminlte')

@section('title', 'Tambah Kelas Baru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-plus text-indigo-600 mr-2"></i>
        <span>Tambah Kelas Baru</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('classes.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Tata Letak Grid (2/3 dan 1/3) — sama persis dengan edit.blade.php --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">

        {{-- KOLOM KIRI: FORM UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100" id="classFormCard">

                {{-- CARD HEADER --}}
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chalkboard mr-2 text-indigo-500"></i> Form Tambah Kelas
                    </h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('classes.index') }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-lg 
                                  shadow-sm text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="p-6">
                    <form action="{{ route('classes.store') }}" method="POST" id="classForm">
                        @csrf

                        @php
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        @endphp

                        {{-- ALERT ERROR --}}
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-5" role="alert">
                                <h5 class="font-bold text-base flex items-center mb-2"><i class="icon fas fa-ban mr-2"></i> Terjadi Kesalahan!</h5>
                                <ul class="mb-0 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- 1. Nama Kelas --}}
                        <div class="mb-5">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Kelas <span class="text-red-600">*</span>
                            </label>
                            @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                            <input type="text"
                                name="name"
                                id="name"
                                class="{{ $baseInputClass }} border {{ $nameStatusClass }}"
                                value="{{ old('name') }}"
                                placeholder="Contoh: 1A, 2B, 6C"
                                required
                                autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Nama Kelas harus unik. </small>
                        </div>

                        {{-- 2. Tingkat & Jurusan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Tingkat --}}
                            <div class="mb-5">
                                <label for="grade" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Tingkat / Kelas <span class="text-red-600">*</span>
                                </label>
                                @php $gradeStatusClass = $errors->has('grade') ? $errorClass : $normalClass; @endphp
                                <select name="grade"
                                    id="grade"
                                    class="select2bs4 border {{ $gradeStatusClass }}"
                                    required
                                    style="width: 100%;">
                                    <option value="">Pilih Tingkat (1-6)</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ old('grade') == $i ? 'selected' : '' }}>
                                            Kelas {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('grade')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jurusan (readonly, mutlak UMUM) --}}
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jurusan</label>
                                <input type="text"
                                    value="UMUM"
                                    class="border rounded px-3 py-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed"
                                    readonly>
                                <input type="hidden" name="major" value="UMUM">
                                @error('major')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                                <small class="mt-1 text-xs text-gray-500 block">Jurusan bersifat tetap.</small>
                            </div>
                        </div>

                        {{-- 3. Jam Pulang Kelas --}}
                        <div class="mb-5">
                            <label for="dismissal_time" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jam Pulang Kelas <span class="text-red-600">*</span>
                            </label>
                            @php $dismissalTimeStatusClass = $errors->has('dismissal_time') ? $errorClass : $normalClass; @endphp
                            <input type="time"
                                name="dismissal_time"
                                id="dismissal_time"
                                class="{{ $baseInputClass }} border {{ $dismissalTimeStatusClass }}"
                                value="{{ old('dismissal_time') }}">
                            @error('dismissal_time')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">
                                Isi jam pulang khusus untuk kelas ini. Contoh: 10:30. Jika dikosongkan, sistem memakai jam pulang umum.
                            </small>
                        </div>

                        {{-- 4. Keterangan --}}
                        <div class="mb-5">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            @php $descStatusClass = $errors->has('description') ? $errorClass : $normalClass; @endphp
                            <textarea name="description"
                                id="description"
                                class="{{ $baseInputClass }} border {{ $descStatusClass }}"
                                rows="3"
                                placeholder="Keterangan tambahan tentang kelas...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">
                                <span id="char-count">0</span>/500 karakter
                            </small>
                        </div>

                        {{-- 5. Tombol Aksi --}}
                        <div class="mt-6 flex space-x-3 border-t border-gray-100 pt-5">
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                           text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 
                                           transition duration-150 transform hover:-translate-y-0.5"
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Simpan Data
                            </button>
                            <a href="{{ route('classes.index') }}"
                               class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-base font-medium rounded-lg 
                                      text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: INFORMASI & PANDUAN (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">

                {{-- CARD HEADER --}}
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Informasi & Panduan
                    </h3>
                </div>

                {{-- CARD BODY --}}
                <div class="p-6">

                    {{-- Tips Card --}}
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-lg">
                        <h6 class="text-base font-bold mb-3 flex items-center text-indigo-800">
                            <i class="fas fa-lightbulb mr-2 text-indigo-500"></i>Tips Panduan:
                        </h6>
                        
                        <div class="space-y-2.5">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700">Tingkat yang tersedia: <span class="font-semibold text-gray-800">Kelas 1 sampai 6 (MI)</span>.</p>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700">Jurusan otomatis diisi <span class="font-semibold text-gray-800">UMUM</span> untuk semua kelas MI.</p>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700">Nama Kelas harus <span class="font-semibold text-gray-800">unik</span> (tidak boleh ada yang sama).</p>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700"><span class="font-semibold text-gray-800">Jam Pulang Kelas wajib diisi</span> karena jadwal kepulangan tiap tingkatan kelas bisa berbeda.</p>
                            </div>
                        </div>
                    </div>

                   {{-- Statistik --}}
                    <small class="text-xs text-gray-500 font-semibold uppercase tracking-wider block mb-2 border-b pb-1">Statistik Kelas Saat Ini:</small>

                    @php
                        $totalClass = \App\Models\ClassModel::count();
                    @endphp

                    <div class="space-y-2 mt-3">
                        <div class="flex justify-between items-center text-sm p-3 rounded-lg bg-indigo-50/50 border border-indigo-100">
                            <span class="flex items-center text-gray-700 font-medium">
                                <i class="fas fa-graduation-cap mr-2 w-4 text-indigo-500"></i> Total Kelas Saat Ini:
                            </span>
                            <strong class="font-bold text-lg text-indigo-900">{{ $totalClass }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {

    // Inisialisasi Select2 — sama seperti edit.blade.php
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih',
        allowClear: true
    });

    // Hitung karakter keterangan
    $('#description').on('input', function() {
        const maxLength = 500;
        let currentText = $(this).val();
        if (currentText.length > maxLength) {
            currentText = currentText.substring(0, maxLength);
            $(this).val(currentText);
        }
        $('#char-count').text(currentText.length);
    });

    // Loading state saat submit
    $('#classForm').on('submit', function() {
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
    });

    // Auto-format nama kelas saat pilih grade
    $('#grade').on('change', function() {
        const grade = $(this).val();
        if (grade) {
            let currentName = $('#name').val().toUpperCase().trim();
            let suffixMatch = currentName.match(/[A-Z]+$/);
            let suffix = suffixMatch ? suffixMatch[0] : 'A';
            let newName = `${grade}${suffix}`;
            if (!currentName || /^\d/.test(currentName)) {
                $('#name').val(newName);
            }
        }
    });
});
</script>
@stop