@extends('layouts.adminlte')

@section('title', 'Manajemen Pengguna Sistem')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-users-cog text-purple-600 mr-3"></i>
            Manajemen Pengguna
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Kelola akun, peran, dan hak akses pengguna sistem.</p>
    </div>
    
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Pengguna Sistem</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
@php
    $currentTab = $tab ?? 'all'; 
@endphp

<div class="container-fluid px-0">
    
    {{-- STATS SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
             <div class="relative z-10">
                 <h4 class="text-3xl font-bold">{{ $totalUsersCount }}</h4>
                 <p class="text-purple-100 text-sm font-medium">Total Akun Terdaftar</p>
             </div>
             <i class="fas fa-users absolute right-0 bottom-0 opacity-20 text-6xl transform translate-x-2 translate-y-2"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-lg border border-purple-100 flex items-center space-x-4">
             <div class="bg-yellow-100 p-3 rounded-xl text-yellow-600">
                 <i class="fas fa-hourglass-half text-xl"></i>
             </div>
             <div>
                 <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Menunggu</p>
                 <h4 class="text-xl font-bold text-gray-800">{{ $pendingUsersCount }}</h4> 
             </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-lg border border-purple-100 flex items-center space-x-4">
             <div class="bg-green-100 p-3 rounded-xl text-green-600">
                 <i class="fas fa-check-circle text-xl"></i>
             </div>
             <div>
                 <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Sistem</p>
                 <h4 class="text-xl font-bold text-gray-800">Normal</h4>
             </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- TABS & TOOLBAR --}}
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <div class="flex flex-col lg:flex-row justify-between lg:items-center space-y-4 lg:space-y-0">
                
                {{-- Tabs --}}
                <div class="flex space-x-1 bg-gray-200/50 p-1 rounded-xl w-full lg:w-auto overflow-x-auto">
                    @php
                        $tabBase = 'px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 whitespace-nowrap flex items-center';
                        $tabActive = 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5';
                        $tabInactive = 'text-gray-500 hover:text-gray-700 hover:bg-gray-200';
                    @endphp

                    <a href="{{ route('admin.users.index', ['tab' => 'all', 'search' => request('search')]) }}" 
                       class="{{ $tabBase }} {{ $currentTab === 'all' ? $tabActive : $tabInactive }}">
                        <i class="fas fa-users mr-2"></i> Semua
                    </a>
                    <a href="{{ route('admin.users.index', ['tab' => 'pending', 'search' => request('search')]) }}" 
                       class="{{ $tabBase }} {{ $currentTab === 'pending' ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : $tabInactive }}">
                        <i class="fas fa-clock mr-2"></i> Menunggu
                         {{-- Idealnya add badge count disini --}}
                    </a>
                    <a href="{{ route('admin.users.index', ['tab' => 'super_admin_list', 'search' => request('search')]) }}" 
                       class="{{ $tabBase }} {{ $currentTab === 'super_admin_list' ? $tabActive : $tabInactive }}">
                        <i class="fas fa-user-shield mr-2"></i> Admin
                    </a>
                </div>

                {{-- Search & Add --}}
                <div class="flex items-center space-x-3 w-full lg:w-auto">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="relative group flex-1 lg:flex-none">
                        <input type="hidden" name="tab" value="{{ $currentTab }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                        </div>
                        <input type="text" name="search" 
                               class="block w-full lg:w-64 pl-10 pr-4 py-2 border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 text-sm shadow-sm transition-all" 
                               placeholder="Cari User..." value="{{ request('search') }}">
                    </form>
                    
                    <a href="{{ route('admin.users.create') }}" 
                       class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-md transform hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-plus mr-2"></i> Baru
                    </a>
                </div>
            </div>
            
            {{-- Bulk Actions Toolbar --}}
            @if($currentTab !== 'super_admin_list')
            <div class="mt-4 flex flex-wrap items-center gap-2 pt-4 border-t border-gray-100">
                <span id="selected-count" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hidden mr-2">
                    0 Dipilih
                </span>
                
                <button type="button" onclick="confirmBulkApprove()" 
                        class="bulk-action-btn hidden px-3 py-1.5 bg-green-50 text-green-700 text-xs font-bold rounded-lg hover:bg-green-100 border border-green-200 transition-colors">
                    <i class="fas fa-check-double mr-1"></i> Setujui
                </button>
                <button type="button" onclick="confirmBulkToggle()" 
                        class="bulk-action-btn hidden px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-100 border border-amber-200 transition-colors">
                    <i class="fas fa-exchange-alt mr-1"></i> Ubah Status
                </button>
                <button type="button" onclick="confirmBulkDelete()" 
                        class="bulk-action-btn hidden px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg hover:bg-red-100 border border-red-200 transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </button>
            </div>
             {{-- Form Hidden untuk Bulk Action --}}
            <form id="bulk-action-form" method="POST" class="hidden">@csrf</form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-center w-10">
                            @if($currentTab !== 'super_admin_list')
                            <input type="checkbox" id="check-all" class="rounded text-purple-600 border-gray-300 focus:ring-purple-500">
                            @endif
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role & Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-purple-50/30 transition-colors duration-150 group">
                        <td class="px-6 py-4 text-center">
                             @if(!$user->isSuperAdmin())
                             <input type="checkbox" name="selected_users[]" value="{{ $user->username }}" class="bulk-checkbox rounded text-purple-600 border-gray-300 focus:ring-purple-500">
                             @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-600 font-bold shadow-inner">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">Joined {{ $user->created_at->format('M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                @if($user->role === 'wali_kelas')
                                    <div class="text-sm font-semibold text-gray-800 flex items-center" title="NIP (Identitas Utama)">
                                        <i class="fas fa-id-card w-4 text-purple-500 mr-2"></i> {{ $user->username }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center" title="Email (Kontak Opsional)">
                                        <i class="fas fa-envelope w-4 text-gray-400 mr-2"></i> {{ $user->email }}
                                    </div>
                                @elseif($user->role === 'orang_tua')
                                    <div class="text-sm font-semibold text-gray-800 flex items-center" title="Email (Identitas Utama)">
                                        <i class="fas fa-envelope w-4 text-purple-500 mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center" title="Username / ID">
                                        <i class="fas fa-id-card w-4 text-gray-400 mr-2"></i> {{ $user->username }}
                                    </div>
                                @else
                                    <div class="text-sm font-semibold text-gray-800 flex items-center">
                                        <i class="fas fa-envelope w-4 text-purple-500 mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-id-card w-4 text-gray-400 mr-2"></i> {{ $user->username }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <div class="flex flex-col space-y-1">
                                {{-- Role Badge --}}
                                @php
                                    $roleStyles = [
                                        'super_admin' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'wali_kelas' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'orang_tua' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    ];
                                    $style = $roleStyles[$user->role] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg border {{ $style }} w-fit">
                                    {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                </span>

                                {{-- Status Badge --}}
                                @if($user->isSuperAdmin())
                                     <span class="text-xs text-green-600 font-bold flex items-center"><i class="fas fa-check-circle mr-1"></i> System Active</span>
                                @else
                                     @if($user->is_approved)
                                        <span class="text-xs text-green-600 font-bold flex items-center"><i class="fas fa-check mr-1"></i> Disetujui</span>
                                     @else
                                        <span class="text-xs text-red-500 font-bold flex items-center"><i class="fas fa-clock mr-1"></i> Menunggu Audit</span>
                                     @endif
                                @endif
                             </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @if(!$user->isSuperAdmin())
                            <div class="flex justify-center space-x-2">
                                {{-- Toggle Approval --}}
                                <button type="button" 
                                        data-user-id="{{ $user->username }}"
                                        class="js-toggle-approval p-2 rounded-lg transition-all duration-200 border group-hover:shadow-sm {{ $user->is_approved ? 'text-amber-500 hover:bg-amber-500 hover:text-white border-amber-200' : 'text-green-500 hover:bg-green-500 hover:text-white border-green-200' }}"
                                        title="{{ $user->is_approved ? 'Tangguhkan Akun' : 'Setujui Akun' }}">
                                    <i class="fas fa-{{ $user->is_approved ? 'ban' : 'check' }}"></i>
                                </button>
                                
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user->username) }}" 
                                   class="text-blue-500 hover:text-white hover:bg-blue-500 p-2 rounded-lg transition-all duration-200 border border-blue-200 hover:border-blue-500 group-hover:shadow-sm" 
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <button type="button" 
                                        onclick="confirmDelete('{{ $user->username }}', '{{ $user->name }}')"
                                        class="text-red-500 hover:text-white hover:bg-red-500 p-2 rounded-lg transition-all duration-200 border border-red-200 hover:border-red-500 group-hover:shadow-sm"
                                        title="Hapus Akun">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            @else
                                <span class="text-gray-300"><i class="fas fa-lock"></i> Locked</span>
                            @endif

                            {{-- Hidden Forms --}}
                            <form id="delete-form-{{ $user->username }}" action="{{ route('admin.users.destroy', $user->username) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            {{-- Form Toggle Individual (Template akan digunakan di JS, ini cadangan) --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 rounded-full p-6 mb-4">
                                    <i class="fas fa-users-slash text-gray-300 text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Tidak Ada Pengguna</h3>
                                <p class="text-gray-500 mt-1">Kategori ini belum memiliki data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            {{ $users->appends(['tab' => $currentTab, 'search' => request('search')])->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Hidden Templates for JS Actions --}}
<form id="individual-toggle-form" action="" method="POST" class="hidden">@csrf @method('PUT')</form>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ---------- 1. CHECKBOX & BULK UI LOGIC ----------
    document.addEventListener('DOMContentLoaded', () => {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.bulk-checkbox');
        const selectedCountLabel = document.getElementById('selected-count');
        const bulkButtons = document.querySelectorAll('.bulk-action-btn');

        function updateBulkUI() {
            const count = document.querySelectorAll('.bulk-checkbox:checked').length;
            if(count > 0) {
                selectedCountLabel.textContent = `${count} Dipilih`;
                selectedCountLabel.classList.remove('hidden');
                bulkButtons.forEach(btn => btn.classList.remove('hidden'));
            } else {
                selectedCountLabel.classList.add('hidden');
                bulkButtons.forEach(btn => btn.classList.add('hidden'));
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateBulkUI();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateBulkUI));
    });

    // ---------- 2. SWEETALERT ACTIONS ----------
    
    // Hapus Single
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: `Akun "${name}" dan semua data terkait (Wali Kelas/Orang Tua) akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    // Toggle Approval Single (JQuery)
    $(document).on('click', '.js-toggle-approval', function() {
        const userId = $(this).data('userId');
        const form = document.getElementById('individual-toggle-form');
        
        Swal.fire({
            title: 'Ubah Status Akun?',
            text: "Status persetujuan akun ini akan diubah (Aktif <-> Nonaktif).",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Ya, Lanjutkan'
        }).then((result) => {
            if (result.isConfirmed) {
                form.action = '{{ url('admin/users') }}/' + userId + '/toggle-approval';
                form.submit();
            }
        });
    });

    // ---------- 3. BULK ACTIONS LOGIC ----------
    
    // Helper: Get IDs
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
    }

    // Submit Bulk Form
    function submitBulk(route, method = 'POST') { // Default method POST, tapi controller mungkin GET/POST. Kita cek logic lama.
        /* 
           Logic lama controller menggunakan GET untuk bulk actions di beberapa method.
           Namun best practice-nya POST. Tapi karena kita me-refactor VIEW bukan CONTROLLER, 
           kita harus ikuti controller yg ada (GET). 
           WAIT: Di code lama, performBulkSubmission pakai GET.
        */
        const ids = getSelectedIds();
        const form = document.getElementById('bulk-action-form');
        
        // Bersihkan input hidden lama
        form.innerHTML = '@csrf'; // Reset isi tapi pertahankan CSRF token (meski GET tidak butuh, tapi jaga2)

        // Tambahkan method input jika perlu spoofing (tidak perlu untuk GET standar)
        // Set Action
        form.action = route;
        form.method = 'GET'; // Controller User pakai GET untuk bulk, sesuai analisa sebelumnya

        // Append IDs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_users[]';
            input.value = id;
            form.appendChild(input);
        });

        form.submit();
    }

    // Confirm Bulk Approve
    function confirmBulkApprove() {
        const ids = getSelectedIds();
        if(ids.length === 0) return;
        
        Swal.fire({
            title: `Setujui ${ids.length} Akun?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Ya, Setujui Semua'
        }).then((res) => {
            if(res.isConfirmed) submitBulk('{{ route('admin.users.bulkApprove') }}');
        });
    }

    // Confirm Bulk Delete
    function confirmBulkDelete() {
        const ids = getSelectedIds();
        if(ids.length === 0) return;

        Swal.fire({
            title: `Hapus ${ids.length} Akun?`,
            text: 'Aksi ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Hapus Permanen'
        }).then((res) => {
            if(res.isConfirmed) submitBulk('{{ route('admin.users.bulkDelete') }}');
        });
    }

    // Confirm Bulk Toggle
    function confirmBulkToggle() {
        const ids = getSelectedIds();
        if(ids.length === 0) return;

        Swal.fire({
            title: `Ubah Status ${ids.length} Akun?`,
            text: 'Akun aktif akan jadi nonaktif, dan sebaliknya.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ya, Ubah Status'
        }).then((res) => {
            if(res.isConfirmed) submitBulk('{{ route('admin.users.bulkToggleApproval') }}');
        });
    }

    // ---------- 4. ALERT INJECTION ----------
    @if(session('success'))
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: "{!! session('success') !!}", toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
        });
    @endif
    @if(session('error'))
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: "{!! session('error') !!}", toast: true, position: 'top-end', showConfirmButton: false, timer: 5000 });
        });
    @endif

</script>
@stop