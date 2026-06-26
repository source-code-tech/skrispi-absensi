@extends('layouts.adminlte')

@section('title', 'Scan Absensi Kelas')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-lg sm:text-2xl font-bold text-gray-800 tracking-tight">Scan Absensi Harian</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">Kelas: <span class="font-bold text-indigo-600">{{ $class->name ?? 'N/A' }}</span></p>
        </div>
        <nav class="flex text-xs sm:text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Scan Absensi</span>
        </nav>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
        
        {{-- KOLOM KIRI: SCANNER (7/12) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Instructions Card --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-4 sm:p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="flex items-start md:items-center">
                    <div class="bg-white/20 p-2 sm:p-3 rounded-xl mr-3 sm:mr-4 backdrop-blur-sm">
                        <i class="fas fa-qrcode text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold">Mode Pindai Aktif</h3>
                        <p class="text-blue-100 text-xs sm:text-sm opacity-90">
                            Arahkan kartu pelajar siswa ke kamera. Sistem akan mencatat Masuk/Pulang secara otomatis.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Scanner Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex flex-wrap sm:flex-nowrap justify-between items-center bg-gray-50/50">
                    {{-- Left: Title --}}
                    <div class="flex items-center">
                        <h3 class="font-bold text-gray-800 flex items-center text-sm sm:text-lg">
                            <span class="w-1.5 h-5 sm:h-6 bg-indigo-500 rounded-full mr-2 sm:mr-3"></span>
                            Kamera Scanner
                        </h3>
                    </div>

                    {{-- Right: Status --}}
                    <div id="camera-status-indicator" class="flex items-center gap-2 px-2.5 py-1 sm:px-3 sm:py-1.5 bg-white border border-gray-200 text-gray-400 rounded-lg text-[10px] sm:text-xs font-bold shadow-sm transition-all duration-300">
                         <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gray-300"></span>
                         <span>Offline</span>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6 bg-white">
                   
                    {{-- SCANNER CONTAINER --}}
                    <div class="relative bg-black rounded-2xl overflow-hidden shadow-inner mx-auto w-full max-w-[700px] aspect-[4/3]">
                        <div id="scanner" class="w-full h-full object-cover"></div>
                        
                        {{-- Tombol Ganti Kamera (Desain Bulat Minimallist Glassmorphism Sesuai Gambar 2) --}}
                        <button id="btn-flip-camera" class="absolute top-4 right-4 z-30 bg-white/30 hover:bg-white/60 text-white w-12 h-12 rounded-full border-2 border-white/80 backdrop-blur-md transition-all duration-300 flex items-center justify-center shadow-lg hover:scale-105 active:scale-95" title="Ganti Kamera">
                            <div id="flip-icon-container" class="w-6 h-6 flex items-center justify-center">
                                {{-- SVG Ikon Flip Kamera Bawaan (Anti Gagal/Tidak Tergantung FontAwesome) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </div>
                        </button>
                        
                        {{-- Overlay Guide & Green Laser Scanner Box --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                            <div id="scanner-laser-box" class="w-64 h-64 sm:w-80 sm:h-80 border-2 border-indigo-500/50 rounded-xl relative transition-all duration-200">
                                {{-- Corner Edges --}}
                                <div class="laser-edge absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-indigo-500 -mt-1 -ml-1 transition-colors duration-200"></div>
                                <div class="laser-edge absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-indigo-500 -mt-1 -mr-1 transition-colors duration-200"></div>
                                <div class="laser-edge absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-indigo-500 -mb-1 -ml-1 transition-colors duration-200"></div>
                                <div class="laser-edge absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-indigo-500 -mb-1 -mr-1 transition-colors duration-200"></div>
                                
                                {{-- Animated Laser Line --}}
                                <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-indigo-500 to-transparent animate-pulse" style="top: 50%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Status Message Container (Selalu Muncul) --}}
                    <div id="scan-status-container" class="mt-4 transform transition-all duration-300">
                        <div id="scan-status" class="p-3 sm:p-4 rounded-xl border border-dashed border-gray-200 bg-gray-50 text-gray-500 flex items-center justify-center text-xs sm:text-sm font-medium shadow-sm">
                            <span class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                Siap memindai barcode siswa...
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LIVE LOG (5/12) --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 h-full flex flex-col">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="font-bold text-gray-800 flex items-center text-sm sm:text-base">
                        <i class="fas fa-history text-indigo-500 mr-2"></i> Riwayat Scan Hari Ini
                    </h3>
                </div>
                
                {{-- SCROLLABLE LOG AREA --}}
                <div class="flex-1 overflow-y-auto p-3 sm:p-4 custom-scrollbar" style="max-height: 400px; lg:max-height: 520px;">
                    <ul class="space-y-2 sm:space-y-3" id="attendance-log">
                        @forelse($recentAbsences as $absence)
                            @php
                                $status = $absence->status;
                                $isCheckout = !is_null($absence->checkout_time);
                                $displayStatus = $isCheckout ? 'PULANG' : ($status == 'Terlambat' ? 'TERLAMBAT' : 'MASUK');
                                
                                $cardColor = match($displayStatus) {
                                    'PULANG' => 'bg-blue-50 border-blue-100',
                                    'TERLAMBAT' => 'bg-amber-50 border-amber-100',
                                    'MASUK' => 'bg-green-50 border-green-100',
                                    default => 'bg-gray-50 border-gray-100'
                                };
                                $iconColor = match($displayStatus) {
                                    'PULANG' => 'text-blue-600 bg-blue-100',
                                    'TERLAMBAT' => 'text-amber-600 bg-amber-100',
                                    'MASUK' => 'text-green-600 bg-green-100',
                                    default => 'text-gray-600 bg-gray-100'
                                };
                                $icon = match($displayStatus) {
                                    'PULANG' => 'fa-door-open',
                                    'TERLAMBAT' => 'fa-exclamation-triangle',
                                    default => 'fa-check'
                                };
                                $time = $isCheckout ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i');
                            @endphp
                            
                            <li class="p-3 rounded-xl border {{ $cardColor }} hover:shadow-md transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full {{ $iconColor }} flex items-center justify-center flex-shrink-0 text-xs sm:text-base">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $absence->student->name }}</p>
                                        <div class="flex items-center text-[10px] sm:text-xs space-x-2 mt-0.5">
                                            <span class="font-bold opacity-80">{{ $displayStatus }}</span>
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-500 font-mono">{{ $time }}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-6 sm:py-10" id="empty-log-msg">
                                <div class="bg-gray-50 w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-clipboard-list text-gray-300 text-lg sm:text-2xl"></i>
                                </div>
                                <p class="text-gray-400 text-xs sm:text-sm">Belum ada aktivitas scan.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    {{-- AUDIO BEEP --}}
    <audio id="scanSuccessAudio" src="{{ asset('assets/audio/scan-beep.mp3') }}" preload="auto"></audio>
</div>
@stop

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    // --- CONFIGURATIONS ---
    const scanUrl = '{{ route("walikelas.absensi.record") }}'; 
    const csrfToken = '{{ csrf_token() }}';
    const scanDelay = 1000; 
    let lastScanTime = 0;
    let isProcessing = false;
    
    // --- CAMERA FLIP CONFIG ---
    let currentFacingMode = "environment"; 
    const html5QrCode = new Html5Qrcode("scanner"); 
    
    // --- ELEMENTS ---
    const scanStatus = $('#scan-status');
    const attendanceLog = $('#attendance-log');
    const cameraIndicator = $('#camera-status-indicator');
    const laserBox = $('#scanner-laser-box');

    // --- BUZZER AUDIO SYNTHESIZER ---
    function playBuzzer(isSuccess) {
        try {
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
        } catch (e) {
            console.error("AudioContext error:", e);
        }
    }

    // --- CAMERA STATUS HELPER ---
    function updateCameraStatus(status) {
        let color = 'bg-gray-300';
        let text = 'Offline';
        if(status === 'active') { color = 'bg-green-500 animate-pulse'; text = 'Live'; }
        if(status === 'error') { color = 'bg-red-500'; text = 'Error'; }
        
        cameraIndicator.html(`
            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${color}"></span>
            <span class="${status === 'active' ? 'text-green-600' : 'text-gray-400'}">${text}</span>
        `);
    }

    // --- RESET STATUS TO DEFAULT ---
    function resetScanStatus() {
        scanStatus.removeClass('bg-blue-50 text-blue-700 border-blue-100 bg-green-50 text-green-700 border-green-200 bg-amber-50 text-amber-700 border-amber-200 bg-red-50 text-red-700 border-red-200 border-solid')
                  .addClass('p-3 sm:p-4 rounded-xl border border-dashed border-gray-200 bg-gray-50 text-gray-500 flex items-center justify-center text-xs sm:text-sm font-medium shadow-sm')
                  .html(`
                    <span class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Siap memindai barcode siswa...
                    </span>
                  `);
        
        laserBox.removeClass('border-green-500 border-red-500').addClass('border-indigo-500/50');
        $('.laser-edge').removeClass('border-green-500 border-red-500').addClass('border-indigo-500');
    }

    // --- LIVE HISTORY LOG ANIMATED ---
    function addLogItem(data) {
        $('#empty-log-msg').remove();

        const typeMap = {
            'IN': { 
                'Hadir': { status: 'MASUK', subClass: 'bg-green-50 border-green-100', iconClass: 'bg-green-100 text-green-600', icon: 'fa-check' },
                'Terlambat': { status: 'TERLAMBAT', subClass: 'bg-amber-50 border-amber-100', iconClass: 'bg-amber-100 text-amber-600', icon: 'fa-exclamation-triangle' }
            },
            'OUT': { 
                'default': { status: 'PULANG', subClass: 'bg-blue-50 border-blue-100', iconClass: 'bg-blue-100 text-blue-600', icon: 'fa-door-open' }
            }
        };

        const config = (data.type === 'IN') ? typeMap['IN'][data.status] : typeMap['OUT']['default'];
        const time = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

        const itemHtml = `
            <li class="p-3 rounded-xl border ${config.subClass} hover:shadow-md transition-all duration-500 transform translate-y-[-10px] opacity-0" id="new-log-item">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full ${config.iconClass} flex items-center justify-center flex-shrink-0 text-xs sm:text-base">
                        <i class="fas ${config.icon}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">${data.student.name}</p>
                        <div class="flex items-center text-[10px] sm:text-xs space-x-2 mt-0.5">
                            <span class="font-bold opacity-80">${config.status}</span>
                            <span class="text-gray-400">•</span>
                            <span class="text-gray-500 font-mono">${time}</span>
                        </div>
                    </div>
                </div>
            </li>
        `;
        
        attendanceLog.prepend(itemHtml);
        setTimeout(() => {
            $('#new-log-item').removeClass('translate-y-[-10px] opacity-0').removeAttr('id');
        }, 50);
    }

    // --- MAIN BACKEND PROCESSOR ---
    function processBarcode(code) {
        if(isProcessing) return;
        isProcessing = true;
        
        playBuzzer(true);

        laserBox.removeClass('border-indigo-500/50').addClass('border-green-500 project-pulse-green');
        $('.laser-edge').removeClass('border-indigo-500').addClass('border-green-500');

        scanStatus.removeClass('bg-gray-50 text-gray-500 border-dashed')
                  .addClass('bg-blue-50 text-blue-700 border-solid border-blue-100')
                  .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses absensi...');

        $.ajax({
            url: scanUrl,
            method: 'POST',
            data: { _token: csrfToken, barcode: code },
            success: function(res) {
                let isLate = res.status === 'Terlambat';
                let colorClass = isLate ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-green-50 text-green-700 border-green-200';
                let icon = isLate ? 'fa-exclamation-triangle' : 'fa-check-circle';
                
                scanStatus.removeClass('bg-blue-50 text-blue-700 border-blue-100')
                          .addClass(colorClass)
                          .html(`<i class="fas ${icon} mr-2"></i> <span class="font-bold">${res.message}</span>`);

                Swal.fire({
                    icon: isLate ? 'warning' : 'success',
                    title: res.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });

                addLogItem(res);
            },
            error: function(xhr) {
                playBuzzer(false);

                let msg = xhr.responseJSON?.message || 'Gagal memproses data';
                let resType = xhr.responseJSON?.type || '';
                let isWarning = (xhr.status === 409) || (xhr.status === 403 && resType !== 'WRONG_CLASS'); 
                
                let colorClass = isWarning ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-red-50 text-red-700 border-red-200';
                let icon = isWarning ? 'fa-exclamation-circle' : 'fa-times-circle';

                if(!isWarning) {
                    laserBox.removeClass('border-green-500 border-indigo-500/50').addClass('border-red-500');
                    $('.laser-edge').removeClass('border-green-500 border-indigo-500').addClass('border-red-500');
                }

                scanStatus.removeClass('bg-blue-50 text-blue-700 border-blue-100')
                          .addClass(colorClass)
                          .html(`<i class="fas ${icon} mr-2"></i> <span class="font-bold">${msg}</span>`);
                
                Swal.fire({
                    icon: isWarning ? 'warning' : 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500
                });
            },
            complete: function() {
                isProcessing = false; // Buka kunci proses langsung
                resetScanStatus();
                try { html5QrCode.resume(); } catch (e) {}
            }
        });
    }

    // --- QR CODE SCAN CALLBACK ---
    function onScanSuccess(decodedText, decodedResult) {
        let currentTime = new Date().getTime();
        if(currentTime - lastScanTime > scanDelay) {
            lastScanTime = currentTime;
            html5QrCode.pause(); 
            processBarcode(decodedText);
        }
    }

    // --- FUNCTION TO START CAMERA ENGINE ---
    function startCamera(facingMode) {
        const constraint = (facingMode === "environment") 
            ? { facingMode: "environment" } 
            : { facingMode: "user" };

        const config = { 
            fps: 20, 
            qrbox: { width: 300, height: 300 } 
        };

        html5QrCode.start(
            constraint, 
            config, 
            onScanSuccess
        ).then(() => {
            updateCameraStatus('active');
        }).catch(err => {
            console.error(`Gagal menyalakan kamera mode: ${facingMode}`, err);
            updateCameraStatus('error');
            
            Swal.fire({
                icon: 'info',
                title: 'Kamera Tidak Tersedia',
                text: 'Perangkat kamu tidak memiliki kamera sekunder (depan/belakang) lainnya.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            
            currentFacingMode = (facingMode === "environment") ? "environment" : "user";
        });
    }

    // --- CORE INITIALIZATION ---
    $(document).ready(function() {
        // Mulai kamera otomatis saat halaman dimuat
        startCamera(currentFacingMode);

        // Handler Tombol Flip / Ganti Kamera (Desain Lingkaran Glassmorphism + Spinner)
        $('#btn-flip-camera').on('click', function() {
            if (html5QrCode.isScanning()) {
                const btn = $(this);
                const iconContainer = $('#flip-icon-container');
                
                // Kunci tombol jalankan loading spinner murni menggantikan ikon SVG
                btn.prop('disabled', true);
                iconContainer.html('<i class="fas fa-spinner fa-spin text-white text-lg"></i>');

                // 1. Matikan kamera aktif terlebih dahulu
                html5QrCode.stop().then(() => {
                    updateCameraStatus('offline');

                    // 2. Beri jeda aman 300ms agar hardware browser melepas kunci kamera lama
                    setTimeout(() => {
                        // Tukar target kamera
                        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                        
                        const constraint = { facingMode: currentFacingMode };
                        const config = { fps: 20, qrbox: { width: 300, height: 300 } };

                        // 3. Jalankan ulang kamera baru
                        html5QrCode.start(constraint, config, onScanSuccess)
                            .then(() => {
                                updateCameraStatus('active');
                            })
                            .catch(err => {
                                console.warn("Kamera target tidak ditemukan, mengembalikan ke kamera utama...", err);
                                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                                
                                html5QrCode.start({ facingMode: currentFacingMode }, config, onScanSuccess)
                                    .then(() => updateCameraStatus('active'))
                                    .catch(() => updateCameraStatus('error'));
                                
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Gagal Ganti Kamera',
                                    text: 'Tidak ditemukan kamera sekunder di perangkat ini.',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            })
                            .finally(() => {
                                // Kembalikan isi tombol ke SVG semula setelah proses selesai
                                btn.prop('disabled', false);
                                iconContainer.html(`
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                `);
                            });
                    }, 300);

                }).catch(err => {
                    console.error("Gagal menonaktifkan kamera:", err);
                    btn.prop('disabled', false);
                    iconContainer.html(`
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    `);
                });
            }
        });
    });

    $(window).on('beforeunload', function(){
        if(html5QrCode.isScanning()){
            html5QrCode.stop().catch(()=>{});
        }
    });
</script>
@endsection