<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Pastikan Model User di-import

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // =======================================================
        // 💡 REGISTRASI GATES (Otorisasi Berbasis Peran)
        // =======================================================
        
        // Gate untuk Super Admin
        Gate::define('access-super-admin', function (User $user) {
            return $user->role === 'super_admin';
        });

        // Gate untuk Wali Kelas
        Gate::define('access-wali-kelas', function (User $user) {
            return $user->role === 'wali_kelas';
        });

        // Gate untuk Orang Tua
        Gate::define('access-orang-tua', function (User $user) {
            return $user->role === 'orang_tua';
        });
        
        // Gate untuk Akses Admin (Super Admin + Peran Lain yang mungkin ada)
        Gate::define('manage-admin', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin']); 
        });

        // Gate untuk Akses Wali Kelas atau lebih tinggi
        Gate::define('manage-homeroom-teacher-level', function (User $user) {
            return in_array($user->role, ['super_admin', 'wali_kelas']);
        });
    }
}