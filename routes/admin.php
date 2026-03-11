<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\RutaController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:web', 'role:1'])->group(function () {
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
        Route::put('/usuarios/{doc_usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');

        // Módulo de Rutas (Admin)
        Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
        Route::post('/rutas', [RutaController::class, 'store'])->name('rutas.store');
        Route::put('/rutas/{ruta}', [RutaController::class, 'update'])->name('rutas.update');
        Route::get('/rutas/export', [RutaController::class, 'export'])->name('rutas.export');
        Route::get('/rutas/barrios/{id_ciudad}', [RutaController::class, 'getBarriosByCiudad'])->name('rutas.barrios');

        // Módulo de Documentos
        Route::get('documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
        Route::post('documentos', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::get('documentos/{id}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
        Route::put('documentos/{id}', [DocumentoController::class, 'update'])->name('documentos.update');
        Route::delete('documentos/{id}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
        Route::get('documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');
        Route::get('documentos/export', [DocumentoController::class, 'export'])->name('documentos.export');
    });
});
