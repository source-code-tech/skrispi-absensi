<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Absence;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    // Helper untuk mendapatkan Class Code Wali Kelas
    protected function getClassId()
    {
        $user = Auth::user();
        return $user->homeroomTeacher->class_code ?? null;
    }

    // Helper untuk mengarahkan jika tidak mengampu kelas
    protected function checkClassAssignment()
    {
        $classId = $this->getClassId();
        if (!$classId) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas. Silakan hubungi admin.');
        }
        return $classId;
    }

    /**
     * Menampilkan daftar siswa yang diampu. (READ)
     */
    public function index(Request $request)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId; // Handle redirect

        $search = $request->get('search');
        $class = ClassModel::find($classId); // Ambil model Class untuk header

        $students = Student::with('class')
            ->where('class_code', $classId) // 🛑 BATASAN KELAS
            ->when($search, function($query) use ($search) {
                 $query->where('name', 'like', "%{$search}%")
                       ->orWhere('nisn', 'like', "%{$search}%")
                       ->orWhere('nis', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('walikelas.students.index', compact('students', 'class'));
    }

    /**
     * 💡 Tampilkan form untuk membuat siswa baru. (CREATE - Form)
     */
    public function create()
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId; 
        
        $class = ClassModel::findOrFail($classId);
        // Wali kelas hanya bisa menambah di kelasnya sendiri
        return view('walikelas.students.create', compact('class'));
    }

    /**
     * 💡 Simpan data siswa baru ke database. (CREATE - Store)
     */
    public function store(Request $request)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        $request->validate([
            'nisn' => 'required|string|unique:students,nisn|max:20',
            'nis' => 'nullable|string|unique:students,nis|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:500',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'photo' => 'nullable|image|max:1024|mimes:jpg,jpeg,png', 
        ]);

        $data = $request->all();
        $data['class_code'] = $classId; // 🛑 Batasan Kritis: Paksa Class Code Wali Kelas

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos/students', 'public');
            $data['photo'] = $path;
        } else {
            $data['photo'] = 'default_avatar.png'; 
        }
        
        $data['status'] = 'active';

        Student::create($data);

        return redirect()->route('walikelas.students.index')
                             ->with('success', 'Data siswa berhasil ditambahkan ke kelas Anda!');
    }

    /**
     * 💡 Tampilkan form untuk mengedit siswa. (UPDATE - Form)
     */
    public function edit(Student $student)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        // 🛑 Otorisasi: Pastikan siswa ada di kelas Wali Kelas
        if ($student->class_code !== $classId) {
            abort(403, 'Akses Ditolak. Anda hanya dapat mengedit siswa di kelas yang Anda ampu.');
        }
            
        $class = ClassModel::findOrFail($classId);

        return view('walikelas.students.edit', compact('student', 'class'));
    }

    /**
     * 💡 Perbarui data siswa di database. (UPDATE - Store)
     */
    public function update(Request $request, Student $student)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        // 🛑 Otorisasi: Pastikan siswa ada di kelas Wali Kelas
        if ($student->class_code !== $classId) {
            abort(403, 'Akses Ditolak.');
        }

        // Validasi, mengecualikan ID siswa saat ini
        $request->validate([
            'nisn' => 'required|string|unique:students,nisn,' . $student->nisn . ',nisn|max:20',
            'nis' => 'nullable|string|unique:students,nis,' . $student->nisn . ',nisn|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:500',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'photo' => 'nullable|image|max:1024|mimes:jpg,jpeg,png', 
        ]);

        $data = $request->except(['class_code', 'status']); // Wali kelas tidak boleh mengubah class_code/status

        // Penanganan Update Foto
        if ($request->hasFile('photo')) {
            if ($student->photo && $student->photo != 'default_avatar.png') {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('photos/students', 'public');
            $data['photo'] = $path;
        } 

        $student->update($data);

        return redirect()->route('walikelas.students.index')
                             ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * 💡 Hapus data siswa. (DELETE)
     */
    public function destroy(Student $student)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        // 🛑 Otorisasi: Pastikan siswa ada di kelas Wali Kelas
        if ($student->class_code !== $classId) {
            abort(403, 'Akses Ditolak.');
        }
        
        $studentName = $student->name;
        
        if ($student->photo && $student->photo != 'default_avatar.png') {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();
        
        return redirect()->route('walikelas.students.index')
                             ->with('success', "Data siswa {$studentName} berhasil dihapus.");
    }
    
    /**
     * 💡 Proses penghapusan massal (Bulk Delete) siswa.
     */
    public function bulkDelete(Request $request)
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,nisn',
        ], ['selected_students.required' => 'Pilih minimal satu siswa untuk dihapus.']);

        $students = Student::whereIn('nisn', $request->selected_students)
                           ->where('class_code', $classId) // 🛑 BATASAN KELAS
                           ->get();

        $count = $students->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dapat dihapus di kelas Anda.');
        }

        // Hapus file foto dari storage
        foreach ($students as $student) {
            if ($student->photo && $student->photo != 'default_avatar.png') {
                Storage::disk('public')->delete($student->photo);
            }
        }

        Student::whereIn('nisn', $students->pluck('nisn'))->delete();

        return redirect()->route('walikelas.students.index')
                             ->with('success', "{$count} data siswa berhasil dihapus secara massal.");
    }

    /**
     * 💡 Tampilkan detail siswa tertentu. (READ - Detail)
     */
    public function show(Student $student)
    {
        $user = Auth::user();
        $classId = $this->getClassId();

        if ($student->class_code !== $classId) {
            abort(403, 'Akses Ditolak. Anda hanya dapat melihat detail siswa di kelas yang Anda ampu.');
        }

        // 💡 KUNCI: Muat relasi parents
        $student->loadMissing('parents'); 

        $historyAbsences = Absence::where('student_nisn', $student->nisn)
                                  ->orderBy('attendance_time', 'desc')
                                  ->take(10)
                                  ->get();

        // Mengirimkan $student (yang sudah membawa parents) dan historyAbsences
        return view('walikelas.students.show', compact('student', 'historyAbsences'));
    }

    /**
     * 💡 Tampilkan kartu pelajar dan generate barcode. (FITUR BARCODE TUNGGAL)
     */
    public function generateBarcode(Student $student)
    {
        $classId = $this->getClassId();

        if ($student->class_code !== $classId) {
            abort(403, 'Akses Ditolak. Anda tidak dapat mencetak kartu siswa di luar kelas Anda.');
        }
        
        $student->loadMissing('class'); 
        
        if (!$student->barcode_data) {
            $student->update(['barcode_data' => Str::uuid()->toString()]);
            $student->refresh(); 
        }

        $barcode_string = $student->barcode_data; 
        
        $qrcode_svg = QrCode::size(200)
                           ->margin(2)
                           ->format('svg')
                           ->generate($barcode_string);
    
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        return view('walikelas.students.barcode', compact('student', 'qrcode_svg', 'settings'));
    }
    /**
     * 💡 Generate barcode untuk semua siswa aktif di kelas yang diampu (bulk).
     */
    public function generateBulkBarcode()
    {
        $classId = $this->checkClassAssignment();
        if (!is_numeric($classId)) return $classId;

        $students = Student::with('class')
            ->where('class_code', $classId) // 🛑 BATASAN KELAS KUNCI
            ->where('status', 'active')
            ->orderBy('name', 'asc') // Urutkan berdasarkan nama agar teratur
            ->get();
        
        $barcodeData = [];
        
        // Muat settings untuk logo/nama sekolah di kartu
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        foreach ($students as $student) {
            // Pastikan barcode_data terisi (jika belum, generate UUID baru)
            if (!$student->barcode_data) {
                $student->update(['barcode_data' => Str::uuid()->toString()]);
                $student->refresh();
            }

            // Generate QR Code dalam format SVG
            $qrcode_svg = QrCode::size(100) // Ukuran kecil untuk bulk
                                ->margin(2)
                                ->format('svg')
                                ->generate($student->barcode_data);
            
            $barcodeData[] = [
                'student' => $student,
                'qrcode_svg' => $qrcode_svg // Mengirim QR SVG string
            ];
        }
        
        // 💡 Menggunakan view baru untuk Wali Kelas
        return view('walikelas.students.barcode_bulk', compact('barcodeData', 'settings'));
    }
}