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
            'fecha.date'       => 'La fecha ingresada no es válida.',
            'id_estado.required' => 'El estado es obligatorio.',
            'id_estado.exists' => 'El estado seleccionado no es válido.'
        ];
    }

    /**
     * Reglas de validación adicionales.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->fecha) return;
            
            $fechaObj = \Carbon\Carbon::parse($this->fecha);
            $user = auth()->user();
            $nit = $user ? $user->getActiveNit() : null;

            // Identificar el ID del viaje actual para ignorarlo en ediciones
            // Buscamos en el parámetro de la ruta 'asignacion'
            $viajeId = $this->route('asignacion');
            if (is_object($viajeId)) {
                $viajeId = $viajeId->id_viaje;
            }

            // 1. No permitir fechas pasadas (solo creación)
            if ($fechaObj->isPast() && !$this->isMethod('PUT')) {
                 $validator->errors()->add('fecha', 'No se permiten asignaciones con fechas o horas pasadas.');
            }

            // Rango de turno de 8 horas
            $inicioConflicto = $fechaObj->copy()->subHours(8);
            $finConflicto = $fechaObj->copy()->addHours(8);

            // 2. Validación de conflictos para el BUS (dentro de la misma empresa)
            $conflictBus = \App\Models\Viaje::where('placa', $this->placa)
                ->where('fecha', '>', $inicioConflicto)
                ->where('fecha', '<', $finConflicto)
                ->whereHas('bus', function($q) use ($nit) {
                    $q->where('NIT', $nit);
                })
                ->when($viajeId, function($q) use ($viajeId) {
                    $q->where('id_viaje', '!=', $viajeId);
                })
                ->exists();
            
            if ($conflictBus) {
                $validator->errors()->add('placa', 'Este vehículo ya tiene una ruta asignada que se cruza con este horario (rango de 8h).');
            }

            // 3. Validación de conflictos para el CONDUCTOR (dentro de la misma empresa)
            if ($this->doc_us) {
                $conflictConductor = \App\Models\Viaje::where('doc_us', $this->doc_us)
                    ->where('fecha', '>', $inicioConflicto)
                    ->where('fecha', '<', $finConflicto)
                    ->when($viajeId, function($q) use ($viajeId) {
                        $q->where('id_viaje', '!=', $viajeId);
                    })
                    ->exists();

                if ($conflictConductor) {
                    $validator->errors()->add('doc_us', 'Este conductor ya tiene un viaje asignado que se solapa con este turno de 8 horas.');
                }
            }
        });
    }
}
