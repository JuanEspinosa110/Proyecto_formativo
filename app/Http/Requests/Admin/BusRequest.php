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
        $user = auth()->user();
        $nit = $user ? $user->getActiveNit() : null;

        return [
            'placa' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'size:6',
                'regex:/^[A-Z]{3}[0-9]{3}$/',
                \Illuminate\Validation\Rule::unique('bus', 'placa')
                    ->where(fn ($query) => $query->where('NIT', $nit))
                    ->ignore($currentPlaca, 'placa')
            ],

            'modelo' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ\s]+\s[0-9]{4}$/u'
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
                'digits:8'
            ],

            'numero_chasis' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'digits:17',
                \Illuminate\Validation\Rule::unique('bus', 'numero_chasis')->ignore($currentPlaca, 'placa')
            ],

            'numero_motor' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min_digits:8',
                'max_digits:17',
                \Illuminate\Validation\Rule::unique('bus', 'numero_motor')->ignore($currentPlaca, 'placa')
            ],

            'doc_propietario' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min_digits:6',
                'max_digits:10'
            ],

            'nombre_propietario' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:2',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ\s]+$/u'
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
            'placa.regex' => 'La placa debe tener 3 letras y 3 números (Ej: ABC123).',
            'placa.unique' => 'Este bus ya está registrado en su empresa.',
       
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.regex' => 'El modelo debe incluir Marca y Año (Ej: Toyota 2019).',

            'linc_transito.required' => 'La licencia de tránsito es obligatoria.',
            'linc_transito.digits' => 'La licencia debe tener exactamente 8 caracteres numéricos.',
            
            'numero_chasis.required' => 'El número de chasis es obligatorio.',
            'numero_chasis.digits' => 'El chasis debe tener exactamente 17 caracteres numéricos.',
            'numero_chasis.unique' => 'Este número de chasis ya está registrado en el sistema.',
            
            'numero_motor.required' => 'El número de motor es obligatorio.',
            'numero_motor.min_digits' => 'El motor debe tener entre 8 y 17 caracteres numéricos.',
            'numero_motor.max_digits' => 'El motor debe tener entre 8 y 17 caracteres numéricos.',
            'numero_motor.numeric' => 'El motor solo debe contener números.',
            'numero_motor.unique' => 'Este número de motor ya está registrado en el sistema.',

            'doc_propietario.required' => 'El documento del propietario es obligatorio.',
            'doc_propietario.numeric' => 'El documento solo debe contener números.',
            'doc_propietario.min_digits' => 'El documento debe tener mínimo 6 dígitos.',
            'doc_propietario.max_digits' => 'El documento no debe superar los 10 dígitos.',

            'nombre_propietario.required' => 'El nombre del propietario es obligatorio.',
            'nombre_propietario.regex' => 'El nombre solo puede contener letras y espacios.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos numéricos.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese una dirección de correo válida.',
    ];
    }
}
