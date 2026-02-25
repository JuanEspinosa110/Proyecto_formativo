<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class TipoUsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_tipo' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'nombre_tipo.required' => 'El nombre del tipo de usuario es obligatorio.',
            'nombre_tipo.max' => 'El nombre no puede superar los 100 caracteres.',
        ];
    }
}
