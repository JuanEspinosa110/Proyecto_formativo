<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConductorController;

/*
|--------------------------------------------------------------------------
| Conductor Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:web', 'role:conductor', 'CheckNit', 'prevent-back-history'])
    ->prefix('conductor')
    ->name('conductor.')
    ->group(function () {
        
        // Dashboard principal y calendario
        Route::get('/dashboard', [ConductorController::class, 'dashboard'])->name('dashboard');

        // Gestión de Fallas Mecánicas
        Route::post('/reportar-falla', [ConductorController::class, 'reportarFalla'])->name('reportarFalla');
        Route::get('/historial-fallas', [ConductorController::class, 'historialFallas'])->name('fallas');

        // Control de Jornada (Turnos)
        Route::post('/turno/iniciar/{id}', [ConductorController::class, 'iniciarTurno'])->name('iniciarTurno');
        Route::post('/turno/finalizar/{id}', [ConductorController::class, 'finalizarTurno'])->name('finalizarTurno');

        // Control de Recorridos (IDA / VUELTA)
        Route::post('/recorrido/iniciar/{id}', [ConductorController::class, 'iniciarRecorrido'])->name('iniciarRecorrido');
        Route::post('/recorrido/finalizar/{id}', [ConductorController::class, 'finalizarRecorrido'])->name('finalizarRecorrido');
        Route::get('/historial-recorridos', [ConductorController::class, 'historialRecorridos'])->name('recorridos');

        // Operación de Pasajeros (Scanner/Venta)
        Route::post('/recorrido/{id}/registrar-pasajero', [ConductorController::class, 'registrarPasajero'])->name('registrarPasajero');
    });
