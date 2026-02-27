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
            'modelo' => trim($this->modelo)
        ]);
    }

    public function rules(): bool|array
    {
        $placa = $this->route('bus') ? $this->route('bus')->placa : null;

        return [
            'placa' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'regex:/^[A-Z]{3}-?[0-9]{3}$/',
                Rule::unique('bus', 'placa')->ignore($placa, 'placa')
            ],
            'modelo' => [
                'required',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+ [0-9]{4}$/'
            ],
            'capacidad_pasajeros' => 'required|integer|min:10',
            'kilometraje' => 'required|integer|min:0',
            'id_estado' => 'required|exists:estado,id_estado',
            'linc_transito' => 'nullable|numeric',
            'numero_chasis' => 'nullable|string|max:17',
            'numero_motor' => 'nullable|string|max:14',
            'doc_propietario'    => 'nullable|numeric',
            'nombre_propietario' => 'required|string|max:100',
            'telefono'           => 'nullable|string|max:20',
            'correo'             => 'nullable|email|max:100',
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
