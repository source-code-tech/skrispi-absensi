@extends('layouts.adminlte')

@section('title', 'Manajemen Data Kelas')

{{-- Content Header removed in favor of custom header in content section --}}

@section('content')
    {{-- PAGE HEADER WITH ACTIONS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Data Kelas</h2>
            <p class="text-sm text-gray-500">Kelola daftar kelas, tingkat, dan jurusan.</p>
        </div>
        <a href="{{ route('classes.create') }}" 
           class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white 
                  bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 
                  shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Tambah Kelas
        </a>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        
        {{-- SUCCESS/ERROR ALERTS (Absolute positioning within padding) --}}
        @if (session('success') || session('error'))
            <div class="px-6 pt-6">
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-100/50 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                        <button type="button" class="ml-auto text-emerald-400 hover:text-emerald-600" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-100/50 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500 text-lg"></i>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                        <button type="button" class="ml-auto text-red-400 hover:text-red-600" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        {{-- TABLE CONTAINER --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4 text-center">Nama Kelas</th>
                        <th class="px-6 py-4">Tingkat</th>
                        <th class="px-6 py-4">Jurusan</th>
                        <th class="px-6 py-4 text-center">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($classes as $class)
                        <tr class="group hover:bg-indigo-50/30 transition duration-200">
                            <td class="px-6 py-4 text-center text-sm text-gray-400 font-medium">
                                {{ $loop->iteration + (($classes->currentPage() - 1) * $classes->perPage()) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($class->code, 0, 2) }}
                                    </div>
                                   
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $gradeColors = [
                                        '7' => 'bg-cyan-100 text-cyan-800',
                                        '8' => 'bg-blue-100 text-blue-800',
                                        '9' => 'bg-indigo-100 text-indigo-800',
                                        '10' => 'bg-orange-100 text-orange-800',
                                        '11' => 'bg-pink-100 text-pink-800',
                                        '12' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $badge = $gradeColors[$class->grade] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                    Kelas {{ $class->grade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($class->major)
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-graduation-cap text-gray-300 mr-2"></i> {{ $class->major }}
                                    </span>
                                @else
                                    <span class="text-gray-300 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- Placeholder Count (Bisa diupdate di Controller nanti untuk menggunakan withCount) --}}
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $class->students_count ?? '-' }} Siswa
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2 opacity-80 group-hover:opacity-100 transition">
                                    <a href="{{ route('classes.edit', $class->code) }}" 
                                       class="p-2 rounded-lg text-amber-500 hover:bg-amber-50 transition" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition" 
                                            title="Hapus"
                                            onclick="confirmDelete({{ $class->code }}, '{{ $class->code }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $class->code }}" action="{{ route('classes.destroy', $class->code) }}" method="POST" class="hidden"> 
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="bg-gray-50 rounded-full h-24 w-24 flex items-center justify-center mb-4">
                                        <i class="fas fa-school text-gray-300 text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada Data Kelas</h3>
                                    <p class="text-gray-500 text-sm mb-6 max-w-sm">Mulai dengan menambahkan kelas baru untuk mengatur struktur sekolah Anda.</p>
                                    <a href="{{ route('classes.create') }}" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                                        <i class="fas fa-plus mr-2"></i> Tambah Kelas Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($classes->hasPages())
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4">
                {{ $classes->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@stop

@section('js')
{{-- SweetAlert JS dan Logika Delete Confirmation (Logika Tidak Diubah, hanya warna) --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
function confirmDelete(id, className) {
    Swal.fire({
        title: 'Hapus Kelas?',
        html: `Yakin ingin menghapus kelas <strong>${className}</strong>?<br>
        Data terkait seperti siswa dan wali kelas juga dapat terpengaruh.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // red-600
        cancelButtonColor: '#4f46e5', // indigo-600
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}

$(document).ready(function() {
    // Menghilangkan alert (menggunakan jQuery yang sudah dimuat)
    setTimeout(() => $('.alert').slideUp(300, function() { $(this).remove(); }), 5000); 
    
    // Tooltip (Jika Anda memiliki JS Bootstrap dimuat di master layout)
    // Jika tidak menggunakan Bootstrap JS, hapus baris ini atau ganti dengan library tooltip Tailwind
    // $('[title]').tooltip();
});
</script>
@stop