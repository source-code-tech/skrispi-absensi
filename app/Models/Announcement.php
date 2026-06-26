<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'target_type', // 'all', 'class'
        'target_class_code',
        'is_active',
    ];

    /**
     * Relasi ke Kelas (jika target_type = class)
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'target_class_code', 'code');
    }

    /**
     * Scope untuk mengambil pengumuman aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
