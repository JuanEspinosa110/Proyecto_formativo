<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\Configuracion\TipoDocumentoController;
use App\Http\Controllers\SuperAdmin\Configuracion\TipoAsignacionController;
use App\Http\Controllers\SuperAdmin\Configuracion\EstadoController;
use App\Http\Controllers\SuperAdmin\Configuracion\TipoEmpresaController;
use App\Http\Controllers\SuperAdmin\Configuracion\TipoUsuarioController;
use App\Http\Controllers\SuperAdmin\Configuracion\CiudadController;
use App\Http\Controllers\SuperAdmin\Configuracion\TipoMantenimientoController;
use App\Http\Controllers\SuperAdmin\BarrioController;

Route::middleware(['auth:superadmin', 'prevent-back-history'])->prefix('superadmin/configuracion')->name('superadmin.configuracion.')->group(function () {

    // Tipo Documento
    Route::get('tipo-documento', [TipoDocumentoController::class, 'index'])->name('tipo-documento.index');
    Route::post('tipo-documento', [TipoDocumentoController::class, 'store'])->name('tipo-documento.store');
    Route::put('tipo-documento/{id}', [TipoDocumentoController::class, 'update'])->name('tipo-documento.update');
    Route::get('tipo-documento/export', [TipoDocumentoController::class, 'exportExcel'])->name('tipo-documento.export');

    // Tipo Asignacion
    Route::get('tipo-asignacion', [TipoAsignacionController::class, 'index'])->name('tipo-asignacion.index');
    Route::post('tipo-asignacion', [TipoAsignacionController::class, 'store'])->name('tipo-asignacion.store');
    Route::put('tipo-asignacion/{id}', [TipoAsignacionController::class, 'update'])->name('tipo-asignacion.update');
    Route::get('tipo-asignacion/export', [TipoAsignacionController::class, 'exportExcel'])->name('tipo-asignacion.export');

    // Estados
    Route::get('estados', [EstadoController::class, 'index'])->name('estados.index');
    Route::post('estados', [EstadoController::class, 'store'])->name('estados.store');
    Route::put('estados/{id}', [EstadoController::class, 'update'])->name('estados.update');
    Route::get('estados/export', [EstadoController::class, 'exportExcel'])->name('estados.export');

    // Tipo Empresa
    Route::get('tipo-empresa', [TipoEmpresaController::class, 'index'])->name('tipo-empresa.index');
    Route::post('tipo-empresa', [TipoEmpresaController::class, 'store'])->name('tipo-empresa.store');
    Route::put('tipo-empresa/{id}', [TipoEmpresaController::class, 'update'])->name('tipo-empresa.update');
    Route::get('tipo-empresa/export', [TipoEmpresaController::class, 'exportExcel'])->name('tipo-empresa.export');

    // Tipo Usuario
    Route::get('tipo-usuario', [TipoUsuarioController::class, 'index'])->name('tipo-usuario.index');
    Route::post('tipo-usuario', [TipoUsuarioController::class, 'store'])->name('tipo-usuario.store');
    Route::put('tipo-usuario/{id}', [TipoUsuarioController::class, 'update'])->name('tipo-usuario.update');
    Route::get('tipo-usuario/export', [TipoUsuarioController::class, 'exportExcel'])->name('tipo-usuario.export');

    // Ciudades
    Route::get('ciudades', [CiudadController::class, 'index'])->name('ciudades.index');
    Route::post('ciudades', [CiudadController::class, 'store'])->name('ciudades.store');
    Route::put('ciudades/{id}', [CiudadController::class, 'update'])->name('ciudades.update');
    Route::post('departamentos', [CiudadController::class, 'storeDepartamento'])->name('ciudades.storeDepartamento');
    Route::get('ciudades/export', [CiudadController::class, 'exportExcel'])->name('ciudades.export');

    // Tipo Mantenimiento
    Route::get('tipo-mantenimiento', [TipoMantenimientoController::class, 'index'])->name('tipo-mantenimiento.index');
    Route::post('tipo-mantenimiento', [TipoMantenimientoController::class, 'store'])->name('tipo-mantenimiento.store');
    Route::put('tipo-mantenimiento/{id}', [TipoMantenimientoController::class, 'update'])->name('tipo-mantenimiento.update');
    Route::get('tipo-mantenimiento/export', [TipoMantenimientoController::class, 'exportExcel'])->name('tipo-mantenimiento.export');

    // Barrios
    Route::get('barrios', [BarrioController::class, 'index'])->name('barrios.index');
    Route::post('barrios', [BarrioController::class, 'store'])->name('barrios.store');
    Route::put('barrios/{id}', [BarrioController::class, 'update'])->name('barrios.update');
    Route::get('barrios/export', [BarrioController::class, 'export'])->name('barrios.export');
});
