<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:web'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Módulo de Buses
        Route::get('/buses', [BusController::class, 'index'])->name('buses.index');
        Route::post('/buses', [BusController::class, 'store'])->name('buses.store');
        Route::put('/buses/{bus}', [BusController::class, 'update'])->name('buses.update');
        Route::get('/buses/export', [BusController::class, 'export'])->name('buses.export');


        // Otros módulos
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    });
});
