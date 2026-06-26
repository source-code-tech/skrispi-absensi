@extends('layouts.adminlte')

@section('title', 'Detail Siswa: ' . ($student->name ?? 'N/A'))

@section('content')
<div class="space-y-6">
    
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Detail Siswa</h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1" aria-label="Breadcrumb">
                <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('walikelas.students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition">Data Siswa</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Detail</span>
            </nav>
        </div>
        <a href="{{ route('walikelas.students.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 shadow-sm transition transform hover:-translate-y-0.5">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl relative mb-4 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl relative mb-4 flex items-center shadow-sm">
            <i class="fas fa-ban mr-3 text-lg"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: FOTO & INFORMASI UTAMA (1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 text-center overflow-hidden relative">
                {{-- Background Decoration --}}
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                
                {{-- Foto Siswa --}}
                @php
                    $photoPath = $student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo) 
                                 ? asset('storage/' . $student->photo) 
                                 : asset('images/default_avatar.png');
                @endphp
                <div class="relative w-32 h-32 mx-auto mt-8 mb-4">
                    <img src="{{ $photoPath }}" alt="Foto {{ $student->name }}" 
                         class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg relative z-10">
                </div>
                
                <h4 class="text-xl font-bold text-gray-900">{{ $student->name }}</h4>
                <p class="text-sm text-gray-500 font-medium mb-4">{{ $student->class->name ?? 'N/A' }}</p>
                
                <div class="space-y-3 border-t border-gray-100 pt-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">NISN</span>
                        <span class="font-bold text-gray-800">{{ $student->nisn }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">NIS</span>
                        <span class="font-bold text-gray-800">{{ $student->nis ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Status</span>
                        @if($student->status == 'active')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Non-Aktif</span>
                        @endif
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col space-y-2">
                    <a href="{{ route('walikelas.students.barcode', $student->nisn) }}" target="_blank"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-print mr-2"></i> Cetak Kartu
                    </a>
                    <a href="{{ route('walikelas.students.edit', $student->nisn) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-orange-200 text-sm font-bold rounded-xl shadow-sm text-orange-600 bg-orange-50 hover:bg-orange-100 transition duration-150">
                        <i class="fas fa-edit mr-2"></i> Edit Data
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DETAIL, RELASI, & RIWAYAT (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. Informasi Pribadi --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Informasi Pribadi
                    </h3>
                </div>
                <div class="p-6 text-sm space-y-4">
                
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-gray-50 pb-3">
                        <div class="text-gray-500 font-medium">Jenis Kelamin</div>
                        <div class="sm:col-span-2 text-gray-800 font-semibold">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->gender == 'Laki-laki' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                <i class="fas fa-{{ $student->gender == 'Laki-laki' ? 'mars' : 'venus' }} mr-1"></i> {{ $student->gender }}
                            </span>
                        </div>
                    </div>
                   
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-gray-50 pb-3">
                        <div class="text-gray-500 font-medium">Tempat, Tanggal Lahir</div>
                        <div class="sm:col-span-2 text-gray-800 font-semibold">
                            {{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->isoFormat('D MMMM YYYY') : '-' }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-gray-500 font-medium">Alamat</div>
                        <div class="sm:col-span-2 text-gray-800 font-semibold leading-relaxed">{{ $student->address ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- 2. RELASI ORANG TUA/WALI --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-users mr-2 text-purple-500"></i> Kontak Orang Tua / Wali
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($student->parents as $parent)
                        <div class="flex items-start justify-between bg-purple-50 rounded-xl p-4 mb-3 border border-purple-100 hover:shadow-md transition duration-200">
                            <div class="flex items-start">
                                <div class="bg-purple-200 rounded-full p-2 text-purple-700 mr-3">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div>
                                    <h5 class="text-gray-900 font-bold text-sm">{{ $parent->name }}</h5>
                                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded-full bg-white text-purple-700 border border-purple-200 mt-1 mb-1">
                                        {{ $parent->relation_status }}
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1 flex items-center">
                                        <i class="fab fa-whatsapp text-green-500 mr-1.5 text-sm"></i> 
                                        {{ $parent->phone_number }}
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Edit Button (If exists route) --}}
                            @if(Route::has('walikelas.parents.edit'))
                            <a href="{{ route('walikelas.parents.edit', $parent->nik) }}" class="text-gray-400 hover:text-orange-500 p-2 transition">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <div class="bg-gray-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-user-friends text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Belum ada data orang tua yang terhubung.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- 3. Riwayat Absensi Terakhir --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-teal-500"></i> Riwayat Absensi (Terakhir)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    @if($historyAbsences->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                             <p>Belum ada riwayat absensi.</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b border-gray-100">
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Masuk</th>
                                    <th class="px-6 py-3">Pulang</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($historyAbsences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                            {{ $absence->attendance_time->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-600">
                                            {{ $absence->attendance_time->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-600">
                                            {{ $absence->checkout_time ? $absence->checkout_time->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-3">
                                             <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full 
                                                {{ $absence->status == 'Hadir' ? 'bg-green-100 text-green-700' : 
                                                  ($absence->status == 'Terlambat' ? 'bg-amber-100 text-amber-700' : 
                                                  ($absence->status == 'Izin' ? 'bg-blue-100 text-blue-700' : 
                                                  ($absence->status == 'Sakit' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700'))) }}">
                                                {{ $absence->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Auto-dismiss alerts
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@endsection