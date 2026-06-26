@extends('layouts.adminlte')

@section('title', 'Kelola Pengumuman')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-bullhorn text-purple-600 mr-3"></i>
            Kelola Pengumuman
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Buat pengumuman untuk seluruh siswa atau kelas tertentu.</p>
    </div>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-purple-50/30 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-list mr-2"></i> Daftar Pengumuman</h3>
                
                {{-- Update Link ke Route Create --}}
                <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl shadow hover:bg-indigo-700 transition duration-150 font-bold text-sm flex items-center">
                    <i class="fas fa-plus mr-2"></i> Buat Pengumuman
                </a>
            </div>
            
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Target</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($announcements as $announcement)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $announcement->title }}</div>
                                    <div class="text-sm text-gray-500 truncate w-64">{{ \Illuminate\Support\Str::limit($announcement->content, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($announcement->target_type == 'all')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Semua Kelas</span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Kelas {{ $announcement->class->name ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 {{ $announcement->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-bold">
                                        {{ $announcement->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $announcement->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        {{-- Tombol Show --}}
                                        <a href="{{ route('announcements.show', $announcement->id) }}" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('announcements.edit', $announcement->id) }}" class="text-indigo-600 hover:text-indigo-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus pengumuman ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50">
                                    <i class="far fa-clipboard fa-3x mb-3 text-gray-300"></i>
                                    <p>Belum ada pengumuman yang dibuat.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $announcements->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Auto-dismiss alerts (jika ada)
    setTimeout(function() {
         $('.alert').fadeOut(400);
    }, 5000);
</script>
@stop
