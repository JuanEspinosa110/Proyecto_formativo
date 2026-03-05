<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nit = $this->route('empresa'); // For update

        return [
            // EMPRESA
            'NIT' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'digits:10',
                Rule::unique('empresa', 'NIT')->ignore($nit, 'NIT')
            ],
            'nombre_empresa' => ['required', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'telefono_empresa' => 'required|digits_between:7,15',
            'correo_corporativo' => 'required|email',

            // REPRESENTANTE
            'doc_representante' => 'required|digits_between:7,10',
            'primer_nombre_repre' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
            'segundo_nombre_repre' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
            'primer_apellido_repre' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
            'segundo_apellido_repre' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
            'telefono_representante' => 'required|digits_between:7,15',
            'correo_representante' => 'required|email',

            // UBICACIÓN
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'id_estado' => 'nullable|exists:estado,id_estado',
        ];
    }

    public function messages(): array
    {
        return [
            'NIT.required' => 'El NIT es obligatorio.',
            'NIT.digits' => 'El NIT debe tener exactamente 10 dígitos.',
            'NIT.unique' => 'Ya existe una empresa registrada con este NIT.',
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
            'nombre_empresa.regex' => 'El nombre solo puede contener letras y espacios.',
            'id_ciudad.required' => 'Debe seleccionar una ciudad.',
        ];
    }
}
