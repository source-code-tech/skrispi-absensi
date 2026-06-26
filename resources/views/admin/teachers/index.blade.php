@extends('layouts.adminlte')

@section('title', 'Manajemen Data Wali Kelas')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Purple --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i> 
            <span>Manajemen Data Wali Kelas</span>
        </h1>
        <small class="text-sm text-gray-500 block mt-1">Kelola akun wali kelas dan penugasan kelas.</small>
    </div>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Wali Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="space-y-6">
    
    {{-- PAGE HEADER WITH ACTIONS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Wali Kelas</h2>
            <p class="text-sm text-gray-500">Total <span class="font-bold text-purple-600">{{ count($teachers) }}</span> Wali Kelas terdaftar.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            {{-- Tambah Wali Kelas --}}
            <a href="{{ route('teachers.create') }}" 
               class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white 
                      bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                      shadow-lg shadow-purple-200 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Tambah Wali Kelas
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        
        {{-- TABLE --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4">Email (Login)</th>
                        <th class="px-6 py-4">Kelas Diampu</th>
                        <th class="px-6 py-4 text-center">Akses Instan (QR)</th> {{-- Judul Kolom Baru --}}
                        <th class="px-6 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
    @forelse($teachers as $teacher)
        {{-- Tambahkan items-center di sini agar semua isi baris sejajar secara vertikal --}}
        <tr class="group hover:bg-purple-50/30 transition duration-200 items-center">
            
            {{-- No --}}
            <td class="px-6 py-4 text-center text-sm text-gray-400 font-medium">
                {{ $loop->iteration }}
            </td>

            {{-- Nama --}}
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold border border-purple-100 mr-3 flex-shrink-0">
                        {{ substr($teacher->name, 0, 1) }}
                    </div>
                    <span class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition">{{ $teacher->name }}</span>
                </div>
            </td>

            {{-- Email --}}
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                {{ $teacher->email }}
            </td>

            {{-- Kelas (Sudah diberi whitespace-nowrap agar badge tidak patah) --}}
            <td class="px-6 py-4 whitespace-nowrap">
                @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->class)
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                        {{ $teacher->homeroomTeacher->class->name }} 
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                        Belum Diampu
                    </span>
                @endif
            </td>
            
            {{-- Akses Instan --}}
            <td class="px-6 py-4 text-center whitespace-nowrap">
                @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->scan_token)
                    @php $bypassUrl = route('walikelas.quick_scan', $teacher->homeroomTeacher->scan_token); @endphp
                    <div class="flex items-center justify-center gap-2">
                        <input type="text" id="bypassLinkInput_{{ $teacher->username }}" class="hidden" value="{{ $bypassUrl }}">
                        <button type="button" onclick="copyAdminBypassLink('{{ $teacher->username }}')" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition shadow-sm">
                            <i class="fas fa-copy mr-1"></i> Salin
                        </button>
                        <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($bypassUrl) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                            <i class="fas fa-qrcode mr-1"></i> QR
                        </a>
                    </div>
                @else
                    <span class="text-xs text-gray-400 italic">Token Kosong</span>
                @endif
            </td>

            {{-- Aksi --}}
            <td class="px-6 py-4 text-center whitespace-nowrap">
                <div class="flex justify-center space-x-1">
                    <a href="{{ route('teachers.edit', $teacher->username) }}" class="p-2 rounded-lg text-gray-500 hover:bg-amber-50 hover:text-amber-600 transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="p-2 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" onclick="confirmDelete({{ $teacher->username }}, '{{ $teacher->name }}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        {{-- Handle empty tetap sama --}}
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// --- LOGIKA JAVASCRIPT UTAMA ---

const SWAL_COLOR = {
    danger: '#dc2626', // red-600
    confirm: '#7c3aed', // purple-600 (Custom for Teachers)
    cancel: '#6b7280', // gray-500
};

// Fungsi Salin Link Bypass Baru
function copyAdminBypassLink(userId) {
    const copyText = document.getElementById(`bypassLinkInput_${userId}`);
    
    if (copyText) {
        navigator.clipboard.writeText(copyText.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: 'Link bypass login berhasil disalin ke clipboard.',
                confirmButtonColor: SWAL_COLOR.confirm,
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(err => {
            alert("Gagal menyalin link: " + err);
        });
    }
}

// Fungsi Hapus Global
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Wali Kelas?',
        text: `Yakin ingin menghapus akun "${name}"? Penugasan kelas akan otomatis terhapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: SWAL_COLOR.danger,
        cancelButtonColor: SWAL_COLOR.cancel,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true, 
    }).then((r) => { 
        if (r.isConfirmed) {
            const form = document.getElementById(`delete-form-${id}`);
            if (form) {
                form.submit(); 
            } else {
                Swal.fire('Error', 'Form hapus tidak ditemukan.', 'error');
            }
        }
    });
}

// 💡 DIRECT INJECTION UNTUK MEMASTIKAN ALERT MUNCUL
@if(session('success'))
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: {!! json_encode(session('success')) !!},
                confirmButtonText: 'Oke',
                confirmButtonColor: SWAL_COLOR.confirm,
                timer: 4000,
                timerProgressBar: true
            });
        }, 500); 
    });
@endif

@if(session('error'))
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonText: 'Tutup',
                confirmButtonColor: SWAL_COLOR.danger
            });
        }, 500);
    });
@endif
</script>
@stop