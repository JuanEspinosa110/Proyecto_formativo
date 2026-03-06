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
            'placa.size' => 'La placa debe tener exactamente 6 caracteres (3 letras y 3 números).',
            'placa.regex' => 'La placa debe tener 3 letras iniciales y 3 números finales. Ej: ABC-123',
            'placa.unique' => 'Esta placa ya se encuentra registrada en el sistema.',
       
            'modelo.required' => 'El modelo del vehículo es obligatorio.',
            'modelo.string' => 'El modelo debe ser un texto válido.',
            'modelo.min' => 'El modelo debe tener mínimo 3 caracteres.',
            'modelo.max' => 'El modelo no puede superar los 100 caracteres.',
            'modelo.regex' => 'El modelo solo puede contener letras, números, espacios y guiones.',

            'capacidad_pasajeros.required' => 'La capacidad de pasajeros es obligatoria.',
            'capacidad_pasajeros.integer' => 'La capacidad de pasajeros debe ser un número entero.',
            'capacidad_pasajeros.min' => 'La capacidad de pasajeros debe ser mínimo 1.',
            'capacidad_pasajeros.max' => 'La capacidad de pasajeros no puede ser mayor a 80.',

            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.numeric' => 'El kilometraje debe ser un valor numérico.',
            'kilometraje.min' => 'El kilometraje no puede ser negativo.',
            'kilometraje.max' => 'El kilometraje no puede superar los 9.999.999.',

            'id_estado.required' => 'Debe seleccionar un estado.',
            'id_estado.integer' => 'El estado seleccionado no es válido.',
            'id_estado.exists' => 'El estado seleccionado no existe en el sistema.',

            'linc_transito.required' => 'El número de licencia de tránsito es obligatorio.',
            'linc_transito.digits_between' => 'La licencia de tránsito debe tener entre 5 y 20 dígitos.',

            'numero_chasis.required' => 'El número de chasis es obligatorio.',
            'numero_chasis.string' => 'El número de chasis debe ser un texto válido.',
            'numero_chasis.size' => 'El número de chasis debe tener exactamente 17 caracteres.',
            'numero_chasis.regex' => 'El número de chasis solo puede contener letras mayúsculas (excepto I, O, Q) y números.',

            'numero_motor.required' => 'El número de motor es obligatorio.',
            'numero_motor.string' => 'El número de motor debe ser un texto válido.',
            'numero_motor.min' => 'El número de motor debe tener mínimo 5 caracteres.',
            'numero_motor.max' => 'El número de motor no puede superar los 14 caracteres.',
            'numero_motor.regex' => 'El número de motor solo puede contener letras mayúsculas y números.',

            'doc_propietario.required' => 'El documento del propietario es obligatorio.',
            'doc_propietario.digits_between' => 'El documento del propietario debe tener entre 6 y 15 dígitos.',

            'nombre_propietario.required' => 'El nombre del propietario es obligatorio.',
            'nombre_propietario.string' => 'El nombre del propietario debe ser texto válido.',
            'nombre_propietario.min' => 'El nombre del propietario debe tener mínimo 3 caracteres.',
            'nombre_propietario.max' => 'El nombre del propietario no puede superar los 100 caracteres.',
            'nombre_propietario.regex' => 'El nombre solo puede contener letras y espacios.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Debe ingresar un correo electrónico válido.',
            'correo.max' => 'El correo electrónico no puede superar los 150 caracteres.',
    ];
    }
}
