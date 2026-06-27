@extends('layouts.adminlte')

@section('title', 'Tambah Wali Kelas Baru')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Purple/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-2"></i>
            <span>Tambah Wali Kelas Baru</span>
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('teachers.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Wali Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Baru</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden"> 
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-id-card mr-2 text-purple-500"></i> Data Akun & Penugasan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Lengkapi data akun untuk membuat wali kelas baru.</p>
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
                    
                    <form action="{{ route('teachers.store') }}" method="POST" id="teacherForm" class="space-y-6">
                        @csrf
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Purple/Ungu)
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            
                            {{-- NIP --}}
                            <div class="sm:col-span-2">
                                <label for="nip" class="{{ $labelClass }}">NIP (Nomor Induk Pegawai) <span class="text-red-500">*</span></label>
                                <input type="text" name="nip" id="nip" 
                                        class="@error('nip') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        value="{{ old('nip') }}" 
                                        placeholder="Contoh: 198010202005011001"
                                        required autofocus>
                                <p class="mt-1 text-xs text-gray-500 font-medium">Penting: NIP ini akan otomatis digunakan sebagai Username login Wali Kelas.</p>
                                @error('nip') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Nama Guru --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                        class="@error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        value="{{ old('name') }}" 
                                        placeholder="Nama lengkap guru"
                                        required>
                                @error('name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label for="email" class="{{ $labelClass }}">Email (Login) <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" 
                                        class="@error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        value="{{ old('email') }}" 
                                        placeholder="Email unik (Contoh: budi@sekolah.sch.id)"
                                        required>
                                @error('email') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div class="sm:col-span-2">
                                <label for="password" class="{{ $labelClass }}">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" id="password" 
                                        class="@error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        placeholder="Minimal 8 karakter"
                                        required>
                                @error('password') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Kelas yang Diampu --}}
                        <div class="pt-4 border-t border-gray-100">
                             <label for="class_code" class="{{ $labelClass }}">Tugaskan ke Kelas (Opsional)</label>
                             <div class="relative">
                                <select name="class_code" id="class_code" class="w-full">
                                    <option value="">-- Pilih Kelas (Kosongkan jika belum mengampu) --</option>
                                    @foreach($availableClasses as $class)
                                        <option value="{{ $class->code }}" {{ old('class_code') == $class->code ? 'selected' : '' }}>
                                            {{ $class->code }} (Tingkat {{ $class->grade }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('class_code') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <p class="mt-2 text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i> Hanya kelas yang belum memiliki Wali Kelas yang ditampilkan.</p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('teachers.index') }}" 
                               class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-bold rounded-xl shadow-sm
                                      text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                                Batal
                            </a>
                            {{-- Tombol Submit --}}
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg 
                                           text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                                           focus:ring-4 focus:ring-purple-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Simpan Data
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
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-lightbulb mr-2 text-amber-500"></i> Informasi</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    
                    <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                        <p class="text-purple-800 font-semibold mb-1">Role Otomatis</p>
                        <p class="text-xs text-purple-700">Akun baru akan otomatis diberikan peran <strong>Wali Kelas</strong>.</p>
                    </div>

                    <div>
                        <h6 class="font-bold text-gray-800 mb-2">Tips Pengisian:</h6>
                        <ul class="list-disc ml-5 space-y-2">
                            <li><strong>Email</strong> harus unik dan aktif (jika ada notifikasi email).</li>
                            <li><strong>Password</strong> disarankan menggunakan kombinasi huruf dan angka.</li>
                            <li>Jika kelas yang dituju tidak ada di daftar, kemungkinan kelas tersebut <strong>sudah memiliki wali kelas</strong>. Cek daftar wali kelas terlebih dahulu.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 4 theme
        $('#class_code').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Kelas (Opsional) --',
            allowClear: true,
            width: '100%'
        });

        // Form submission loading state
        $('#teacherForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
    });
</script>
@stop