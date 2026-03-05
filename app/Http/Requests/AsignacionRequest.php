<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignacionRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtener las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            'placa'      => 'required|exists:bus,placa',
            'id_ruta'    => 'required|exists:ruta,id_ruta',
            'doc_us'     => 'required|exists:usuario,doc_usuario',
            'fecha'      => 'required|date',
            'id_estado'  => 'required|exists:estado,id_estado',
        ];
    }

    /**
     * Obtener los mensajes de error para las reglas definidas.
     */
    public function messages(): array
    {
        return [
            'placa.required'   => 'El bus es obligatorio.',
            'placa.exists'     => 'El bus seleccionado no es válido.',
            'id_ruta.required' => 'La ruta es obligatoria.',
            'id_ruta.exists'   => 'La ruta seleccionada no es válida.',
            'doc_us.required'  => 'El conductor es obligatorio.',
            'doc_us.exists'    => 'El conductor seleccionado no es válido.',
            'fecha.required'   => 'La fecha de asignación es obligatoria.',
            'fecha.date'       => 'La fecha ingredada no es válida.',
            'id_estado.required' => 'El estado es obligatorio.',
            'id_estado.exists' => 'El estado seleccionado no es válido.'
        ];
    }
}
