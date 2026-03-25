<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pasajero\DashboardController;
use App\Http\Controllers\Pasajero\TarjetaController;
use App\Http\Controllers\Pasajero\RutaController;
use App\Http\Controllers\Pasajero\RecargaController;
use App\Http\Controllers\Pasajero\HistorialController;
use App\Http\Controllers\Pasajero\PerfilController;
use App\Http\Controllers\Pasajero\MapaController;

// ── Rutas de onboarding (sin check de tarjeta) ─────────────────
Route::middleware(['auth:web', 'role:pasajero'])
     ->prefix('pasajero')
     ->name('pasajero.')
     ->group(function () {

        // Onboarding de tarjeta
        Route::get ('tarjeta/sin-tarjeta', [TarjetaController::class, 'sinTarjeta'])
             ->name('tarjeta.sin-tarjeta');
        Route::post('tarjeta/registrar',   [TarjetaController::class, 'registrar'])
             ->name('tarjeta.registrar');
        Route::post('tarjeta/comprar',     [TarjetaController::class, 'comprar'])
             ->name('tarjeta.comprar');

        // Dashboard general de bienvenida (accesible para todos los pasajeros)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Mi tarjeta (redirige a saldo si tiene tarjeta, o a sin-tarjeta si no)
        Route::get('tarjeta', [TarjetaController::class, 'index'])->name('tarjeta.index');

        // Rutas disponibles (accesible sin tarjeta)
        Route::get('rutas', [RutaController::class, 'index'])->name('rutas.index');

        // Puntos de recarga (accesible sin tarjeta)
        Route::get('recargas', [RecargaController::class, 'index'])->name('recargas.index');

        // Mapa de paradas (accesible sin tarjeta)
        Route::get('mapa', [MapaController::class, 'index'])->name('mapa');

});

// ── Rutas con check de tarjeta ─────────────────────────────────
Route::middleware(['auth:web', 'role:pasajero', 'CheckTarjeta'])
     ->prefix('pasajero')
     ->name('pasajero.')
     ->group(function () {

        // Saldo / Mi tarjeta (solo para usuarios con tarjeta)
        Route::get('saldo', [TarjetaController::class, 'saldo'])->name('saldo');

        // Cambio de tarjeta (pérdida/robo/deterioro)
        Route::get('tarjeta/cambiar', [TarjetaController::class, 'cambiar'])->name('tarjeta.cambiar');
        Route::post('tarjeta/cambiar/iniciar', [TarjetaController::class, 'iniciarCambio'])->name('tarjeta.iniciar-cambio');
        Route::get('tarjeta/verificar-cambio', [TarjetaController::class, 'verificarCambioForm'])->name('tarjeta.verificar-cambio');
        Route::post('tarjeta/confirmar-cambio', [TarjetaController::class, 'confirmarCambio'])->name('tarjeta.confirmar-cambio');

        // Historial (recargas + viajes) — solo con tarjeta
        Route::get('historial',        [HistorialController::class, 'index'])   ->name('historial.index');
        Route::get('historial/viajes', [HistorialController::class, 'viajes'])  ->name('historial.viajes');
        Route::get('historial/recargas',[HistorialController::class, 'recargas'])->name('historial.recargas');

        // Perfil
        Route::get ('perfil',        [PerfilController::class, 'edit'])  ->name('perfil.edit');
        Route::put ('perfil',        [PerfilController::class, 'update'])->name('perfil.update');
        Route::post('perfil/foto',   [PerfilController::class, 'foto'])  ->name('perfil.foto');
        Route::put ('perfil/password',[PerfilController::class, 'password'])->name('perfil.password');

        // Recarga con Stripe
        Route::get('tarjeta/recargar', [TarjetaController::class, 'recargar'])->name('tarjeta.recargar');
        Route::post('tarjeta/recargar', [TarjetaController::class, 'procesarRecarga'])->name('tarjeta.procesarRecarga');

});
