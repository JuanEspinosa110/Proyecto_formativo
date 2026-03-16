<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\Reportes\ReporteFinancieroController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\RecuperarPasswordController;
use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\PropietarioController;

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
    PerfilSeguridadController,
    PlanLicenciaController,
    RutaController,
    CiudadController,
};

require base_path('routes/superadmin.php');
// Rutas Administrativas (Panel Empresas)
require base_path('routes/admin.php');

use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [RegistroController::class, 'store'])
    ->name('register.store');

Route::get('/recuperar', [RecuperarPasswordController::class, 'index'])->name('recuperar'); 

Route::post(
    '/recuperar/enviar-codigo',
    [RecuperarPasswordController::class, 'enviarCodigo']
)->name('password.send.code');

Route::get('/codigo', function () {
    return view('auth.codigo');
})->name('password.codigo.form');

Route::post('/codigo/verificar', [RecuperarPasswordController::class, 'verificarCodigo'])
    ->name('password.verify.code');

Route::post('/codigo/reenviar', [RecuperarPasswordController::class, 'reenviarCodigo'])
    ->name('password.resend.code');

// Mostrar vista nueva contraseña
Route::get(
    '/nueva-password',
    [RecuperarPasswordController::class, 'mostrarNuevaPassword']
)->name('password.nueva.form');



// Procesar actualización
Route::post(
    '/nueva-password',
    [RecuperarPasswordController::class, 'actualizarPassword']
)->name('password.update');



Route::middleware('auth:web')->group(function () {

    Route::get('/pasajero/dashboard', fn() => view('pasajeros.index'))
        ->name('pasajero.dashboard')->middleware('role:2');

    Route::get('/empresa/dashboard', fn() => view('empresa.dashboard'))
        ->name('empresa.dashboard')->middleware('role:3');

});

// ==========================================
// RAMP: PANEL PROPIETARIO (Independiente)
// ==========================================
Route::middleware(['auth:web', 'role:6,9'])->prefix('propietario')->name('propietario.')->group(function () {
    Route::get('/dashboard', [PropietarioController::class, 'dashboard'])->name('dashboard');
    Route::post('/documento', [PropietarioController::class, 'subirDocumento'])->name('subirDocumento');
    Route::post('/gasto', [PropietarioController::class, 'registrarGasto'])->name('registrarGasto');
    Route::put('/documento/{id}', [PropietarioController::class, 'actualizarDocumento'])->name('actualizarDocumento');
    Route::get('/bus/{placa}/detalles', [PropietarioController::class, 'verVehiculo'])->name('verVehiculo');
    Route::get('/bus/{placa}/historial-documental', [PropietarioController::class, 'historialDocumental'])->name('historialDocumental');
    Route::get('/asignacion/{id}/detalle', [PropietarioController::class, 'getDetalleAsignacion'])->name('detalleAsignacion');
});



