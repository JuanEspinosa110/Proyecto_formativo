<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\RutaController;
use App\Http\Controllers\Auxiliar\ReporteController;

Route::prefix('empresa')->name('empresa.')->group(function () {

    // 🟢 Grupo GENERAL (Administrador [1] y Auxiliar [4, 8])
    Route::middleware(['auth:web', 'role:1,4,8'])->group(function () {

            // Dashboard
            Route::get('/', [DashboardController::class , 'index'])->name('dashboard');
            Route::get('/dashboard/stats', [DashboardController::class , 'stats'])->name('dashboard.stats');

            // Módulo de Asignaciones (Restringir Delete en Controlador para Auxiliar)
            Route::resource('asignaciones', AsignacionController::class)
                ->only(['index', 'create', 'store', 'update', 'destroy']);
            // Módulo de Usuarios (Compartido)
            Route::get('/usuarios', [UsuarioController::class , 'index'])->name('usuarios.index');
            Route::post('/usuarios', [UsuarioController::class , 'store'])->name('usuarios.store');
            Route::put('/usuarios/{doc_usuario}', [UsuarioController::class , 'update'])->name('usuarios.update');


            // Módulo de Documentos
            Route::get('documentos/solicitudes', [DocumentoController::class , 'solicitudes'])->name('documentos.solicitudes');
            Route::get('documentos', [DocumentoController::class , 'index'])->name('documentos.index');
            Route::get('documentos/create', [DocumentoController::class , 'create'])->name('documentos.create');
            Route::post('documentos', [DocumentoController::class , 'store'])->name('documentos.store');
            Route::get('documentos/{id}/edit', [DocumentoController::class , 'edit'])->name('documentos.edit');
            Route::put('documentos/{id}', [DocumentoController::class , 'update'])->name('documentos.update');
            Route::delete('documentos/{id}', [DocumentoController::class , 'destroy'])->name('documentos.destroy');
            Route::get('documentos/{id}/download', [DocumentoController::class , 'download'])->name('documentos.download');
            Route::get('documentos/export', [DocumentoController::class , 'export'])->name('documentos.export');
            Route::post('documentos/{id}/aprobar', [DocumentoController::class , 'aprobar'])->name('documentos.aprobar');
            Route::post('documentos/{id}/rechazar', [DocumentoController::class , 'rechazar'])->name('documentos.rechazar');

            // Módulo de Buses (Compartido)
            Route::get('buses', [BusController::class , 'index'])->name('buses.index');
            Route::post('buses', [BusController::class , 'store'])->name('buses.store');
            Route::put('buses/{bus:placa}', [BusController::class , 'update'])->name('buses.update');
            Route::delete('buses/{bus:placa}', [BusController::class , 'destroy'])->name('buses.destroy');
            Route::get('buses/export', [BusController::class , 'export'])->name('buses.export');
            Route::get('buses/{placa}/historial-documental', [BusController::class , 'historialDocumental'])->name('buses.historialDocumental');
            Route::get('buses/{placa}/gastos', [BusController::class , 'getGastos'])->name('buses.gastos');
            Route::get('buses/{placa}', [BusController::class , 'show'])->name('buses.show');
            Route::get('buses/propietario/{doc_propietario}', [BusController::class , 'getPropietario'])->name('buses.propietario');

            // Módulo de Reportes
            Route::get('/reportes', [ReporteController::class , 'index'])->name('reportes.index');
            Route::get('/reportes/export', [ReporteController::class , 'export'])->name('reportes.export');
        }
        );

        // 🔴 Grupo Exclusivo ADMINISTRADOR (Rol 1)
        Route::middleware(['auth:web', 'role:1'])->group(function () {



            // Módulo de Buses (Movido a General arriba)
    
            // Módulo de Rutas
            Route::get('/rutas', [RutaController::class , 'index'])->name('rutas.index');
            Route::post('/rutas', [RutaController::class , 'store'])->name('rutas.store');
            Route::put('/rutas/{ruta}', [RutaController::class , 'update'])->name('rutas.update');
            Route::get('/rutas/export', [RutaController::class , 'export'])->name('rutas.export');
            Route::get('/rutas/barrios/{id_ciudad}', [RutaController::class , 'getBarriosByCiudad'])->name('rutas.barrios');
        }
        );    });

// 🔵 Grupo Exclusivo AUXILIAR (Rol 4)
Route::prefix('auxiliar')->name('auxiliar.')
    ->middleware(['auth:web', 'role:4'])
    ->group(function () {

        // Dashboard
        Route::get('/', [\App\Http\Controllers\Auxiliar\DashboardController::class , 'index'])->name('dashboard');

        // Usuarios (Propietarios, Conductores) - Reusando Admin
        Route::get('/usuarios', [UsuarioController::class , 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class , 'store'])->name('usuarios.store');
        Route::put('/usuarios/{doc_usuario}', [UsuarioController::class , 'update'])->name('usuarios.update');

        // Buses - Manejado en grupo general
    
        // Documentos - Manejado en grupo general
    
        // Asignaciones - Reusando Admin
        Route::get('/asignaciones', [AsignacionController::class , 'index'])->name('asignaciones.index');
        Route::post('/asignaciones', [AsignacionController::class , 'store'])->name('asignaciones.store');
        Route::put('/asignaciones/{id}', [AsignacionController::class , 'update'])->name('asignaciones.update');
        Route::delete('/asignaciones/{id}', [AsignacionController::class , 'destroy'])->name('asignaciones.destroy');

        // Reportes
        Route::get('/reportes', [\App\Http\Controllers\Auxiliar\ReporteController::class , 'index'])->name('reportes.index');
        Route::post('/reportes/export', [\App\Http\Controllers\Auxiliar\ReporteController::class , 'export'])->name('reportes.export');

    });
