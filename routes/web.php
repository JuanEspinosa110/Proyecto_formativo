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



Route::get(
    '/superadmin/dashboard/stats',
    [DashboardController::class, 'superAdminStats']
)->name('superadmin.dashboard.stats');


Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth:superadmin'])
    ->group(function () {

        Route::get('/roles', [RolController::class, 'index'])
            ->name('roles.index');

 
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->name('usuarios.index');

            Route::get('/usuarios/{user}', [UsuarioController::class, 'show'])
            ->name('usuarios.show');

        Route::post('/usuarios/{user}/password', [UsuarioController::class, 'updatePassword'])
            ->name('usuarios.password');

        Route::get('/usuarios/{user}/documentos', [UsuarioController::class, 'documentos'])
            ->name('usuarios.documentos');

        Route::get('/usuarios/{user}/afiliaciones', [UsuarioController::class, 'afiliaciones'])
            ->name('usuarios.afiliaciones');

        Route::get('/usuarios/{user}/buses', [UsuarioController::class, 'buses'])
            ->name('usuarios.buses');

        Route::get('/usuarios/{user}/asignaciones', [UsuarioController::class, 'asignaciones'])
            ->name('usuarios.asignaciones');

        Route::middleware('auth:web')->group(function () {

        Route::get('/usuario/dashboard', function () {
        return view('usuarios.dashboard');
             })->name('usuarios.dashboard');

            
     });

     // =================== EMPRESAS ===================

        Route::get('/empresas', [EmpresaController::class, 'index'])
            ->name('empresas.index');

        Route::get('/empresas/create', [EmpresaController::class, 'create'])
            ->name('empresas.create');

        Route::post('/empresas', [EmpresaController::class, 'store'])
            ->name('empresas.store');

        Route::get('/empresas/{id}', [EmpresaController::class, 'show'])
            ->name('empresas.show');

        Route::get('/empresas/{id}/edit', [EmpresaController::class, 'edit'])
            ->name('empresas.edit');

        Route::put('/empresas/{id}', [EmpresaController::class, 'update'])
            ->name('empresas.update');

        Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy'])
            ->name('empresas.destroy');

        // Toggle estado
        Route::put('/empresas/{id}/toggle', [EmpresaController::class, 'toggleEstado'])
            ->name('empresas.toggle');

        // Auxiliares
        Route::get('/empresas/{id}/auxiliares', [EmpresaController::class, 'auxiliares'])
            ->name('empresas.auxiliares');

        // Buses
        Route::get('/empresas/{id}/buses', [EmpresaController::class, 'buses'])
            ->name('empresas.buses');

        // Documentos
        Route::get('/empresas/{id}/documentos', [EmpresaController::class, 'documentos'])
            ->name('empresas.documentos');

        Route::post('/empresas/{id}/documentos', [EmpresaController::class, 'uploadDocumento'])
            ->name('empresas.documentos.upload');

       


        Route::get('/documentos', [DocumentoController::class, 'index'])
            ->name('documentos.index');

        Route::get('/documentos', [DocumentoController::class, 'index'])
            ->name('documentos.index');

        Route::get('/documentos/{id}/edit', [DocumentoController::class, 'edit'])
            ->name('documentos.edit');

        Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])
            ->name('documentos.download');

        Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])
            ->name('documentos.destroy');

        Route::get('/tarjetas', [TarjetaController::class, 'index'])
            ->name('tarjetas.index');

        Route::get('/licencias', [LicenciaController::class, 'index'])
            ->name('licencias.index');

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes.index');

        Route::get('/alertas', [AlertaController::class, 'index'])
            ->name('alertas.index');

        Route::get('/alertas/create', [AlertaController::class, 'create'])
            ->name('alertas.create');

        Route::get('/configuracion', [ConfiguracionController::class, 'index'])
            ->name('configuracion.index');

    });

    

    

