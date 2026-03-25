<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestorSetp\DashboardController;
use App\Http\Controllers\GestorSetp\RutaController;
use App\Http\Controllers\GestorSetp\EmpresaController;
use App\Http\Controllers\GestorSetp\BusController;
use App\Http\Controllers\GestorSetp\DocumentoController;
use App\Http\Controllers\SuperAdmin\GestorSetpController;

/*
|--------------------------------------------------------------------------
| Rutas del módulo Gestor SETP
|--------------------------------------------------------------------------
| Estas rutas requieren autenticación con el guard 'web' y
| que el usuario tenga id_tipo_usuario = 11 (Gestor Setp).
| El middleware 'gestor.setp' verifica ese rol.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', 'role:gestor_setp'])
    ->prefix('gestor-setp')
    ->name('gestor-setp.')
    ->group(function () {

        // ── Dashboard ───────────────────────────────────────────────
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Rutas ───────────────────────────────────────────────────
        Route::prefix('rutas')->name('rutas.')->group(function () {
            Route::get('/',          [RutaController::class, 'index'])   ->name('index');
            Route::get('/crear',     [RutaController::class, 'create'])  ->name('create');
            Route::post('/',         [RutaController::class, 'store'])   ->name('store');
            Route::get('/{id}/editar', [RutaController::class, 'edit']) ->name('edit');
            Route::put('/{id}',      [RutaController::class, 'update'])  ->name('update');
            Route::patch('/{id}/estado', [RutaController::class, 'toggleEstado'])->name('toggle-estado');
            // Asignación de ruta a empresa
            Route::get('/{id}/asignar',  [RutaController::class, 'formAsignar']) ->name('form-asignar');
            Route::post('/{id}/asignar', [RutaController::class, 'asignar'])     ->name('asignar');
            Route::delete('/{id_asignacion}/desasignar', [RutaController::class, 'desasignar'])->name('desasignar');
        });

        // ── Empresas de transporte ───────────────────────────────────
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/',         [EmpresaController::class, 'index']) ->name('index');
            Route::get('/{nit}',    [EmpresaController::class, 'show'])  ->name('show');
        });

        // ── Buses ────────────────────────────────────────────────────
        Route::prefix('buses')->name('buses.')->group(function () {
            Route::get('/',                         [BusController::class, 'index'])        ->name('index');
            Route::get('/{placa}',                  [BusController::class, 'show'])         ->name('show');
            Route::patch('/{placa}/estado',         [BusController::class, 'cambiarEstado'])->name('cambiar-estado');
        });

        // ── Documentos ───────────────────────────────────────────────
        Route::prefix('documentos')->name('documentos.')->group(function () {
            Route::get('/',                          [DocumentoController::class, 'index'])  ->name('index');
            Route::get('/{id}',                      [DocumentoController::class, 'show'])   ->name('show');
            Route::post('/avisar/{placa}',           [DocumentoController::class, 'enviarAviso'])->name('avisar');
            Route::patch('/inactivar-bus/{placa}',   [DocumentoController::class, 'inactivarBus'])->name('inactivar-bus');
        });
    });

/*
|--------------------------------------------------------------------------
| Rutas SuperAdmin → gestión de Gestores SETP
|--------------------------------------------------------------------------
| Estas rutas están en el contexto del superadmin (guard superadmin) y
| permiten crear / editar / inactivar usuarios con rol Gestor SETP.
| Se agregan al grupo superadmin ya definido en web.php pero se declaran
| aquí para mantener la separación de responsabilidades.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:superadmin'])
    ->prefix('superadmin/gestores-setp')
    ->name('superadmin.gestores-setp.')
    ->group(function () {
        Route::get('/',              [GestorSetpController::class, 'index'])  ->name('index');
        Route::get('/crear',         [GestorSetpController::class, 'create']) ->name('create');
        Route::post('/',             [GestorSetpController::class, 'store'])  ->name('store');
        Route::get('/{doc}/editar',  [GestorSetpController::class, 'edit'])   ->name('edit');
        Route::put('/{doc}',         [GestorSetpController::class, 'update']) ->name('update');
        Route::patch('/{doc}/estado',[GestorSetpController::class, 'toggleEstado'])->name('toggle-estado');
        Route::delete('/{doc}',      [GestorSetpController::class, 'destroy'])->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Rutas SuperAdmin → gestión de Gestores de Recargas
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\SuperAdmin\GestorRecargasController;

Route::middleware(['auth:superadmin'])
    ->prefix('superadmin/gestores-recargas')
    ->name('superadmin.gestores-recargas.')
    ->group(function () {
        Route::get('/',              [GestorRecargasController::class, 'index'])       ->name('index');
        Route::get('/crear',         [GestorRecargasController::class, 'create'])      ->name('create');
        Route::post('/',             [GestorRecargasController::class, 'store'])       ->name('store');
        Route::get('/{doc}/editar',  [GestorRecargasController::class, 'edit'])        ->name('edit');
        Route::put('/{doc}',         [GestorRecargasController::class, 'update'])      ->name('update');
        Route::patch('/{doc}/estado',[GestorRecargasController::class, 'toggleEstado'])->name('toggle-estado');
        Route::delete('/{doc}',      [GestorRecargasController::class, 'destroy'])     ->name('destroy');
    });
