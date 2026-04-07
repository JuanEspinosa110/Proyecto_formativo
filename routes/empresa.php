<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\RutaController;
use App\Http\Controllers\Auxiliar\ReporteController;

Route::prefix('empresa')->name('empresa.')->group(function () {
    
    // Se requiere autenticación, rol adecuado, tener un NIT asociado (CheckNit) y prevenir caché (prevent-back-history)
    Route::middleware(['auth:web', 'role:1,4', 'CheckNit', 'prevent-back-history'])->group(function () {
        
        // Dashboard Premium (Pestañas)
        Route::get('/', [EmpresaController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/stats', [EmpresaController::class, 'stats'])->name('dashboard.stats');
        Route::get('/bus-detalle/{placa}', [EmpresaController::class, 'showBus'])->name('bus.detalle.ajax');

        // Módulo de Asignaciones
        Route::resource('asignaciones', AsignacionController::class)
            ->only(['index', 'store', 'update', 'destroy', 'create']);
        Route::get('asignaciones/disponibilidad', [AsignacionController::class, 'getDisponibilidad'])->name('asignaciones.disponibilidad');
        Route::post('asignaciones/{id}/inactivar', [EmpresaController::class, 'inactivarViaje'])->name('asignaciones.inactivar');

        // Módulo de Reportes
        Route::get('/reportes/descargar', [EmpresaController::class, 'descargarReporte'])->name('reportes.descargar');

        // Módulo de Usuarios (Gestión de Conductores y Auxiliares)
        Route::get('/usuarios/export', [UsuarioController::class, 'export'])->name('usuarios.export');
        Route::get('/usuarios', [EmpresaController::class, 'dashboard'])->name('usuarios.index');
        Route::post('/usuarios', [EmpresaController::class, 'storeUsuario'])->name('usuarios.store');
        Route::put('/usuarios/{doc_usuario}', [EmpresaController::class, 'updateUsuario'])->name('usuarios.update');
        Route::patch('/usuarios/{doc_usuario}/status', [UsuarioController::class, 'updateStatus'])->name('usuarios.updateStatus');

        // Módulo de Documentos
        Route::get('documentos/solicitudes', [DocumentoController::class, 'solicitudes'])->name('documentos.solicitudes');
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

        // Módulo de Buses
        Route::get('buses/export', [BusController::class, 'export'])->name('buses.export');
        Route::get('buses/propietario/{doc_propietario}', [BusController::class, 'getPropietario'])->name('buses.propietario');
        Route::get('buses', [BusController::class, 'index'])->name('buses.index');
        Route::post('buses', [BusController::class, 'store'])->name('buses.store');
        Route::put('buses/{bus:placa}', [BusController::class, 'update'])->name('buses.update');
        Route::delete('buses/{bus:placa}', [BusController::class, 'destroy'])->name('buses.destroy');
        Route::patch('buses/{bus:placa}/status', [BusController::class, 'updateStatus'])->name('buses.updateStatus');
        Route::get('buses/{placa}/historial-documental', [BusController::class, 'historialDocumental'])->name('buses.historialDocumental');
        Route::get('buses/{placa}/gastos', [BusController::class, 'getGastos'])->name('buses.gastos');
        Route::get('buses/{placa}', [BusController::class, 'show'])->name('buses.show');

        // Módulo de Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
    });

    // 🔴 Grupo Exclusivo ADMINISTRADOR (Rol 1)
    Route::middleware(['auth:web', 'role:1', 'CheckNit', 'prevent-back-history'])->group(function () {
        // Módulo de Rutas (Solo Admin)
        Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
        Route::post('/rutas', [RutaController::class, 'store'])->name('rutas.store');
        Route::put('/rutas/{ruta}', [RutaController::class, 'update'])->name('rutas.update');
        Route::get('/rutas/export', [RutaController::class, 'export'])->name('rutas.export');
        Route::get('/rutas/barrios/{id_ciudad}', [RutaController::class, 'getBarriosByCiudad'])->name('rutas.barrios');
    });
});

// 🔵 Rutas adicionales AUXILIAR (Rol 4) - Si necesita un prefijo específico
Route::prefix('auxiliar')->name('auxiliar.')
    ->middleware(['auth:web', 'role:4', 'CheckNit', 'prevent-back-history'])
    ->group(function () {
        // Redirigir o reusar dashboard específico si difiere del general
        Route::get('/', [\App\Http\Controllers\Auxiliar\DashboardController::class, 'index'])->name('dashboard');
    });

