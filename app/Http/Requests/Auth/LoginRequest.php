<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            // ✅ TAMBAHAN: validasi role yang dipilih dari tab (admin / wali_kelas / orang_tua)
            'role' => ['required', 'string', 'in:super_admin,wali_kelas,orang_tua'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // =====================================================
        // ✅ TAMBAHAN: VALIDASI ROLE SESUAI TAB YANG DIPILIH
        // =====================================================
        // Login (email+password) sudah benar, tapi role yang dipilih
        // di tab (Admin/Wali Kelas/Orang Tua) belum tentu sama dengan
        // role asli user tersebut di database. Maka di sini dicek ulang.
        $user = Auth::user();
        $selectedRole = $this->input('role');

        if ($user->role !== $selectedRole) {
            // Logout paksa karena role tidak sesuai dengan tab yang dipilih
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $this->roleMismatchMessage($selectedRole),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Pesan error spesifik ketika role yang dipilih di tab tidak sesuai
     * dengan role asli akun di database.
     */
    protected function roleMismatchMessage(string $selectedRole): string
    {
        $labels = [
            'super_admin' => 'Admin',
            'wali_kelas'  => 'Wali Kelas',
            'orang_tua'   => 'Orang Tua',
        ];

        $selectedLabel = $labels[$selectedRole] ?? $selectedRole;

        return "Akun ini bukan akun {$selectedLabel}. Silakan pilih tab login yang sesuai dengan peran Anda.";
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}