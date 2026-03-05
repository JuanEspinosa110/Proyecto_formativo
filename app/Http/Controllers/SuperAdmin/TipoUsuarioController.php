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

    $tipos = $query->orderBy('id_tipo_usuario', 'asc')
                   ->paginate(5);

    return view('superadmin.configuracion.tipo_usuario.index', compact('tipos'));
    }

   // ... dentro de TipoUsuarioController.php

    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo' => [
                'required',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/',
                function ($attribute, $value, $fail) {
                    $exists = TipoUsuario::whereRaw('LOWER(nombre_tipo) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail('El tipo de usuario "' . $value . '" ya se encuentra registrado.');
                    }
                }
            ]
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio.',
            'nombre_tipo.regex' => 'Solo se permiten letras y espacios.'
        ]);

        TipoUsuario::create([
            'nombre_tipo' => strtoupper($request->nombre_tipo) 
        ]);

        return redirect()
            ->route('superadmin.tipo_usuario.index')
            ->with('success', 'Tipo de usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_tipo' => [
                'required',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = TipoUsuario::whereRaw('LOWER(nombre_tipo) = ?', [strtolower($value)])
                        ->where('id_tipo_usuario', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El tipo de usuario "' . $value . '" ya existe.');
                    }
                }
            ]
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio.',
            'nombre_tipo.regex' => 'Solo se permiten letras y espacios.'
        ]);

        $tipo = TipoUsuario::findOrFail($id);
        
        $tipo->update([
            'nombre_tipo' => strtoupper($request->nombre_tipo)
        ]);

        return redirect()
            ->route('superadmin.tipo_usuario.index')
            ->with('success', 'Tipo de usuario actualizado correctamente.');
    }
}
