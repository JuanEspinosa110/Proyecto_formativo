<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'primer_nombre' => [
                'required',
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ],
            
            'segundo_nombre' => [
                'nullable',
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ],

            'primer_apellido' => [
                'required',
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ],
            
            'segundo_apellido' => [
                'required', // Rule 3: Mandatory
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ],

            'doc_usuario' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{8,11}$/', // Permite 9 a 12 dígitos, no inicie con 0
                'unique:usuario,doc_usuario'
            ],

            'correo' => [
                'required',
                'email',
                'max:150',
                'unique:usuario,correo'
            ],

            'telefono' => [
                'required', // Rule 3: Mandatory
                'numeric',
                'digits:10'
            ],

            'id_tipo_usuario' => [
                'required',
                'exists:tipo_usuario,id_tipo_usuario'
            ],

            'foto_usuario' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],

            'NIT' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{8,11}$/', // Permite 9 a 12 dígitos, no inicie con 0
            ],
        ];
    }

    public function messages()
    {
        return [

            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras.',
            'primer_nombre.min' => 'El primer nombre debe tener mínimo 2 caracteres.',

            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras.',
            'segundo_nombre.min' => 'El segundo nombre debe tener mínimo 2 caracteres.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras.',
            'primer_apellido.min' => 'El primer apellido debe tener mínimo 2 caracteres.',

            'segundo_apellido.required' => 'El segundo apellido es obligatorio.',
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras.',
            'segundo_apellido.min' => 'El segundo apellido debe tener mínimo 2 caracteres.',

            'doc_usuario.required' => 'El documento de identidad es obligatorio.',
            'doc_usuario.numeric' => 'El documento solo puede contener números.',
            'doc_usuario.regex' => 'El documento debe tener mínimo 9 dígitos y no puede iniciar con 0.',
            'doc_usuario.unique' => 'Este número de documento ya está registrado.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese una dirección de correo válida.',
            'correo.unique' => 'Este correo ya está en uso.',

            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.numeric' => 'El teléfono solo debe contener números.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',

            'id_tipo_usuario.required' => 'Debe seleccionar un rol operativo.',
            'id_tipo_usuario.exists' => 'El rol seleccionado no es válido.',

            'NIT.required' => 'El NIT de la empresa es obligatorio.',
            'NIT.numeric' => 'El NIT solo debe contener números.',
            'NIT.regex' => 'El NIT debe tener mínimo 9 dígitos y no puede iniciar con 0.',

            'id_estado.required' => 'El estado de la cuenta es obligatorio.',
        ];
    }
}