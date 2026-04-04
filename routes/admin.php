<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\RutaController;
use App\Http\Controllers\JefeMantenimiento\MantenimientoController;
use App\Http\Controllers\JefeMantenimiento\ReporteFallaController;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:web', 'role:1', 'CheckNit'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Módulo de Buses
        Route::get('buses', [BusController::class, 'index'])->name('buses.index');
        Route::post('buses', [BusController::class, 'store'])->name('buses.store');
        Route::put('buses/{bus:placa}', [BusController::class, 'update'])->name('buses.update');
        Route::delete('buses/{bus:placa}', [BusController::class, 'destroy'])->name('buses.destroy');
        Route::get('buses/export', [BusController::class, 'export'])->name('buses.export');
        Route::get('buses/{placa}/historial-documental', [BusController::class, 'historialDocumental'])->name('buses.historialDocumental');
        Route::get('buses/{placa}/gastos', [BusController::class, 'getGastos'])->name('buses.gastos');
        Route::get('buses/{placa}', [BusController::class, 'show'])->name('buses.show');
        Route::get('buses/propietario/{doc_propietario}', [BusController::class, 'getPropietario'])->name('buses.propietario');

        // Módulo de Asignaciones
        Route::get('asignaciones/disponibilidad', [AsignacionController::class, 'getDisponibilidad'])->name('asignaciones.disponibilidad');
        Route::resource('asignaciones', AsignacionController::class)
            ->only(['index', 'create', 'store', 'update', 'destroy']);


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
        Route::post('documentos/{id}/aprobar', [DocumentoController::class, 'aprobar'])->name('documentos.aprobar');
        Route::post('documentos/{id}/rechazar', [DocumentoController::class, 'rechazar'])->name('documentos.rechazar');

        // ─── Módulo de Mantenimiento (Admin) ──────────────────────────────────
        // Bandeja de reportes de fallas
        Route::get('mantenimiento/reportes', [ReporteFallaController::class, 'indexAdmin'])->name('mantenimiento.reportes');
        Route::get('mantenimiento/reportes/{id}/atender', [ReporteFallaController::class, 'attendAdmin'])->name('mantenimiento.reportes.attend');
        Route::get('mantenimiento/api/reportes-pendientes/{placa}', [ReporteFallaController::class, 'getPendingByBus'])->name('mantenimiento.api.reportes-pendientes');
        // Gestión de mantenimientos
        Route::get('mantenimiento', [MantenimientoController::class, 'indexAdmin'])->name('mantenimiento.index');
        Route::get('mantenimiento/create', [MantenimientoController::class, 'create'])->name('mantenimiento.create');
        Route::post('mantenimiento', [MantenimientoController::class, 'store'])->name('mantenimiento.store');
        Route::get('mantenimiento/{id}', [MantenimientoController::class, 'show'])->name('mantenimiento.show');

        // Finalizar mantenimiento (libera el bus)
        Route::post('mantenimiento/{id}/finalizar', [MantenimientoController::class, 'finalizar'])->name('mantenimiento.finalizar');

        // Historial por bus
        Route::get('buses/{placa}/historial', [MantenimientoController::class, 'historialBus'])->name('buses.historial');
    });
});
