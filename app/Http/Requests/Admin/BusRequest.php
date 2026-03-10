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
        $input = [
            'modelo' => trim($this->modelo),
            'numero_chasis' => strtoupper(trim($this->numero_chasis)),
            'numero_motor' => strtoupper(trim($this->numero_motor)),
            'nombre_propietario' => trim($this->nombre_propietario),
            'telefono' => trim($this->telefono),
            'correo' => strtolower(trim($this->correo)),
        ];

        if ($this->has('placa')) {
            $input['placa'] = strtoupper(trim($this->placa));
        }

        $this->merge($input);
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $bus = $this->route('bus');
        $currentPlaca = $bus instanceof \App\Models\Bus ? $bus->placa : $bus;

        return [
            'placa' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'size:6',
                'regex:/^[A-Z]{3}[0-9]{3}$/',
                \Illuminate\Validation\Rule::unique('bus', 'placa')->ignore($currentPlaca, 'placa')
            ],

            'modelo' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z0-9\s\-]+$/'
            ],

            'capacidad_pasajeros' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'min:1',
                'max:80'
            ],

            'kilometraje' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min:0',
                'max:9999999'
            ],

            'id_estado' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:estado,id_estado'
            ],

            'linc_transito' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'regex:/^[1-9][0-9]{8,19}$/' // Ajustado a 9 dígitos como en Usuarios
            ],

            'numero_chasis' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'size:17',
                'regex:/^[A-HJ-NPR-Z0-9]+$/'
            ],

            'numero_motor' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:5',
                'max:14',
                'regex:/^[A-Z0-9]+$/'
            ],

            'doc_propietario' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'regex:/^[1-9][0-9]{8,14}$/' // Ajustado a 9 dígitos como en Usuarios
            ],

            'nombre_propietario' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ],

            'telefono' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'digits:10'
            ],

            'correo' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:150'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.required' => 'La placa es obligatoria.',
            'placa.size' => 'La placa debe tener 6 caracteres.',
            'placa.regex' => 'Formato de placa inválido (3 letras y 3 números).',
            'placa.unique' => 'Esta placa ya está registrada.',
       
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.min' => 'El modelo debe tener mínimo 3 caracteres.',

            'capacidad_pasajeros.required' => 'La capacidad es obligatoria.',
            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'id_estado.required' => 'El estado es obligatorio.',

            'linc_transito.required' => 'La licencia de tránsito es obligatoria.',
            'linc_transito.regex' => 'La licencia debe tener mínimo 9 dígitos y no iniciar con 0.',
            'numero_chasis.required' => 'El número de chasis es obligatorio.',
            'numero_motor.required' => 'El número de motor es obligatorio.',
            'doc_propietario.required' => 'El documento del propietario es obligatorio.',
            'doc_propietario.regex' => 'El documento debe tener mínimo 9 dígitos y no iniciar con 0.',

            'nombre_propietario.required' => 'El nombre del propietario es obligatorio.',
            'nombre_propietario.min' => 'El nombre debe tener mínimo 2 caracteres.',
            'nombre_propietario.regex' => 'El nombre solo puede contener letras.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
    ];
    }
}
