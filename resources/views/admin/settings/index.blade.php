@extends('layouts.adminlte')

@section('title', 'Pengaturan Umum Sistem')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-cogs text-purple-600 mr-3"></i>
            Pengaturan Sistem
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Konfigurasi data sekolah dan parameter operasional sistem.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Pengaturan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- KOLOM KIRI: FORM CONFIG (8/12) --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-sliders-h mr-2 text-purple-600"></i> Form Konfigurasi
                    </h3>
                </div>
                
                <div class="p-8">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                        @csrf
                        @method('PUT')
                        
                        @php
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                        @endphp
        
                        {{-- BAGIAN 1: IDENTITAS SEKOLAH --}}
                        <div class="mb-8">
                            <h5 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-dashed border-gray-200 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                                    <i class="fas fa-school"></i>
                                </span>
                                Identitas Sekolah
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama Sekolah --}}
                                <div class="md:col-span-2">
                                    <label for="school_name" class="{{ $labelClass }}">{{ $keys['school_name'] ?? 'Nama Sekolah' }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-university text-gray-400"></i>
                                        </div>
                                        <input type="text" name="school_name" id="school_name" 
                                                class="pl-10 {{ $errors->has('school_name') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('school_name', $settings['school_name'] ?? '') }}" 
                                                placeholder="Contoh: SMP Negeri 1 Jakarta"
                                                required>
                                    </div>
                                    @error('school_name') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                </div>
                                
                                {{-- Logo Sekolah --}}
                                <div class="md:col-span-2">
                                    <label for="school_logo_file" class="{{ $labelClass }}">Upload Logo Sekolah</label>
                                    <div class="flex items-center space-x-4 p-4 border rounded-xl bg-gray-50 border-gray-200 @error('school_logo_file') border-red-300 bg-red-50 @enderror">
                                        <div class="flex-shrink-0 w-16 h-16 bg-white rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                                             @php
                                                $currentLogoPath = $settings['school_logo'] ?? '';
                                                $logoExists = !empty($currentLogoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($currentLogoPath);
                                                $logoPath = $logoExists ? asset('storage/' . $currentLogoPath) : asset('images/default_logo.png'); 
                                            @endphp
                                            <img src="{{ $logoPath }}" alt="Preview" id="logo-preview" class="w-full h-full object-contain">
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="school_logo_file" id="school_logo_file" 
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer"
                                                accept="image/png, image/jpeg, image/jpg">
                                            <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG, Maksimal 2MB.</p>
                                        </div>
                                    </div>
                                    @error('school_logo_file') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- BAGIAN 2: PARAMETER WAKTU --}}
                        <div class="mb-8">
                            <h5 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-dashed border-gray-200 flex items-center">
                                <span class="bg-amber-100 text-amber-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                                    <i class="fas fa-clock"></i>
                                </span>
                                Parameter Waktu Absensi
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Jam Masuk --}}
                                <div>
                                    <label for="attendance_start_time" class="{{ $labelClass }}">Jam Masuk <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-hourglass-start text-gray-400"></i>
                                        </div>
                                        @php $startTime = substr(old('attendance_start_time', $settings['attendance_start_time'] ?? '07:00'), 0, 5); @endphp
                                        <input type="time" name="attendance_start_time" id="attendance_start_time" 
                                                class="pl-10 {{ $errors->has('attendance_start_time') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ $startTime }}" required>
                                    </div>
                                    @error('attendance_start_time') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Jam Pulang --}}
                                <div>
                                    <label for="attendance_end_time" class="{{ $labelClass }}">Jam Pulang <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-hourglass-end text-gray-400"></i>
                                        </div>
                                        @php $endTime = substr(old('attendance_end_time', $settings['attendance_end_time'] ?? '15:00'), 0, 5); @endphp
                                        <input type="time" name="attendance_end_time" id="attendance_end_time" 
                                                class="pl-10 {{ $errors->has('attendance_end_time') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ $endTime }}" required>
                                    </div>
                                    @error('attendance_end_time') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Toleransi --}}
                                <div>
                                    <label for="late_tolerance_minutes" class="{{ $labelClass }}">Toleransi (Menit) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-stopwatch text-gray-400"></i>
                                        </div>
                                        <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes" 
                                                class="pl-10 {{ $errors->has('late_tolerance_minutes') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('late_tolerance_minutes', $settings['late_tolerance_minutes'] ?? 10) }}" min="0" required>
                                    </div>
                                    @error('late_tolerance_minutes') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- BAGIAN 3: WHATSAPP --}}
                        <div class="mb-8">
                            <h5 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-dashed border-gray-200 flex items-center">
                                <span class="bg-green-100 text-green-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                Integrasi WhatsApp (Opsional)
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="wa_api_endpoint" class="{{ $labelClass }}">Endpoint API</label>
                                    <input type="url" name="wa_api_endpoint" id="wa_api_endpoint" 
                                            class="{{ $errors->has('wa_api_endpoint') ? $inputErrorClass : $inputClass }}" 
                                            value="{{ old('wa_api_endpoint', $settings['wa_api_endpoint'] ?? '') }}"
                                            placeholder="https://api.whatsapp-service.com/send">
                                    @error('wa_api_endpoint') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="wa_api_key" class="{{ $labelClass }}">API Key / Token</label>
                                    <input type="text" name="wa_api_key" id="wa_api_key" 
                                            class="{{ $errors->has('wa_api_key') ? $inputErrorClass : $inputClass }}" 
                                            value="{{ old('wa_api_key', $settings['wa_api_key'] ?? '') }}"
                                            placeholder="Masukkan token rahasia...">
                                    @error('wa_api_key') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- BAGIAN 4: KONTAK & MEDIA SOSIAL --}}
                        <div class="mb-8">
                            <h5 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-dashed border-gray-200 flex items-center">
                                <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">
                                    <i class="fas fa-address-book"></i>
                                </span>
                                Kontak & Media Sosial
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Email --}}
                                <div>
                                    <label for="school_email" class="{{ $labelClass }}">Email Resmi</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" name="school_email" id="school_email" 
                                                class="pl-10 {{ $errors->has('school_email') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('school_email', $settings['school_email'] ?? '') }}"
                                                placeholder="admin@sekolah.sch.id">
                                    </div>
                                    @error('school_email') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Telepon --}}
                                <div>
                                    <label for="school_phone" class="{{ $labelClass }}">No. Telepon</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input type="text" name="school_phone" id="school_phone" 
                                                class="pl-10 {{ $errors->has('school_phone') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('school_phone', $settings['school_phone'] ?? '') }}"
                                                placeholder="(021) 12345678">
                                    </div>
                                    @error('school_phone') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="md:col-span-2">
                                    <label for="school_address" class="{{ $labelClass }}">Alamat Lengkap</label>
                                    <textarea name="school_address" id="school_address" rows="2"
                                            class="{{ $errors->has('school_address') ? $inputErrorClass : $inputClass }}"
                                            placeholder="Jl. Merdeka No. 45, Jakarta">{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                                    @error('school_address') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Facebook --}}
                                <div>
                                    <label for="social_facebook" class="{{ $labelClass }}">Link Facebook</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fab fa-facebook text-gray-400"></i>
                                        </div>
                                        <input type="url" name="social_facebook" id="social_facebook" 
                                                class="pl-10 {{ $errors->has('social_facebook') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                                                placeholder="https://facebook.com/sekolah">
                                    </div>
                                    @error('social_facebook') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- Instagram --}}
                                <div>
                                    <label for="social_instagram" class="{{ $labelClass }}">Link Instagram</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fab fa-instagram text-gray-400"></i>
                                        </div>
                                        <input type="url" name="social_instagram" id="social_instagram" 
                                                class="pl-10 {{ $errors->has('social_instagram') ? $inputErrorClass : $inputClass }}" 
                                                value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                                                placeholder="https://instagram.com/sekolah">
                                    </div>
                                    @error('social_instagram') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- TOMBOL SIMPAN --}}
                        <div class="pt-6 border-t border-gray-100 flex justify-end">
                             <button type="submit" 
                                    class="inline-flex items-center px-8 py-4 border border-transparent text-base font-bold rounded-xl shadow-lg 
                                           text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                                           focus:ring-4 focus:ring-purple-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitSettingsBtn">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: PREVIEW & INFO (4/12) --}}
        <div class="lg:col-span-4 mt-6 lg:mt-0">
             
            {{-- QUICK PREVIEW --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-eye mr-2 text-indigo-500"></i> Preview Kop Laporan</h3>
                </div>
                <div class="p-6 text-center">
                     <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <img src="{{ $logoPath }}" class="h-16 w-auto mx-auto mb-2" id="preview-kop-logo">
                        <h4 class="font-bold text-gray-800 text-lg" id="preview-kop-name">{{ $settings['school_name'] ?? 'Nama Sekolah' }}</h4>
                        <p class="text-xs text-gray-400 mt-2">Tampilan ini akan muncul pada dokumen PDF.</p>
                     </div>
                </div>
            </div>

           {{-- INFO PANEL --}}
<div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl shadow-xl border border-gray-100 overflow-hidden text-white">
    <div class="p-6">
        <h3 class="text-lg font-bold flex items-center mb-4"><i class="fas fa-info-circle mr-2"></i> Petunjuk Sistem</h3>
        <div class="space-y-4 text-indigo-100 text-sm">
            <p class="bg-white/10 p-3 rounded-xl border border-white/10">
                <strong>Jam Masuk & Pulang</strong><br>
                Jam masuk dihitung dari pengaturan umum. Jam pulang kelas bisa berbeda-beda; jika tidak diisi, sistem pakai jam pulang umum. Scan pulang hanya aktif saat jam pulang berlaku.
            </p>
            <p class="bg-white/10 p-3 rounded-xl border border-white/10">
                <strong>WhatsApp Gateway</strong><br>
                Jika aktif, notifikasi kehadiran dikirim otomatis ke orang tua. Pastikan endpoint valid.
            </p>
        </div>
    </div>
</div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // 1. Live Preview Logo
        const logoInput = document.getElementById('school_logo_file');
        const logoPreview = document.getElementById('logo-preview');
        const kopLogo = document.getElementById('preview-kop-logo');
        
        if (logoInput) {
            logoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                        kopLogo.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // 2. Live Preview School Name
        $('#school_name').on('input', function() {
           $('#preview-kop-name').text($(this).val()); 
        });

        // 3. Form Submission
        $('#settingsForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            
            const submitBtn = $('#submitSettingsBtn');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // 4. Alerts
        @if(session('success'))
             Swal.fire({ 
                 icon: 'success', 
                 title: 'Disimpan!', 
                 text: '{{ session('success') }}', 
                 timer: 3000,
                 showConfirmButton: false
             });
        @endif
        
        @if(session('error'))
             Swal.fire({ 
                 icon: 'error', 
                 title: 'Gagal', 
                 text: '{{ session('error') }}', 
                 timer: 3000,
                 showConfirmButton: false
             });
        @endif
        
        @if($errors->any())
             Swal.fire({ 
                 icon: 'warning', 
                 title: 'Periksa Input', 
                 text: 'Terdapat kesalahan pada data yang Anda masukkan.', 
                 timer: 3000,
                 showConfirmButton: false
             });
        @endif
    });
</script>
@stop