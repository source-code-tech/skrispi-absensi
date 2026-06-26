@extends('layouts.adminlte')

@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="space-y-6">
    
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Tambah Siswa Baru</h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1" aria-label="Breadcrumb">
                <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('walikelas.students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition">Data Siswa</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Tambah</span>
            </nav>
        </div>
        <a href="{{ route('walikelas.students.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 shadow-sm transition transform hover:-translate-y-0.5">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('walikelas.students.store') }}" method="POST" id="studentForm" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"> 
            
            {{-- KOLOM KIRI: DATA UTAMA & FORM (2/3 Kolom) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden"> 
                    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                <i class="fas fa-user-plus text-sm"></i>
                            </span>
                            Data Utama Siswa
                        </h3>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-6">
                        @php
                            $baseInputClass = 'w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-200 ease-in-out bg-gray-50/50 focus:bg-white';
                            $errorClass = 'border-red-500 focus:ring-red-200 focus:border-red-500';
                        @endphp
                        
                        {{-- NISN & NIS --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nisn" class="block text-sm font-bold text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
                                <input type="text" name="nisn" id="nisn" 
                                        class="{{ $baseInputClass }} @error('nisn') {{ $errorClass }} @enderror" 
                                        value="{{ old('nisn') }}" placeholder="Contoh: 0054321001" required maxlength="20">
                                @error('nisn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="nis" class="block text-sm font-bold text-gray-700 mb-2">NIS (Opsional)</label>
                                <input type="text" name="nis" id="nis" 
                                        class="{{ $baseInputClass }} @error('nis') {{ $errorClass }} @enderror" 
                                        value="{{ old('nis') }}" placeholder="Contoh: 21221001" maxlength="15">
                                @error('nis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Nama Lengkap  --}}
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                        class="{{ $baseInputClass }} @error('name') {{ $errorClass }} @enderror" 
                                        value="{{ old('name') }}" placeholder="Nama Lengkap Siswa" required maxlength="100">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                        </div>

                        {{-- Jenis Kelamin (Tanpa Kelas) --}}
                        <div class="grid grid-cols-1 gap-6">
                             <div>
                                <label for="gender" class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" class="{{ $baseInputClass }} @error('gender') {{ $errorClass }} @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                        </div>

                        {{-- Tanggal & Tempat Lahir --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="birth_place" class="block text-sm font-bold text-gray-700 mb-2">Tempat Lahir</label>
                                <input type="text" name="birth_place" id="birth_place" 
                                        class="{{ $baseInputClass }} @error('birth_place') {{ $errorClass }} @enderror" 
                                        value="{{ old('birth_place') }}" maxlength="100">
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Lahir</label>
                                <input type="date" name="birth_date" id="birth_date" 
                                        class="{{ $baseInputClass }} @error('birth_date') {{ $errorClass }} @enderror" 
                                        value="{{ old('birth_date') }}">
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                             <a href="{{ route('walikelas.students.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:scale-[1.02] transition transform">
                                <i class="fas fa-save mr-2"></i> Simpan Siswa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- KOLOM KANAN: FOTO & INFO --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Foto Card --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/30">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <i class="fas fa-camera text-indigo-500 mr-2"></i> Foto Profil
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        <div class="relative w-40 h-40 mx-auto mb-6 group">
                            <img id="photo-preview" src="{{ asset('images/default_avatar.png') }}" alt="Preview" 
                                 class="w-full h-full rounded-full object-cover border-4 border-white shadow-xl group-hover:scale-105 transition duration-300">
                             <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">Ganti Foto</span>
                             </div>
                             <input type="file" name="photo" id="photo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Klik foto untuk mengupload.</p>
                        <p class="text-xs text-indigo-500 font-medium bg-indigo-50 py-1 px-2 rounded-lg inline-block">Max 2MB (JPG/PNG)</p>
                        @error('photo') <p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                {{-- Info Card --}}
                <div class="bg-indigo-900 rounded-3xl shadow-xl border border-indigo-800 overflow-hidden text-white relative">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <div class="p-6 relative z-10">
                        <h4 class="text-lg font-bold mb-3 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-400 mr-2"></i> Tips
                        </h4>
                        <ul class="space-y-3 text-indigo-100 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-2 text-indigo-400"></i>
                                <span>NISN wajib unik. Gunakan data Dapodik.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-2 text-indigo-400"></i>
                                <span>Siswa otomatis masuk ke kelas Anda: <b>{{ $class->name ?? '...' }}</b></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-2 text-indigo-400"></i>
                                <span>Gunakan foto formal untuk kartu pelajar.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photo-preview');

        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { photoPreview.src = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                photoPreview.src = '{{ asset('images/default_avatar.png') }}';
            }
        });
    });

    $(document).ready(function() {
        $('#studentForm').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).addClass('transform transition duration-150 ease-in-out').html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });

        $('#nisn, #nis').on('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });
       
        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Error!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }); @endif
    });
</script>
@stop