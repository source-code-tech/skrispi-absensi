{{-- resources/views/layouts/partials/footer.blade.php (UPDATED UI) --}}

@php
    // --- LOGIKA PHP (Tidak Diubah) ---
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Sekolah';
@endphp

{{-- Menghapus 'fixed bottom-0 right-0 left-0 md:left-64 z-20' --}}
<footer class="mt-8 pt-4 pb-20 md:pb-6 
                bg-gray-100/50 border-t border-gray-200 
                text-sm text-gray-600 
                px-4 sm:px-6 
                transition-all duration-300 ease-in-out">

    {{-- Container Footer --}}
    <div class="flex flex-col sm:flex-row justify-between items-start w-full max-w-7xl mx-auto">
        
        {{-- Sisi Kanan: Versi Aplikasi (Tambahan opsional) --}}
        <div class="order-1 sm:order-2 mb-2 sm:mb-0 text-xs text-right w-full sm:w-auto">
             <span class="text-gray-400">Versi: </span>
             <strong class="font-medium text-gray-500">v1.1.0</strong>
        </div>
        
        {{-- Sisi Kiri: Copyright Info --}}
        <div class="order-2 sm:order-1 text-left w-full sm:w-auto">
            <strong class="font-semibold text-gray-700">
                Copyright &copy; {{ date('Y') }} 
                {{-- Mengganti text-blue-600 menjadi text-indigo-600 --}}
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-bold">
                    {{ $schoolName }}
                </a>.
            </strong> 
            <span class="block sm:inline text-xs text-gray-500 font-light mt-1 sm:mt-0">
                All rights reserved. 
            </span>
        </div>
    </div>
</footer>