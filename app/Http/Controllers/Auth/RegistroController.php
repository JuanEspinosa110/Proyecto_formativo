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
            'doc_usuario' => ['required', 'numeric', 'digits_between:6,10', 'not_in:0', 'unique:usuario,doc_usuario'],
            'primer_nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'],
            'primer_apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'],
            'segundo_apellido' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'],
            'correo' => ['required', 'email', 'unique:usuario,correo'],
            'telefono' => ['required', 'string', 'min:7', 'max:20'],
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
            'doc_usuario.numeric' => 'El número de documento debe contener solo dígitos.',
            'doc_usuario.digits_between' => 'El número de documento debe tener entre 6 y 12 dígitos.',
            'doc_usuario.not_in' => 'El número de documento no puede ser 0.',
            'doc_usuario.unique' => 'Este número de documento ya se encuentra registrado en SIGU.',

            // Nombres
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.min' => 'El primer nombre debe tener al menos 2 caracteres.',
            'primer_nombre.max' => 'El primer nombre no puede superar los 100 caracteres.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras, espacios y guiones.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.min' => 'El primer apellido debe tener al menos 2 caracteres.',
            'primer_apellido.max' => 'El primer apellido no puede superar los 100 caracteres.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras, espacios y guiones.',

            'segundo_apellido.min' => 'El segundo apellido debe tener al menos 2 caracteres.',
            'segundo_apellido.max' => 'El segundo apellido no puede superar los 100 caracteres.',
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras, espacios y guiones.',

            // Correo
            'correo.required' => 'Debe ingresar un correo electrónico.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'correo.unique' => 'Este correo ya está registrado en SIGU.',

            // Teléfono
            'telefono.required' => 'Debe ingresar un número de teléfono.',
            'telefono.min' => 'El número de teléfono debe tener al menos 7 caracteres.',
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
            'id_ciudad' => 730001,
            'id_estado' => 1
        ]);

        return redirect()->route('login')
            ->with('success', 'Registro exitoso. Ahora puede iniciar sesión en SIGU.');
    }

}
