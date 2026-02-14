<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Validation\Rules\Password;


class RegistroController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'doc_usuario' => 'required|string|unique:usuario,doc_usuario',
        'primer_nombre' => 'required|string|max:100',
        'primer_apellido' => 'required|string|max:100',
        'segundo_apellido' => 'nullable|string|max:100',
        'correo' => 'required|email|unique:usuario,correo',
        'telefono' => 'required|string|max:20',

        'password' => [
            'required', 
            'confirmed',
            password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()         
            ]
    ]); 

    Usuario::create([
        'doc_usuario' => $request->doc_usuario,
        'primer_nombre' => $request->primer_nombre,
        'segundo_nombre' => $request->segundo_nombre,
        'primer_apellido' => $request->primer_apellido,
        'segundo_apellido' => $request->segundo_apellido,
        'correo' => $request->correo,
        'telefono' => $request->telefono,
        'password' => Hash::make($request->password), // 🔐 AQUÍ SE HASHEA
        'id_tipo_usuario' => 2, // PASAJERO
        'id_estado' => 1 // Activo (si usas estados)
    ]);

    return redirect()->route('login')
        ->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
}
}
