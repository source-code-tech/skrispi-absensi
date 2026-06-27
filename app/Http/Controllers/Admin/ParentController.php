<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use Exception; // Diperlukan untuk penanganan error umum

class ParentController extends Controller
{
    /**
     * Tampilkan daftar Orang Tua dengan paginasi dan pencarian. (READ)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $parents = ParentModel::with('user', 'students.class')
                             ->when($search, function($query) use ($search) {
                                 $query->where('name', 'like', "%{$search}%")
                                       ->orWhere('phone_number', 'like', "%{$search}%")
                                       ->orWhereHas('students', function($q) use ($search) {
                                           $q->where('name', 'like', "%{$search}%");
                                       });
                             })
                             // 💡 PERUBAHAN: Urutkan berdasarkan ID (DESC)
                             ->orderBy('id', 'desc')
                             ->paginate(15);
                               
        return view('admin.parents.index', compact('parents'));
    }

    // -----------------------------------------------------------------
    // CREATE
    // -----------------------------------------------------------------

    public function create()
    {
        // Hanya ambil Siswa yang BELUM memiliki Orang Tua
        $students = Student::with('class')
            ->whereDoesntHave('parents') 
            ->orderBy('name')
            ->get();
            
        return view('admin.parents.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone_number' => 'required|unique:parents,phone_number',
            'relation_status' => 'nullable|string|max:50',
            'student_nisns' => 'required|array|min:1', 
            'student_nisns.*' => 'exists:students,nisn',
        ]);

        DB::beginTransaction();
        try {
            $username = explode('@', $request->email)[0];
            $user = User::create([
                'username' => $username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'orang_tua',
                'is_approved' => true,
            ]);

            $parent = ParentModel::create([
                'user_username' => $user->username,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'relation_status' => $request->relation_status,
            ]);

            $parent->students()->attach($request->student_nisns);

            DB::commit();
            return redirect()->route('parents.index')->with('success', 'Akun Orang Tua berhasil ditambahkan!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    // -----------------------------------------------------------------
    // UPDATE
    // -----------------------------------------------------------------

    /**
     * Tampilkan form untuk mengedit akun Orang Tua. (UPDATE - Form)
     */
    public function edit(ParentModel $parent)
    {
        // 1. Ambil NISN siswa yang sudah terhubung dengan *akun orang tua lain*
        $assignedStudentsNisns = DB::table('parent_student')
                                 ->where('parent_id', '!=', $parent->id) 
                                 ->pluck('student_nisn')
                                 ->toArray();
        
        // 2. Ambil SEMUA Siswa (termasuk yang sudah terhubung dengan akun ini)
        $students = Student::with('class')->orderBy('name')->get(); 
        
        // 3. Ambil NISN siswa yang saat ini terhubung dengan akun ini
        $selectedStudentNisns = $parent->students->pluck('nisn')->toArray();

        return view('admin.parents.edit', compact('parent', 'students', 'selectedStudentNisns', 'assignedStudentsNisns'));
    }

    /**
     * Perbarui akun Orang Tua dan relasi siswa. (UPDATE - Store)
     */
    public function update(Request $request, ParentModel $parent)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($parent->user_username, 'username')],
            'password' => 'nullable|min:8',
            'phone_number' => ['required', Rule::unique('parents', 'phone_number')->ignore($parent->id)],
            'relation_status' => 'nullable|max:50',
            'student_nisns' => 'required|array|min:1',
            'student_nisns.*' => 'exists:students,nisn',
        ]);

        // Opsional: Validasi kustom untuk penautan siswa ke orang tua lain
        $isStudentAssignedToOther = DB::table('parent_student')
                                       ->where('parent_id', '!=', $parent->id)
                                       ->whereIn('student_nisn', $request->student_nisns)
                                       ->exists();

        if ($isStudentAssignedToOther) {
             return redirect()->back()->with('error', 'Salah satu siswa yang dipilih sudah terhubung dengan akun orang tua lain.')->withInput();
        }

        DB::beginTransaction();
        try {
            $user = $parent->user;
            $userData = $request->only('name', 'email');
            if ($request->password) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $parent->update($request->only('name', 'phone_number', 'relation_status'));

            // Sinkronisasi relasi Many-to-Many
            $parent->students()->sync($request->student_nisns);

            DB::commit();
            return redirect()->route('parents.index')->with('success', 'Data Orang Tua berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    // -----------------------------------------------------------------
    // DELETE
    // -----------------------------------------------------------------

    public function destroy(ParentModel $parent)
    {
        $parentName = $parent->name;
        
        DB::beginTransaction();
        try {
            // 1. Hapus relasi M:M ke siswa
            $parent->students()->detach(); 

            // 2. Hapus akun User (disarankan karena user_id ada di parent)
            if ($parent->user) {
                 $parent->user->delete(); 
            }

            // 3. Hapus ParentModel
            $parent->delete(); 
            
            DB::commit();
            return redirect()->route('parents.index')->with('success', "Akun Orang Tua {$parentName} berhasil dihapus.");
        
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}