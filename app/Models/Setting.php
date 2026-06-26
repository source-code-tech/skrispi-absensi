<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    // Non-default: Mencegah Laravel mencari kolom 'updated_at' dan 'created_at' jika tabel Anda tidak memilikinya (meskipun kita menambahkannya di migrasi)
    // public $timestamps = true; 
}