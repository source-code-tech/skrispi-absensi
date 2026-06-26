@extends('layouts.adminlte')

@section('title', 'Detail Pengumuman')

@section('content_header')
<div class="flex justify-between items-center max-w-4xl mx-auto">
    <div class="flex items-center space-x-2 text-sm text-gray-500">
        <a href="{{ route('announcements.index') }}" class="hover:text-indigo-600 transition">Pengumuman</a>
        <span>/</span>
        <span class="font-bold text-gray-700">Detail</span>
    </div>
    <div class="flex space-x-2">
         <a href="{{ route('announcements.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <a href="{{ route('announcements.edit', $announcement->id) }}" class="px-4 py-2 bg-amber-500 text-white font-bold rounded-xl shadow-sm hover:bg-amber-600 transition transform hover:-translate-y-0.5">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
    </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 max-w-4xl">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative mb-8">
            <div class="h-4 bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600"></div>
            
            <div class="p-8 md:p-12">
                {{-- Header Meta --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 pb-8 border-b border-gray-100">
                    <div class="flex items-center space-x-4">
                         @if($announcement->is_active)
                            <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold ring-1 ring-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span> Terbit (Aktif)
                            </span>
                        @else
                             <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold ring-1 ring-gray-200">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span> Draft
                            </span>
                        @endif
                        
                        <div class="flex items-center text-sm text-gray-400 font-medium">
                            <i class="far fa-calendar-alt mr-2"></i> {{ $announcement->created_at->translatedFormat('l, d F Y') }}
                            <span class="mx-2">•</span>
                            <i class="far fa-clock mr-2"></i> {{ $announcement->created_at->format('H:i') }} WIB
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Target:</span>
                         @if($announcement->target_type == 'all')
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100">
                                🌐 Semua Kelas
                            </span>
                        @else
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">
                                🏫 Kelas {{ $announcement->class->name ?? 'Unknown' }}
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Judul --}}
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-8 leading-tight tracking-tight">
                    {{ $announcement->title }}
                </h1>
                
                {{-- Konten RICH TEXT --}}
                <div class="prose prose-lg prose-indigo max-w-none text-gray-600 leading-relaxed">
                    {!! $announcement->content !!} {{-- Menggunakan unescaped syntax untuk CKEditor output --}}
                </div>
                
            </div>
            
            {{-- Footer Decoration --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-400 font-medium italic">
                    Dibuat oleh Admin • Terakhir diperbarui {{ $announcement->updated_at->diffForHumans() }}
                </p>
                <div class="flex -space-x-2">
                    {{-- Avatar placeholder for polished look --}}
                    <div class="w-8 h-8 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-indigo-500 text-xs font-bold">A</div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
