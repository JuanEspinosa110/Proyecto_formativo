<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorTiempo\DashboardController;
use App\Http\Controllers\ControladorTiempo\DespachoController;
use App\Http\Controllers\ControladorTiempo\MonitoreoController;
use App\Http\Controllers\ControladorTiempo\PlanillaController;

Route::prefix('controlador-tiempo')
    ->name('controlador-tiempo.')
    ->middleware(['auth:web', 'role:7', 'CheckNit'])
    ->group(function () {

        // Dashboard principal
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Módulo de Despacho
        Route::get('despacho', [DespachoController::class, 'index'])->name('despacho.index');

        // Módulo de Monitoreo en Tiempo Real
        Route::get('monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');

        // Módulo de Planillas
        Route::get('planillas', [PlanillaController::class, 'index'])->name('planillas.index');
        Route::post('planillas', [PlanillaController::class, 'store'])->name('planillas.store');
        Route::post('planillas/{id}/novedad', [PlanillaController::class, 'registrarNovedad'])->name('planillas.novedad');

        // Verificación QR
        Route::prefix('verificacion')->name('verificacion.')->group(function() {
            Route::get('/scanner', [App\Http\Controllers\ControladorTiempo\VerificacionController::class, 'scanner'])->name('scanner');
            Route::get('/{id}', [App\Http\Controllers\ControladorTiempo\VerificacionController::class, 'show'])->name('show');
            Route::post('/{id}/checkpoint', [App\Http\Controllers\ControladorTiempo\VerificacionController::class, 'registrarCheckpoint'])->name('checkpoint');
            Route::post('/{id}/incidencia', [App\Http\Controllers\ControladorTiempo\VerificacionController::class, 'registrarIncidencia'])->name('incidencia');
        });
    });
