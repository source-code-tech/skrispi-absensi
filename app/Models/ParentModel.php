<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;
    
    // Menetapkan nama tabel utama secara eksplisit
    protected $table = 'parents'; 
    
    protected $fillable = [
        'user_username',
        'name',
        'relation_status',
        'phone_number',
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
            'parent_id',            // Kunci asing Model ini di tabel pivot
            'student_nisn'            // Kunci asing Model Student di tabel pivot
        )->with('class');
    }
    
    // Aksesor: Digunakan di ParentController@index
    public function getStudentNisnsAttribute()
    {
        return $this->students->pluck('nisn');
    }
}