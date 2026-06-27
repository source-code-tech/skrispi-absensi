@extends('layouts.adminlte')

@section('title', 'Edit Akun: ' . $teacher->name)

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
            {{-- Menggunakan warna Amber untuk Edit --}}
            <i class="fas fa-user-edit text-amber-500 mr-2"></i> 
            <span>Edit Akun Wali Kelas</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data atau penugasan kelas untuk <strong>{{ $teacher->name }}</strong>.</p>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('teachers.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Wali Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit Akun</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM EDIT UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden"> 
                <div class="p-6 border-b border-gray-100 bg-amber-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-edit mr-2 text-amber-500"></i> Form Edit Data</h3>
                </div>
                <div class="p-6">
                    
                    {{-- Validasi Error Global --}}
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl relative mb-6 shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle mt-1 mr-3"></i>
                                <div>
                                    <span class="font-bold">Terjadi Kesalahan!</span>
                                    <ul class="mt-1 list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('teachers.update', $teacher->username) }}" method="POST" id="teacherForm" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Purple/Ungu)
                            $currentClassId = $teacher->homeroomTeacher->class_code ?? null; // Ambil ID kelas yang diampu saat ini
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition duration-200 bg-gray-50 focus:bg-white';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            {{-- NIP (Readonly) --}}
                            <div class="sm:col-span-2">
                                <label for="nip" class="{{ $labelClass }}">NIP (Nomor Induk Pegawai) - <span class="text-xs text-gray-500">Tidak dapat diubah</span></label>
                                <input type="text" name="nip" id="nip" 
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed" 
                                        value="{{ $teacher->username }}" 
                                        readonly>
                            </div>

                            {{-- Nama Guru --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                        class="@error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        value="{{ old('name', $teacher->name) }}" 
                                        placeholder="Nama lengkap guru"
                                        required>
                                @error('name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label for="email" class="{{ $labelClass }}">Email (Login) <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" 
                                        class="@error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        value="{{ old('email', $teacher->email) }}" 
                                        placeholder="Email unik untuk login"
                                        required>
                                @error('email') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Password (Opsional) --}}
                            <div class="sm:col-span-2">
                                <label for="password" class="{{ $labelClass }}">Password Baru (Opsional)</label>
                                <input type="password" name="password" id="password" 
                                        class="@error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror"
                                        placeholder="Isi hanya jika ingin mengganti password">
                                <p class="mt-2 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</p>
                                @error('password') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Kelas yang Diampu --}}
                        <div class="pt-4 border-t border-gray-100">
                             <label for="class_code" class="{{ $labelClass }}">Tugaskan ke Kelas</label>
                             <div class="relative">
                                <select name="class_code" id="class_code" class="w-full">
                                    <option value="">-- Hapus Kelas yang Diampu / Kosongkan --</option>
                                    
                                    @foreach($availableClasses as $class)
                                        @php
                                            $isDisabled = $class->homeroomTeacher && $class->homeroomTeacher->user_username !== $teacher->username;
                                            $isSelected = old('class_code') == $class->code || $currentClassId == $class->code;
                                            $isCurrent = $class->code == $currentClassId;
                                        @endphp
                                        
                                        <option value="{{ $class->code }}" 
                                                {{ $isSelected ? 'selected' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}>
                                            {{ $class->code }} (Tingkat {{ $class->grade }})
                                            @if ($isCurrent) (Kelas Saat Ini) @endif
                                            @if ($isDisabled) (Sudah Diampu Guru Lain) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('class_code') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <p class="mt-2 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i> Pilih "Hapus Kelas" atau kosongkan untuk menonaktifkan status wali kelas.</p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('teachers.index') }}" 
                               class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-bold rounded-xl shadow-sm 
                                      text-gray-700 bg-white hover:bg-gray-50 transition duration-150 transform hover:scale-[1.02]">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            {{-- Tombol Perbarui Data (Amber) --}}
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg 
                                           text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 
                                           focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Perbarui Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- KOLOM KANAN: INFO & TIPS (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-user-tag mr-2 text-amber-500"></i> Status Saat Ini</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-gray-500">Nama Akun</span>
                        <span class="font-bold text-gray-800">{{ $teacher->name }}</span>
                    </div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-gray-500">Peran Sistem</span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700">Wali Kelas</span>
                    </div>
                    
                    <div class="pt-2">
                        <h6 class="font-bold text-gray-800 mb-2">Kelas Saat Ini:</h6>
                        @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->class)
                            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-center">
                                <span class="text-lg font-bold text-indigo-700 block">{{ $teacher->homeroomTeacher->class->code }}</span>
                                <span class="text-xs text-indigo-500">(Tingkat {{ $teacher->homeroomTeacher->class->grade }})</span>
                            </div>
                            <p class="text-xs mt-3 text-gray-500">Kelas ini akan di-update jika Anda memilih kelas baru di form utama.</p>
                        @else
                            <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm font-bold text-gray-500 block">Belum Mengampu Kelas</span>
                            </div>
                            <p class="text-xs mt-3 text-gray-500">Pilih kelas di form utama untuk menugaskan.</p>
                        @endif
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
    $(document).ready(function() {
        // Initialize Select2
        $('#class_code').select2({
            theme: 'bootstrap4',
            placeholder: '-- Hapus Kelas yang Diampu / Kosongkan --',
            allowClear: true,
            width: '100%'
        });

        // Form submission loading state
        $('#teacherForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memperbarui...');
        });
    });
</script>
@stop