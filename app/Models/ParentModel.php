<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;
    
    // Menetapkan nama tabel utama secara eksplisit
    protected $table = 'parents'; 
    
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'nik',
        'user_username', 
        'name',
        'phone_number',
        'relation_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_username', 'username');
    }

    /**
     * Relasi Many-to-Many ke Student.
     */
    public function students()
    {
        return $this->belongsToMany(
            Student::class, 
            'parent_student',       // Nama tabel pivot yang BENAR di database
            'parent_nik',            // Kunci asing Model ini di tabel pivot
            'student_nisn'            // Kunci asing Model Student di tabel pivot
        )->with('class');
    }
    
    // Aksesor: Digunakan di ParentController@index
    public function getStudentNisnsAttribute()
    {
        return $this->students->pluck('nisn');
    }
}