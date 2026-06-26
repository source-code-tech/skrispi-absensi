@extends('layouts.adminlte')

@section('title', 'Detail Absensi Anak')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
        <i class="fas fa-clipboard-list text-indigo-600 mr-2"></i>
        <span>Detail Absensi - {{ $absence->student->name ?? 'Anak' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('orangtua.report.index') }}" class="text-indigo-600 hover:text-indigo-800">Riwayat</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Detail</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="space-y-6">

    {{-- INFO UTAMA --}}
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        
        {{-- Header Card --}}
        <div class="p-6 border-b border-gray-100 bg-indigo-50/30 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Riwayat Tanggal {{ $absence->attendance_time->format('d M Y') }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $absence->student->name ?? '-' }} • Kelas {{ $absence->student->class->name ?? '-' }}
                </p>
            </div>
            @php
                $statusStyle = match($absence->status) {
                    'Hadir'     => 'bg-green-100 text-green-700',
                    'Terlambat' => 'bg-amber-100 text-amber-700',
                    'Alpha'     => 'bg-red-100 text-red-700',
                    'Izin'      => 'bg-blue-100 text-blue-700',
                    'Sakit'     => 'bg-cyan-100 text-cyan-700',
                    default     => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusStyle }}">
                {{ $absence->status }}
            </span>
        </div>

        {{-- Body Card --}}
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            {{-- Kolom Kiri --}}
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Anak</p>
                    <p class="text-sm font-bold text-gray-800">{{ $absence->student->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kelas</p>
                    <p class="text-sm font-bold text-gray-800">{{ $absence->student->class->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusStyle }}">{{ $absence->status }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</p>
                    <p class="text-sm text-gray-700">{{ $absence->notes ?? 'Tidak ada' }}</p>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Waktu Masuk</p>
                    <p class="text-sm font-bold text-gray-800">
                        <i class="fas fa-sign-in-alt text-green-500 mr-1"></i>
                        {{ $absence->attendance_time->format('H:i:s') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Waktu Pulang</p>
                    <p class="text-sm font-bold text-gray-800">
                        <i class="fas fa-sign-out-alt text-red-400 mr-1"></i>
                        {{ $absence->checkout_time?->format('H:i:s') ?? '-' }}
                    </p>
                </div>
                @if($absence->late_duration)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Durasi Terlambat</p>
                    <p class="text-sm font-bold text-amber-600">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $absence->late_duration }} menit
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- LOG KOREKSI --}}
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 flex items-center">
                <span class="bg-indigo-100 p-2 rounded-lg mr-3 text-indigo-600">
                    <i class="fas fa-history text-sm"></i>
                </span>
                Log Koreksi Manual
            </h3>
        </div>
        <div class="p-6">
            @if($absence->is_manual_corrected)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <div class="flex items-start">
                        <div class="bg-amber-100 p-2 rounded-xl mr-4">
                            <i class="fas fa-edit text-amber-600"></i>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-bold text-amber-800">
                                Record ini telah dikoreksi oleh staf sekolah
                            </p>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p>
                                    <span class="font-semibold">Pengoreksi:</span>
                                    {{ $absence->corrected_by ?? 'N/A' }}
                                </p>
                                <p>
                                    <span class="font-semibold">Alasan Koreksi:</span>
                                    <em>{{ $absence->correction_note ?? 'Tidak ada catatan.' }}</em>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                    <div class="flex items-start">
                        <div class="bg-green-100 p-2 rounded-xl mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <p class="text-sm font-medium text-green-800">
                            Data ini tercatat secara otomatis melalui sistem scan dan belum pernah diubah.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div>
        <a href="{{ route('orangtua.report.index') }}"
           class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl shadow-sm transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
        </a>
    </div>

</div>
@stop