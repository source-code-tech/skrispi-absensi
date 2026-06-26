@extends('layouts.adminlte')

@section('title', 'Kelola Jadwal Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="far fa-calendar-alt text-purple-600 mr-3"></i> Jadwal Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Pilih kelas untuk mengelola jadwal pelajaran.</p>
    </div>
</div>
@stop

@section('content')
<div class="row">
    @foreach($classes as $class)
    <div class="col-md-4 col-sm-6 col-12 mb-4">
        <a href="{{ route('admin.schedules.show', $class->code) }}" class="block group">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-2xl relative">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition duration-300">
                    <i class="fas fa-calendar-alt fa-5x text-indigo-600"></i>
                </div>
                <div class="p-6 relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">
                             Grade {{ $class->grade }}
                        </span>
                        <i class="fas fa-arrow-right text-gray-300 group-hover:text-indigo-500 transition duration-300"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800 group-hover:text-indigo-600 transition duration-300">
                        {{ $class->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ $class->major ?? 'Umum' }}
                    </p>
                    <div class="mt-4 flex items-center text-xs text-gray-400">
                         <i class="fas fa-user-tie mr-2"></i> {{ $class->homeroomTeacher->user->name ?? 'Belum ada Wali Kelas' }}
                    </div>
                </div>
                <div class="h-2 bg-gradient-to-r from-purple-500 to-indigo-500 group-hover:h-3 transition-all duration-300"></div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@stop
