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
                'min:3',
                'max:30',
                'regex:/^[\pL]+$/u'
            ],
            
            'segundo_nombre' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[\pL]+$/u'
            ],

            'primer_apellido' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[\pL]+$/u'
            ],
            
            'segundo_apellido' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[\pL]+$/u'
            ],

            'doc_usuario' => [
                'required',
                'digits_between:6,12',
                'numeric',
                'unique:usuarios,doc_usuario'
            ],

            'correo' => [
                'required',
                'email:rfc,dns',
                'max:100',
                'unique:usuarios,correo'
            ],

            'telefono' => [
                'nullable',
                'digits_between:7,15'
            ],

            'rol' => [
                'required',
                'in:admin,operador,usuario'
            ],

            'estado' => [
                'required',
                'boolean'
            ],
        ];
    }

    public function messages()
    {
        return [

            'primer_nombre.required' => 'El nombre es obligatorio.',
            'primer_nombre.regex' => 'El nombre solo puede contener letras.',
            'primer_nombre.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'primer_nombre.max' => 'El nombre no puede superar los 30 caracteres.',

            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras.',
            'segundo_nombre.min' => 'El segundo nombre debe tener mínimo 3 caracteres.',
            'segundo_nombre.max' => 'El segundo nombre no puede superar los 30 caracteres.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras.',
            'primer_apellido.min' => 'El primer apellido debe tener mínimo 3 caracteres.',
            'primer_apellido.max' => 'El primer apellido no puede superar los 30 caracteres.',

            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras.',
            'segundo_apellido.min' => 'El segundo apellido debe tener mínimo 3 caracteres.',
            'segundo_apellido.max' => 'El segundo apellido no puede superar los 30 caracteres.',

            'doc_usuario.required' => 'El documento es obligatorio.',
            'doc_usuario.numeric' => 'El documento solo puede contener números.',
            'doc_usuario.unique' => 'Este documento ya está registrado.',

            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.unique' => 'Este correo ya está registrado.',

            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',

            'rol.required' => 'Debe seleccionar un rol válido.',
            'rol.in' => 'El rol seleccionado no es válido.',

            'estado.required' => 'El estado es obligatorio.',
        ];
    }
}