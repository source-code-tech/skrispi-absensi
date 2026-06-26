<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentModel; // Diperlukan
use App\Models\HomeroomTeacher; // Diperlukan
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use Exception; // Diperlukan untuk penanganan error umum

class UserController extends Controller
{
    /**
     * Tampilkan daftar semua user. (READ)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $authId = Auth::id();
        $tab = $request->get('tab', 'all'); 

        $roles = [
            'super_admin' => 'Super Admin', 
            'wali_kelas' => 'Wali Kelas', 
            'orang_tua' => 'Orang Tua'
        ];

        $query = User::query();

        if ($tab === 'all') {
            $query->where('role', '!=', 'super_admin')->where('id', '!=', $authId);
        } elseif ($tab === 'pending') {
            $query->where('role', '!=', 'super_admin')->where('id', '!=', $authId)->where('is_approved', false);
        } elseif ($tab === 'super_admin_list') {
            $query->where('role', 'super_admin');
        } else {
             $query->where('role', '!=', 'super_admin')->where('id', '!=', $authId);
             $tab = 'all';
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                   $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($tab === 'super_admin_list') {
            $users = $query->orderBy('name', 'asc')->paginate(15);
        } else {
            $users = $query->orderBy('is_approved', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        }
            
            
        // Hitung statistik untuk summary card
        $totalUsersCount = User::where('role', '!=', 'super_admin')->count();
        $pendingUsersCount = User::where('role', '!=', 'super_admin')->where('is_approved', 0)->count();

        return view('admin.users.index', compact('users', 'roles', 'tab', 'search', 'totalUsersCount', 'pendingUsersCount'));
    }
    
    public function create()
    {
        $roles = ['wali_kelas' => 'Wali Kelas', 'orang_tua' => 'Orang Tua'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store user baru dan relasi terkait (CREATE).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['wali_kelas', 'orang_tua'])],
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Akun User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_approved' => true,
            ]);

            $successMessage = 'Akun pengguna berhasil ditambahkan dan diaktifkan.';
            
            // 2. 💡 BUAT RECORD TAMBAHAN BERDASARKAN PERAN
            if ($user->role === 'orang_tua') {
                $parent = ParentModel::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    // FIX: Gunakan placeholder jika field wajib tapi form tidak menyediakan
                    'phone_number' => '0', 
                    'relation_status' => 'Wali',
                ]);
                
                DB::commit();
                // Redirect ke Edit Parent untuk menautkan siswa (parents.edit)
                return redirect()->route('parents.edit', $parent->id)
                                 ->with('success', 'Akun Orang Tua berhasil dibuat. Sekarang tautkan siswa ke akun ini dan lengkapi kontak.');

            } elseif ($user->role === 'wali_kelas') {
                // Buat record HomeroomTeacher dengan class_id = null
                HomeroomTeacher::create([
                    'user_id' => $user->id,
                    'class_id' => null, // Harus NULLABLE di DB
                ]);
                
                DB::commit();
                // Redirect ke Edit Teacher (teachers.edit) untuk menautkan kelas
                return redirect()->route('teachers.edit', $user->id)
                                 ->with('success', 'Akun Wali Kelas berhasil dibuat. Sekarang tautkan guru ini ke kelas yang diampu.');
            }

            // Jika peran tidak memerlukan relasi tambahan, commit di sini
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', $successMessage);

        } catch (Exception $e) {
            DB::rollBack();
            // Log::error('Gagal menyimpan User/Parent/Teacher: ' . $e->getMessage()); 
            
            return redirect()->back()->with('error', 'Gagal menyimpan data. Pastikan semua field unik dan kolom di DB mendukung NULL.')->withInput();
        }
    }
    
    public function edit(User $user)
    {
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat mengedit akun Super Admin.');
        }
        $roles = ['wali_kelas' => 'Wali Kelas', 'orang_tua' => 'Orang Tua'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Perbarui akun User dan migrasi relasi terkait (UPDATE).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)], 
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['wali_kelas', 'orang_tua', 'super_admin'])],
            'is_approved' => 'required|boolean', 
        ]);
        
        if ($user->role === 'super_admin' && $user->id !== Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak diizinkan mengubah akun Super Admin.');
        }
        
        $oldRole = $user->role;
        $newRole = $request->role;
        
        $data = $request->only('name', 'email', 'role', 'is_approved');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        DB::beginTransaction();
        try {
            $user->update($data);

            // 💡 MIGRASI PERAN (Jika peran berubah)
            if ($oldRole !== $newRole) {
                
                // A. PEMBERSIHAN DATA LAMA (Hapus relasi lama)
                if ($oldRole === 'orang_tua' && $user->parentRecord) {
                    $user->parentRecord->students()->detach(); 
                    $user->parentRecord->delete();           
                } elseif ($oldRole === 'wali_kelas' && $user->homeroomTeacher) {
                    $user->homeroomTeacher->delete();
                }
                
                // B. INISIASI DATA BARU (Buat relasi baru)
                if ($newRole === 'wali_kelas') {
                    HomeroomTeacher::create([
                        'user_id' => $user->id,
                        'class_id' => null, 
                    ]);
                    DB::commit();
                    return redirect()->route('teachers.edit', $user->id)
                                     ->with('success', 'Peran berhasil diubah ke Wali Kelas. Silakan tautkan kelas.');
                                     
                } elseif ($newRole === 'orang_tua') {
                    $parent = ParentModel::create([
                        'user_id' => $user->id,
                        'name' => $user->name, 
                        'phone_number' => $request->phone_number ?? '0', // Placeholder
                        'relation_status' => $request->relation_status ?? 'Wali',
                    ]);
                    DB::commit();
                    return redirect()->route('parents.edit', $parent->id)
                                     ->with('success', 'Peran berhasil diubah ke Orang Tua. Silakan tautkan siswa.');
                }
            }
            
            // Jika peran tidak berubah atau tidak ada relasi tambahan yang diperlukan
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', "Akun pengguna berhasil diperbarui.");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("User Update/Migrasi Peran Gagal: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data dan migrasi peran. Cek log server.')->withInput();
        }
    }

    /**
     * Hapus akun user. (DELETE)
     */
    public function destroy(User $user)
    {
        // 1. PROTEKSI UTAMA
        if ($user->isSuperAdmin() || $user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus akun ini karena merupakan Super Admin atau akun yang sedang Anda gunakan.');
        }

        $userName = $user->name;
        
        // 2. HAPUS SEMUA RELASI TERKAIT (Menggunakan Transaction)
        DB::beginTransaction();
        try {
            if ($user->homeroomTeacher) { 
                $user->homeroomTeacher->delete(); 
            }
            
            if ($user->parentRecord) {
                // Detach relasi siswa sebelum menghapus ParentModel
                if (method_exists($user->parentRecord, 'students')) {
                    $user->parentRecord->students()->detach(); 
                }
                $user->parentRecord->delete(); 
            }

            // 3. HAPUS AKUN UTAMA
            $user->delete(); 
            DB::commit();

            // 4. REDIRECT SUKSES
            return redirect()->route('admin.users.index')->with('success', "Akun {$userName} berhasil dihapus.");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FATAL DELETE ERROR for user ' . $user->id . ': ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus akun karena masalah database. Mohon coba lagi atau cek log server.');
        }
    }
    
