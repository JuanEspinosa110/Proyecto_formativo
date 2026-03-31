<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestorRecargas\GestorRecargaController;
use App\Http\Controllers\GestorRecargas\TitularidadTarjetaController;


// Dashboard solo para ADMIN RECARGAS (rol 10)
Route::middleware(['auth:web', 'role:10', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    Route::get('/dashboard', [GestorRecargaController::class, 'dashboard'])->name('dashboard');
});

// Resto de módulos solo para GESTOR DE RECARGAS (rol 8)
Route::middleware(['auth:web', 'role:8', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    Route::get('/recargar', [GestorRecargaController::class, 'createRecarga'])->name('recargar');
    Route::get('/recargar/consultar', [GestorRecargaController::class, 'consultarTarjeta'])->name('recargar.consultar');
    Route::post('/recargar', [GestorRecargaController::class, 'storeRecarga'])->name('recargar.store');
    Route::get('/historial', [GestorRecargaController::class, 'historial'])->name('historial');
    // Titularidad de tarjeta
    Route::get('/titularidad', [TitularidadTarjetaController::class, 'index'])->name('titularidad');
    Route::post('/titularidad/buscar-usuario', [TitularidadTarjetaController::class, 'buscarUsuario'])->name('titularidad.buscar');
    Route::post('/titularidad/enviar-codigo', [TitularidadTarjetaController::class, 'enviarCodigo'])->name('titularidad.enviar-codigo');
    Route::post('/titularidad/consultar-cooldown', [TitularidadTarjetaController::class, 'consultarCooldown'])->name('titularidad.consultar-cooldown');
    Route::post('/titularidad/cambiar', [TitularidadTarjetaController::class, 'cambiar'])->name('titularidad.cambiar');
    Route::post('/titularidad/resetear-cooldown', [TitularidadTarjetaController::class, 'resetearCooldown'])->name('gestor-recargas.titularidad.resetear-cooldown');
});

// Solo ADMIN de EMPRESA DE RECARGA (id_rol=1, NIT=800222333) puede gestionar usuarios
Route::middleware(['auth:web', 'empresaRecargaAdmin'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    // Gestión de usuarios de la propia empresa
    Route::get('/usuarios', [GestorRecargaController::class, 'usuariosIndex'])->name('usuarios.index');
    Route::get('/usuarios/crear', [GestorRecargaController::class, 'usuariosCreate'])->name('usuarios.create');
    Route::post('/usuarios', [GestorRecargaController::class, 'usuariosStore'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [GestorRecargaController::class, 'usuariosEdit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [GestorRecargaController::class, 'usuariosUpdate'])->name('usuarios.update');
    Route::post('/usuarios/{id}/estado', [GestorRecargaController::class, 'usuariosToggleStatus'])->name('usuarios.toggle');
});
