<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TipoEmpresaRequest extends FormRequest
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
                'max:50',

                // Solo letras y espacios con tildes
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',

                Rule::unique('tipo_empresa', 'nombre_tipo')
                    ->ignore($id, 'id_tipo_empresa'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_tipo.required' => 'El nombre del tipo de empresa es obligatorio.',
            'nombre_tipo.string' => 'El nombre debe ser texto.',
            'nombre_tipo.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre_tipo.max' => 'El nombre no puede superar los 50 caracteres.',
            'nombre_tipo.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_tipo.unique' => 'Ya existe un tipo de empresa con ese nombre.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->nombre_tipo) {

            $nombre = strip_tags(trim($this->nombre_tipo));

            // Elimina espacios múltiples
            $nombre = preg_replace('/\s+/', ' ', $nombre);

            // Normaliza formato
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

            $existe = DB::table('tipo_empresa')
                ->whereRaw('LOWER(nombre_tipo) = ?', [$nombre])
                ->when($id, function ($query) use ($id) {
                    $query->where('id_tipo_empresa', '!=', $id);
                })
                ->exists();

            if ($existe) {
                $validator->errors()->add(
                    'nombre_tipo',
                    'Ya existe un tipo de empresa con ese nombre (validación estricta).'
                );
            }
        });
    }
}