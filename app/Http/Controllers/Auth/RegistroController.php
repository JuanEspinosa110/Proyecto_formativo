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
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
        ]
    ], [

        // Documento
        'doc_usuario.required' => 'Debe ingresar su número de documento.',
        'doc_usuario.unique' => 'Este número de documento ya se encuentra registrado en SIGU.',

        // Nombres
        'primer_nombre.required' => 'El primer nombre es obligatorio.',
        'primer_nombre.max' => 'El primer nombre no puede superar los 100 caracteres.',

        'primer_apellido.required' => 'El primer apellido es obligatorio.',
        'primer_apellido.max' => 'El primer apellido no puede superar los 100 caracteres.',

        'segundo_apellido.max' => 'El segundo apellido no puede superar los 100 caracteres.',

        // Correo
        'correo.required' => 'Debe ingresar un correo electrónico.',
        'correo.email' => 'Ingrese un correo electrónico válido.',
        'correo.unique' => 'Este correo ya está registrado en SIGU.',

        // Teléfono
        'telefono.required' => 'Debe ingresar un número de teléfono.',
        'telefono.max' => 'El número de teléfono no puede superar los 20 caracteres.',

        // Contraseña
        'password.required' => 'Debe crear una contraseña.',
        'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
        'password.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
        'password.numbers' => 'La contraseña debe incluir al menos un número.',
        'password.symbols' => 'La contraseña debe incluir al menos un símbolo.',

    ]);

    Usuario::create([
        'doc_usuario' => $request->doc_usuario,
        'primer_nombre' => $request->primer_nombre,
        'segundo_nombre' => $request->segundo_nombre,
        'primer_apellido' => $request->primer_apellido,
        'segundo_apellido' => $request->segundo_apellido,
        'correo' => $request->correo,
        'telefono' => $request->telefono,
        'password' => Hash::make($request->password),
        'id_tipo_usuario' => 2,
        'id_estado' => 1
    ]);

    return redirect()->route('login')
        ->with('success', 'Registro exitoso. Ahora puede iniciar sesión en SIGU.');
}

}
