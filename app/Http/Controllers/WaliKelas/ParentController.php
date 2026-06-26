<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    // Helper untuk mengecek otorisasi (apakah siswa Parent ini di kelas Wali Kelas)
    protected function checkParentAuthorization(ParentModel $parent)
    {
        $classId = Auth::user()->homeroomTeacher->class_id ?? null;
        if (!$classId) return false;

        // Cek apakah ada siswa yang terhubung dengan Parent ini yang juga diampu Wali Kelas
        return $parent->students()->where('class_id', $classId)->exists();
    }

    /**
     * Tampilkan form edit untuk Orang Tua tertentu.
     */
    public function edit(ParentModel $parent)
    {
        if (!$this->checkParentAuthorization($parent)) {
            abort(403, 'Akses Ditolak. Data orang tua ini tidak terhubung dengan siswa di kelas Anda.');
        }

        // Relasi status yang tersedia
        $relationStatuses = ['Ayah', 'Ibu', 'Wali'];

        return view('walikelas.parents.edit', compact('parent', 'relationStatuses'));
    }

    /**
     * Perbarui data Orang Tua (khususnya kontak dan relasi).
     */
    public function update(Request $request, ParentModel $parent)
    {
        if (!$this->checkParentAuthorization($parent)) {
            abort(403, 'Akses Ditolak.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'relation_status' => 'required|in:Ayah,Ibu,Wali',
            'phone_number' => 'required|string|max:20', 
        ]);

        $parent->update($request->only(['name', 'relation_status', 'phone_number']));

        // Redirect kembali ke halaman detail siswa yang pertama terhubung
        $student = $parent->students()->first();
        
        if ($student) {
            return redirect()->route('walikelas.students.show', $student->id)
                             ->with('success', "Data Orang Tua/Wali {$parent->name} berhasil diperbarui.");
        }

        return redirect()->route('walikelas.dashboard')->with('success', 'Data Orang Tua berhasil diperbarui.');
    }
}