@extends('layouts.adminlte')

@section('title', 'Edit Mata Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="fas fa-edit text-amber-500 mr-3"></i> Edit Mata Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi mata pelajaran.</p>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition">
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

                <form action="{{ route('admin.subjects.update', $subject->code) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        {{-- Nama Mapel --}}
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-book text-gray-400"></i>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" required 
                                       class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3" 
                                       placeholder="Contoh: Matematika Wajib">
                            </div>
                        </div>

                        {{-- Kode Mapel --}}
                        <div>
                            <label for="code" class="block text-sm font-bold text-gray-700 mb-2">
                                Kode Mata Pelajaran (Opsional)
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-barcode text-gray-400"></i>
                                </div>
                                <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}" 
                                       class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3" 
                                       placeholder="Contoh: MTK-001">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.subjects.index') }}" class="bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-200 transition duration-200">
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
