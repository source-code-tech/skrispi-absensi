<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\Student;
use App\Models\Setting;
use App\Models\ClassModel;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoMarkAlpha extends Command
{
    protected $signature = 'absence:auto-alpha';
    protected $description = 'Auto tandai Alfa untuk siswa yang tidak scan hari ini';

    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $count = 0;

            // Skip weekend
    if ($today->isWeekend()) {
        $this->info('Hari ini weekend, auto-alpha dilewati.');
        return;
    }

    // Skip hari libur nasional
   $isHoliday = Holiday::where('date', $today->format('Y-m-d'))->exists();
    if ($isHoliday) {
        $this->info('Hari ini hari libur, auto-alpha dilewati.');
        return;
    }

        $classes = ClassModel::where('status', 'active')->get();

        foreach ($classes as $class) {
            $classDismissalTime = $class->dismissal_time
                ? substr($class->dismissal_time, 0, 5)
                : null;

            $endTimeSetting = $classDismissalTime
                ?? Setting::where('key', 'attendance_end_time')->value('value')
                ?? '15:00';

            $jamPulang = Carbon::parse($today->format('Y-m-d') . ' ' . $endTimeSetting);

            if ($now->lessThan($jamPulang)) {
                continue;
            }

            $students = Student::where('class_id', $class->id)
                ->where('status', 'active')
                ->get();

            foreach ($students as $student) {
                $sudahAbsen = Absence::where('student_id', $student->id)
                    ->whereDate('attendance_time', $today)
                    ->exists();

                if (!$sudahAbsen) {
                    Absence::create([
                        'student_id'      => $student->id,
                        'attendance_time' => $jamPulang,
                        'status'          => 'Alfa',
                        'recorded_by'     => 'System Auto',
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Auto-Alpha selesai: {$count} siswa ditandai Alfa.");
    }
}