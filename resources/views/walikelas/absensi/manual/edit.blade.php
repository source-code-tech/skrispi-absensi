@extends('layouts.adminlte')

@section('title', 'Koreksi Absensi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Koreksi Data Absensi</h2>
            <p class="text-sm text-gray-500 mt-1">Siswa: <span class="font-bold text-indigo-600">{{ $attendance->student->name ?? 'N/A' }}</span></p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.absensi.manual.index') }}" class="text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </nav>
    </div>
    
    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: FORM (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-amber-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-amber-500 mr-2"></i> Form Perubahan Data
                    </h3>
                </div>
                
                <div class="p-8">
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm font-medium">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Mohon periksa kembali inputan Anda.
                        </div>
                    @endif

                    <form action="{{ route('walikelas.absensi.manual.update', $attendance->id) }}" method="POST" id="editAbsenceForm" class="space-y-6">
                        @csrf @method('PUT')
                        <input type="hidden" name="nis" value="{{ $attendance->student->nis ?? '' }}">

                        {{-- CURRENT INFO BOX --}}
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Masuk</span>
                                <span class="font-mono font-bold text-gray-800">{{ $attendance->attendance_time ? $attendance->attendance_time->format('H:i') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Pulang</span>
                                <span class="font-mono font-bold text-gray-800">{{ $attendance->checkout_time ? $attendance->checkout_time->format('H:i') : 'Belum' }}</span>
                            </div>
                        </div>

                        {{-- STATUS SELECTION --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Status Baru <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                                @foreach(['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpha'] as $status)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="status" value="{{ $status }}" class="peer sr-only" required {{ old('status', $attendance->status) == $status ? 'checked' : '' }}>
                                        <div class="rounded-xl border-2 border-gray-200 p-3 text-center transition-all bg-white hover:bg-gray-50 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:shadow-sm">
                                            <span class="text-xs sm:text-sm font-bold">{{ $status }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- NOTES --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan</label>
                            <textarea class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition text-sm" 
                                      name="notes" rows="2">{{ old('notes', $attendance->notes) }}</textarea>
                        </div>

                        {{-- AUDIT REASON (REQUIRED) --}}
                        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-200">
                            <label class="block text-sm font-bold text-amber-800 mb-2">
                                <i class="fas fa-file-signature mr-1"></i> Alasan Koreksi (Audit Log) <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-4 py-3 rounded-xl border border-amber-300 bg-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition text-sm" 
                                      name="correction_reason" rows="2" required placeholder="Jelaskan alasan perubahan data ini...">{{ old('correction_reason', $attendance->correction_note) }}</textarea>
                            <p class="text-xs text-amber-600 mt-2 font-medium">Wajib diisi untuk rekam jejak sistem.</p>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <button type="button" onclick="confirmDelete()" class="text-red-500 hover:text-red-700 font-bold text-sm px-4 py-2 rounded-lg hover:bg-red-50 transition">
                                <i class="fas fa-trash mr-1"></i> Hapus Data
                            </button>
                            <button type="submit" id="saveBtn" class="px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition transform hover:-translate-y-1">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    <form id="deleteForm" action="{{ route('walikelas.absensi.destroy', $attendance->id) }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: TIPS (1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                 <div class="relative z-10">
                     <h3 class="font-bold text-lg mb-4 flex items-center"><i class="fas fa-info-circle mr-2"></i> Penting</h3>
                     <ul class="space-y-3 text-sm text-indigo-100">
                         <li class="flex items-start">
                             <i class="fas fa-dot-circle mt-1 mr-2 text-xs"></i>
                             <span>Mengubah status ke <b>Sakit/Izin/Alpha</b> otomatis mereset jam pulang.</span>
                         </li>
                         <li class="flex items-start">
                             <i class="fas fa-dot-circle mt-1 mr-2 text-xs"></i>
                             <span>Semua perubahan tercatat di <b>Audit Log</b> beserta nama pengubah.</span>
                         </li>
                     </ul>
                 </div>
                 {{-- Decor --}}
                 <div class="absolute bottom-0 right-0 -mb-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            </div>

            @if($attendance->is_manual_corrected)
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                <h4 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Riwayat Terakhir</h4>
                <div class="text-sm space-y-2">
                    <p><span class="text-gray-500">Oleh:</span> <br> <span class="font-medium text-gray-900">{{ $attendance->corrected_by ?? 'System' }}</span></p>
                    <p><span class="text-gray-500">Alasan:</span> <br> <span class="font-medium text-gray-900 italic">"{{ $attendance->correction_note }}"</span></p>
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#editAbsenceForm').on('submit', function() {
        if(this.checkValidity()) {
            $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        }
    });

    function confirmDelete() {
        Swal.fire({
            title: 'Hapus Permanen?',
            text: "Data absensi ini akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) $('#deleteForm').submit();
        });
    }

    // Auto fade alerts
    setTimeout(() => $('.alert-dismissible').fadeOut(), 5000);
</script>
@endsection