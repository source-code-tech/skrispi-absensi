@extends('layouts.adminlte')

@section('title', 'Edit Data Orang Tua: ' . $parent->name)

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-user-edit text-orange-500 mr-2"></i>
        <span>Edit Data Wali: {{ $parent->name }}</span>
    </h1>
    {{-- Breadcrumb disederhanakan --}}
    <a href="{{ route('walikelas.students.index') }}" class="btn btn-sm btn-default border">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Siswa
    </a>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- KOLOM KIRI: FORM EDIT (2/3) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-phone mr-2 text-gray-500"></i> Koreksi Kontak Orang Tua</h3>
            </div>

            <div class="p-5">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Harap periksa kembali input Anda.
                    </div>
                @endif

                <form action="{{ route('walikelas.parents.update', $parent->nik) }}" method="POST" id="editParentForm" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @php
                        $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500';
                        $errorBorder = 'border-red-500';
                        $defaultBorder = 'border-gray-300';
                    @endphp

                    {{-- Nama Orang Tua --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Wali <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $parent->name) }}" required class="{{ $inputClass }} @error('name') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status Relasi --}}
                    <div>
                        <label for="relation_status" class="block text-sm font-medium text-gray-700 mb-1">Status Relasi <span class="text-red-600">*</span></label>
                        <select name="relation_status" id="relation_status" required class="{{ $inputClass }} @error('relation_status') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            <option value="">Pilih Status</option>
                            @foreach($relationStatuses as $status)
                                <option value="{{ $status }}" {{ old('relation_status', $parent->relation_status) == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('relation_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP/WhatsApp <span class="text-red-600">*</span></label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $parent->phone_number) }}" required class="{{ $inputClass }} @error('phone_number') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                        <small class="text-xs text-gray-500 mt-1 block">Pastikan nomor formatnya benar (mis: 628xxxx) untuk notifikasi WA.</small>
                        @error('phone_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 border-t border-gray-200 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-lg shadow-sm 
                                text-white bg-orange-500 hover:bg-orange-600 transition duration-150" id="submitBtn">
                            <i class="fas fa-save mr-2"></i> Simpan Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Informasi Tambahan (1/3) --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-info-circle mr-2 text-gray-500"></i> Informasi Audit</h3>
            </div>
            <div class="p-5 text-sm">
                <p class="text-gray-600">Perubahan pada data ini hanya memengaruhi kontak Orang Tua, bukan akun pengguna (jika ada). Pastikan nomor HP benar untuk kelancaran notifikasi absensi.</p>
                <ul class="list-disc ml-5 mt-4 space-y-1 text-gray-600">
                    @foreach($parent->students as $student)
                        <li>Terhubung dengan siswa: <strong class="text-gray-800">{{ $student->name }}</strong> ({{ $student->class->name ?? 'N/A' }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Form submission loading state
        $('#editParentForm').on('submit', function() {
            const submitBtn = $('#submitBtn');
            if (this.checkValidity() === false) {
                 return;
            }
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
    });
</script>
@endsection

@push('css')
<style>
/* --- CUSTOM CSS --- */
.text-orange-500 { color: #f97316; } 
.bg-orange-500 { background-color: #f97316 !important; }
.hover\:bg-orange-600:hover { background-color: #ea580c !important; }

/* FIXES FOR TAILWIND/BOOTSTRAP MIXTURES */
.form-control, select {
    border-radius: 0.5rem;
}
</style>
@endpush