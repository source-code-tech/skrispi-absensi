@extends('layouts.adminlte')

@section('title', 'Buat Pengumuman Baru')

@section('content_header')
<div class="flex justify-between items-center max-w-5xl mx-auto">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-bullhorn text-purple-600 mr-3"></i> Buat Pengumuman
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Sampaikan informasi penting kepada siswa atau orang tua dengan tampilan menarik.</p>
    </div>
    <a href="{{ route('announcements.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition transform hover:-translate-y-0.5">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 max-w-5xl w-full">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative">
            {{-- Decorative Header --}}
            <div class="h-2 bg-gradient-to-r from-purple-500 via-indigo-500 to-blue-500"></div>

            <form action="{{ route('announcements.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Judul & Konten (Kiri - Lebar) --}}
                    <div class="md:col-span-2 space-y-6">
                        <div class="form-group">
                            <label class="block font-bold text-gray-700 mb-2 pl-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 font-semibold text-gray-800 placeholder-gray-400" required placeholder="Contoh: Jadwal Penerimaan Rapor Semester Ganjil">
                        </div>
                        
                        <div class="form-group">
                            <label class="block font-bold text-gray-700 mb-2 pl-1">Konten / Isi Pengumuman <span class="text-red-500">*</span></label>
                            <div class="prose max-w-none">
                                <textarea name="content" id="editor" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500" placeholder="Tulis isinya disini..."></textarea>
                            </div>
                            <p class="text-xs text-gray-400 mt-2 font-medium">* Gunakan fitur editing di atas untuk mempercantik pengumuman.</p>
                        </div>
                    </div>

                    {{-- Sidebar Opsi (Kanan - Sempit) --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200/60">
                            <h4 class="font-bold text-gray-800 mb-4 flex items-center text-sm uppercase tracking-wider">
                                <i class="fas fa-bullseye text-indigo-500 mr-2"></i> Target Penerima
                            </h4>
                            
                            <div class="form-group mb-4">
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Tipe Target</label>
                                <select name="target_type" class="w-full px-3 py-2.5 rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-sm font-semibold text-gray-700 bg-white" id="targetType" required>
                                    <option value="all">🌐 Semua Kelas</option>
                                    <option value="class">🏫 Kelas Spesifik</option>
                                </select>
                            </div>
                            
                            <div class="form-group hidden" id="targetClassGroup">
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Pilih Kelas</label>
                                <select name="target_class_code" class="w-full px-3 py-2.5 rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-sm font-semibold text-gray-700 bg-white">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->code }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200/60">
                             <h4 class="font-bold text-gray-800 mb-4 flex items-center text-sm uppercase tracking-wider">
                                <i class="fas fa-toggle-on text-green-500 mr-2"></i> Status Publikasi
                            </h4>
                             <div class="flex items-center space-x-3 bg-white p-3 rounded-xl border border-gray-100">
                                 <label class="flex items-center cursor-pointer">
                                     <input type="radio" class="form-radio text-green-500 h-5 w-5 focus:ring-green-200" name="is_active" value="1" checked>
                                     <span class="ml-2 font-bold text-gray-700 text-sm">Terbit</span>
                                 </label>
                                 <label class="flex items-center cursor-pointer ml-4">
                                     <input type="radio" class="form-radio text-gray-400 h-5 w-5 focus:ring-gray-200" name="is_active" value="0">
                                     <span class="ml-2 font-bold text-gray-500 text-sm">Draft</span>
                                 </label>
                             </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-purple-700 transition transform hover:-translate-y-1 flex justify-center items-center group">
                            <span class="mr-2">Simpan Pengumuman</span>
                            <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>

<script>
    // Inisialisasi CKEditor
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
            placeholder: 'Tuliskan isi pengumuman yang informatif disini...'
        })
        .catch(error => {
            console.error(error);
        });

    // Logika Target Penerima
    $('#targetType').change(function() {
        if($(this).val() === 'class') {
            $('#targetClassGroup').removeClass('hidden').hide().slideDown();
            $('select[name="target_class_code"]').attr('required', true);
        } else {
            $('#targetClassGroup').slideUp();
            $('select[name="target_class_code"]').removeAttr('required');
        }
    });
</script>

{{-- Custom Style untuk CKEditor agar matching dengan Tailwind --}}
<style>
    .ck-editor__editable_inline {
        min-height: 300px;
        border-radius: 0 0 0.75rem 0.75rem !important;
        border-color: #E5E7EB !important; /* gray-200 */
        padding: 1rem !important;
    }
    .ck-toolbar {
        border-radius: 0.75rem 0.75rem 0 0 !important;
        border-color: #E5E7EB !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: #E5E7EB !important;
    }
</style>
@stop
