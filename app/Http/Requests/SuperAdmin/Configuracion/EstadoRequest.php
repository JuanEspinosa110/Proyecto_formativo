<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstadoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nombre_estado' => ucfirst(strtolower(trim($this->nombre_estado)))
        ]);
    }

    public function rules()
    {
        return [
            'nombre_estado' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',
                Rule::unique('estado', 'nombre_estado')
                    ->ignore($this->route('id'), 'id_estado')
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_estado.required' => 'El nombre del estado es obligatorio.',
            'nombre_estado.string' => 'El nombre debe ser texto.',
            'nombre_estado.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre_estado.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre_estado.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_estado.unique' => 'Este estado ya existe en el sistema.',
        ];
    }
}