    public function toggleApproval(User $user)
    {
        if ($user->role === 'super_admin' || $user->id === Auth::id()) {
             return redirect()->route('admin.users.index')->with('error', 'Tidak diizinkan mengubah status akun ini.');
        }
        $user->is_approved = !$user->is_approved;
        $user->save();
        $status = $user->is_approved ? 'Disetujui' : 'Ditolak/Ditangguhkan';
        return redirect()->back()->with('success', "Status akun {$user->name} berhasil diubah menjadi: {$status}.");
    }
    
    public function bulkDelete(Request $request)
    {
        $userIds = $request->input('selected_users');
        if (is_string($userIds)) { $userIds = array_filter(explode(',', $userIds)); }
        $request->validate(['selected_users' => 'required|array']);
        $authId = Auth::id();

        $validUserIds = User::whereIn('id', $userIds)
            ->where('role', '!=', 'super_admin')->where('id', '!=', $authId)->pluck('id');
            
        if ($validUserIds->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada akun yang dapat dihapus. Pastikan Anda memilih Non-Admin.');
        }
        
        $count = 0;
        $usersToDelete = User::whereIn('id', $validUserIds)->get();
        
        // Hapus relasi dan akun satu per satu untuk memastikan detach M:M berjalan
        foreach ($usersToDelete as $user) {
             try {
                 if ($user->homeroomTeacher) { $user->homeroomTeacher->delete(); } 
                 
                 if ($user->parentRecord) { 
                     // Detach relasi siswa sebelum menghapus ParentModel
                     if (method_exists($user->parentRecord, 'students')) {
                         $user->parentRecord->students()->detach(); 
                     }
                     $user->parentRecord->delete(); 
                 }
                 
                 $user->delete();
                 $count++;
             } catch (Exception $e) { 
                 Log::error("Gagal menghapus user ID {$user->id} secara massal: " . $e->getMessage()); 
             }
        }
        
        return redirect()->route('admin.users.index')->with('success', "{$count} akun pengguna berhasil dihapus secara massal.");
    }
    