Route::middleware('auth:superadmin')->group(function () {

    Route::get('/superadmin/dashboard', fn() => view('superadmin.dashboard'))
        ->name('superadmin.dashboard');
});



Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth:superadmin'])
    ->group(function () 
    {


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
        Route::get('licencias', [LicenciaController::class, 'index'])->name('licencias.index');
        route::get('lincencias/export', [LicenciaController::class, 'export'])->name('licencias.export');
        route::get('lincencias/exportExcel', [LicenciaController::class, 'exportExcel'])->name('licencias.exportExcel');
        Route::get('licencias/{id}/detalles', [LicenciaController::class, 'getDetalles'])->name('detalles');
        // PASO 1 creación de licencia
        Route::get('licencias/crear', [LicenciaController::class, 'create'])->name('licencias.create');
        Route::post('licencias/paso1', [LicenciaController::class, 'guardarPaso1'])->name('licencias.guardar-paso1');
        // PASO 2 creación de licencia
        Route::get('licencias/crear/paso2', [LicenciaController::class, 'crearPaso2'])->name('licencias.crear-paso2');
        Route::post('licencias', [LicenciaController::class, 'store'])->name('licencias.store');

        Route::get('licencias/{id}/editar', [LicenciaController::class, 'edit'])->name('licencias.edit');
        Route::put('licencias/{id}', [LicenciaController::class, 'update'])->name('licencias.update');
        Route::get('licencias/{id}/gestionar-estado', [LicenciaController::class, 'gestionarEstado'])->name('licencias.gestionar-estado');
        Route::patch('/licencias/{id}/actualizar-estado', [LicenciaController::class, 'actualizarEstado'])->name('superadmin.licencias.actualizar-estado');
        Route::patch('licencias/{id}/estado', [LicenciaController::class, 'actualizarEstado'])->name('licencias.actualizar-estado');
        Route::get('licencias/{id}/renovar', [LicenciaController::class, 'renovar'])->name('licencias.renovar');
        Route::put('licencias/{id}/renovar', [LicenciaController::class, 'procesarRenovacion'])->name('licencias.procesar-renovacion');
        Route::get('licencias/historial', [LicenciaController::class, 'historial'])->name('licencias.historial');
        Route::get('licencias/verificar-nit/{nit}', [LicenciaController::class, 'verificarNit']);
        Route::get('licencias/plan/{id_plan}', [LicenciaController::class, 'getPlanData']);
        Route::get('licencias/ciudades/{id_departamento}', [LicenciaController::class, 'getCiudades']);

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/licencias', [LicenciaController::class, 'index'])->name('licencias.index');
        Route::get('/licencias/crear', [LicenciaController::class, 'create'])->name('licencias.create');
        Route::get('/licencias/configurar-plan', [LicenciaController::class, 'configurarPlan'])->name('licencias.configurar-plan');
        Route::get('/licencias/{id}/editar', [LicenciaController::class, 'edit'])->name('licencias.edit');



        // Ruta para obtener ciudades por departamento (AJAX)
        Route::get('/empresas/ciudades/{id_departamento}', [EmpresaController::class, 'getCiudadesByDepartamento'])
        ->name('superadmin.empresas.ciudades');
    
        // Rutas CRUD de Empresas
        Route::resource('empresas', EmpresaController::class);
        Route::get('empresas/export/csv', [EmpresaController::class, 'exportCsv'])->name('empresas.export.csv');
        Route::get('empresas/export/excel', [EmpresaController::class, 'exportExcel'])->name('empresas.export.excel');

        // PLANES DE LICENCIA
        Route::get('planes', [PlanLicenciaController::class, 'index'])->name('planes.index');
        Route::get('planes/crear', [PlanLicenciaController::class, 'create'])->name('planes.create');
        Route::post('planes', [PlanLicenciaController::class, 'store'])->name('planes.store');
        Route::get('planes/{id}/editar', [PlanLicenciaController::class, 'edit'])->name('planes.edit');
        Route::put('planes/{id}', [PlanLicenciaController::class, 'update'])->name('planes.update');
        Route::delete('planes/{id}', [PlanLicenciaController::class, 'destroy'])->name('planes.destroy');
        Route::post('planes/{id}/toggle-estado', [PlanLicenciaController::class, 'toggleEstado']);


        Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');




        //Reportes 
        Route::get('reportes', [ReporteController::class, 'index'])
            ->name('reportes.index');

        Route::get('/reportes/pdf', [ReporteController::class, 'exportPdf'])
            ->name('reportes.pdf');

        // Tarjetas
        Route::get('/tarjetas/{tarjeta}', [TarjetaController::class, 'show'])
            ->name('tarjetas.show');

        Route::put('/tarjetas/{tarjeta}', [TarjetaController::class, 'update'])
            ->name('tarjetas.update');

        // Estadísticas de SuperAdmin
        Route::get('/dashboard/stats', [DashboardController::class, 'superAdminStats'])
            ->name('dashboard.stats');

        // Ruta de inactivar usuarios (SuperAdmin)
        Route::patch('usuarios/{doc}/inactivar', [UsuarioController::class, 'inactivar'])
            ->name('superadmin.usuarios.inactivar');


    });

