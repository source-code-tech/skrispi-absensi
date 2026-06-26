<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D; // Pastikan ini ada
use Illuminate\Validation\ValidationException;
use Exception;
use App\Exports\StudentsImportTemplate;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar semua siswa. (READ)
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $classId = $request->get('class_id');

            // Ambil semua kelas untuk filter dropdown
            $classes = ClassModel::orderBy('grade')->orderBy('name')->get();

            $query = Student::with('class')
                            ->join('classes', 'students.class_code', '=', 'classes.code')
                            ->select('students.*'); // Pastikan select students.* agar ID tidak tertimpa ID kelas

            // 1. Logic Pencarian
            if ($search) {
                $query->where(function($q) use ($search) {
                     $q->where('students.name', 'like', "%{$search}%")
                       ->orWhere('students.nisn', 'like', "%{$search}%")
                       ->orWhere('students.nis', 'like', "%{$search}%");
                });
            }
            
            // 2. Logic Filter Kelas
            if ($classId) {
                $query->where('classes.code', $classId);
            }

            // 3. Logic Pengurutan Final
            $students = $query->orderBy('classes.grade', 'asc')
                              ->orderBy('classes.name', 'asc')
                              ->orderBy('students.name', 'asc')
                              ->paginate(15)
                              ->withQueryString();
            
            return view('admin.students.index', compact('students', 'classes'));

        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk membuat siswa baru. (CREATE - Form)
     */
    public function create()
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
            
        return view('admin.students.create', compact('classes'));
    }

    /**
     * Simpan data siswa baru ke database. (CREATE - Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|unique:students,nisn|max:20',
            'nis' => 'nullable|string|unique:students,nis|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_code' => 'required|exists:classes,code',
            'address' => 'nullable|string|max:500',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png', 
        ], [
        // --- TAMBAHKAN KODE INI ---
        'nisn.unique'   => 'NISN sudah terdaftar, silakan gunakan NISN lain.',
        'nis.unique'    => 'NIS sudah terdaftar, silahkan gunakan NIS lain.',
        'nisn.required' => 'NISN wajib diisi.',
        'name.required' => 'Nama lengkap wajib diisi.',
        // ---------------------------
    ]);
            
        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Penanganan Upload Foto
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('photos/students', 'public');
                $data['photo'] = $path;
            } else {
                $data['photo'] = 'default_avatar.png'; 
            }
            
            $data['status'] = 'active';
            $data['barcode_data'] = Str::uuid()->toString();

            Student::create($data);

            DB::commit();

            return redirect()->route('students.index')
                             ->with('success', 'Data siswa berhasil ditambahkan.');

        } catch (Exception $e) {
            DB::rollBack();
            // Hapus foto jika sudah terlanjur terupload tapi DB gagal
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit. (UPDATE - Form)
     */
    public function edit(Student $student)
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
            
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Perbarui data siswa. (UPDATE - Store)
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->nisn . ',nisn',
            'nis' => 'nullable|string|max:20|unique:students,nis,' . $student->nisn . ',nisn',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_code' => 'required|exists:classes,code',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ], [
        // --- TAMBAHKAN KODE INI ---
        'nisn.unique' => 'NISN sudah dipakai siswa lain.',
        'nis.unique'  => 'NIS sudah dipakai siswa lain.',
       
    ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            // Penanganan Update Foto
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika bukan default
                if ($student->photo && $student->photo !== 'default_avatar.png') {
                    if (Storage::disk('public')->exists($student->photo)) {
                        Storage::disk('public')->delete($student->photo);
                    }
                }
                $data['photo'] = $request->file('photo')->store('photos/students', 'public');
            }

            $student->update($data);

            DB::commit();

            return redirect()->route('students.index')
                             ->with('success', 'Data siswa berhasil diperbarui.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data siswa. (DELETE)
     */
    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();

            $name = $student->name;
            
            // Hapus Foto
            if ($student->photo && $student->photo !== 'default_avatar.png') {
                if (Storage::disk('public')->exists($student->photo)) {
                    Storage::disk('public')->delete($student->photo);
                }
            }

            $student->delete();

            DB::commit();

            return redirect()->route('students.index')
                             ->with('success', "Siswa {$name} berhasil dihapus.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Massal.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,nisn',
        ]);

        try {
            DB::beginTransaction();

            $students = Student::whereIn('nisn', $request->selected_students)->get();
            $count = $students->count();

            foreach ($students as $student) {
                if ($student->photo && $student->photo !== 'default_avatar.png') {
                    if (Storage::disk('public')->exists($student->photo)) {
                        Storage::disk('public')->delete($student->photo);
                    }
                }
                $student->delete();
            }

            DB::commit();

            return redirect()->route('students.index')
                             ->with('success', "{$count} siswa berhasil dihapus.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan hapus massal: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Barcode Satuan.
     */
    public function generateBarcode(Student $student)
    {
        try {
            if (!$student->barcode_data) {
                $student->update(['barcode_data' => Str::uuid()->toString()]);
            }

            // Generate SVG QR Code
            $qrcode_svg = QrCode::size(200)->margin(1)->format('svg')->generate($student->barcode_data);
            
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

            return view('admin.students.barcode', compact('student', 'qrcode_svg', 'settings'));

        } catch (Exception $e) {
            return back()->with('error', 'Gagal memuat kartu siswa: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Barcode Massal.
     */
    public function generateBulkBarcode()
    {
        try {
            // Ambil siswa aktif, urutkan per kelas agar rapi saat dicetak
            $students = Student::with('class')
                        ->where('students.status', 'active')
                        ->join('classes', 'students.class_code', '=', 'classes.code')
                        ->orderBy('classes.grade', 'asc')
                        ->orderBy('classes.name', 'asc')
                        ->orderBy('students.name', 'asc')
                        ->select('students.*')
                        ->get();
            
            $barcodeData = [];
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

            foreach ($students as $student) {
                if (!$student->barcode_data) {
                    $student->update(['barcode_data' => Str::uuid()->toString()]);
                }
                
                $qrcode_svg = QrCode::size(200)->margin(1)->format('svg')->generate($student->barcode_data);
                
                $barcodeData[] = [
                    'student' => $student,
                    'qrcode_svg' => $qrcode_svg
                ];
            }
            
            return view('admin.students.barcode_bulk', compact('barcodeData', 'settings'));

        } catch (Exception $e) {
            return back()->with('error', 'Gagal memuat halaman cetak massal: ' . $e->getMessage());
        }
    }

    /**
     * Import Excel.
     */
    public function importForm()
    {
        $classes = ClassModel::where('status', 'active')->orderBy('grade')->get();
        return view('admin.students.import_form', compact('classes'));
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new StudentsImportTemplate, 
            'Template_Import_Siswa.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls|max:5120']); // Max 5MB

        try {
            $import = new StudentsImport;
            Excel::import($import, $request->file('file'));

            return redirect()->route('students.index')
                             ->with('success', 'Import selesai! ' . $import->getRowCount() . ' data diproses.')
                             ->with('close_loading', true);

        } catch (ValidationException $e) {
             return back()->withErrors($e->errors())->with('error', 'Validasi Import Gagal.')->with('close_loading', true);
        } catch (Exception $e) {
             return back()->with('error', 'Import Gagal: ' . $e->getMessage())->with('close_loading', true);
        }
    }

    /**
     * Export Excel.
     */
    public function export()
    {
        try {
            return Excel::download(new StudentsExport, 'Data_Siswa_'.date('Y-m-d_H-i').'.xlsx');
        } catch (Exception $e) {
            // return back()->with('error', 'Gagal export data: ' . $e->getMessage()); // Jika return back gagal karena headers sent (binary output)
             return response()->json(['error' => 'Gagal export: ' . $e->getMessage()], 500);

        }
    }

    // --- Helper Routes ---
    public function activate(Student $student) {
        $student->update(['status' => 'active']);
        return back()->with('success', 'Siswa diaktifkan.');
    }

    public function deactivate(Student $student) {
        $student->update(['status' => 'inactive']);
        return back()->with('success', 'Siswa dinonaktifkan.');
    }
}