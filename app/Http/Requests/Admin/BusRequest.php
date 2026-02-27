<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
{
    $this->merge([
        'placa' => strtoupper(trim($this->placa)),
        'modelo' => trim($this->modelo),
        'numero_chasis' => strtoupper(trim($this->numero_chasis)),
        'numero_motor' => strtoupper(trim($this->numero_motor)),
        'nombre_propietario' => trim($this->nombre_propietario),
        'telefono' => trim($this->telefono),
        'correo' => strtolower(trim($this->correo)),
    ]);
}

    public function rules(): bool|array
    {
        $placa = $this->route('bus') ? $this->route('bus')->placa : null;

        return [
             'placa' => [
    'required',
    'string',
    'size:6',
    'regex:/^[A-Z]{3}[0-9]{3}$/',
    'unique:bus,placa'
],

        'modelo' => [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:/^[A-Za-z0-9\s\-]+$/'
        ],

        'capacidad_pasajeros' => [
            'required',
            'integer',
            'min:1',
            'max:80'
        ],

        'kilometraje' => [
            'required',
            'numeric',
            'min:0',
            'max:9999999'
        ],

        'id_estado' => [
            'required',
            'integer',
            'exists:estado,id_estado'
        ],

        'linc_transito' => [
            'required',
            'digits_between:5,20'
        ],

        'numero_chasis' => [
            'required',
            'string',
            'size:17',
            'regex:/^[A-HJ-NPR-Z0-9]+$/'
        ],

        'numero_motor' => [
            'required',
            'string',
            'min:5',
            'max:14',
            'regex:/^[A-Z0-9]+$/'
        ],

        'doc_propietario' => [
            'required',
            'digits_between:6,15'
        ],

        'nombre_propietario' => [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'
        ],

        'telefono' => [
            'required',
            'digits_between:7,15'
        ],

        'correo' => [
            'required',
            'email:rfc,dns',
            'max:150'
        ],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.required' => 'Debe ingresar el número de placa.',
            'placa.regex' => 'La placa debe tener 3 letras iniciales y 3 números finales. Ej: ABC-123',
            'placa.unique' => 'Esta placa ya se encuentra registrada en el sistema.',
            'modelo.required' => 'El modelo/referencia es obligatorio.',
            'modelo.regex' => 'Debe ingresar la marca seguida del año en 4 dígitos. Ej: Toyota 2024',
            'capacidad_pasajeros.required' => 'La capacidad es obligatoria.',
            'capacidad_pasajeros.min' => 'La capacidad debe ser de al menos 10 pasajeros.',
            'id_estado.required' => 'El estado operativo es obligatorio.',
            'id_estado.exists' => 'El estado seleccionado no es válido.'
        ];
    }
}
