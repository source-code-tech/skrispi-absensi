<div id="global-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-500 ease-in-out">
    <div class="flex flex-col items-center">
        {{-- Spinner Modern --}}
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 rounded-full border-4 border-gray-200"></div>
            <div class="absolute inset-0 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin"></div>
            
            {{-- Logo Tengah (Opsional, jika ada logo kecil) --}}
            {{-- <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-school text-indigo-600 text-xl"></i>
            </div> --}}
        </div>
        
        {{-- Loading Text --}}
        <h3 class="mt-4 text-lg font-semibold text-gray-700 animate-pulse">
            Memuat @yield('title', 'Halaman')...
        </h3>
    </div>
</div>

<style>
    /* Mencegah scroll saat loading */
    body.loading {
        overflow: hidden !important;
    }
</style>

<script>
    (function() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            // Add loading class immediately
            document.body.classList.add('loading');

            const dismissLoader = () => {
                if (!loader) return;
                
                // Fade out
                loader.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('loading');
                
                // Remove from DOM
                setTimeout(() => {
                    loader.remove();
                }, 500);
            };

            // 1. Standard: Hide when everything is loaded
            window.addEventListener('load', dismissLoader);

            // 2. Fallback: Hide after 3 seconds max (prevent infinite loading)
            setTimeout(dismissLoader, 3000);
        }
    })();
</script>
