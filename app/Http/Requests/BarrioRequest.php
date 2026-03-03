<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarrioRequest extends FormRequest
{
    /**
     * Prepare the data for validation (normalization).
     */
    protected function prepareForValidation()
    {
        if ($this->has('nombre')) {
            $this->merge([
                'nombre' => preg_replace('/\s+/', ' ', trim($this->nombre)),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]+$/u',
                Rule::unique('barrio', 'nombre')
                    ->where('id_ciudad', $this->id_ciudad)
                    ->ignore($id, 'id_barrio'),
            ],
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del barrio es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos (solo letras, números y espacios).',
            'nombre.unique' => 'Ya existe un barrio con este nombre en la ciudad seleccionada.',
            'id_ciudad.required' => 'La ciudad es obligatoria.',
            'id_ciudad.exists' => 'La ciudad seleccionada no es válida.',
        ];
    }
}
