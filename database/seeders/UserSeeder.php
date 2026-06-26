<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon; // Import Carbon untuk timestamp

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Anda hanya membuat user jika tabel masih kosong
      //  if (User::where('role', 'super_admin')->exists()) {
       //     return;
       // }
        
        // 1. AKUN SUPER ADMIN (Otomatis Disetujui & Diverifikasi)
        User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin Sekolah',
            'email' => 'ardiansyahdzan@gmail.com',
            'password' => Hash::make('password'), 
            'role' => 'super_admin',
            // ✅ Status Wajib
            'is_approved' => true, 
            'email_verified_at' => Carbon::now(),
        ]);

        // 2. AKUN WALI KELAS (Otomatis Disetujui & Diverifikasi untuk Testing)
        User::create([
            'username' => 'walikelas_xa',
            'name' => 'Wali Kelas X-A',
            'email' => 'walikelas@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
            // ✅ Status Wajib
            'is_approved' => true, 
            'email_verified_at' => Carbon::now(),
        ]);
        
        // 3. AKUN ORANG TUA (Otomatis Disetujui & Diverifikasi untuk Testing)
        User::create([
            'username' => 'ortu_budi',
            'name' => 'Bapak Budi Santoso',
            'email' => 'ortu@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'orang_tua',
            // ✅ Status Wajib
            'is_approved' => true, 
            'email_verified_at' => Carbon::now(),
        ]);
    }
}