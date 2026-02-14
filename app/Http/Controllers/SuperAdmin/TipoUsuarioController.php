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

        return view('admin.roles.index', compact('tiposUsuario'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.roles.create');
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
                ->route('admin.roles.index')
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
                ->route('admin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }



        return view('admin.roles.edit', compact('tipoUsuario'));
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
                ->route('admin.roles.index')
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
                ->route('admin.roles.index')
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
                ->route('admin.roles.index')
                ->with('success', 'Tipo de usuario eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el tipo de usuario: ' . $e->getMessage());
        }
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
                ->route('admin.roles.index')
                ->with('error', 'Tipo de usuario no encontrado');
        }

        $usuarios = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->where('usuario.id_tipo_usuario', $id)
            ->select('usuario.*', 'estado.nombre_estado', 'ciudad.nombre_city')
            ->paginate(15);

        return view('admin.roles.users', compact('tipoUsuario', 'usuarios'));
    }

    
}
