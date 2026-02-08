<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilSeguridadController extends Controller
{
    /**
     * Mostrar página principal de perfil
     */
    public function index()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login')
                ->with('error', 'Sesión expirada');
        }

        // Obtener actividad reciente
        $actividadReciente = $this->getActividadReciente($superAdmin->doc_super_admin, 10);

        // Estadísticas de seguridad
        $estadisticas = $this->getEstadisticasSeguridad($superAdmin->doc_super_admin);

        return view('superadmin.perfil_seguridad.index', compact('superAdmin', 'actividadReciente', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición de información personal
     */
    public function editarInformacion()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        return view('superadmin.perfil_seguridad.editar_informacion', compact('superAdmin'));
    }

    /**
     * Actualizar información personal
     */
    public function actualizarInformacion(Request $request)
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:150|unique:super_administrador,correo,' . $superAdmin->doc_super_admin . ',doc_super_admin',
            'telefono' => 'nullable|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'correo.required' => 'El correo es obligatorio',
            'correo.email' => 'El correo debe ser válido',
            'correo.unique' => 'Este correo ya está registrado',
        ]);

        try {
            DB::table('super_administrador')
                ->where('doc_super_admin', $superAdmin->doc_super_admin)
                ->update([
                    'nombre' => $validated['nombre'],
                    'correo' => $validated['correo'],
                    'telefono' => $validated['telefono'],
                ]);

            // Registrar actividad
            $this->registrarActividad(
                $superAdmin->doc_super_admin,
                'Actualización de información personal',
                'Perfil y Seguridad'
            );

            return redirect()
                ->route('superadmin.perfil.index')
                ->with('success', 'Información actualizada correctamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la información: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function cambiarContrasena()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        return view('superadmin.perfil_seguridad.cambiar_contrasena', compact('superAdmin'));
    }

    /**
     * Actualizar contraseña
     */
    public function actualizarContrasena(Request $request)
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        $validated = $request->validate([
            'password_actual' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ], [
            'password_actual.required' => 'La contraseña actual es obligatoria',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        // Verificar contraseña actual
        if (!Hash::check($validated['password_actual'], $superAdmin->password)) {
            return back()
                ->withErrors(['password_actual' => 'La contraseña actual es incorrecta'])
                ->withInput();
        }

        try {
            DB::table('super_administrador')
                ->where('doc_super_admin', $superAdmin->doc_super_admin)
                ->update([
                    'password' => Hash::make($validated['password']),
                ]);

            // Registrar actividad
            $this->registrarActividad(
                $superAdmin->doc_super_admin,
                'Cambio de contraseña',
                'Seguridad'
            );

            return redirect()
                ->route('superadmin.perfil.index')
                ->with('success', 'Contraseña actualizada correctamente');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al actualizar la contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar foto de perfil
     */
    public function actualizarFoto(Request $request)
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $validated = $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'foto.required' => 'Debe seleccionar una imagen',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'Solo se permiten imágenes JPG, JPEG o PNG',
            'foto.max' => 'La imagen no debe superar los 2MB',
        ]);

        try {
            // Eliminar foto anterior si existe
            if ($superAdmin->foto_perfil && Storage::disk('public')->exists($superAdmin->foto_perfil)) {
                Storage::disk('public')->delete($superAdmin->foto_perfil);
            }

            // Guardar nueva foto
            $path = $request->file('foto')->store('perfiles/superadmin', 'public');

            DB::table('super_administrador')
                ->where('doc_super_admin', $superAdmin->doc_super_admin)
                ->update([
                    'foto_perfil' => $path,
                ]);

            // Registrar actividad
            $this->registrarActividad(
                $superAdmin->doc_super_admin,
                'Actualización de foto de perfil',
                'Perfil y Seguridad'
            );

            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada correctamente',
                'foto_url' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar foto de perfil
     */
    public function eliminarFoto()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            // Eliminar foto si existe
            if ($superAdmin->foto_perfil && Storage::disk('public')->exists($superAdmin->foto_perfil)) {
                Storage::disk('public')->delete($superAdmin->foto_perfil);
            }

            DB::table('super_administrador')
                ->where('doc_super_admin', $superAdmin->doc_super_admin)
                ->update([
                    'foto_perfil' => null,
                ]);

            // Registrar actividad
            $this->registrarActividad(
                $superAdmin->doc_super_admin,
                'Eliminación de foto de perfil',
                'Perfil y Seguridad'
            );

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar actividad reciente
     */
    public function actividadReciente()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        $actividades = $this->getActividadReciente($superAdmin->doc_super_admin, 50);

        return view('superadmin.perfil_seguridad.actividad_reciente', compact('superAdmin', 'actividades'));
    }

    /**
     * Mostrar actividad (alias de actividadReciente)
     */
    public function actividad()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        // Obtener módulos únicos para el filtro
        $modulos = DB::table('actividad_log')
            ->where('doc_usuario', $superAdmin->doc_super_admin)
            ->whereNull('tipo_usuario')
            ->distinct()
            ->pluck('modulo')
            ->sort()
            ->values();

        // Obtener actividades con filtros opcionales
        $query = DB::table('actividad_log')
            ->where('doc_usuario', $superAdmin->doc_super_admin)
            ->whereNull('tipo_usuario');

        if (request('modulo')) {
            $query->where('modulo', request('modulo'));
        }

        $actividades = $query->orderBy('fecha_registro', 'desc')->paginate(15);

        return view('superadmin.perfil_seguridad.actividad', compact('superAdmin', 'actividades', 'modulos'));
    }

    /**
     * Mostrar opciones de seguridad
     */
    public function seguridad()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        $estadisticas = $this->getEstadisticasSeguridad($superAdmin->doc_super_admin);
        $ultimoAcceso = $this->getUltimoAcceso($superAdmin->doc_super_admin);

        return view('superadmin.perfil_seguridad.seguridad', compact('superAdmin', 'estadisticas', 'ultimoAcceso'));
    }

    /**
     * Exportar datos personales del usuario
     */
    public function exportarDatos()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        // Obtener todas las actividades del usuario
        $actividades = DB::table('actividad_log')
            ->where('doc_usuario', $superAdmin->doc_super_admin)
            ->whereNull('tipo_usuario')
            ->orderBy('fecha_registro', 'desc')
            ->get();

        // Preparar datos para exportar
        $datos = [
            'usuario' => $superAdmin,
            'actividades' => $actividades,
            'fecha_exportacion' => now()->format('Y-m-d H:i:s'),
        ];

        // Generar JSON
        $json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Descargar archivo
        return response($json, 200)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename=datos-personales-' . $superAdmin->doc_super_admin . '.json');
    }

    /**
     * Obtener super administrador autenticado
     */
    private function getSuperAdmin()
    {
        $docSuperAdmin = session('login_superadmin_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        
        if (!$docSuperAdmin) {
            return null;
        }

        return DB::table('super_administrador')
            ->where('doc_super_admin', $docSuperAdmin)
            ->first();
    }

    /**
     * Registrar actividad en el log
     */
    private function registrarActividad($docUsuario, $accion, $modulo)
    {
        DB::table('actividad_log')->insert([
            'doc_usuario' => $docUsuario,
            'tipo_usuario' => null, // Super Admin usa NULL
            'accion' => $accion,
            'modulo' => $modulo,
            'ip_address' => request()->ip(),
            'fecha_registro' => now(),
        ]);
    }

    /**
     * Obtener actividad reciente
     */
    private function getActividadReciente($docUsuario, $limit = 10)
    {
        return DB::table('actividad_log')
            ->where('doc_usuario', $docUsuario)
            ->whereNull('tipo_usuario')
            ->orderBy('fecha_registro', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de seguridad
     */
    private function getEstadisticasSeguridad($docUsuario)
    {
        $hoy = now()->startOfDay();
        $semanaAnterior = now()->subWeek();
        $mesAnterior = now()->subMonth();

        return [
            'accesos_hoy' => DB::table('actividad_log')
                ->where('doc_usuario', $docUsuario)
                ->whereNull('tipo_usuario')
                ->where('accion', 'LIKE', '%Inicio de sesión%')
                ->where('fecha_registro', '>=', $hoy)
                ->count(),
            
            'actividades_semana' => DB::table('actividad_log')
                ->where('doc_usuario', $docUsuario)
                ->whereNull('tipo_usuario')
                ->where('fecha_registro', '>=', $semanaAnterior)
                ->count(),
            
            'actividades_mes' => DB::table('actividad_log')
                ->where('doc_usuario', $docUsuario)
                ->whereNull('tipo_usuario')
                ->where('fecha_registro', '>=', $mesAnterior)
                ->count(),
            
            'total_actividades' => DB::table('actividad_log')
                ->where('doc_usuario', $docUsuario)
                ->whereNull('tipo_usuario')
                ->count(),
        ];
    }

    /**
     * Obtener último acceso
     */
    private function getUltimoAcceso($docUsuario)
    {
        return DB::table('actividad_log')
            ->where('doc_usuario', $docUsuario)
            ->whereNull('tipo_usuario')
            ->where('accion', 'LIKE', '%Inicio de sesión%')
            ->orderBy('fecha_registro', 'desc')
            ->skip(1) // Saltar el acceso actual
            ->first();
    }
}
