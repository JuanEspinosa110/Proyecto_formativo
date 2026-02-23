<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    public function index(Request $request)
    {
       $query = TipoUsuario::query();

    if ($request->filled('buscar')) {
        $query->where('nombre_tipo', 'like', '%' . $request->buscar . '%');
    }

    $tipos = $query->orderBy('nombre_tipo')
                   ->paginate(5);

    return view('superadmin.configuracion.tipo_usuario.index', compact('tipos'));
    }

   // ... dentro de TipoUsuarioController.php

    public function store(Request $request)
    {
        $request->validate([
            // Corregido: Debe coincidir con el 'name' del input en tu index.blade.php
            'nombre_tipo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio.',
            'nombre_tipo.regex' => 'Solo se permiten letras y espacios.'
        ]);

        TipoUsuario::create([
            'nombre_tipo' => $request->nombre_tipo 
        ]);

        return redirect()
            ->route('superadmin.tipo_usuario.index')
            ->with('success', 'Tipo de usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Corregido: Consistencia en el nombre del campo
            'nombre_tipo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio.',
            'nombre_tipo.regex' => 'Solo se permiten letras y espacios.'
        ]);

        $tipo = TipoUsuario::findOrFail($id);
        
        // Es mejor asignar el campo específico para evitar fallos de seguridad
        $tipo->update([
            'nombre_tipo' => $request->nombre_tipo
        ]);

        return redirect()
            ->route('superadmin.tipo_usuario.index')
            ->with('success', 'Tipo de usuario actualizado correctamente.');
    }
}
