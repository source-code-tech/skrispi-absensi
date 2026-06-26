<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Daftar slot waktu yang tetap
        $timeSlots = [
            ['start' => '07:30', 'end' => '09:00'],
            ['start' => '09:15', 'end' => '10:45'],
            ['start' => '11:00', 'end' => '12:30'],
        ];

        $classes = ClassModel::all();
        $subjects = Subject::all();

        foreach ($classes as $class) {
            foreach ($days as $day) {
                // Ambil 3 mapel acak untuk diisi ke 3 slot waktu
                $dailySubjects = $subjects->random(3); 

                foreach ($timeSlots as $index => $slot) {
                    Schedule::create([
                        'class_code'   => $class->code,
                        'subject_code' => $dailySubjects[$index]->code,
                        'day'        => $day,
                        'start_time' => $slot['start'],
                        'end_time'   => $slot['end'],
                    ]);
                }
            }
        }

        $this->command->info('✅ Jadwal tidak bentrok berhasil dibuat!');
    }
}