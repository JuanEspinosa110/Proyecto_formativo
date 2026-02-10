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
    ConfiguracionController,
    TipoUsuarioController,
    PerfilSeguridadController,
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

    Route::get('/pasajero/dashboard', fn() => view('pasajeros.index'))
        ->name('pasajero.dashboard');

    Route::get('/empresa/dashboard', fn() => view('empresa.dashboard'))
        ->name('empresa.dashboard');
});

Route::middleware('auth:superadmin')->group(function () {

    Route::get('/superadmin/dashboard', fn() => view('superadmin.dashboard'))
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

        // Tipos de Usuario (Roles)
        Route::get('roles', [TipoUsuarioController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [TipoUsuarioController::class, 'create'])->name('roles.create');
        Route::post('roles', [TipoUsuarioController::class, 'store'])->name('roles.store');
        Route::get('roles/{id}/edit', [TipoUsuarioController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{id}', [TipoUsuarioController::class, 'update'])->name('roles.update');
        Route::delete('roles/{id}', [TipoUsuarioController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{id}/permissions', [TipoUsuarioController::class, 'showPermissions'])->name('roles.permissions.show');
        Route::get('roles/{id}/usuarios', [TipoUsuarioController::class, 'users'])->name('roles.users');

        // Perfil y Seguridad
        Route::get('perfil_seguridad', [PerfilSeguridadController::class, 'index'])->name('perfil.index');
        Route::get('perfil_seguridad/editar-informacion', [PerfilSeguridadController::class, 'editarInformacion'])->name('perfil.editar-informacion');
        Route::put('perfil_seguridad/actualizar-informacion', [PerfilSeguridadController::class, 'actualizarInformacion'])->name('perfil.actualizar-informacion');
        Route::get('perfil_seguridad/cambiar-contrasena', [PerfilSeguridadController::class, 'cambiarContrasena'])->name('perfil.cambiar-contrasena');
        Route::put('perfil_seguridad/cambiar-contrasena', [PerfilSeguridadController::class, 'actualizarContrasena'])->name('perfil.actualizar-contrasena');
        Route::post('perfil_seguridad/actualizar-foto', [PerfilSeguridadController::class, 'actualizarFoto'])->name('perfil.actualizar-foto');
        Route::delete('perfil_seguridad/eliminar-foto', [PerfilSeguridadController::class, 'eliminarFoto'])->name('perfil.eliminar-foto');
        Route::get('perfil_seguridad/seguridad', [PerfilSeguridadController::class, 'seguridad'])->name('perfil.seguridad');
        Route::get('perfil_seguridad/exportar-datos', [PerfilSeguridadController::class, 'exportarDatos'])->name('perfil.exportar-datos');

        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/tarjetas', [TarjetaController::class, 'index'])->name('tarjetas.index');
        
        // Licencias
        Route::get('/licencias', [LicenciaController::class, 'index'])->name('licencias.index');
        Route::get('/licencias/crear', [LicenciaController::class, 'create'])->name('licencias.create');
        Route::get('/licencias/configurar-plan', [LicenciaController::class, 'configurarPlan'])->name('licencias.configurar-plan');
        Route::get('/licencias/{id}/editar', [LicenciaController::class, 'edit'])->name('licencias.edit');
        
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    });
