@extends('layouts.adminlte')

@section('title', 'Data Siswa Kelas')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-user-graduate text-indigo-600 mr-2"></i> Data Siswa Kelas {{ $class->code ?? '' }}
        </h1>
        <small class="text-sm text-gray-500 block mt-1">Kelola data siswa di kelas yang Anda ampu.</small>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Data Siswa</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER WITH ACTIONS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Siswa</h2>
            <p class="text-sm text-gray-500">Total: <span class="font-bold text-indigo-600">{{ $students->total() }}</span> Siswa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            {{-- Cetak Semua --}}
            <a href="{{ route('walikelas.students.barcode.bulk') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-sm font-bold text-amber-700 hover:bg-amber-100 shadow-sm transition">
                <i class="fas fa-print mr-2"></i> Cetak Semua Kartu
            </a>
            
            {{-- Tambah Siswa --}}
            <a href="{{ route('walikelas.students.create') }}" 
               class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white 
                      bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 
                      shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Tambah Siswa
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        
        {{-- TOOLBAR / FILTER --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50/30">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                {{-- Left: Filter Info (Empty for Wali Kelas as class is fixed) --}}
                <div class="hidden lg:block"></div>

                {{-- Right: Search Form --}}
                <form action="{{ route('walikelas.students.index') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                    <div class="relative w-full sm:w-64">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" 
                               class="pl-10 pr-4 py-2.5 rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full shadow-sm transition" 
                               placeholder="Cari Nama, NIS, atau NISN..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>

            {{-- BULK ACTIONS BAR --}}
            <div id="bulk-actions" class="hidden mt-4 pt-4 border-t border-gray-200 flex items-center justify-between animate-fade-in-down bg-red-50 p-3 rounded-xl border border-red-100">
                <span class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-2"></i> <span id="selected-count">0</span> Siswa Dipilih
                </span>
                <div class="flex space-x-2">
                    <button type="button" onclick="confirmBulkDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-sm flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus Siswa Terpilih
                    </button>
                </div>
            </div>
        </div>

        {{-- SUCCESS/ERROR ALERTS --}}
        {{-- (Script JS di bawah yang akan menangani Flash Session ke SweetAlert) --}}

        {{-- TABLE --}}
        <div class="overflow-x-auto w-full">
            <form id="bulk-delete-form" action="{{ route('walikelas.students.bulkDelete') }}" method="POST" class="hidden"> 
                @csrf @method('DELETE') 
            </form>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4 text-center w-12">
                            <input type="checkbox" id="check-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Identitas (NIS/N)</th>
                        <th class="px-6 py-4">L/P</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="group hover:bg-indigo-50/30 transition duration-200">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="selected_students[]" value="{{ $student->nisn }}" class="bulk-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-sm text-gray-400 font-medium">
                                {{ $loop->iteration + (($students->currentPage() - 1) * $students->perPage()) }}
                            </td>
                            {{-- Siswa --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($student->photo && $student->photo != 'default_avatar.png')
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm" src="{{ asset('storage/' . $student->photo) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-100">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $student->name }}</div>
                                       
                                    </div>
                                </div>
                            </td>
                            {{-- Identitas --}}
                            <td class="px-6 py-4">
                                <span class="block text-sm font-medium text-gray-800">{{ $student->nisn }}</span>
                                <span class="block text-xs text-gray-500 tracking-wide font-mono mt-0.5">{{ $student->nis ?? '-' }}</span>
                            </td>
                            {{-- L/P --}}
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold px-2 py-1 rounded {{ $student->gender == 'Laki-laki' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                    {{ substr($student->gender, 0, 1) }}
                                </span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if($student->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-1 opacity-80 group-hover:opacity-100 transition">
                                     {{-- Detail --}}
                                     <a href="{{ route('walikelas.students.show', $student->nisn) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Print --}}
                                    <a href="{{ route('walikelas.students.barcode', $student->nisn) }}" target="_blank" 
                                       onclick="window.open(this.href, 'CetakBarcode', 'width=600,height=800'); return false;"
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition" title="Cetak Barcode">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('walikelas.students.edit', $student->nisn) }}" 
                                       class="p-1.5 rounded-lg text-gray-500 hover:bg-amber-50 hover:text-amber-600 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <button type="button" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" 
                                            onclick="confirmDelete({{ $student->nisn }}, '{{ $student->name }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $student->nisn }}" action="{{ route('walikelas.students.destroy', $student->nisn) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="bg-gray-50 rounded-full h-24 w-24 flex items-center justify-center mb-4">
                                        <i class="fas fa-user-graduate text-gray-300 text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak ada Data Siswa</h3>
                                    <p class="text-gray-500 text-sm mb-6 max-w-sm">Tambahkan siswa baru untuk memulai.</p>
                                    <a href="{{ route('walikelas.students.create') }}" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                                        <i class="fas fa-plus mr-2"></i> Tambah Siswa Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($students->hasPages())
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4">
                {{ $students->appends(['search' => request('search')])->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" class="hidden"></div>
    @endif
    @if(session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    // Config colors
    const SWAL_COLOR = {
        danger: '#dc2626', confirm: '#4f46e5', success: '#10b981', cancel: '#6b7280', warning: '#f59e0b',
    };

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Siswa?',
            text: `Yakin ingin menghapus "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: SWAL_COLOR.danger,
            cancelButtonColor: SWAL_COLOR.cancel,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((r) => { 
            if (r.isConfirmed) document.getElementById(`delete-form-${id}`).submit(); 
        });
    }

    function confirmBulkDelete() {
        const selectedIds = $('input.bulk-checkbox:checked').map(function(){ return this.value; }).get();
        if (selectedIds.length === 0) return Swal.fire('Perhatian!', 'Pilih minimal satu siswa.', 'info');
        
        Swal.fire({
            title: 'Hapus Massal?',
            text: `Anda akan menghapus ${selectedIds.length} siswa terpilih.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: SWAL_COLOR.danger,
            cancelButtonColor: SWAL_COLOR.cancel,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((r) => { 
            if (r.isConfirmed) {
                const bulkForm = $('#bulk-delete-form');
                bulkForm.empty();
                bulkForm.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                bulkForm.append('<input type="hidden" name="_method" value="DELETE">');
                selectedIds.forEach(id => {
                    bulkForm.append('<input type="hidden" name="selected_students[]" value="' + id + '">');
                });
                bulkForm.removeClass('hidden').submit();
            }
        });
    }

    $(document).ready(function() {
        $('#check-all').on('click', function() {
            $('input.bulk-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
        });
        $(document).on('change', 'input.bulk-checkbox, #check-all', function() {
            const count = $('input.bulk-checkbox:checked').length;
            $('#selected-count').text(count);
            count > 0 ? $('#bulk-actions').removeClass('hidden').addClass('flex') : $('#bulk-actions').addClass('hidden').removeClass('flex');
        });

        // Show SweetAlert from Flash
        const successMsg = $('#flash-success').data('message');
        if(successMsg) Swal.fire({ icon: 'success', title: 'Berhasil', text: successMsg, timer: 3000, showConfirmButton: false });

        const errorMsg = $('#flash-error').data('message');
        if(errorMsg) Swal.fire({ icon: 'error', title: 'Gagal', text: errorMsg });
    });
</script>
@stop