<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class EstadoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_estado' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'nombre_estado.required' => 'El nombre del estado es obligatorio.',
            'nombre_estado.max' => 'El nombre no puede superar los 100 caracteres.',
        ];
    }
}
