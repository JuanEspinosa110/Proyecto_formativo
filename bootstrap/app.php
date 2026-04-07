<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'CheckTarjeta' => \App\Http\Middleware\CheckTarjeta::class,
            'CheckNit' => \App\Http\Middleware\CheckNitAsociado::class,
            'empresaRecargaAdmin' => \App\Http\Middleware\EmpresaRecargaAdminMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
        //
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Forzar public_path a la raíz si estamos en el hosting (donde public/index.php no existe)
if (!file_exists($app->basePath('public/index.php'))) {
    $app->usePublicPath($app->basePath());
}

return $app;