    public function bulkApprove(Request $request)
    {
        $userIds = $request->input('selected_users');
        if (is_string($userIds)) { $userIds = array_filter(explode(',', $userIds)); }
        $request->validate(['selected_users' => 'required|array']);
        $totalSelected = count($userIds);

        $countUpdated = User::whereIn('id', $userIds)
            ->where('role', '!=', 'super_admin')->where('id', '!=', Auth::id())->where('is_approved', false)
            ->update(['is_approved' => true]); 

        $alreadyApproved = $totalSelected - $countUpdated;
        if ($totalSelected === 0 || ($countUpdated === 0 && $alreadyApproved > 0)) {
            $message = $alreadyApproved > 0 ? "Semua ({$alreadyApproved}) akun sudah disetujui sebelumnya." : "Tidak ada akun yang valid untuk disetujui atau kriteria tidak terpenuhi.";
            return redirect()->back()->with('error', $message);
        }
        $message = '';
        if ($countUpdated > 0) { $message .= "{$countUpdated} akun berhasil disetujui."; }
        if ($alreadyApproved > 0) { $message .= " {$alreadyApproved} akun sudah disetujui sebelumnya."; }
        return redirect()->route('admin.users.index', ['tab' => 'pending'])->with('success', trim($message));
    }

    public function bulkToggleApproval(Request $request)
    {
        $userIds = $request->input('selected_users'); 
        if (is_string($userIds)) { $userIds = array_filter(explode(',', $userIds)); }
        $request->validate(['selected_users' => 'required|array']);
        $authId = Auth::id();

        $validUserIds = User::whereIn('id', $userIds)
            ->where('role', '!=', 'super_admin')->where('id', '!=', $authId)->pluck('id');

        if ($validUserIds->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada akun yang valid untuk diubah statusnya.');
        }
        $usersToToggle = User::whereIn('id', $validUserIds)->get();
        $activatedCount = 0;
        $deactivatedCount = 0;

        foreach ($usersToToggle as $user) {
            $user->is_approved = !$user->is_approved;
            $user->save();
            if ($user->is_approved) { $activatedCount++; } else { $deactivatedCount++; }
        }
        $message = "Operasi Massal Selesai: {$activatedCount} akun diaktifkan, {$deactivatedCount} akun ditangguhkan.";
        return redirect()->route('admin.users.index', ['tab' => 'all'])->with('success', $message);
    }
}