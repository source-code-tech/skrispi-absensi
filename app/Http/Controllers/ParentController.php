<?php

namespace App\Http\Controllers\Parent; // Menggunakan Parent namespace yang lebih spesifik

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Absence;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParentController extends Controller
{
    /**
     * Tampilkan Dashboard Orang Tua dengan riwayat absensi anak.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Pastikan Eager Loading relasi children dan class-nya
        $parents = \App\Models\ParentModel::with('students.class')
            ->when($search, function($query) use ($search) {
                // Logika pencarian yang Anda gunakan
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
                // Anda mungkin perlu join jika mencari berdasarkan nama anak
            })
            
            // 🔥 PERBAIKAN: Mengurutkan berdasarkan waktu pembuatan terbaru (DESC)
            ->orderBy('created_at', 'desc') 
            
            ->paginate(15);
            
        return view('admin.parents.index', compact('parents'));
    }
}