<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\{
    DashboardController,
    RolController,
    UsuarioController,
    EmpresaController,
    DocumentoController,
    TarjetaController,
    LicenciaController,
    ReporteController,
    AlertaController,
    ConfiguracionController
};

Route::get('/', function () {
    return view('index');
})->name('home');


Route::get('/superadmin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::view('/register', 'auth.register')->name('register');    

Route::middleware('auth:web')->group(function () {

    Route::get('/pasajero/dashboard', fn () => view('pasajeros.index'))
        ->name('pasajero.dashboard');

    Route::get('/empresa/dashboard', fn () => view('empresa.dashboard'))
        ->name('empresa.dashboard');
});

Route::middleware('auth:superadmin')->group(function () {

    Route::get('/superadmin/dashboard', fn () => view('superadmin.dashboard'))
        ->name('superadmin.dashboard');
});


Route::get('/superadmin/dashboard/stats', 
    [DashboardController::class, 'superAdminStats']
)->name('superadmin.dashboard.stats');

Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth:superadmin'])
    ->group(function () {

        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/tarjetas', [TarjetaController::class, 'index'])->name('tarjetas.index');
        Route::get('/licencias', [LicenciaController::class, 'index'])->name('licencias.index');
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    }); 


