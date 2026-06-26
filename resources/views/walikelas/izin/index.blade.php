@extends('layouts.adminlte')

@section('title', 'Permintaan Izin Siswa')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER & BREADCRUMB --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
         <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Permintaan Izin/Sakit</h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1" aria-label="Breadcrumb">
                <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Permintaan Izin</span>
            </nav>
        </div>
    </div>

    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl relative shadow-sm flex items-center alert-dismissible">
            <i class="fas fa-check-circle mr-3 text-lg"></i> 
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl relative shadow-sm flex items-center alert-dismissible">
            <i class="fas fa-ban mr-3 text-lg"></i> 
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- INFO CARD --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-3xl shadow-xl text-white p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="bg-white/20 p-4 rounded-2xl mr-4 backdrop-blur-sm">
                    <i class="fas fa-envelope-open-text text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Daftar Pengajuan Izin</h3>
                    <p class="text-indigo-100 text-sm opacity-90">
                        Proses pengajuan izin atau sakit dari orang tua siswa kelas Anda.
                    </p>
                </div>
            </div>
            {{-- Simple Stat Pill --}}
            <div class="bg-white/20 backdrop-blur-md px-5 py-2 rounded-xl border border-white/10">
                 <span class="text-xs font-bold uppercase tracking-wider text-indigo-100 block text-center">Total Request</span>
                 <span class="text-2xl font-extrabold text-center block">{{ $izinRequests->total() }}</span>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Tanggal Izin</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Status & Waktu</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($izinRequests as $request)
                    <tr class="hover:bg-gray-50/50 transition duration-150 group">
                        {{-- Siswa --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center mr-3 font-bold text-sm">
                                    {{ substr($request->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">{{ $request->student->name ?? '-' }}</span>
                                    <span class="block text-xs text-gray-500">{{ $request->student->class->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 align-top">
                             <div class="text-sm font-semibold text-gray-800">
                                <i class="far fa-calendar-alt text-gray-400 mr-1"></i>
                                {{ $request->request_date->translatedFormat('d M Y') }}
                            </div>
                        </td>

                        {{-- Jenis --}}
                        <td class="px-6 py-4 align-top">
                            @php
                                $typeClass = $request->type == 'Sakit' ? 'bg-rose-100 text-rose-700 border-rose-200' : 'bg-amber-100 text-amber-700 border-amber-200';
                                $icon = $request->type == 'Sakit' ? 'fa-procedures' : 'fa-clipboard-list';
                            @endphp
                            <span class="px-3 py-1 inline-flex items-center text-xs font-bold rounded-lg border {{ $typeClass }}">
                                <i class="fas {{ $icon }} mr-1.5"></i> {{ $request->type }}
                            </span>
                        </td>

                        {{-- Keterangan --}}
                        <td class="px-6 py-4 align-top">
                            <p class="text-sm text-gray-600 italic line-clamp-2" title="{{ $request->reason }}">
                                "{{ $request->reason }}"
                            </p>
                            @if($request->attachment_path)
                                <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank" 
                                   class="inline-flex items-center mt-2 text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    <i class="fas fa-paperclip mr-1"></i> Lihat Lampiran
                                </a>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 align-top">
                            @php
                                $statusBtn = match($request->status) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'Approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border {{ $statusBtn }}">
                                {{ $request->status }}
                            </span>
                            <div class="text-xs text-gray-400 mt-1">
                                <i class="far fa-clock mr-1"></i> {{ $request->created_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- AKSI --}}
                        {{-- AKSI --}}
                        <td class="px-6 py-4 align-top text-center">
                            <div class="flex justify-center items-center space-x-2">
                                @if($request->status === 'Pending')
                                    <button onclick="confirmProcess('{{ $request->id }}', 'approve')" 
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-green-50 text-green-600 hover:bg-green-100 hover:scale-110 transition shadow-sm border border-green-200"
                                            title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="confirmProcess('{{ $request->id }}', 'reject')" 
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:scale-110 transition shadow-sm border border-red-200"
                                            title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    {{-- Jika sudah diproses, tampilkan badge status kecil saja atau kosong --}}
                                @endif

                                {{-- Tombol Hapus: Selalu muncul (baik Pending maupun Selesai) --}}
                                <button onclick="confirmProcess('{{ $request->id }}', 'delete')" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 hover:scale-110 transition shadow-sm border border-gray-200 hover:border-red-200"
                                        title="Hapus Data">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 rounded-full p-4 mb-3">
                                    <i class="fas fa-inbox text-gray-300 text-3xl"></i>
                                </div>
                                <h4 class="text-gray-900 font-bold">Tidak ada permintaan baru</h4>
                                <p class="text-gray-500 text-sm mt-1">Belum ada data izin/sakit yang perlu diproses.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($izinRequests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $izinRequests->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Konfirmasi SweetAlert
    // Konfirmasi SweetAlert
    window.confirmProcess = function(id, action) {
        let title, text, confirmBtnColor, confirmBtnText, method;

        if (action === 'approve') {
            title = 'Setujui Permintaan?';
            text = 'Siswa akan dicatat sebagai Izin/Sakit pada tanggal absensi.';
            confirmBtnColor = '#4f46e5'; // Indigo
            confirmBtnText = 'Ya, Setujui';
            method = 'POST';
        } else if (action === 'reject') {
            title = 'Tolak Permintaan?';
            text = 'Permintaan akan ditolak dan tidak masuk absensi.';
            confirmBtnColor = '#ef4444'; // Red
            confirmBtnText = 'Ya, Tolak';
            method = 'POST';
        } else if (action === 'delete') {
            title = 'Hapus Data?';
            text = 'Data pengajuan ini akan dihapus permanen. Lanjutkan?';
            confirmBtnColor = '#d946ef'; // Fuchsia/Pinkish for warning but different from danger
            confirmBtnText = 'Ya, Hapus';
            method = 'DELETE';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#9ca3af',
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Form Submit Logic
                const form = document.createElement('form');
                
                // Construct URL correctly
                if (action === 'delete') {
                     // Route: walikelas/izin/{id}
                     form.action = '{{ url('walikelas/izin') }}/' + id;
                } else {
                     // Route: walikelas/izin/{id}/{action}
                     form.action = '{{ url('walikelas/izin') }}/' + id + '/' + action; 
                }

                form.method = 'POST'; // Laravel method spoofing requires POST
                form.style.display = 'none';

                const csrfToken = document.createElement('input');
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add Method Spoofing for DELETE if needed
                if (method === 'DELETE') {
                    const methodInput = document.createElement('input');
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                }

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Auto Hide Alerts
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@endsection