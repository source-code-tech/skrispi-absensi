<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\HomeroomTeacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Digunakan untuk transaksi
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /**
     * Tampilkan daftar Wali Kelas dan Kelas yang diampu. (READ)
     */
    public function index()
    {
        // Menggunakan leftJoin untuk mengurutkan berdasarkan nama kelas dan tingkat,
        // sambil tetap menampilkan guru yang belum mengampu kelas (homeroomTeacher = null).
        $teachers = User::where('role', 'wali_kelas')
            ->leftJoin('homeroom_teachers', 'users.username', '=', 'homeroom_teachers.user_username')
            ->leftJoin('classes', 'homeroom_teachers.class_code', '=', 'classes.code')
            // 🚨 Urutkan berdasarkan Tingkat (Grade ASC) dan Nama Kelas (ASC)
            // Guru tanpa kelas akan muncul lebih dulu atau terakhir, tergantung DBMS,
            // lalu diurutkan dari Kelas 7, 8, 9, dst.
            ->orderBy('classes.grade', 'asc') 
            ->orderBy('classes.name', 'asc')
            ->orderBy('users.name', 'asc') // Urutan kedua untuk nama guru jika kelasnya sama
            ->select('users.*') // Sangat penting: Ambil kolom dari tabel users agar mendapatkan objek User yang benar
            ->with('homeroomTeacher.class')
            ->get();
            
        return view('admin.teachers.index', compact('teachers'));
    }

    // -----------------------------------------------------------------
    // CREATE (Tambah Akun)
    // -----------------------------------------------------------------

    /**
     * Tampilkan form untuk membuat akun Wali Kelas baru. (CREATE - Form)
     */
    public function create()
    {
        // Ambil ID Kelas yang sudah memiliki wali kelas
        $assignedClassCodes = HomeroomTeacher::pluck('class_code')->toArray();
        
        // Ambil Kelas yang BELUM memiliki wali kelas
        $availableClasses = ClassModel::whereNotIn('code', $assignedClassCodes)
                                      ->orderBy('grade')->orderBy('name')
                                      ->get();

        return view('admin.teachers.create', compact('availableClasses'));
    }

    /**
     * Simpan akun Wali Kelas baru dan hubungkan dengan kelas. (CREATE - Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
            // Pastikan class_code unik dan ada di tabel classes
            'class_code' => 'nullable|unique:homeroom_teachers,class_code|exists:classes,code', 
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Akun User (Wali Kelas)
            $username = explode('@', $request->email)[0];
            $user = User::create([
                'username' => $username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'wali_kelas', 
            ]);

            // 2. Hubungkan dengan Kelas jika class_code ada
            if ($request->class_code) {
                HomeroomTeacher::create([
                    'nip' => (string) rand(1000000000, 9999999999),
                    'user_username' => $user->username,
                    'class_code' => $request->class_code,
                    'scan_token' => Str::random(32),
                ]);
            }

            DB::commit();
            return redirect()->route('teachers.index')
                             ->with('success', 'Akun Wali Kelas berhasil ditambahkan dan ditugaskan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menambahkan akun: ' . $e->getMessage());
        }
    }
    
    // -----------------------------------------------------------------
    // UPDATE (Edit Akun)
    // -----------------------------------------------------------------

    /**
     * Tampilkan form untuk mengedit akun Wali Kelas. (UPDATE - Form)
     */
    public function edit(User $teacher)
    {
        // Pastikan user yang diedit adalah Wali Kelas
        if ($teacher->role !== 'wali_kelas') {
            abort(404);
        }

        // Kelas yang sedang diampu guru ini
        $currentClassCode = $teacher->homeroomTeacher->class_code ?? null;

        // Kelas yang sudah diampu guru lain (list yang harus dihindari)
        $assignedClasses = HomeroomTeacher::where('user_username', '!=', $teacher->username)
                                        ->pluck('class_code')
                                        ->toArray();
                                        
        // Ambil SEMUA kelas (untuk mengisi dropdown)
        $availableClasses = ClassModel::orderBy('grade')->orderBy('name')->get();

        return view('admin.teachers.edit', compact('teacher', 'availableClasses', 'currentClassCode', 'assignedClasses'));
    }

    /**
     * Perbarui akun Wali Kelas dan kelas yang diampu. (UPDATE - Store)
     */
    public function update(Request $request, User $teacher)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            // Email harus unik, kecuali email milik user ini sendiri
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->username, 'username')], 
            'password' => 'nullable|string|min:8',
            'class_code' => [
                'nullable', 
                'exists:classes,code',
                // Pastikan class_code unik, kecuali jika itu adalah penugasan yang sudah ada (homeroomTeacher->nip)
                Rule::unique('homeroom_teachers', 'class_code')
                    ->ignore($teacher->homeroomTeacher->nip ?? null, 'nip'), 
            ],
        ]);

        try {
            DB::beginTransaction();

            // 2. Update Akun User
            $data = $request->only('name', 'email');
            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }
            $teacher->update($data);

            // 3. Update/Sinkronisasi Kelas yang Diampu
            $homeroom = HomeroomTeacher::where('user_username', $teacher->username)->first();

                if ($request->class_code) {
                // Ada kelas yang dipilih: Ciptakan atau Perbarui Penugasan
                if ($homeroom) {
                    // Jika data lama diupdate dan kebetulan tokennya masih kosong, langsung buatkan otomatis
                    $updatedData = ['class_code' => $request->class_code];
                    if (empty($homeroom->scan_token)) {
                        $updatedData['scan_token'] = Str::random(32);
                    }
                    $homeroom->update($updatedData);
                } else {
                    HomeroomTeacher::create([
                        'nip' => (string) rand(1000000000, 9999999999),
                        'user_username' => $teacher->username, 
                        'class_code' => $request->class_code,
                        'scan_token' => Str::random(32), // OTOMATIS ISI TOKEN JIKA SEBELUMNYA BELUM ADA KELAS
                    ]);
                } 
            } elseif ($homeroom) {
                // Kelas dihilangkan (user memilih "-- Hapus Kelas"): Hapus Penugasan lama
                $homeroom->delete();
            }
            DB::commit();
            return redirect()->route('teachers.index')
                             ->with('success', 'Data Wali Kelas berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------
    // DELETE (Hapus Akun)
    // -----------------------------------------------------------------

    /**
     * Hapus akun Wali Kelas. (DELETE)
     */
    public function destroy(User $teacher)
    {
        // Hapus akun User akan menghapus relasi di homeroom_teachers 
        // secara otomatis (asumsi onDelete('cascade') di migration)
        $teacher->delete(); 
        
        return redirect()->route('teachers.index')
                         ->with('success', "Akun Wali Kelas {$teacher->name} berhasil dihapus.");
    }
}