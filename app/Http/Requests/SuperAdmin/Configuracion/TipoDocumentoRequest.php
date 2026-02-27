<?php

namespace App\Http\Requests\SuperAdmin\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TipoDocumentoRequest extends FormRequest
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
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',

                Rule::unique('tipo_documentos', 'nombre')
                    ->ignore($id, 'id_tipo_documento'),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'id_estado' => [
                'required',
                'integer',
                'exists:estados,id_estado',
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.unique' => 'Ya existe un tipo de documento con ese nombre.',

            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',

            'id_estado.required' => 'Debe seleccionar un estado.',
            'id_estado.integer' => 'El estado es inválido.',
            'id_estado.exists' => 'El estado seleccionado no existe.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->nombre) {
            $nombre = trim($this->nombre);
            $nombre = preg_replace('/\s+/', ' ', $nombre);
            $nombre = ucwords(strtolower($nombre));

            $this->merge([
                'nombre' => $nombre
            ]);
        }

        if ($this->descripcion) {
            $descripcion = trim($this->descripcion);
            $descripcion = preg_replace('/\s+/', ' ', $descripcion);

            $this->merge([
                'descripcion' => $descripcion
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $id = $this->route('id');
            $nombre = strtolower($this->nombre);

            $existe = DB::table('tipo_documentos')
                ->whereRaw('LOWER(nombre) = ?', [$nombre])
                ->when($id, function ($query) use ($id) {
                    $query->where('id_tipo_documento', '!=', $id);
                })
                ->exists();

            if ($existe) {
                $validator->errors()->add(
                    'nombre',
                    'Ya existe un tipo de documento con ese nombre (validación estricta).'
                );
            }
        });
    }
}