<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // WAJIB: Import Str helper untuk UUID

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nisn', 
        'nis', 
        'name', 
        'gender', 
        'class_code', 
        'address', 
        'birth_place', 
        'birth_date', 
        'photo', 
        'status', 
        'barcode_data', 
    ];

    // 💡 PERBAIKAN: Gunakan $casts modern
    protected $casts = [
        'birth_date' => 'date', // Mengonversi birth_date menjadi objek Carbon
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 💡 METHOD BOOT: Otomatisasi pengisian barcode_data sebelum record disimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->barcode_data)) {
                $student->barcode_data = Str::uuid()->toString();
            }
        });
    }
    
    // Relasi One-to-Many ke ClassModel
    public function class() 
    {
        return $this->belongsTo(ClassModel::class, 'class_code', 'code');
    }

    /**
     * Relasi Many-to-Many ke ParentModel (Orang Tua).
     */
    public function parents()
    {
        return $this->belongsToMany(
            ParentModel::class, 
            'parent_student', 
            'student_nisn', 
            'parent_nik' 
        );
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'student_nisn', 'nisn');
    }
}