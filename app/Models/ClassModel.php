<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;
    
    // 🔥 PERBAIKAN SEBELUMNYA: Ini sudah benar, menentukan nama tabel eksplisit.
    protected $table = 'classes'; 
    
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // ✅ PERBAIKAN SEKARANG: Tambahkan major dan description ke fillable
   protected $fillable = [
                'code',
                'grade', 
                'major',
                'description',
                'dismissal_time',
                ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_code', 'code');
    }

    public function homeroomTeacher()
    {
        // Relasi Kebalikan: Satu Kelas memiliki satu Wali Kelas
        // Asumsi model HomeroomTeacher ada
        return $this->hasOne(HomeroomTeacher::class, 'class_code', 'code');
    }
}