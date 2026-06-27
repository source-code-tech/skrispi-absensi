@extends('layouts.adminlte')

@section('title', 'Edit Jadwal Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="fas fa-edit text-amber-500 mr-3"></i> Edit Jadwal Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Edit jadwal pelajaran kelas {{ $schedule->class->grade }} {{ $schedule->class->code }}.</p>
    </div>
    <a href="{{ route('admin.schedules.show', $schedule->class_code) }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
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

                <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- Hidden Class ID (Not Editable) --}}
                    <input type="hidden" name="class_code" value="{{ $schedule->class_code }}">

                    <div class="space-y-6">
                        {{-- Info Kelas (Readonly) --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kelas</label>
                            <input type="text" value="{{ $schedule->class->grade }} {{ $schedule->class->code }}" disabled
                                   class="bg-gray-100 block w-full sm:text-sm border-gray-300 rounded-xl py-3 text-gray-500">
                        </div>

                        {{-- Hari --}}
                        <div>
                            <label for="day" class="block text-sm font-bold text-gray-700 mb-2">
                                Hari <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <select name="day" id="day" required class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3">
                                    @foreach($days as $day)
                                        <option value="{{ $day }}" {{ old('day', $schedule->day) == $day ? 'selected' : '' }}>{{ $day }}</option>
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
                                <select name="subject_code" id="subject_code" required class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-xl py-3">
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->code }}" {{ old('subject_code', $schedule->subject_code) == $subject->code ? 'selected' : '' }}>
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
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" required 
                                       class="focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-gray-300 rounded-xl py-3">
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}" required 
                                       class="focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-gray-300 rounded-xl py-3">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.schedules.show', $schedule->class_code) }}" class="bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-200 transition duration-200">
                            Batal
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
