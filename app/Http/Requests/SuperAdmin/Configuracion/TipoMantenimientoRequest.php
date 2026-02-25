<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class TipoMantenimientoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del tipo de mantenimiento es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
        ];
    }
}
