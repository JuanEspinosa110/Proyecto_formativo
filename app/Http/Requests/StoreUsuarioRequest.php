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
                'max:30',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+(\s[\pLÁÉÍÓÚáéíóúÑñ]+)?$/u'
            ],
            
            'segundo_nombre' => [
                'nullable',
                'string',
                'min:2',
                'max:30',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+(\s[\pLÁÉÍÓÚáéíóúÑñ]+)?$/u'
            ],

            'primer_apellido' => [
                'required',
                'string',
                'min:2',
                'max:30',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+(\s[\pLÁÉÍÓÚáéíóúÑñ]+)?$/u'
            ],
            
            'segundo_apellido' => [
                'required',
                'string',
                'min:2',
                'max:30',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+(\s[\pLÁÉÍÓÚáéíóúÑñ]+)?$/u'
            ],

            'doc_usuario' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{5,9}$/',
                'unique:usuario,doc_usuario'
            ],

            'correo' => [
                'required',
                'email',
                'max:150',
                'unique:usuario,correo'
            ],

            'telefono' => [
                'required',
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
            'password' => [
                'nullable',
                'string',
                'min:8'
            ],
        ];
    }

    public function messages()
    {
        return [

            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras y máximo dos palabras.',
            'primer_nombre.min' => 'El primer nombre debe tener mínimo 2 caracteres.',
            'primer_nombre.max' => 'El primer nombre no debe exceder los 30 caracteres.',

            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras y máximo dos palabras.',
            'segundo_nombre.min' => 'El segundo nombre debe tener mínimo 2 caracteres.',
            'segundo_nombre.max' => 'El segundo nombre no debe exceder los 30 caracteres.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras y máximo dos palabras.',
            'primer_apellido.min' => 'El primer apellido debe tener mínimo 2 caracteres.',
            'primer_apellido.max' => 'El primer apellido no debe exceder los 30 caracteres.',

            'segundo_apellido.required' => 'El segundo apellido es obligatorio.',
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras y máximo dos palabras.',
            'segundo_apellido.min' => 'El segundo apellido debe tener mínimo 2 caracteres.',
            'segundo_apellido.max' => 'El segundo apellido no debe exceder los 30 caracteres.',

            'doc_usuario.required' => 'El documento de identidad es obligatorio.',
            'doc_usuario.numeric' => 'El documento solo puede contener números.',
            'doc_usuario.regex' => 'El documento debe tener entre 6 y 10 dígitos y no puede iniciar en 0.',
            'doc_usuario.unique' => 'Este número de documento ya está registrado.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese una dirección de correo válida.',
            'correo.unique' => 'Este correo ya está en uso.',

            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.numeric' => 'El teléfono solo debe contener números.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',

            'id_tipo_usuario.required' => 'Debe seleccionar un rol operativo.',
            'id_tipo_usuario.exists' => 'El rol seleccionado no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

            'id_estado.required' => 'El estado de la cuenta es obligatorio.',
        ];
    }
}