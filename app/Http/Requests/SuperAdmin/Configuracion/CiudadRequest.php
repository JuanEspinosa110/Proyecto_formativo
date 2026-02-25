<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class CiudadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_city' => 'required|string|max:100',
            'id_departamento' => 'required|exists:departamento,id_departamento'
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
