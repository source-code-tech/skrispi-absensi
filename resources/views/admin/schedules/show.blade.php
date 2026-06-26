@extends('layouts.adminlte')

@section('title', 'Atur Jadwal - ' . $classModel->name)

@section('content_header')
<div class="flex justify-between items-center">
    <div>
         <a href="{{ route('admin.schedules.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
        </a>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="far fa-calendar-alt text-purple-600 mr-3"></i> Jadwal {{ $classModel->name }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">Kelola jadwal pelajaran untuk kelas ini (Senin - Jumat).</p>
    </div>
    <a href="{{ route('admin.schedules.create', ['class_code' => $classModel->code]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg transform transition hover:-translate-y-1 flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Jadwal
    </a>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($days as $day)
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-full">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">{{ $day }}</h3>
            <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">
                {{ isset($schedules[$day]) ? count($schedules[$day]) . ' Mapel' : 'Libur' }}
            </span>
        </div>
        <div class="p-6 flex-1">
            @if(isset($schedules[$day]) && count($schedules[$day]) > 0)
                <ul class="space-y-4">
                    @foreach($schedules[$day] as $schedule)
                    <li class="relative pl-6 border-l-2 border-indigo-200 group">
                        <div class="absolute -left-1.5 top-1.5 w-3 h-3 bg-indigo-500 rounded-full border-2 border-white shadow-sm"></div>
                        <div class="flex justify-between items-start">
                             <div>
                                <h4 class="font-bold text-gray-800 text-sm">{{ $schedule->subject->name }}</h4>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </p>
                             </div>
                             <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" 
                                    class="text-gray-300 hover:text-amber-500 transition duration-150">
                                   <i class="fas fa-edit"></i>
                               </a>
                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition duration-150">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                             </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4">
                    <i class="far fa-calendar-minus fa-2x mb-2 opacity-50"></i>
                    <span class="text-xs italic">Tidak ada jadwal</span>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

@stop
