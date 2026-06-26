<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\HomeroomTeacher; 
use App\Models\Student; 
use Illuminate\Support\Facades\DB; 
use App\Models\Setting;

class ClassController extends Controller
{
    /**
     * Tampilkan daftar kelas dengan paginasi. (READ)
     */
    public function index()
    {
        // 🔥 PERBAIKAN: Mengurutkan data berdasarkan 'grade' (tingkat, dari 1 ke 12) 
        //             kemudian diurutkan berdasarkan 'name' (nama kelas) secara ascending.
        $classes = ClassModel::withCount('students')
                             ->orderBy('grade', 'asc') // Urutan utama: 1, 2, ..., 12
                             ->orderBy('name', 'asc')  // Urutan sekunder: 7A, 7B, 7C
                             ->paginate(15);
        
        return view('admin.classes.index', compact('classes'));
    }

    // -----------------------------------------------------------------
    // CREATE (Tambah Kelas)
    // -----------------------------------------------------------------

    /**
     * Tampilkan form tambah kelas. (CREATE - Form)
     */
    public function create()
    {
        return view('admin.classes.create');
    }

    /**
     * Simpan kelas baru ke database. (CREATE - Store)
     */
    public function store(Request $request)
    {
        $globalStartTime = Setting::where('key', 'attendance_start_time')->value('value') ?? '07:00';
        
        $request->validate([
            'code' => 'required|string|max:20|unique:classes,code',
            'name' => 'required|string|max:255|unique:classes,name', 
            'grade' => 'required|integer|min:1|max:6',
            'major' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'dismissal_time' => 'required|date_format:H:i|after:' . $globalStartTime,
        ], [
            'dismissal_time.required' => 'Jam pulang kelas wajib diisi.',
            'dismissal_time.date_format' => 'Format jam pulang harus berupa Jam:Menit (contoh: 16:45).',
            'dismissal_time.after' => 'Jam pulang kelas harus lebih besar dari jam masuk sekolah umum (' . $globalStartTime . ').',
        ]);

        $data = $request->all();
        if (empty($data['code'])) {
            $data['code'] = \Illuminate\Support\Str::slug($request->name);
        }
        ClassModel::create($data);

        return redirect()->route('classes.index')->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    // -----------------------------------------------------------------
    // UPDATE (Edit Kelas)
    // -----------------------------------------------------------------

    /**
     * Tampilkan form edit kelas. (UPDATE - Form)
     */
    public function edit(ClassModel $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    /**
     * Perbarui data kelas. (UPDATE - Store)
     */
    public function update(Request $request, ClassModel $class)
    {
        $globalStartTime = Setting::where('key', 'attendance_start_time')->value('value') ?? '07:00';

        $request->validate([
            'code' => 'required|string|max:20|unique:classes,code,' . $class->code . ',code',
            'name' => 'required|string|max:255|unique:classes,name,' . $class->code . ',code', 
            'grade' => 'required|integer|min:1|max:12', 
            'major' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'dismissal_time' => 'required|date_format:H:i|after:' . $globalStartTime,
        ], [
            'dismissal_time.required' => 'Jam Pulang Kelas wajib diisi.',
            'dismissal_time.date_format' => 'Format jam pulang harus berupa Jam:Menit (contoh: 16:45).',
            'dismissal_time.after' => 'Jam pulang kelas harus lebih besar dari jam masuk sekolah umum (' . $globalStartTime . ').',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil diperbarui!');
    }

    // -----------------------------------------------------------------
    // DELETE (Hapus Kelas)
    // -----------------------------------------------------------------

    /**
     * Hapus data kelas dari database. (DELETE)
     */
    public function destroy(ClassModel $class)
    {
        // FUNGSI HAPUS AMAN: Cek Relasi Siswa
        $studentCount = Student::where('class_code', $class->code)->count();
        if ($studentCount > 0) {
            return redirect()->back()->with('error', "Gagal menghapus! Masih ada {$studentCount} siswa yang terdaftar di kelas ini.");
        }

        // FUNGSI HAPUS AMAN: Cek Relasi Wali Kelas
        $homeroomCount = HomeroomTeacher::where('class_code', $class->code)->count();
        if ($homeroomCount > 0) {
            return redirect()->back()->with('error', "Gagal menghapus! Kelas ini masih diampu oleh Wali Kelas. Hapus penugasan wali kelas terlebih dahulu.");
        }

        // Jika tidak ada relasi, hapus
        $className = $class->name;
        $class->delete();

        return redirect()->route('classes.index')->with('success', "Kelas {$className} berhasil dihapus.");
    }
}