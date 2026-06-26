<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Mengimpor model yang diperlukan
use App\Models\HomeroomTeacher; 
use App\Models\ParentModel; 

class User extends Authenticatable // implements MustVerifyEmail dihapus
{
    use Notifiable, HasFactory;

    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    // Accessor untuk ditampilkan di JSON/View
    protected $appends = [
        'role_label', 
        'role_badge_class',
    ];

    // =======================================================
    // 1. ACCESSORS (Untuk Tampilan Dinamis)
    // =======================================================

    /**
     * Mendapatkan label peran yang mudah dibaca.
     */
    public function getRoleLabelAttribute(): string
    {
        return [
            'super_admin' => 'Super Admin',
            'wali_kelas' => 'Wali Kelas',
            'orang_tua' => 'Orang Tua',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
        ][$this->role] ?? 'Unknown Role';
    }
    
    /**
     * Mendapatkan kelas CSS badge berdasarkan peran.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        if ($this->role === 'wali_kelas') return 'badge-info';
        if ($this->role === 'orang_tua') return 'badge-primary';
        if ($this->role === 'super_admin') return 'badge-danger'; 
        return 'badge-secondary';
    }

    // =======================================================
    // 2. HELPER ROLE METHODS (Pengecekan Peran Cepat)
    // =======================================================
    
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function isOrangTua(): bool
    {
        return $this->role === 'orang_tua';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    // =======================================================
    // 3. RELATIONSHIPS (Diperbarui dengan asumsi user_id)
    // =======================================================
    
    /**
     * Relasi ke data Guru Wali Kelas.
     * Asumsi: foreign key di tabel homeroom_teachers adalah 'user_id'.
     */
    public function homeroomTeacher(): HasOne
    {
        return $this->hasOne(HomeroomTeacher::class, 'user_username', 'username'); 
    }

    /**
     * Relasi ke data Orang Tua.
     * Asumsi: foreign key di tabel parents adalah 'user_id'.
     */
    public function parentRecord(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_username', 'username'); 
    }
}