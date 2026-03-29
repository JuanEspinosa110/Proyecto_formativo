<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestorRecargas\GestorRecargaController;

// Rol ID 8 = GESTOR DE RECARGAS
Route::middleware(['auth:web', 'role:8', 'CheckNit'])->prefix('gestor-recargas')->name('gestor-recargas.')->group(function () {
    Route::get('/dashboard', [GestorRecargaController::class, 'dashboard'])->name('dashboard');
    
    // Recargas
    Route::get('/recargar', [GestorRecargaController::class, 'createRecarga'])->name('recargar');
    Route::get('/recargar/consultar', [GestorRecargaController::class, 'consultarTarjeta'])->name('recargar.consultar');
    Route::post('/recargar', [GestorRecargaController::class, 'storeRecarga'])->name('recargar.store');
    
    // Historial
    Route::get('/historial', [GestorRecargaController::class, 'historial'])->name('historial');
    
    // Gestión de usuarios de la propia empresa
    Route::get('/usuarios', [GestorRecargaController::class, 'usuariosIndex'])->name('usuarios.index');
    Route::get('/usuarios/crear', [GestorRecargaController::class, 'usuariosCreate'])->name('usuarios.create');
    Route::post('/usuarios', [GestorRecargaController::class, 'usuariosStore'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [GestorRecargaController::class, 'usuariosEdit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [GestorRecargaController::class, 'usuariosUpdate'])->name('usuarios.update');
    Route::post('/usuarios/{id}/estado', [GestorRecargaController::class, 'usuariosToggleStatus'])->name('usuarios.toggle');
});
