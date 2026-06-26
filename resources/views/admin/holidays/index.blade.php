@extends('layouts.adminlte')

@section('title', 'Kelola Hari Libur')

@section('content')
<div class="bg-white rounded-3xl shadow p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <span class="bg-red-100 p-2 rounded-lg mr-3 text-red-600">
                <i class="fas fa-calendar-times"></i>
            </span>
            Kelola Hari Libur & Tanggal Merah
        </h2>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- SYNC GOOGLE CALENDAR --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6">
        <div class="flex items-center mb-3">
            <span class="bg-blue-100 p-2 rounded-lg mr-3 text-blue-600">
                <i class="fab fa-google"></i>
            </span>
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Sync dari Google Calendar</h3>
                <p class="text-xs text-gray-500">Import hari libur nasional Indonesia otomatis. Data yang sudah ada tidak akan duplikat.</p>
            </div>
        </div>
        <form action="{{ route('admin.holidays.sync') }}" method="POST" class="flex items-center gap-3 flex-wrap">
            @csrf
            <select name="year" class="border border-blue-200 bg-white rounded-xl px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition flex items-center">
                <i class="fas fa-sync-alt mr-2"></i> Sync Sekarang
            </button>
        </form>
    </div>

    {{-- Form Tambah Manual --}}
    <form action="{{ route('admin.holidays.store') }}" method="POST" 
          class="bg-gray-50 rounded-2xl p-5 mb-6 flex gap-4 flex-wrap items-end border border-gray-100">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="date" value="{{ old('date') }}" required
                class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   placeholder="contoh: Hari Raya Idul Fitri" required
                class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-plus mr-1"></i> Tambah Manual
            </button>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Hari</th>
                    <th class="px-4 py-3 text-left">Keterangan</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($holidays as $i => $holiday)
                <tr class="border-t border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $holiday->date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $holiday->date->translatedFormat('l') }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $holiday->name }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST"
                            onsubmit="return confirm('Hapus hari libur ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-1 rounded-lg text-xs font-semibold transition">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">
                        <i class="fas fa-calendar-check text-3xl mb-2 block"></i>
                        Belum ada hari libur. Coba sync dari Google Calendar!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@stop