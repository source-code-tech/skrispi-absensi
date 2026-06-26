<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
    'student_nisn', 
    'attendance_time',
    'checkout_time', 
    'status', 
    'late_duration', 
    'notes',
    'recorded_by',

    // 🌟 Cuma nambahin 3 baris log audit ini di bawahnya:
    'is_manual_corrected',
    'corrected_by',
    'correction_note'
];

    protected $casts = [
        'attendance_time' => 'datetime',
        'checkout_time' => 'datetime', // Penting untuk absensi pulang
    ];

    /**
     * Relasi Many-to-One ke Siswa
     */
    public function student()
    {
        // Absence memiliki satu Siswa
        return $this->belongsTo(Student::class, 'student_nisn', 'nisn');
    }
}