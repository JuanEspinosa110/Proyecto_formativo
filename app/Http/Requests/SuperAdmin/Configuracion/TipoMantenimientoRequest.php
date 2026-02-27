<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TipoMantenimientoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [

            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:100',

                // Solo letras y espacios con tildes
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',

                Rule::unique('tipo_mantenimientos', 'nombre')
                    ->ignore($id, 'id_tipo_mantenimiento'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del tipo de mantenimiento es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.unique' => 'Ya existe un tipo de mantenimiento con ese nombre.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->nombre) {

            $nombre = trim($this->nombre);

            // Eliminar espacios múltiples
            $nombre = preg_replace('/\s+/', ' ', $nombre);

            // Normalizar formato
            $nombre = ucwords(strtolower($nombre));

            $this->merge([
                'nombre' => $nombre
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $id = $this->route('id');
            $nombre = strtolower($this->nombre);

            $existe = DB::table('tipo_mantenimientos')
                ->whereRaw('LOWER(nombre) = ?', [$nombre])
                ->when($id, function ($query) use ($id) {
                    $query->where('id_tipo_mantenimiento', '!=', $id);
                })
                ->exists();

            if ($existe) {
                $validator->errors()->add(
                    'nombre',
                    'Ya existe un tipo de mantenimiento con ese nombre (validación estricta).'
                );
            }
        });
    }
}