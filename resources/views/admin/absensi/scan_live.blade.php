@extends('layouts.adminlte')

@section('title', 'Absensi QR Scan Terpusat')

@section('content_header')
{{-- HEADER: Compact Style --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2 sm:mt-4 mb-2">
    <div class="flex flex-col md:flex-row justify-between items-center bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
        {{-- Title Area --}}
        <div class="flex items-center space-x-3 sm:space-x-4 mb-2 sm:mb-0 w-full md:w-auto">
            <div class="bg-indigo-50 p-1.5 sm:p-3 rounded-lg sm:rounded-xl flex-shrink-0">
                <i class="fas fa-qrcode text-indigo-600 text-base sm:text-2xl"></i>
            </div>
            <div>
                <h1 class="text-base sm:text-2xl font-bold text-gray-800 tracking-tight">Scan Absensi</h1>
                <p class="hidden sm:block text-sm text-gray-500">Pemindaian kartu pelajar real-time</p>
            </div>
        </div>
        
        {{-- Breadcrumb / Actions --}}
        <nav class="flex space-x-2 text-[10px] sm:text-sm font-medium w-full md:w-auto justify-end items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-indigo-600 transition flex items-center">
                <i class="fas fa-home mr-1"></i> Dashboard
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                Scan
            </span>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT COLUMN: SCANNER AREA (7/12) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Camera Card --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap sm:flex-nowrap justify-between items-center gap-4">
                    {{-- Left: Title --}}
                    <div class="flex items-center">
                        <h3 class="font-bold text-gray-700 flex items-center text-lg">
                            <span class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></span>
                            Kamera Aktif
                        </h3>
                    </div>

                    {{-- Right: Controls --}}
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                        {{-- Live Badge --}}
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-green-200 text-green-700 rounded-lg text-xs font-bold shadow-sm">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            LIVE
                        </div>

                         {{-- Camera Selector --}}
                        <div id="camera-selector-container" class="hidden">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400 group-hover:text-indigo-500 transition-colors">
                                    <i class="fas fa-camera text-xs"></i>
                                </div>
                                <select id="camera-select" class="pl-9 pr-8 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all hover:border-indigo-300 w-full sm:w-auto cursor-pointer appearance-none">
                                    {{-- Options populated via JS --}}
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Camera Feed Wrapper --}}
                <div class="p-0 bg-black flex justify-center items-center relative h-[320px] sm:h-[500px] aspect-auto overflow-hidden rounded-b-3xl">
                    
                    {{-- The Scanner Element --}}
                    <div id="scanner" class="w-full h-full relative z-10"></div>

                    {{-- CSS Viewfinder Overlay --}}
                    <div class="absolute inset-0 pointer-events-none z-20 flex items-center justify-center">
                        <div class="w-2/3 h-2/3 sm:w-64 sm:h-64 border-2 border-white/30 rounded-3xl relative">
                            {{-- Corners --}}
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-indigo-500 rounded-tl-xl shadow-sm"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-indigo-500 rounded-tr-xl shadow-sm"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-indigo-500 rounded-bl-xl shadow-sm"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-indigo-500 rounded-br-xl shadow-sm"></div>
                            
                            {{-- Scanning Animation Line --}}
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-indigo-400 opacity-80 animate-scan shadow-[0_0_15px_rgba(99,102,241,0.8)]"></div>
                        </div>
                    </div>
                </div>

                {{-- Status & Footer --}}
                <div class="px-6 py-4 bg-white border-t border-gray-100 z-30 relative">
                    <div id="scan-status" class="hidden"></div>
                    <p class="text-center text-gray-400 text-xs">
                        Posisikan wajah/kartu di dalam kotak area scan.
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: RECENT ACTIVITY LOG (5/12) --}}
        <div class="lg:col-span-5 h-full">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 h-full flex flex-col overflow-hidden">
                {{-- Log Header --}}
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-700 flex items-center">
                        <i class="fas fa-history text-indigo-400 mr-2"></i> Log Aktivitas
                    </h3>
                    <button onclick="location.reload()" class="text-gray-400 hover:text-indigo-600 transition" title="Refresh Log">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                {{-- Scrollable List Area --}}
                <div class="flex-1 relative bg-gray-50/30">
                    <div class="custom-log-area h-[400px] sm:h-[600px] overflow-y-auto px-4 py-4 space-y-3" id="attendance-log-container">
                         <ul id="attendance-log" class="space-y-3">
                            @forelse($recentAbsences as $absence)
                                @php
                                    $status = $absence->status;
                                    $isOut = $absence->checkout_time != null;
                                    $isLate = $status == 'Terlambat';
                                    
                                    // Status Logic for UI
                                    if($isOut) {
                                        $borderColor = 'border-indigo-500';
                                        $bgColor = 'bg-indigo-50';
                                        $textColor = 'text-indigo-700';
                                        $icon = 'fa-door-open';
                                        $label = 'PULANG';
                                        $time = $absence->checkout_time->format('H:i');
                                    } elseif($isLate) {
                                        $borderColor = 'border-amber-500';
                                        $bgColor = 'bg-amber-50';
                                        $textColor = 'text-amber-700';
                                        $icon = 'fa-exclamation-triangle';
                                        $label = 'TERLAMBAT';
                                        $time = $absence->attendance_time->format('H:i');
                                    } else {
                                        $borderColor = 'border-emerald-500';
                                        $bgColor = 'bg-emerald-50';
                                        $textColor = 'text-emerald-700';
                                        $icon = 'fa-check';
                                        $label = 'MASUK';
                                        $time = $absence->attendance_time->format('H:i');
                                    }
                                @endphp
                                <li class="bg-white border text-sm rounded-xl p-3 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 {{ $borderColor }} group">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center {{ $textColor }}">
                                                <i class="fas {{ $icon }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 group-hover:text-indigo-600 transition">{{ $absence->student->name ?? 'Siswa' }}</p>
                                                <p class="text-xs text-gray-500">{{ $absence->student->class->name ?? 'Kelas' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-xs font-bold {{ $textColor }}">{{ $label }}</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $time }}</span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="flex flex-col items-center justify-center h-48 text-gray-400 text-center">
                                    <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-200"></i>
                                    <p>Belum ada data absensi masuk.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    
                    {{-- Fade effect at bottom --}}
                    <div class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    {{-- 💡 Load html5-qrcode dan library pendukung --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // --- Variabel Global & Konfigurasi ---
        const scanUrl = '{{ route("admin.absensi.record") }}';
        const csrfToken = '{{ csrf_token() }}';
        
        // 💡 KONFIGURASI PENTING
        const RESCAN_DELAY = 1500; // Jeda waktu (ms) sebelum boleh scan kartu BERIKUTNYA
        let isProcessing = false;  // Flag processing
        
        const html5QrCode = new Html5Qrcode("scanner"); 
        const scanStatus = $('#scan-status');

        // --- Konstanta UI ---
        // Kita menggunakan konten HTML/Tailwind langsung untuk status
        const READY_HTML = `
            <div class="flex items-center p-4 mb-4 text-green-800 rounded-xl bg-green-50 border border-green-100 shadow-sm animate__animated animate__fadeIn">
                <div class="flex-shrink-0 bg-green-200 p-2 rounded-full mr-3 text-green-600">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <span class="font-bold block">Siap Scan</span>
                    <span class="text-xs">Arahkan kartu ke kamera.</span>
                </div>
            </div>`;
            
        const PROCESSING_HTML = `
            <div class="flex items-center p-4 mb-4 text-indigo-800 rounded-xl bg-indigo-50 border border-indigo-100 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-circle-notch fa-spin text-2xl mr-3 text-indigo-500"></i>
                <div>
                    <span class="font-bold block">Memproses...</span>
                    <span class="text-xs">Mohon tunggu sebentar.</span>
                </div>
            </div>`;

        function showToast(type, message, title = 'Notifikasi') {
            let icon = 'info';
            if (type === 'success' || type === 'primary') icon = 'success';
            if (type === 'warning') icon = 'warning';
            if (type === 'danger') icon = 'error';
            
            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'colored-toast' // Optional: jika ingin custom styling lagi
                }
            });
        }

        function logToSidebar(message, studentName, className, type, time) {
            // Mapping Logic (Sama seperti View PHP)
            let borderColor, bgColor, textColor, icon, label;
            
            if (type === 'primary' || type === 'OUT') { // Pulang
                borderColor = 'border-indigo-500'; bgColor = 'bg-indigo-50'; textColor = 'text-indigo-700'; icon = 'fa-door-open'; label = 'PULANG';
            } else if (type === 'warning' || type === 'Terlambat') { // Terlambat
                borderColor = 'border-amber-500'; bgColor = 'bg-amber-50'; textColor = 'text-amber-700'; icon = 'fa-exclamation-triangle'; label = 'TERLAMBAT';
            } else if (type === 'success' || type === 'IN') { // Hadir
                borderColor = 'border-emerald-500'; bgColor = 'bg-emerald-50'; textColor = 'text-emerald-700'; icon = 'fa-check'; label = 'MASUK';
            } else { // Error/Gagal
                borderColor = 'border-red-500'; bgColor = 'bg-red-50'; textColor = 'text-red-700'; icon = 'fa-times'; label = 'GAGAL';
            }

            const logEntry = `
                <li class="bg-white border text-sm rounded-xl p-3 shadow-sm hover:shadow-md transition-all duration-300 border-l-4 ${borderColor} animate__animated animate__fadeInLeft mb-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full ${bgColor} flex items-center justify-center ${textColor}">
                                <i class="fas ${icon}"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">${studentName}</p>
                                <p class="text-xs text-gray-500">${className}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold ${textColor}">${label}</span>
                            <span class="text-xs text-gray-400 font-mono">${time}</span>
                        </div>
                    </div>
                </li>`;
            
            $('#attendance-log').prepend(logEntry);
            
            // Clean empty message and trim list
            $('#attendance-log').find('li').filter(function() { return $(this).text().includes('Belum ada data'); }).remove();
            while ($('#attendance-log').children().length > 15) { $('#attendance-log').find('li').last().remove(); }
        }

        function playBeep(isSuccess) {
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const osc = context.createOscillator();
            const gain = context.createGain();
            osc.connect(gain);
            gain.connect(context.destination);
            
            if(isSuccess) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, context.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, context.currentTime + 0.1);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(200, context.currentTime);
                osc.frequency.linearRampToValueAtTime(100, context.currentTime + 0.3);
            }
            
            gain.gain.setValueAtTime(0.1, context.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + (isSuccess ? 0.3 : 0.5));
            osc.start(context.currentTime);
            osc.stop(context.currentTime + (isSuccess ? 0.3 : 0.5));
        }

        function processBarcode(code) {
            isProcessing = true;
            scanStatus.removeClass('hidden').html(PROCESSING_HTML);

            $.ajax({
                url: scanUrl,
                method: 'POST',
                data: { _token: csrfToken, barcode: code },
                success: function(response) {
                    playBeep(true);
                    
                    let type = 'danger'; // Default
                    if (response.type === 'IN') type = response.status === 'Terlambat' ? 'warning' : 'success';
                    else if (response.type === 'OUT') type = 'primary';
                    
                    let time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    showToast(type, response.message);
                    logToSidebar(response.message, response.student.name, response.student.class, type, time);
                    
                    // Reset UI
                    scanStatus.html(READY_HTML);
                },
                error: function(xhr) {
                    playBeep(false);
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Kesalahan Server.';
                    showToast('danger', errorMsg);
                    
                    // Show error in panel momentarily
                    const errorHtml = `
                        <div class="flex items-center p-4 mb-4 text-red-800 rounded-xl bg-red-50 border border-red-100 shadow-sm animate__animated animate__headShake">
                            <i class="fas fa-exclamation-circle text-2xl mr-3 text-red-500"></i>
                            <div>
                                <span class="font-bold block">Gagal Scan</span>
                                <span class="text-xs">${errorMsg}</span>
                            </div>
                        </div>`;
                    scanStatus.html(errorHtml);
                },
                complete: function() {
                    setTimeout(() => {
                        isProcessing = false;
                        // Return to Ready state unless error persists (logic simplified)
                         scanStatus.html(READY_HTML);
                    }, RESCAN_DELAY);
                }
            });
        }
        
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            if (!decodedText || decodedText.length < 3) return;
            processBarcode(decodedText);
        }

        $(document).ready(function() {
            const config = { fps: 15, qrbox: { width: 250, height: 250 } }; // Aspect Ratio removed for responsiveness
            
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                scanStatus.removeClass('hidden').html(READY_HTML);
            })
            .catch((err) => {
                const errHtml = `
                    <div class="p-4 mb-4 text-red-800 rounded-xl bg-red-100 border border-red-200">
                        <strong>Kamera Error:</strong> ${err.message || 'Tidak dapat akses kamera.'}
                    </div>`;
                scanStatus.removeClass('hidden').html(errHtml);
            });
        });

        $(window).on('beforeunload', function(){
            if (html5QrCode.isScanning()) html5QrCode.stop().catch(e=>{});
        });
    </script>
@endsection

@section('css')
<style>
    /* Premium Scan Animation */
    @keyframes scan {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    .animate-scan {
        animation: scan 2.5s infinite linear;
    }
    
    /* Scrollbar Styling */
    .custom-log-area::-webkit-scrollbar {
        width: 6px;
    }
    .custom-log-area::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .custom-log-area::-webkit-scrollbar-thumb {
        background: #c7c7c7; 
        border-radius: 4px;
    }
    .custom-log-area::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0; 
    }
    
    /* Scanner Object Fit */
    #scanner video {
        width: 100% !important; 
        height: 100% !important; 
        object-fit: cover;
        border-radius: 0.75rem; /* rounded-xl matches Tailwind */
    }
</style>
@endsection