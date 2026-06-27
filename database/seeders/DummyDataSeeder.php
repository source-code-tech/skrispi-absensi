<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\ParentModel;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Models\HomeroomTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema; 

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $this->command->info('✅ Menggunakan Faker Indonesia untuk data dummy.');
        
        // Cek Keberadaan User untuk menghindari duplikasi
        if (User::where('email', 'wk1@test.com')->exists()) {
            $this->command->info('Data dummy sudah ada. Melewati proses seeding.');
            return;
        }

        // ==========================================================
        // 🔥 PERBAIKAN KRITIS: NONAKTIFKAN FOREIGN KEY & TRUNCATE
        // ==========================================================
        Schema::disableForeignKeyConstraints(); 

        $this->command->info('⏳ Membersihkan tabel-tabel data lama...');
        
        // Bersihkan semua tabel
        DB::table('settings')->truncate();
        User::where('role', '!=', 'super_admin')->delete(); 
        ClassModel::truncate(); 
        HomeroomTeacher::truncate();
        Student::truncate();
        ParentModel::truncate();
        DB::table('parent_student')->truncate(); 
        
        // ==========================================================
        // 1. BUAT KELAS SMP SPESIFIK (7A, 7B, 8A, 8B, 9A, 9B)
        // ==========================================================
        $this->command->info('⏳ Membuat 6 Data Kelas SMP Spesifik...');
        
        $smpClassesData = [
            ['code' => '7A', 'grade' => 7, 'major' => 'UMUM'],
        ];

        $classes = [];
        foreach ($smpClassesData as $data) {
             $classes[] = ClassModel::create($data);
        }
        $this->command->info('✅ 6 Data Kelas SMP Berhasil Dibuat.');
        
        // ==========================================================
        // 2. BUAT WALI KELAS DUMMY & RELASI
        // ==========================================================
        $numTeachers = count($classes);
        $this->command->info("⏳ Membuat $numTeachers Akun Wali Kelas Dummy...");
        $teachers = [];
        
        // Simpan User Wali Kelas
        foreach ($classes as $index => $class) {
            $name = $faker->name('male');
            $nip = $faker->unique()->numerify('198#########'); // Format NIP 13 digit
            
            $user = User::create([
                'username' => $nip, // 💡 Wali Kelas memakai NIP sebagai Username
                'name' => $name,
                'email' => 'wk' . ($index + 1) . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
                'is_approved' => true,
                'email_verified_at' => Carbon::now(), 
            ]);
            $teachers[] = $user;
            
            // Hubungkan User ke HomeroomTeacher & Kelas (Karena ID Kelas valid, ini aman)
            HomeroomTeacher::create([
                'nip' => $nip,
                'user_username' => $user->username,
                'class_code' => $class->code,
            ]);
        }
        $this->command->info("✅ {$numTeachers} Akun Wali Kelas Dummy Berhasil Dibuat & Ditautkan ke Kelas.");

        // ==========================================================
        // 3. BUAT SISWA DUMMY
        // ==========================================================
        $totalStudents = 1;
        $this->command->info("⏳ Membuat $totalStudents Data Siswa Dummy...");
        $students = [];
        $uniqueNisns = [];
        
        while (count($students) < $totalStudents) {
            $nisn = $faker->unique()->randomNumber(9, true);
            if (!in_array($nisn, $uniqueNisns)) {
                $uniqueNisns[] = $nisn;
                
                $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
                $name = $gender == 'Laki-laki' ? $faker->name('male') : $faker->name('female');

                $students[] = Student::create([
                    'nisn' => $nisn,
                    'nis' => $faker->unique()->randomNumber(6, true), 
                    'name' => $name,
                    'gender' => $gender,
                    'class_code' => $faker->randomElement($classes)->code,
                    'barcode_data' => Str::uuid(),
                    'status' => 'active',
                    'photo' => 'default_avatar.png',
                ]);
            }
        }
        $this->command->info("✅ $totalStudents Data Siswa Dummy Berhasil Dibuat.");
        
        // ==========================================================
        // 4. BUAT ORANG TUA DUMMY & RELASI
        // ==========================================================
        $numParents = 1; 
        $this->command->info("⏳ Membuat $numParents Akun Orang Tua Dummy...");
        $parentRecords = [];
        $now = Carbon::now();

        for ($i = 1; $i <= $numParents; $i++) {
            $user = User::create([
                'username' => 'ortu' . $i,
                'name' => $faker->name,
                'email' => 'ortu' . $i . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'orang_tua',
                'is_approved' => true, 
                'email_verified_at' => $now,
            ]);

            $parentRecords[] = ParentModel::create([
                'user_username' => $user->username,
                'name' => $user->name,
                'relation_status' => $faker->randomElement(['Ayah', 'Ibu']),
                // 💡 FIX: phone_number di ParentModel harus unik dan menggunakan format e164
                'phone_number' => $faker->unique()->e164PhoneNumber, 
            ]);
        }
        $this->command->info("✅ $numParents Akun Orang Tua Dummy Berhasil Dibuat.");

        // Hubungkan Orang Tua ke Siswa (Simulasi M:M)
        $studentNisns = collect($students)->pluck('nisn');
        $studentsToAssign = $studentNisns->shuffle()->take($numParents); 
        
        // Hubungkan setiap Ortu dengan setidaknya satu siswa
        foreach ($parentRecords as $index => $parent) {
            if (isset($studentsToAssign[$index])) {
                $parent->students()->attach($studentsToAssign[$index]);
            }
        }
        
        // Tambahkan relasi tambahan untuk simulasi Ortu punya lebih dari 1 anak
        $assignedStudentNisns = DB::table('parent_student')->pluck('student_nisn')->toArray();
        $remainingStudents = $studentNisns->diff($assignedStudentNisns)->values();

        for ($i = 0; $i < 10 && $remainingStudents->isNotEmpty(); $i++) {
            if (isset($parentRecords[$i])) {
                $extraChild = $remainingStudents->pop(); 
                $parentRecords[$i]->students()->attach($extraChild);
            }
        }
        
        $this->command->info('✅ Relasi Orang Tua ke Siswa Berhasil Dibuat (Simulasi M:M).');
        
        // ==========================================================
        // 5. BUAT PENGATURAN SISTEM
        // ==========================================================
        $this->command->info('⏳ Menambahkan data Pengaturan Sistem...');
        
        $settingsData = [
            ['key' => 'school_name', 'value' => 'SMP Negeri Sejahtera Jaya'],
            ['key' => 'attendance_start_time', 'value' => '07:00'],
            ['key' => 'late_tolerance_minutes', 'value' => '10'],
            ['key' => 'attendance_end_time', 'value' => '15:00'], 
            ['key' => 'wa_api_endpoint', 'value' => 'https://dummy-wa-api.com/send'],
            ['key' => 'wa_api_key', 'value' => 'API-DUMMY-KEY-12345'],
            ['key' => 'school_logo', 'value' => null],
        ];

        $now = Carbon::now();
        foreach ($settingsData as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                [
                    'value' => $data['value'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
        $this->command->info('✅ Data Pengaturan Sistem berhasil ditambahkan.');
        
        // 🔥 PERBAIKAN KRITIS: Aktifkan kembali pemeriksaan Foreign Key di akhir seeder
        Schema::enableForeignKeyConstraints();
    }
}