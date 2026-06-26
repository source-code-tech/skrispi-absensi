@extends('layouts.guest') 

@section('title', 'Verifikasi Email Anda')

@section('content')
<div class="login-box modern-login-box">
    <div class="card custom-login-card shadow-lg">
        <div class="card-header text-center modern-card-header">
            <i class="fas fa-envelope-open-text fa-3x text-warning mb-3"></i>
            <div class="h3 mb-1">
                <span class="font-weight-bold">Verifikasi Email</span>
            </div>
            <p class="text-muted small mb-0">Langkah Terakhir!</p>
        </div>
        
        <div class="card-body">
            
            {{-- Pesan Status (Setelah registrasi atau pengiriman ulang sukses) --}}
            @if (session('status') || session('success'))
                <div class="alert alert-success small mb-3">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            {{-- Pesan Error (Jika rate limiting terpicu) --}}
            @if (session('resent'))
                <div class="alert alert-success small mb-3">
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            @endif

            {{-- Pesan Utama --}}
            <p class="text-center text-muted small">
                Kami telah mengirimkan tautan verifikasi ke alamat email Anda. Mohon periksa kotak masuk Anda.
            </p>

            {{-- ✅ FORM POST UNTUK MENGIRIM ULANG (Route: verification.send) --}}
            <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                @csrf 
                
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block btn-modern-submit">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </div>
                </div>
            </form>
            
            {{-- Logout untuk Ganti Akun --}}
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-center d-block mt-3 small text-secondary">
                Bukan Anda? Ganti Akun
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Pastikan jQuery dimuat di layouts/guest.blade.php
    $(document).ready(function() {
        // Logika untuk menampilkan loading saat email dikirim ulang
        $('form[action="{{ route('verification.send') }}"]').on('submit', function() {
            const btn = $(this).find('button');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
        });
    });
</script>
@endpush

@push('css')
<style>
/* Tambahkan gaya modern-login-box, custom-login-card, dll. dari file login/register Anda */

.login-box {
    width: 380px; 
}
.custom-login-card {
    border-radius: 1.5rem !important;
}
.modern-card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #e3e6f0;
    padding: 1.5rem 1rem;
}
.text-warning { color: #f6c23e !important; }
</style>
@endpush