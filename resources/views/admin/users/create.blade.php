@extends('layouts.adminlte')

@section('title', 'Tambah Pengguna Baru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-3"></i>
            Tambah Pengguna
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Pengguna</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Baru</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden"> 
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-id-card mr-2 text-purple-500"></i> Detail Akun & Peran
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Lengkapi data untuk mendaftarkan pengguna baru ke dalam sistem.</p>
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
                    
                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm" class="space-y-6">
                        @csrf
                        
                        @php
                            // Styling Helper
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                        @endphp

                        <div class="grid grid-cols-1 gap-6">
                            
                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                            class="pl-10 @error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('name') }}" 
                                            placeholder="Contoh: Budi Santoso"
                                            required>
                                </div>
                                @error('name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="{{ $labelClass }}">Email (Login) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                            class="pl-10 @error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('email') }}" 
                                            placeholder="email@sekolah.sch.id"
                                            required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-1">Pastikan email aktif dan unik.</p>
                                @error('email') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Password --}}
                                <div>
                                    <label for="password" class="{{ $labelClass }}">Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" name="password" id="password" 
                                                class="pl-10 @error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                                placeholder="Min. 8 Karakter"
                                                required>
                                    </div>
                                    @error('password') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                </div>
                                
                                {{-- Peran (Role) --}}
                                <div>
                                    <label for="role" class="{{ $labelClass }}">Peran (Role) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-user-tag text-gray-400"></i>
                                        </div>
                                        <select name="role" id="role" class="pl-10 {{ $inputClass }}" required>
                                            <option value="">-- Pilih Peran --</option>
                                            @foreach($roles as $key => $label)
                                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-bold rounded-xl shadow-sm
                                      text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                                Batal
                            </a>
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
        
        {{-- KOLOM KANAN: INFO (1/3) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Peran</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <p class="text-blue-800 font-semibold mb-1">Otomatisasi Sistem</p>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Jika Anda memilih peran <strong>Wali Kelas</strong> atau <strong>Orang Tua</strong>, sistem akan otomatis membuat profil terkait dan mengarahkan Anda ke halaman edit untuk melengkapi data (seperti memilih Kelas atau menautkan Siswa).
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-chalkboard-teacher text-purple-500 mt-1 mr-2"></i>
                            <div>
                                <strong class="block text-gray-800">Wali Kelas</strong>
                                <span class="text-xs">Memiliki akses ke absen kelas yang diampu.</span>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-user-friends text-orange-500 mt-1 mr-2"></i>
                            <div>
                                <strong class="block text-gray-800">Orang Tua</strong>
                                <span class="text-xs">Memiliki akses memantau absen siswa yang ditautkan.</span>
                            </div>
                        </div>
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
        // Form submission loading state
        $('#userForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // Alert Error Toast 
        @if($errors->any())
             Swal.fire({ 
                 icon: 'error', 
                 title: 'Gagal Menyimpan', 
                 text: 'Periksa kembali isian formulir Anda.', 
                 toast: true, 
                 position: 'top-end', 
                 showConfirmButton: false, 
                 timer: 5000 
             });
        @endif
    });
</script>
@stop