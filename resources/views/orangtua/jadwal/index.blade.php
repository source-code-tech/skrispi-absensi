@extends('layouts.adminlte')

@section('title', 'Jadwal Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="far fa-calendar-alt text-purple-600 mr-3"></i> Jadwal Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Jadwal mata pelajaran ananda minggu ini.</p>
    </div>
    <a href="{{ route('orangtua.dashboard') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition">
        <i class="fas fa-arrow-left mr-1"></i> Dashboard
    </a>
</div>
@stop

@section('content')
<div class="space-y-8">
    @foreach($schedules as $studentName => $studentSchedules)
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 mb-8">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-user-graduate mr-3 text-yellow-300"></i> {{ $studentName }}
                </h3>
            </div>
            
            <div class="p-6">
                {{-- Responsive Tabs Logic (Mobile: Stacked, Desktop: Tabs) --}}
                <div x-data="{ activeTab: '{{ now()->locale('id')->translatedFormat('l') }}' }">
                    {{-- Nav Tabs (Desktop) --}}
                    <div class="flex space-x-2 overflow-x-auto pb-4 mb-4 border-b border-gray-100 no-scrollbar">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                            <button @click="activeTab = '{{ $day }}'" 
                                    :class="{ 'bg-indigo-600 text-white shadow-md': activeTab === '{{ $day }}', 'bg-gray-50 text-gray-600 hover:bg-gray-100': activeTab !== '{{ $day }}' }"
                                    class="px-4 py-2 rounded-xl text-sm font-bold transition duration-200 whitespace-nowrap">
                                {{ $day }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Content --}}
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                        <div x-show="activeTab === '{{ $day }}'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="space-y-3">
                            
                            @if(isset($studentSchedules[$day]) && count($studentSchedules[$day]) > 0)
                                @foreach($studentSchedules[$day] as $schedule)
                                    <div class="flex items-center p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition duration-200 group">
                                        <div class="flex-shrink-0 w-16 h-16 bg-indigo-50 rounded-xl flex flex-col items-center justify-center text-indigo-600 border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                                            <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                            <span class="text-[10px] opacity-75">s/d</span>
                                            <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition duration-200">
                                                {{ $schedule->subject->name }}
                                            </h4>
                                            @if($schedule->subject->code)
                                                <span class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $schedule->subject->code }}</span>
                                            @endif
                                        </div>
                                        <div class="ml-auto">
                                            <i class="fas fa-book-reader text-gray-300 group-hover:text-indigo-200 text-2xl transition duration-300"></i>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                        <i class="far fa-calendar-check text-gray-300 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Tidak ada jadwal</h3>
                                    <p class="text-gray-500 text-sm">Hari ini tidak ada KBM atau libur.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
@stop

@section('css')
<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@stop
