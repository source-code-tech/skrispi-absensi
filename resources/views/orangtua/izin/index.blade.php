@extends('layouts.adminlte')

@section('title', 'Form Pengajuan Izin/Sakit')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-file-signature text-purple-600 mr-3"></i>
            Pengajuan Izin
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Ajukan izin atau sakit untuk putra/putri Anda secara online.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Izin</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- KOLOM KIRI: FORM CONFIG (5/12) --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden sticky top-6">
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-paper-plane mr-2 text-purple-600"></i> Form Pengajuan
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('orangtua.izin.store') }}" method="POST" enctype="multipart/form-data" id="izinForm">
                        @csrf
                        
                        @php
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                        @endphp
        
                        {{-- Pilih Siswa --}}
                        <div class="mb-5">
                            <label for="student_nisn" class="{{ $labelClass }}">Pilih Siswa <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="student_nisn" id="student_nisn" class="w-full select2-form-control" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($parentRecord->students as $student)
                                        <option value="{{ $student->nisn }}" {{ old('student_nisn') == $student->nisn ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->class->code ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('student_nisn') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal & Jenis --}}
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label for="request_date" class="{{ $labelClass }}">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="request_date" id="request_date" 
                                        class="{{ $errors->has('request_date') ? $inputErrorClass : $inputClass }}" 
                                        value="{{ old('request_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                 @error('request_date') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="type" class="{{ $labelClass }}">Jenis <span class="text-red-500">*</span></label>
                                <select name="type" id="type" class="{{ $errors->has('type') ? $inputErrorClass : $inputClass }}" required>
                                    <option value="Sakit" {{ old('type') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Izin" {{ old('type') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                </select>
                                 @error('type') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-5">
                            <label for="reason" class="{{ $labelClass }}">Alasan / Keterangan <span class="text-red-500">*</span></label>
                            <textarea name="reason" id="reason" rows="3" 
                                      class="{{ $errors->has('reason') ? $inputErrorClass : $inputClass }}" 
                                      placeholder="Jelaskan alasan izin secara singkat..." required>{{ old('reason') }}</textarea>
                            @error('reason') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Lampiran --}}
                        <div class="mb-6">
                            <label for="attachment" class="{{ $labelClass }}">Lampiran (Opsional)</label>
                            <div class="flex items-center space-x-4 p-4 border rounded-xl bg-gray-50 border-gray-200">
                                <div class="flex-shrink-0 text-gray-400">
                                     <i class="fas fa-paperclip fa-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="attachment" id="attachment" 
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer"
                                        accept="image/*, application/pdf">
                                    <p class="mt-1 text-xs text-gray-500">Max 2MB (JPG, PNG, PDF). Wajib untuk Sakit (Surat Dokter).</p>
                                </div>
                            </div>
                            @error('attachment') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                         {{-- Button Simpan --}}
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-6 py-4 border border-transparent text-base font-bold rounded-xl shadow-lg 
                                       text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                                       focus:ring-4 focus:ring-purple-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                id="submitIzinBtn">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Pengajuan
                        </button>

                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: RIWAYAT (7/12) --}}
        <div class="lg:col-span-7 mt-6 lg:mt-0">
             <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-history mr-2 text-indigo-500"></i> Riwayat Pengajuan</h3>
                </div>
                <div class="p-0">
                    @if($requests->isEmpty())
                        <div class="p-10 text-center text-gray-400">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada riwayat pengajuan izin.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Info</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Lampiran</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($requests as $req)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800">{{ $req->student->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->request_date)->translatedFormat('d F Y') }}</div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $req->type == 'Sakit' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $req->type }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-600 line-clamp-2 w-48">{{ $req->reason }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                             @php
                                                $statusClass = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    'Approved' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                                                ][$req->status] ?? 'bg-gray-100 text-gray-800';
                                                
                                                $icon = [
                                                    'Pending' => 'fa-clock',
                                                    'Approved' => 'fa-check-circle',
                                                    'Rejected' => 'fa-times-circle',
                                                ][$req->status] ?? 'fa-question';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex items-center text-xs font-bold rounded-full border {{ $statusClass }}">
                                                <i class="fas {{ $icon }} mr-1.5"></i> {{ $req->status }}
                                            </span>
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($req->attachment_path)
                                                <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition duration-150" title="Lihat Lampiran">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100">
                             {{ $requests->links('pagination::tailwind') }}
                        </div>
                    @endif
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
        // 1. Initialize Select2
        $('.select2-form-control').select2({ theme: 'bootstrap4', placeholder: '-- Pilih Siswa --', allowClear: true });
        
        // CSS Style Fix for Select2
        $('.select2-container--bootstrap4 .select2-selection--single').css('height', '50px');
        $('.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered').css({
            'line-height': '48px',
            'padding-left': '1rem' 
        });

        // 2. Form Loading State
        $('#izinForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            $('#submitIzinBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
        });
        
        // 3. Alerts
        @if(session('success'))
             Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false });
        @endif
       @if(session('error'))
             Swal.fire({ 
                icon: 'error', 
                title: 'Gagal', 
                text: '{{ session('error') }}', 
                showConfirmButton: true, 
                confirmButtonText: 'Mengerti' 
             });
        @endif
    });
</script>
@stop

@section('css')
<style>
/* CSS Override untuk Select2 agar selaras dengan input Tailwind */
.select2-container--bootstrap4 .select2-selection--single {
    border: 1px solid #e5e7eb !important; /* border-gray-200 */
    border-radius: 0.75rem !important; /* rounded-xl */
    background-color: #f9fafb !important; /* bg-gray-50 */
}
.select2-container--bootstrap4 .select2-selection--single:focus {
    border-color: #a855f7 !important; /* border-purple-500 */
    box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1) !important;
}
</style>
@stop