<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoUsuarioController extends Controller
{
    /**
     * Mostrar lista de tipos de usuario
     */
    public function index()
    {
        $tiposUsuario = DB::table('tipo_usuario')
            ->orderBy('id_tipo_usuario', 'asc')
            ->get();

        // Contar usuarios por tipo
        foreach ($tiposUsuario as $tipo) {
            $tipo->usuarios_count = DB::table('usuario')
                ->where('id_tipo_usuario', $tipo->id_tipo_usuario)
                ->count();
        }

        return view('superadmin.roles.index', compact('tiposUsuario'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Permisos mockeados para la vista (datos de ejemplo)
        $permissions = $this->getPermissionsMock();

        return view('superadmin.roles.create', compact('permissions'));
    }

    /**
     * Guardar nuevo tipo de usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_tipo' => 'required|string|max:50|unique:tipo_usuario,nombre_tipo',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio',
            'nombre_tipo.unique' => 'Ya existe un tipo de usuario con ese nombre',
        ]);

        try {
            DB::table('tipo_usuario')->insert([
                'nombre_tipo' => $validated['nombre_tipo'],
            ]);

            return redirect()
                ->route('superadmin.roles.index')
                ->with('success', 'Tipo de usuario creado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el tipo de usuario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $tipoUsuario = DB::table('tipo_usuario')
            ->where('id_tipo_usuario', $id)
            ->first();

        if (!$tipoUsuario) {
            return redirect()
                ->route('superadmin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }

        // Permisos mockeados para la vista (datos de ejemplo)
        $permissions = $this->getPermissionsMock();
        $rolePermissions = []; // Array vacío por ahora

        return view('superadmin.roles.edit', compact('tipoUsuario', 'permissions', 'rolePermissions'));
    }

    /**
     * Actualizar tipo de usuario
     */
    public function update(Request $request, $id)
    {
        $tipoUsuario = DB::table('tipo_usuario')
            ->where('id_tipo_usuario', $id)
            ->first();

        if (!$tipoUsuario) {
            return redirect()
                ->route('superadmin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }

        $validated = $request->validate([
            'nombre_tipo' => 'required|string|max:50|unique:tipo_usuario,nombre_tipo,' . $id . ',id_tipo_usuario',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio',
            'nombre_tipo.unique' => 'Ya existe un tipo de usuario con ese nombre',
        ]);

        try {
            DB::table('tipo_usuario')
                ->where('id_tipo_usuario', $id)
                ->update([
                    'nombre_tipo' => $validated['nombre_tipo'],
                ]);

            return redirect()
                ->route('superadmin.roles.index')
                ->with('success', 'Tipo de usuario actualizado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el tipo de usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar tipo de usuario
     */
    public function destroy($id)
    {
        try {
            // Verificar si el tipo tiene usuarios asignados
            $usersCount = DB::table('usuario')
                ->where('id_tipo_usuario', $id)
                ->count();
            
            if ($usersCount > 0) {
                return back()->with('error', "No se puede eliminar el tipo de usuario porque tiene {$usersCount} usuario(s) asignado(s)");
            }

            DB::table('tipo_usuario')
                ->where('id_tipo_usuario', $id)
                ->delete();

            return redirect()
                ->route('superadmin.roles.index')
                ->with('success', 'Tipo de usuario eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el tipo de usuario: ' . $e->getMessage());
        }
    }

    /**
     * Ver permisos de un tipo de usuario (MOCKUP)
     */
    public function showPermissions($id)
    {
        $tipoUsuario = DB::table('tipo_usuario')
            ->where('id_tipo_usuario', $id)
            ->first();

        if (!$tipoUsuario) {
            return redirect()
                ->route('superadmin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }

        // Permisos mockeados organizados por módulo
        $permissionsByModule = $this->getPermissionsMockByModule($id);

        return view('superadmin.roles.permissions', compact('tipoUsuario', 'permissionsByModule'));
    }

    /**
     * Mostrar usuarios con tipo específico
     */
    public function users($id)
    {
        $tipoUsuario = DB::table('tipo_usuario')
            ->where('id_tipo_usuario', $id)
            ->first();

        if (!$tipoUsuario) {
            return redirect()
                ->route('superadmin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }

        $usuarios = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->where('usuario.id_tipo_usuario', $id)
            ->select('usuario.*', 'estado.nombre_estado', 'ciudad.nombre_city')
            ->paginate(15);

        return view('superadmin.roles.users', compact('tipoUsuario', 'usuarios'));
    }

    /**
     * Obtener permisos mock agrupados para la vista
     */
    private function getPermissionsMock()
    {
        return collect([
            'Dashboard' => [
                (object)['id' => 1, 'name' => 'dashboard.ver', 'description' => 'Ver panel de control', 'module' => 'Dashboard'],
                (object)['id' => 2, 'name' => 'dashboard.estadisticas', 'description' => 'Ver estadísticas completas', 'module' => 'Dashboard'],
            ],
            'Usuarios' => [
                (object)['id' => 3, 'name' => 'usuarios.ver', 'description' => 'Ver listado de usuarios', 'module' => 'Usuarios'],
                (object)['id' => 4, 'name' => 'usuarios.crear', 'description' => 'Crear nuevos usuarios', 'module' => 'Usuarios'],
                (object)['id' => 5, 'name' => 'usuarios.editar', 'description' => 'Editar información de usuarios', 'module' => 'Usuarios'],
                (object)['id' => 6, 'name' => 'usuarios.eliminar', 'description' => 'Eliminar usuarios del sistema', 'module' => 'Usuarios'],
            ],
            'Empresas' => [
                (object)['id' => 7, 'name' => 'empresas.ver', 'description' => 'Ver listado de empresas', 'module' => 'Empresas'],
                (object)['id' => 8, 'name' => 'empresas.crear', 'description' => 'Registrar nuevas empresas', 'module' => 'Empresas'],
                (object)['id' => 9, 'name' => 'empresas.editar', 'description' => 'Editar información de empresas', 'module' => 'Empresas'],
                (object)['id' => 10, 'name' => 'empresas.eliminar', 'description' => 'Eliminar empresas', 'module' => 'Empresas'],
            ],
            'Tarjetas' => [
                (object)['id' => 11, 'name' => 'tarjetas.ver', 'description' => 'Ver listado de tarjetas', 'module' => 'Tarjetas'],
                (object)['id' => 12, 'name' => 'tarjetas.gestionar_saldo', 'description' => 'Gestionar saldo de tarjetas', 'module' => 'Tarjetas'],
                (object)['id' => 13, 'name' => 'tarjetas.bloquear', 'description' => 'Bloquear o desbloquear tarjetas', 'module' => 'Tarjetas'],
            ],
            'Documentación' => [
                (object)['id' => 14, 'name' => 'documentos.ver', 'description' => 'Ver documentos del sistema', 'module' => 'Documentación'],
                (object)['id' => 15, 'name' => 'documentos.aprobar', 'description' => 'Aprobar o rechazar documentos', 'module' => 'Documentación'],
            ],
        ]);
    }

    /**
     * Obtener permisos mock por módulo según el tipo de usuario
     */
    private function getPermissionsMockByModule($tipoId)
    {
        $allPermissions = $this->getPermissionsMock();

        // Simular permisos según tipo de usuario
        if ($tipoId == 1) { // Admin - todos los permisos
            return $allPermissions;
        } else if ($tipoId == 2) { // Pasajero - permisos limitados
            return collect([
                'Dashboard' => [
                    (object)['id' => 1, 'name' => 'dashboard.ver', 'description' => 'Ver panel de control', 'module' => 'Dashboard'],
                ],
                'Tarjetas' => [
                    (object)['id' => 11, 'name' => 'tarjetas.ver', 'description' => 'Ver listado de tarjetas', 'module' => 'Tarjetas'],
                ],
            ]);
        }

        return collect([]);
    }
}
