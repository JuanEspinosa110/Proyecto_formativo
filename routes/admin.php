<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;

use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\RutaController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:web'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Módulo de Buses
        Route::get('buses', [BusController::class, 'index'])->name('buses.index');
        Route::post('buses', [BusController::class, 'store'])->name('buses.store');
        Route::put('buses/{bus:placa}', [BusController::class, 'update'])->name('buses.update');
        Route::delete('buses/{bus:placa}', [BusController::class, 'destroy'])->name('buses.destroy');
        Route::get('buses/export', [BusController::class, 'export'])->name('buses.export');

        // Módulo de Asignaciones
        Route::resource('asignaciones', AsignacionController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Otros módulos
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

        // Módulo de Rutas (Admin)
        Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
        Route::post('/rutas', [RutaController::class, 'store'])->name('rutas.store');
        Route::put('/rutas/{ruta}', [RutaController::class, 'update'])->name('rutas.update');
        Route::get('/rutas/export', [RutaController::class, 'export'])->name('rutas.export');
        Route::get('/rutas/barrios/{id_ciudad}', [RutaController::class, 'getBarriosByCiudad'])->name('rutas.barrios');
    });
});
