<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CiudadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
{
    $this->merge([
        'nombre_city' => trim($this->nombre_city)
    ]);
}


    public function rules()
    {
        return [
            'id_ciudad' => [
                'bail',
                'required',
                'string', // o 'integer' según tu migración
                'min:3',
                'max:10',
                Rule::unique('ciudad', 'id_ciudad')
            ],
            'nombre_city' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('ciudad', 'nombre_city')
            ],
            'id_departamento' => [
                'bail',
                'required',
                'string',
                'exists:departamento,id_departamento'
            ],
        ];
    }

    public function messages()
    {
        return [
            'id_ciudad.required' => 'El código postal es obligatorio.',
            'id_ciudad.unique' => 'El código postal ya existe.',
            'id_ciudad.min' => 'El código postal debe tener al menos 3 caracteres.',
            'id_ciudad.max' => 'El código postal no puede superar los 6 caracteres.',
            'nombre_city.required' => 'El nombre de la ciudad es obligatorio.',
            'nombre_city.unique' => 'El nombre de la ciudad ya existe.',
            'nombre_city.min' => 'El nombre de la ciudad debe tener al menos 3 caracteres.',
            'nombre_city.max' => 'El nombre de la ciudad no puede superar los 100 caracteres.',
            'id_departamento.required' => 'Debe seleccionar un departamento.',
            'id_departamento.exists' => 'El departamento seleccionado no es válido.',
            'id_departamento.integer' => 'El departamento debe ser un valor numérico.',
        ];
    }
}
