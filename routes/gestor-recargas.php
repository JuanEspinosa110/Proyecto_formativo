<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestorRecargas\GestorRecargaController;
use App\Http\Controllers\GestorRecargas\TitularidadTarjetaController;

// ----------------------------------------------------------------------
// 1. Módulos COMPARTIDOS para Admin (10) y Gestor (8)
// ----------------------------------------------------------------------
Route::middleware(['auth:web', 'role:gestor_recargas', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    Route::get('/dashboard', [GestorRecargaController::class, 'dashboard'])->name('dashboard');
    Route::get('/historial', [GestorRecargaController::class, 'historial'])->name('historial');
});

// ----------------------------------------------------------------------
// 2. Módulos EXCLUSIVOS para Gestor de Recargas (solo rol 8)
// ----------------------------------------------------------------------
Route::middleware(['auth:web', 'role:8', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    Route::get('/recargar', [GestorRecargaController::class, 'createRecarga'])->name('recargar');
    Route::get('/recargar/consultar', [GestorRecargaController::class, 'consultarTarjeta'])->name('recargar.consultar');
    Route::post('/recargar', [GestorRecargaController::class, 'storeRecarga'])->name('recargar.store');
    
    // Titularidad de tarjeta
    Route::get('/titularidad', [TitularidadTarjetaController::class, 'index'])->name('titularidad');
    Route::post('/titularidad/buscar-usuario', [TitularidadTarjetaController::class, 'buscarUsuario'])->name('titularidad.buscar');
    Route::post('/titularidad/enviar-codigo', [TitularidadTarjetaController::class, 'enviarCodigo'])->name('titularidad.enviar-codigo');
    Route::post('/titularidad/consultar-cooldown', [TitularidadTarjetaController::class, 'consultarCooldown'])->name('titularidad.consultar-cooldown');
    Route::post('/titularidad/cambiar', [TitularidadTarjetaController::class, 'cambiarTarjeta'])->name('titularidad.cambiar');
    Route::post('/titularidad/resetear-cooldown', [TitularidadTarjetaController::class, 'resetearCooldown'])->name('gestor-recargas.titularidad.resetear-cooldown');
});

// ----------------------------------------------------------------------
// 3. Módulos EXCLUSIVOS para Admin de Recargas (solo rol 10)
// ----------------------------------------------------------------------
Route::middleware(['auth:web', 'role:admin_recargas', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    // Gestión de usuarios de la propia empresa
    Route::get('/usuarios', [GestorRecargaController::class, 'usuariosIndex'])->name('usuarios.index');
    Route::get('/usuarios/crear', [GestorRecargaController::class, 'usuariosCreate'])->name('usuarios.create');
    Route::post('/usuarios', [GestorRecargaController::class, 'usuariosStore'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [GestorRecargaController::class, 'usuariosEdit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [GestorRecargaController::class, 'usuariosUpdate'])->name('usuarios.update');
    Route::post('/usuarios/{id}/estado', [GestorRecargaController::class, 'usuariosToggleStatus'])->name('usuarios.toggle');
});
