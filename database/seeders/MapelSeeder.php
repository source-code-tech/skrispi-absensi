<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            ['code' => 'PAI-AQ', 'name' => 'Al-Qur\'an Hadits'],
            ['code' => 'PAI-AK', 'name' => 'Akidah Akhlak'],
            ['code' => 'PAI-FI', 'name' => 'Fikih'],
            ['code' => 'PAI-SK', 'name' => 'Sejarah Kebudayaan Islam'],
            ['code' => 'BIN',   'name' => 'Bahasa Indonesia'],
            ['code' => 'MAT',   'name' => 'Matematika'],
            ['code' => 'IPA',   'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS',   'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'PKN',   'name' => 'Pendidikan Pancasila'],
            ['code' => 'BIG',   'name' => 'Bahasa Inggris'],
            ['code' => 'BAR',   'name' => 'Bahasa Arab'],
            ['code' => 'SBK',   'name' => 'Seni Budaya dan Prakarya'],
            ['code' => 'PJOK',  'name' => 'PJOK (Olahraga)'],
        ];

        foreach ($mapel as $m) {
            // Gunakan 'code' sesuai dengan kolom yang ada di database Anda
            DB::table('subjects')->updateOrInsert(
                ['code' => $m['code']], 
                ['name' => $m['name']]
            );
        }

        $this->command->info('✅ Data Mata Pelajaran berhasil diisi!');
    }
}