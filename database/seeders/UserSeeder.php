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

    }
}