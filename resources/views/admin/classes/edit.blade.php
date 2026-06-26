@extends('layouts.adminlte')

@section('title', 'Edit Kelas: ' . $class->name)

@section('content_header')
{{-- CUSTOM HEADER (Menggunakan Tailwind & Warna Indigo/Amber) --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    {{-- Judul Halaman --}}
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Menggunakan warna Amber untuk Edit --}}
        <i class="fas fa-edit text-amber-500 mr-2"></i> 
        <span>Edit Kelas: {{ $class->name }}</span>
    </h1>
    
    {{-- Breadcrumb --}}
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Menggunakan Indigo untuk link --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('classes.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Tata Letak Grid (2/3 dan 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6"> 
        
        {{-- KOLOM KIRI: FORM EDIT UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            {{-- Mengganti card menjadi box Tailwind --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100" id="classEditFormCard">
                
                {{-- CARD HEADER --}}
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chalkboard mr-2 text-indigo-500"></i> Form Edit Kelas
                    </h3>
                    <div class="flex-shrink-0">
                        {{-- Tombol Kembali --}}
                        <a href="{{ route('classes.index') }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-lg 
                                  shadow-sm text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="p-6">
                    <form action="{{ route('classes.update', $class->code) }}" method="POST" id="classEditForm">
                        @csrf
                        @method('PUT')

                        {{-- 💡 Helper untuk Input Styling --}}
                        @php
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        @endphp

                        {{-- ✅ ALERT ERROR (Mengganti alert Bootstrap ke Tailwind) --}}
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
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Kelas <span class="text-red-600">*</span></label>
                            @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                            <input type="text"
                                name="name"
                                id="name"
                                class="{{ $baseInputClass }} border {{ $nameStatusClass }}"
                                value="{{ old('name', $class->name) }}"
                                placeholder="Contoh: 1A, 2B, 6C"
                                required
                                autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Nama Kelas harus unik.</small>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           
                        {{-- 2. Tingkat (Grade 1-6) --}}
                            <div class="mb-5">
                                <label for="grade" class="block text-sm font-semibold text-gray-700 mb-1">Tingkat / Kelas <span class="text-red-600">*</span></label>
                                @php $gradeStatusClass = $errors->has('grade') ? $errorClass : $normalClass; @endphp
                                <select name="grade" 
                                        id="grade" 
                                        class="select2bs4 border {{ $gradeStatusClass }}" 
                                        required
                                        style="width: 100%;">
                                    <option value="">Pilih Tingkat (1-6)</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ old('grade', $class->grade) == $i ? 'selected' : '' }}>
                                            Kelas {{ $i }} 
                                        </option>
                                    @endfor
                                </select>
                                @error('grade')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                                                        
                                    {{-- 3. Jurusan (readonly, mutlak UMUM) --}}
                                <div class="mb-5">
                                    <label for="major" class="block text-sm font-semibold text-gray-700 mb-1">Jurusan</label>
                                    <input type="text" 
                                        value="UMUM" 
                                        class="border rounded px-3 py-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed"
                                        readonly>
                                    <input type="hidden" name="major" value="UMUM">
                                    <small class="mt-1 text-xs text-gray-500 block">Jurusan bersifat tetap.</small>
            
                                @error('major')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                               
                            </div>
                        </div>

                        {{-- Jam Pulang Kelas --}}
                        <div class="mb-5">
                                <label for="dismissal_time" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Jam Pulang Kelas <span class="text-red-600">*</span>
                                </label>
                             @php $dismissalTimeStatusClass = $errors->has('dismissal_time') ? $errorClass : $normalClass; @endphp

                            <input type="time"
                                name="dismissal_time"
                                id="dismissal_time"
                                class="{{ $baseInputClass }} border {{ $dismissalTimeStatusClass }}"
                                value="{{ old('dismissal_time', $class->dismissal_time ? substr($class->dismissal_time, 0, 5) : '') }}">

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
                                placeholder="Keterangan tambahan tentang kelas...">{{ old('description', $class->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">
                                <span id="char-count">{{ strlen(old('description', $class->description ?? '')) }}</span>/500 karakter
                            </small>
                        </div>

                        {{-- 5. Status --}}
                        <div class="mb-5">
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                            @php $statusStatusClass = $errors->has('status') ? $errorClass : $normalClass; @endphp
                            <select name="status" id="status" class="select2bs4 border {{ $statusStatusClass }}" style="width: 100%;">
                                <option value="active" {{ old('status', $class->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $class->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Kelas non-aktif tidak akan muncul dalam pemilihan absensi.</small>
                        </div>

                        {{-- 6. Tombol Aksi (Perbarui & Hapus) --}}
                        <div class="mt-6 flex justify-between items-center border-t border-gray-100 pt-5">
                            <div>
                                {{-- Tombol Perbarui Data (Amber) --}}
                                <button type="submit" 
                                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                                text-gray-800 bg-amber-400 hover:bg-amber-500 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 
                                                transition duration-150 transform hover:-translate-y-0.5" 
                                        id="submitEditBtn">
                                    <i class="fas fa-save mr-2"></i> Perbarui Data
                                </button>
                                {{-- Tombol Batal --}}
                                <a href="{{ route('classes.index') }}" 
                                   class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-base font-medium rounded-lg 
                                          text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ml-3">
                                    <i class="fas fa-times mr-2"></i> Batal
                                </a>
                            </div>
                            
                            {{-- Tombol Hapus (Merah) --}}
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                            text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-red-500/50 
                                            transition duration-150 transform hover:scale-[1.05]"
                                    onclick="confirmDelete({{ $class->code }}, '{{ $class->name }}')">
                                <i class="fas fa-trash mr-1"></i> Hapus Kelas
                            </button>
                        </div>
                    </form>

                    <form id="delete-form-{{ $class->code }}" 
                          action="{{ route('classes.destroy', $class->code) }}" 
                          method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Sidebar Info (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                
                {{-- CARD HEADER INFO --}}
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Informasi Kelas
                    </h3>
                </div>
                
                {{-- CARD BODY INFO --}}
                <div class="p-6">
                    
                   {{-- Tips Card (Mengganti alert-info ke Tailwind) --}}
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
                                <p class="text-sm text-gray-700">Nama Kelas harus <span class="font-semibold text-gray-800">unik</span> (tidak boleh ada yang sama).</p>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700"><span class="font-semibold text-gray-800">Jam Pulang Kelas wajib diisi</span> karena jadwal kepulangan tiap tingkatan kelas bisa berbeda.</p>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-indigo-500 text-xs mt-1 flex-shrink-0"></i>
                                <p class="text-sm text-gray-700">Penghapusan kelas hanya bisa dilakukan jika tidak ada siswa atau wali kelas yang ditugaskan.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Saat Ini (Mengganti styling Bootstrap) --}}
                    <div class="mt-3">
                        <small class="text-xs text-gray-500 font-semibold uppercase tracking-wider block mb-2">Detail Saat Ini:</small>
                        <div class="border rounded-lg p-4 bg-gray-50 space-y-2">
                            <div class="flex justify-between"><span>Nama:</span> <strong class="text-gray-800">{{ $class->name }}</strong></div>
                            <div class="flex justify-between"><span>Tingkat:</span> <strong class="text-gray-800">{{ $class->grade }}</strong></div>
                            <div class="flex justify-between"><span>Jurusan:</span> <strong class="text-gray-800">{{ $class->major ?? '-' }}</strong></div>
                            <div class="flex justify-between"><span>Jam Pulang Kelas:</span> <strong class="text-gray-800">{{ $class->dismissal_time ? \Carbon\Carbon::parse($class->dismissal_time)->format('H:i') : '-' }}</strong></div>
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span>Status:</span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full 
                                             {{ $class->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $class->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Preview Perubahan --}}
                    <div class="mt-4">
                        <small class="text-xs text-gray-500 font-semibold uppercase tracking-wider block mb-2">Preview Nama Kelas:</small>
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <code id="class-preview" class="text-sm font-mono text-gray-800">{{ $class->name }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- HAPUS @section('css') yang lama --}}

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    // --- FUNGSI HAPUS KELAS (Mengganti warna tombol ke Tailwind) ---
    function confirmDelete(id, className) {
        Swal.fire({
            title: 'Hapus Kelas?',
            html: `Yakin ingin menghapus kelas <strong>${className}</strong>?<br>Tindakan ini tidak dapat dibatalkan dan akan mempengaruhi data siswa terkait.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // red-600
            cancelButtonColor: '#4f46e5', // indigo-600
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    $(document).ready(function() {
        // Initialize Select2 for Grade input
        // Menggunakan Select2 untuk Grade, Major, dan Status
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih',
            allowClear: true
        });

        // FUNGSI UPDATE PREVIEW NAMA KELAS
        function updateClassPreview() {
            const grade = $('#grade').val();
            const major = $('#major').val(); // Ambil nilai mentah major
            const nameInput = $('#name').val().trim();
            
            let previewText = nameInput || 'Tulis Nama Kelas...';
            
            // Logika untuk menampilkan preview jika Nama Kelas diinput
            $('#class-preview').text(previewText);

            // Jika ada perubahan di input utama, beri warna text warning (amber)
            if (nameInput !== '{{ $class->name }}' || grade !== '{{ $class->grade }}' || major !== '{{ $class->major ?? '' }}') {
                $('#class-preview').addClass('text-red-500 font-bold').removeClass('text-gray-800');
            } else {
                $('#class-preview').removeClass('text-red-500 font-bold').addClass('text-gray-800');
            }
        }
        
        // --- EVENT LISTENERS ---

        // Auto-update preview when any relevant field changes
        $('#grade, #major, #name').on('change keyup', function() {
            updateClassPreview();
        });

        // Show character count for description
        $('#description').on('input', function() {
            const maxLength = 500;
            let currentText = $(this).val();
            
            if (currentText.length > maxLength) {
                currentText = currentText.substring(0, maxLength);
                $(this).val(currentText);
            }

            $('#char-count').text(currentText.length);
        });
        
        // Form submission loading state
        $('#classEditForm').on('submit', function() {
            const submitBtn = $('#submitEditBtn');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...');
        });

        // Initialize preview on page load
        updateClassPreview(); 
    });
</script>
@stop