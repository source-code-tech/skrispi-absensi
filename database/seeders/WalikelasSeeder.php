<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\HomeroomTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // DATA WALI KELAS MI NURUL AMIN
        // Seeder ini HANYA menambahkan wali kelas ke kelas yang sudah ada.
        // Jalankan setelah kelas 1A-6C sudah dibuat di sistem.
        // ============================================================

        $waliKelas = [
            ['nama' => 'Ustadz Ahmad Fauzi, S.Pd.I.',        'email' => 'ahmad.fauzi@mischool.sch.id',      'password' => 'Ahmad@2024',      'kelas' => '1A'],
            ['nama' => 'Ustadzah Siti Rahmawati, S.Pd.',     'email' => 'siti.rahmawati@mischool.sch.id',   'password' => 'Siti@2024',       'kelas' => '1B'],
            ['nama' => 'Ustadzah Nurul Hidayah, S.Pd.',      'email' => 'nurul.hidayah@mischool.sch.id',    'password' => 'Nurul@2024',      'kelas' => '1C'],
            ['nama' => 'Ustadz Muhammad Yusuf, S.Pd.I.',     'email' => 'm.yusuf@mischool.sch.id',          'password' => 'Yusuf@2024',      'kelas' => '2A'],
            ['nama' => 'Ustadzah Fatimah Azzahra, S.Pd.',    'email' => 'fatimah.azzahra@mischool.sch.id',  'password' => 'Fatimah@2024',    'kelas' => '2B'],
            ['nama' => 'Ustadz Hasan Basri, S.Pd.I.',        'email' => 'hasan.basri@mischool.sch.id',      'password' => 'Hasan@2024',      'kelas' => '2C'],
            ['nama' => 'Ustadzah Khadijah Nur, M.Pd.',       'email' => 'khadijah.nur@mischool.sch.id',     'password' => 'Khadijah@2024',   'kelas' => '3A'],
            ['nama' => 'Ustadz Zainul Arifin, S.Pd.I.',      'email' => 'zainul.arifin@mischool.sch.id',    'password' => 'Zainul@2024',     'kelas' => '3B'],
            ['nama' => 'Ustadzah Maryam Solehah, S.Pd.',     'email' => 'maryam.solehah@mischool.sch.id',   'password' => 'Maryam@2024',     'kelas' => '3C'],
            ['nama' => 'Ustadz Abdul Hamid, S.Pd.I.',        'email' => 'abdul.hamid@mischool.sch.id',      'password' => 'Abdul@2024',      'kelas' => '4A'],
            ['nama' => 'Ustadzah Aisyah Permata, S.Pd.',     'email' => 'aisyah.permata@mischool.sch.id',   'password' => 'Aisyah@2024',     'kelas' => '4B'],
            ['nama' => 'Ustadz Ridwan Hakim, M.Pd.I.',       'email' => 'ridwan.hakim@mischool.sch.id',     'password' => 'Ridwan@2024',     'kelas' => '4C'],
            ['nama' => 'Ustadzah Halimah Tusadiyah, S.Pd.',  'email' => 'halimah.tusadiyah@mischool.sch.id','password' => 'Halimah@2024',    'kelas' => '5A'],
            ['nama' => 'Ustadz Syaifullah Amin, S.Pd.I.',    'email' => 'syaifullah.amin@mischool.sch.id',  'password' => 'Syaifullah@2024', 'kelas' => '5B'],
            ['nama' => 'Ustadzah Rohimah Dewi, M.Pd.',       'email' => 'rohimah.dewi@mischool.sch.id',     'password' => 'Rohimah@2024',    'kelas' => '5C'],
            ['nama' => 'Ustadz Mukhlis Fathoni, M.Pd.I.',    'email' => 'mukhlis.fathoni@mischool.sch.id',  'password' => 'Mukhlis@2024',    'kelas' => '6A'],
            ['nama' => 'Ustadzah Badriyah Salim, S.Pd.',     'email' => 'badriyah.salim@mischool.sch.id',   'password' => 'Badriyah@2024',   'kelas' => '6B'],
            ['nama' => 'Ustadz Ihsan Kamil, S.Pd.I.',        'email' => 'ihsan.kamil@mischool.sch.id',      'password' => 'Ihsan@2024',      'kelas' => '6C'],
        ];

        // Cegah duplikasi — skip jika sudah pernah dijalankan
        if (User::where('email', 'ahmad.fauzi@mischool.sch.id')->exists()) {
            $this->command->warn('⚠️  Data wali kelas MI Nurul Amin sudah ada. Seeder dilewati.');
            return;
        }

        $berhasil  = 0;
        $skipKelas = 0;

        foreach ($waliKelas as $data) {

            // 1. Cari kelas berdasarkan nama (misal: '1A')
            $kelas = ClassModel::where('name', $data['kelas'])->first();

            if (! $kelas) {
                $this->command->warn("⚠️  Kelas {$data['kelas']} tidak ditemukan. Wali kelas {$data['nama']} dilewati.");
                $skipKelas++;
                continue;
            }

            // 2. Buat akun User dengan role wali_kelas
            $username = explode('@', $data['email'])[0];
            $user = User::create([
                'username'          => $username,
                'name'              => $data['nama'],
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'role'              => 'wali_kelas',
                'is_approved'       => true,
                'email_verified_at' => Carbon::now(),
            ]);

            // 3. Buat relasi di tabel homeroom_teachers
            HomeroomTeacher::create([
                'nip'           => (string) rand(1000000000, 9999999999),
                'user_username' => $user->username,
                'class_code'    => $kelas->code,
            ]);

            $this->command->info("✅ {$data['nama']} → Kelas {$data['kelas']}");
            $berhasil++;
        }

        $this->command->newLine();
        $this->command->info("🎉 Selesai! {$berhasil} wali kelas berhasil ditambahkan." . ($skipKelas > 0 ? " {$skipKelas} dilewati karena kelas tidak ditemukan." : ''));
    }
}