@extends('layouts.adminlte')

@section('title', 'Kelola Mata Pelajaran')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER & BREADCRUMB --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Mata Pelajaran</h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Mapel</span>
            </nav>
        </div>
        <a href="{{ route('admin.subjects.create') }}" 
           class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Tambah Mapel
        </a>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- TABLE HEADER --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-book-open text-purple-600 mr-2"></i> Daftar Mata Pelajaran
            </h3>
        </div>

        {{-- TABLE CONTENT --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4 w-32">Kode</th>
                        <th class="px-6 py-4">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subjects as $index => $subject)
                    <tr class="hover:bg-gray-50/50 transition duration-150 group">
                        <td class="px-6 py-4 text-center font-medium text-gray-500">
                            {{ $subjects->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200 font-mono">
                                {{ $subject->code ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700">
                            {{ $subject->name }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center space-x-2">
                                <a href="{{ route('admin.subjects.edit', $subject->code) }}" 
                                   class="w-8 h-8 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-110 transition shadow-sm border border-amber-200"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="deleteSubject({{ $subject->code }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:scale-110 transition shadow-sm border border-red-200"
                                        title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                
                                <form id="delete-form-{{ $subject->code }}" action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 rounded-full p-4 mb-3">
                                    <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                                </div>
                                <h4 class="text-gray-900 font-bold">Belum ada data</h4>
                                <p class="text-gray-500 text-sm mt-1">Silakan tambahkan mata pelajaran baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($subjects->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteSubject(id) {
        Swal.fire({
            title: 'Hapus Mata Pelajaran?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Red
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@stop
