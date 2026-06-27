@extends('layouts.adminlte')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="fas fa-calendar-plus text-purple-600 mr-3"></i> Tambah Jadwal Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Tambahkan jadwal pelajaran baru.</p>
    </div>
    @if($preselectedClass)
    <a href="{{ route('admin.schedules.show', $preselectedClass->id) }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    @else
    <a href="{{ route('admin.schedules.index') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    @endif
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-8">
                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.schedules.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        {{-- Pilih Kelas --}}
                        <div>
                            <label for="class_code" class="block text-sm font-bold text-gray-700 mb-2">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            @if($preselectedClass)
                                <input type="hidden" name="class_code" value="{{ $preselectedClass->id }}">
                                <div class="relative rounded-xl shadow-sm">
                                    <input type="text" value="{{ $preselectedClass->grade }} {{ $preselectedclass->code }}" disabled
                                           class="bg-gray-100 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3 text-gray-500">
                                </div>
                            @else
                                <div class="relative rounded-xl shadow-sm">
                                    <select name="class_code" id="class_code" required class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->code }}" {{ old('class_code') == $class->code ? 'selected' : '' }}>
                                                {{ $class->grade }} {{ $class->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        {{-- Hari --}}
                        <div>
                            <label for="day" class="block text-sm font-bold text-gray-700 mb-2">
                                Hari <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <select name="day" id="day" required class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3">
                                    <option value="">-- Pilih Hari --</option>
                                    @foreach($days as $day)
                                        <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Mata Pelajaran --}}
                        <div>
                            <label for="subject_code" class="block text-sm font-bold text-gray-700 mb-2">
                                Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <select name="subject_code" id="subject_code" required class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->code }}" {{ old('subject_code') == $subject->code ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jam Mulai & Selesai --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required 
                                       class="focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-xl py-3">
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required 
                                       class="focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-xl py-3">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        @if($preselectedClass)
                        <a href="{{ route('admin.schedules.show', $preselectedClass->id) }}" class="bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-200 transition duration-200">
                            Batal
                        </a>
                        @else
                        <a href="{{ route('admin.schedules.index') }}" class="bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-200 transition duration-200">
                            Batal
                        </a>
                        @endif
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
