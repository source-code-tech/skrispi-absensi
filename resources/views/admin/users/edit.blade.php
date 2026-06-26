@extends('layouts.adminlte')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-edit text-purple-600 mr-3"></i>
            Edit Pengguna
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Perbarui profil dan hak akses pengguna.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Pengguna</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Edit</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM UTAMA (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden"> 
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit mr-2 text-purple-500"></i> Informasi Akun
                    </h3>
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
                    
                    <form action="{{ route('admin.users.update', $user->username) }}" method="POST" id="userForm" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        @php
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nama Lengkap --}}
                            <div class="md:col-span-2">
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                            class="pl-10 @error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('name', $user->name) }}" 
                                            required>
                                </div>
                                @error('name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label for="email" class="{{ $labelClass }}">Email (Login) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                            class="pl-10 @error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('email', $user->email) }}" 
                                            required>
                                </div>
                                @error('email') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Password Baru (Opsional) --}}
                            <div class="md:col-span-2">
                                <label for="password" class="{{ $labelClass }}">Password Baru (Opsional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                            class="pl-10 @error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            placeholder="Kosongkan jika tidak ingin mengubah">
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-1">Minimal 8 karakter jika diisi.</p>
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
                                            <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Status Akun --}}
                            <div>
                                <label for="is_approved" class="{{ $labelClass }}">Status Akun</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-toggle-on text-gray-400"></i>
                                    </div>
                                    <select name="is_approved" id="is_approved" class="pl-10 {{ $inputClass }}" required>
                                        <option value="1" {{ old('is_approved', $user->is_approved) == 1 ? 'selected' : '' }}>Disetujui (Aktif)</option>
                                        <option value="0" {{ old('is_approved', $user->is_approved) == 0 ? 'selected' : '' }}>Menunggu/Ditolak</option>
                                    </select>
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
                                           text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Perbarui Data
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
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-shield-alt mr-2 text-blue-500"></i> Keamanan Akun</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <p class="text-amber-800 font-semibold mb-1">Perubahan Peran</p>
                        <p class="text-xs text-amber-700 text-justify">
                            Jika Anda mengubah peran dari <strong>Wali Kelas</strong> atau <strong>Orang Tua</strong> menjadi peran lain, data relasi (seperti kelas yang diampu atau siswa yang ditautkan) mungkin akan <strong>dihapus atau direset</strong> oleh sistem. Harap berhati-hati.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-key text-gray-400 mt-1 mr-2"></i>
                            <div>
                                <strong class="block text-gray-800">Password</strong>
                                <span class="text-xs">Kosongkan jika pengguna tidak meminta reset password.</span>
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
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memperbarui...');
        });
        
        // Alert Error Toast 
        @if($errors->any())
             Swal.fire({ 
                 icon: 'error', 
                 title: 'Gagal Memperbarui', 
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