<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware; // <-- Class sudah diimport

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Perbaikan: Menggunakan nama class yang diimport.
        $middleware->alias([
            'role' => RoleMiddleware::class, 
        ]);
        
        // Catatan: Jika ada middleware lain yang perlu didaftarkan (e.g., global web group), 
        // Anda bisa menambahkannya di sini.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();