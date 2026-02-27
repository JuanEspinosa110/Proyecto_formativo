<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TipoAsignacionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [

            'nombre_tipo' => [
                'required',
                'string',
                'min:3',
                'max:100',

                // Solo letras y espacios (incluye tildes)
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',

                // Unique ignorando el actual en update
                Rule::unique('tipo_asignaciones', 'nombre_tipo')
                    ->ignore($id, 'id_tipo'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_tipo.required' => 'El nombre es obligatorio.',
            'nombre_tipo.string' => 'El nombre debe ser texto.',
            'nombre_tipo.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre_tipo.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre_tipo.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_tipo.unique' => 'Ya existe un tipo con ese nombre.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->nombre_tipo) {

            $nombre = trim($this->nombre_tipo);

            // Elimina espacios dobles internos
            $nombre = preg_replace('/\s+/', ' ', $nombre);

            // Primera letra de cada palabra en mayúscula
            $nombre = ucwords(strtolower($nombre));

            $this->merge([
                'nombre_tipo' => $nombre
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $id = $this->route('id');
            $nombre = strtolower($this->nombre_tipo);

            $existe = DB::table('tipo_asignaciones')
                ->whereRaw('LOWER(nombre_tipo) = ?', [$nombre])
                ->when($id, function ($query) use ($id) {
                    $query->where('id_tipo', '!=', $id);
                })
                ->exists();

            if ($existe) {
                $validator->errors()->add(
                    'nombre_tipo',
                    'Ya existe un tipo con ese nombre (validación estricta).'
                );
            }
        });
    }
}