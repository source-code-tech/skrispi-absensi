<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeroomTeacher extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';
    
   protected $fillable = [
        'nip', 'user_username', 'class_code', 'scan_token'
    ];
    
    // Relasi ke User (Wali Kelas)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_username', 'username');
    }
    
    // Relasi ke Kelas yang diampu
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_code', 'code');
    }
}