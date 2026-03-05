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

        return view('superadmin.perfil_seguridad.index', compact('superAdmin'));
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
            'telefono' => 'nullable|string|max:20', 'regex:/^[0-9]/',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'correo.required' => 'El correo es obligatorio',
            'correo.email' => 'El correo debe ser válido',
            'correo.unique' => 'Este correo ya está registrado',
            'telefono.regex' => 'El teléfono solo puede contener números',
        ]);

        try {
            DB::table('super_administrador')
                ->where('doc_super_admin', $superAdmin->doc_super_admin)
                ->update([
                    'nombre' => $validated['nombre'],
                    'correo' => $validated['correo'],
                    'telefono' => $validated['telefono'],
                ]);


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
     * Mostrar opciones de seguridad
     */
    public function seguridad()
    {
        $superAdmin = $this->getSuperAdmin();
        
        if (!$superAdmin) {
            return redirect()->route('superadmin.login');
        }

        return view('superadmin.perfil_seguridad.seguridad', compact('superAdmin'));
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


        // Preparar datos para exportar
        $datos = [
            'usuario' => $superAdmin,
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


}
