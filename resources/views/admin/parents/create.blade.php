@extends('layouts.adminlte')

@section('title', 'Tambah Orang Tua Baru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-3"></i>
            Tambah Orang Tua
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('parents.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Orang Tua</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Tambah Baru</li>
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
                        <i class="fas fa-id-card mr-2 text-purple-500"></i> Data Akun & Relasi Siswa
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Lengkapi data untuk membuat akun Orang Tua baru.</p>
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
                    
                    <form action="{{ route('parents.store') }}" method="POST" id="parentForm" class="space-y-6">
                        @csrf
                        
                        @php
                            // Styling Helper
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            {{-- Nama Orang Tua --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                            class="pl-10 @error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('name') }}" 
                                            placeholder="Nama lengkap Orang Tua / Wali"
                                            required>
                                </div>
                                @error('name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Nomor HP (WA) --}}
                            <div>
                                <label for="phone_number" class="{{ $labelClass }}">Nomor HP (WhatsApp) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="number" name="phone_number" id="phone_number" 
                                            class="pl-10 @error('phone_number') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('phone_number') }}" 
                                            placeholder="08123456789"
                                            required>
                                </div>
                                @error('phone_number') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Status Hubungan --}}
                            <div>
                                <label for="relation_status" class="{{ $labelClass }}">Status Hubungan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-heart text-gray-400"></i>
                                    </div>
                                    <select name="relation_status" id="relation_status" class="pl-10 {{ $inputClass }}">
                                        <option value="Ayah" {{ old('relation_status') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                        <option value="Ibu" {{ old('relation_status') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                        <option value="Wali" {{ old('relation_status') == 'Wali' ? 'selected' : '' }}>Wali</option>
                                        <option value="Lainnya" {{ old('relation_status') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label for="email" class="{{ $labelClass }}">Email (Untuk Login) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                            class="pl-10 @error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('email') }}" 
                                            placeholder="Email aktif (Contoh: budi@gmail.com)"
                                            required>
                                </div>
                                @error('email') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div class="sm:col-span-2">
                                <label for="password" class="{{ $labelClass }}">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                            class="pl-10 @error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            placeholder="Minimal 8 karakter"
                                            required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-1">Minimal 8 karakter.</p>
                                @error('password') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Pilih Siswa (Anak) --}}
                        <div class="pt-6 border-t border-gray-100">
                             <label for="student_nisns" class="{{ $labelClass }}">Pilih Siswa (Anak) <span class="text-red-500">*</span></label>
                             <div class="relative">
                                <select name="student_nisns[]" id="student_nisns" class="w-full" multiple="multiple" required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->nisn }}" {{ (collect(old('student_nisns'))->contains($student->nisn)) ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->class->name ?? 'No Class' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('student_nisns') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <div class="mt-3 flex items-start p-3 bg-blue-50 rounded-lg text-blue-700 text-xs">
                                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                <p>Cari nama siswa pada kotak di atas. Anda dapat memilih lebih dari satu anak jika mereka bersaudara.</p>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('parents.index') }}" 
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
        
        {{-- KOLOM KANAN: INFO & TIPS (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-lightbulb mr-2 text-amber-500"></i> Informasi</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    
                    <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                        <p class="text-purple-800 font-semibold mb-1">Role Otomatis</p>
                        <p class="text-xs text-purple-700">Akun baru akan otomatis diberikan peran <strong>Orang Tua</strong>.</p>
                    </div>

                    <div>
                        <h6 class="font-bold text-gray-800 mb-2">Pilih Siswa:</h6>
                        <ul class="list-disc ml-5 space-y-2">
                            <li>Gunakan kolom pencarian di menu dropdown untuk mencari nama siswa.</li>
                            <li>Hanya siswa yang <strong>belum memiliki akun Orang Tua</strong> yang muncul di daftar ini.</li>
                            <li>Jika siswa sudah memiliki orang tua terdaftar, Anda harus mengedit akun orang tua tersebut, bukan membuat baru.</li>
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
        // Initialize Select2 (Multiple Select)
        $('#student_nisns').select2({
            theme: 'bootstrap4',
            placeholder: '-- Cari & Pilih Siswa --',
            allowClear: true,
            width: '100%',
            closeOnSelect: false 
        });

        // Form submission loading state
        $('#parentForm').on('submit', function() {
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