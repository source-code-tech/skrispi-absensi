@extends('layouts.adminlte')

@section('title', 'Pengaturan Akun')

@section('content_header')
<div class="flex justify-between items-center max-w-6xl mx-auto">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-cog text-purple-600 mr-3"></i> Pengaturan Akun
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Kelola informasi profil dan keamanan akun Anda.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition transform hover:-translate-y-0.5">
        <i class="fas fa-arrow-left mr-2"></i> Dashboard
    </a>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        
        {{-- KOLOM KIRI (2/3): Update Info & Password --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Update Profile Information --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-blue-500"></div>
                <div class="p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. Update Password --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                 <div class="p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (1/3): Info & Deletion --}}
        <div class="lg:col-span-1 space-y-8">
            
            {{-- Info Card --}}
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-purple-500 opacity-20 rounded-full blur-2xl"></div>
                
                <h4 class="font-bold text-lg mb-4 flex items-center relative z-10">
                    <i class="fas fa-shield-alt mr-2"></i> Keamanan Akun
                </h4>
                <p class="text-sm text-indigo-100 leading-relaxed relative z-10 mb-4">
                    Gunakan password yang kuat (kombinasi huruf, angka, simbol) untuk melindungi data pribadi dan anak Anda. 
                </p>
                <div class="text-xs font-mono bg-white/10 p-3 rounded-lg relative z-10">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-check text-green-400 mr-2"></i> Min. 8 Karakter
                    </div>
                     <div class="flex items-center">
                        <i class="fas fa-check text-green-400 mr-2"></i> Kombinasi Unik
                    </div>
                </div>
            </div>

            {{-- Delete Account (Discreet) --}}
            <div class="bg-red-50 rounded-3xl p-6 border border-red-100">
                <h4 class="font-bold text-red-800 mb-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i> Zona Bahaya
                </h4>
                 <div class="mt-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>
@stop