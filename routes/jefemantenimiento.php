<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JefeMantenimiento\MantenimientoController;
use App\Http\Controllers\JefeMantenimiento\ReporteFallaController;

Route::prefix('jefemantenimiento')->name('jefemantenimiento.')->group(function () {
    Route::middleware(['auth:web', 'role:jefe_mantenimiento'])->group(function () {
        Route::get('/', [MantenimientoController::class, 'dashboard'])->name('dashboard');


        // Módulo de Reportes de Fallas
        Route::get('reportes', [ReporteFallaController::class, 'index'])->name('reportes');
        Route::get('reportes/{id}/atender', [ReporteFallaController::class, 'attend'])->name('reportes.attend');
        
        // Módulo de Mantenimiento (Taller)
        Route::get('mantenimiento', [MantenimientoController::class, 'index'])->name('index');
        Route::get('mantenimiento/create', [MantenimientoController::class, 'create'])->name('create');
        Route::post('mantenimiento', [MantenimientoController::class, 'store'])->name('store');
        Route::get('mantenimiento/{id}', [MantenimientoController::class, 'show'])->name('show');
        // Jefe aprueba la salida del bus (lo libera)
        Route::post('mantenimiento/{id}/aprobar-salida', [MantenimientoController::class, 'aprobarSalida'])->name('aprobar-salida');
    });
});
