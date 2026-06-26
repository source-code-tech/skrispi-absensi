@extends('layouts.adminlte') 

@section('title', 'Manajemen Absensi Manual')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Absensi Harian</h2>
            <p class="text-sm text-gray-500 mt-1">Input manual dan koreksi data kehadiran siswa.</p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Absensi Manual</span>
        </nav>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl relative shadow-sm flex items-center alert-dismissible">
            <i class="fas fa-check-circle mr-3 text-lg"></i> 
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error') || session('warning'))
        @php
            $bg = session('error') ? 'bg-red-50' : 'bg-amber-50';
            $border = session('error') ? 'border-red-500' : 'border-amber-500';
            $text = session('error') ? 'text-red-700' : 'text-amber-700';
            $icon = session('error') ? 'fa-ban' : 'fa-exclamation-triangle';
        @endphp
        <div class="{{ $bg }} border-l-4 {{ $border }} {{ $text }} p-4 rounded-xl relative shadow-sm flex items-center alert-dismissible">
            <i class="fas {{ $icon }} mr-3 text-lg"></i> 
            <span class="font-medium">{{ session('error') ?? session('warning') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: FORM MANUAL (1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Instruction Card --}}
            <div class="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-xl mr-4 backdrop-blur-sm">
                        <i class="fas fa-pen-fancy text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Input Manual</h3>
                        <p class="text-teal-50 text-xs opacity-90 mt-1">
                            Gunakan form ini jika siswa tidak membawa kartu atau izin lisan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <span class="w-2 h-6 bg-teal-500 rounded-full mr-3"></span>
                        Form Kehadiran
                    </h3>
                </div>
                <div class="p-6">
                    <form id="manualAttendanceForm" action="{{ route('walikelas.absensi.manual.store') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        {{-- Pilih Siswa --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Siswa <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select class="w-full select2bs4 border-gray-300 rounded-xl" name="nis" id="manualStudentSelect" required style="width: 100%;">
                                    <option value="">Cari nama siswa...</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->nis }}" {{ old('nis') == $student->nis ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->nis }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('nis') <p class="mt-2 text-xs text-red-500 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alfa'] as $status)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="{{ $status }}" class="peer sr-only" required {{ old('status') == $status ? 'checked' : '' }}>
                                        <div class="rounded-xl border-2 border-gray-200 p-2 text-center transition-all hover:bg-gray-50 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700">
                                            <span class="text-sm font-bold">{{ $status }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('status') <p class="mt-2 text-xs text-red-500 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan (Opsional)</label>
                            <textarea class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition placeholder-gray-400 text-sm" 
                                      name="notes" rows="2" placeholder="Contoh: Izin pulang lebih awal...">{{ old('notes') }}</textarea>
                        </div>
                        
                        <button type="submit" id="manualSubmitBtn" class="w-full py-3 px-6 rounded-xl bg-teal-600 text-white font-bold hover:bg-teal-700 focus:ring-4 focus:ring-teal-500/30 transition transform hover:-translate-y-1 shadow-lg shadow-teal-500/30">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL KOREKSI (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Header Actions --}}
            <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Log Harian</h3>
                        <p class="text-xs text-gray-500">Koreksi data absensi hari ini.</p>
                    </div>
                </div>
                
                {{-- WA Button --}}
                <form action="{{ route('walikelas.absensi.send_daily_absences') }}" method="POST" id="sendWaForm">
                    @csrf
                    <button type="button" onclick="confirmSendWa()" id="sendWaBtn" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-purple-500/30 hover:bg-purple-700 focus:ring-4 focus:ring-purple-500/30 transition transform hover:-translate-y-0.5">
                        <i class="fab fa-whatsapp text-lg mr-2"></i> Kirim Notifikasi Absen (S/I/A)
                    </button>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                                <th class="px-6 py-4 text-center w-12">#</th>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $rowNumber = 1; @endphp
                            @forelse ($todayAttendance as $att)
                                {{-- Hanya tampilkan yang belum pulang --}}
                                @if ($att->checkout_time) @continue @endif 
                                
                                <tr class="hover:bg-gray-50/80 transition duration-150">
                                    <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">{{ $rowNumber++ }}</td>
                                    <td class="px-6 py-4">
                                        <span class="block text-sm font-bold text-gray-900">{{ $att->student->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                        {{ $att->attendance_time ? \Carbon\Carbon::parse($att->attendance_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badges = [
                                                'Hadir' => 'bg-green-100 text-green-700 border-green-200',
                                                'Terlambat' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'Sakit' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                                                'Izin' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'Alfa' => 'bg-red-100 text-red-700 border-red-200',
                                            ];
                                            $badge = $badges[$att->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-lg border {{ $badge }}">{{ ucfirst($att->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 italic truncate max-w-xs">
                                        {{ $att->notes ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('walikelas.absensi.manual.edit', $att->id) }}" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 transition" 
                                               title="Edit Log">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" onclick="confirmDeleteAttendance('{{ $att->id }}')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition" 
                                                    title="Hapus Log">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-att-form-{{ $att->id }}" action="{{ route('walikelas.absensi.destroy', $att->id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-check text-gray-300 text-2xl"></i>
                                            </div>
                                            <h4 class="text-gray-900 font-bold">Data Bersih</h4>
                                            <p class="text-gray-500 text-sm mt-1">Belum ada absensi yang perlu dikoreksi hari ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
        // Init Select2
        $('.select2bs4').select2({ theme: 'bootstrap4', placeholder: 'Pilih Siswa...', allowClear: true });

        // Auto hide alerts
        setTimeout(() => $('.alert-dismissible').fadeOut(), 5000);

        // Submit Loader
        $('#manualAttendanceForm').on('submit', function() {
            if(this.checkValidity()) {
                $('#manualSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
            }
        });
    });

    // SweetAlert Logic
    function confirmSendWa() {
        Swal.fire({
            title: 'Kirim Notifikasi Massal?',
            text: "Kirim WA ke semua wali murid dengan status Sakit, Izin, atau Alpha hari ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#9333ea',
            confirmButtonText: '<i class="fab fa-whatsapp"></i> Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#sendWaBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
                $('#sendWaForm').trigger('submit');
            }
        });
    }

    function confirmDeleteAttendance(id) {
        Swal.fire({
            title: 'Hapus Log?',
            text: "Data ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) $(`#delete-att-form-${id}`).submit();
        });
    }
</script>
@endsection

@section('css')
<style>
/* Select2 Customization */
.select2-container--bootstrap4 .select2-selection--single { height: 46px !important; border-radius: 0.75rem !important; border-color: #d1d5db !important; }
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: 44px !important; padding-left: 1rem !important; }
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { top: 10px !important; }
</style>
@endsection