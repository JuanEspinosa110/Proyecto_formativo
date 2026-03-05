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
                'integer',
                'exists:departamento,id_departamento'
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_city.required' => 'El nombre de la ciudad es obligatorio.',
            'nombre_city.max' => 'La ciudad no puede superar los 100 caracteres.',
            'id_departamento.required' => 'Debe seleccionar un departamento.',
            'id_departamento.exists' => 'El departamento seleccionado no es válido.'
        ];
    }
}
