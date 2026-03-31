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

            // 0. Validación de Autorización de la RUTA para esta Empresa
            if ($nit && $this->id_ruta) {
                $autorizado = \App\Models\ConcesionRuta::where('id_ruta', $this->id_ruta)
                    ->where('NIT', $nit)
                    ->where('id_estado', 1)
                    ->exists();
                
                // Si no tiene concesión específica, ver si es de uso público (sin ninguna concesión)
                if (!$autorizado) {
                    $esPublica = !\App\Models\ConcesionRuta::where('id_ruta', $this->id_ruta)
                        ->where('id_estado', 1)
                        ->exists();
                    
                    if (!$esPublica) {
                        $validator->errors()->add('id_ruta', 'Su empresa no tiene una concesión activa para operar esta ruta y la misma ya está asignada a otras empresas.');
                        return;
                    }
                }
            }

            // 1. No permitir fechas de días pasados (permitimos registrar lo ocurrido hoy)
            if ($fechaObj->isBefore(now()->startOfDay()) && !$this->isMethod('PUT')) {
                 $validator->errors()->add('fecha', 'No se permiten asignaciones de días anteriores al actual.');
            }

            // Identificar el ID del viaje actual para ignorarlo en ediciones
            // En Route::resource('asignaciones', ...) el parámetro es 'asignacione'
            $viajeId = $this->route('asignacione') ?? $this->route('asignacion');
            if (is_object($viajeId)) {
                $viajeId = $viajeId->id_viaje ?? $viajeId->getKey();
            }

            // Rango de turno de 8 horas para validaciones de solapamiento
            $proposedStart = $fechaObj->toDateTimeString();
            $proposedEnd = $fechaObj->copy()->addHours(8)->toDateTimeString();

            // 0. Validación de operabilidad del BUS (Documentos Vigentes)
            $busModel = \App\Models\Bus::where('placa', $this->placa)->first();
            if ($busModel && !$busModel->isOperable()) {
                $validator->errors()->add('placa', 'Este vehículo no se puede asignar porque no posee todos sus documentos vigentes y aprobados.');
                return;
            }

            // 2. Validación de conflictos para el BUS (dentro de la misma empresa)
            // Lógica de solapamiento: (InicioA < FinB) AND (FinA > InicioB)
            $conflictBus = \App\Models\Viaje::where('placa', $this->placa)
                ->where(function($q) use ($proposedStart, $proposedEnd) {
                    $q->where('fecha', '<', $proposedEnd)
                      ->whereRaw('DATE_ADD(fecha, INTERVAL 8 HOUR) > ?', [$proposedStart]);
                })
                ->whereHas('bus', function($q) use ($nit) {
                    if ($nit) $q->where('NIT', $nit);
                })
                ->when($viajeId, function($q) use ($viajeId) {
                    $q->where('id_viaje', '!=', $viajeId);
                })
                ->exists();
            
            if ($conflictBus) {
                $validator->errors()->add('placa', 'Este vehículo ya tiene un turno de 8 horas que se cruza con el horario solicitado.');
            }

            // 3. Validación de conflictos para el CONDUCTOR (Sistema global)
            if ($this->doc_us) {
                // Bloquear asignación si el usuario es un Administrador
                $esAdmin = \App\Models\Usuario::where('doc_usuario', $this->doc_us)
                    ->whereHas('tipoUsuario', function($q) {
                        $q->where('nombre_tipo', 'like', '%admin%');
                    })
                    ->exists();
                
                if ($esAdmin) {
                    $validator->errors()->add('doc_us', 'No se puede asignar un viaje a un usuario con rol de Administrador.');
                    return; // Detener validaciones posteriores para este usuario
                }

                $conflictConductor = \App\Models\Viaje::where('doc_us', $this->doc_us)
                    ->where(function($q) use ($proposedStart, $proposedEnd) {
                        $q->where('fecha', '<', $proposedEnd)
                          ->whereRaw('DATE_ADD(fecha, INTERVAL 8 HOUR) > ?', [$proposedStart]);
                    })
                    ->when($viajeId, function($q) use ($viajeId) {
                        $q->where('id_viaje', '!=', $viajeId);
                    })
                    ->exists();

                if ($conflictConductor) {
                    $validator->errors()->add('doc_us', 'Este conductor ya tiene un turno asignado que se solapa con este horario (8h).');
                }
            }

            // 4. Nueva Validación: Una asignación por día para el CONDUCTOR (Jornada de 8h)
            if ($this->doc_us && $this->fecha) {
                try {
                    $fechaSoloDia = \Carbon\Carbon::parse($this->fecha)->toDateString();
                    
                    $alreadyAssignedToday = \App\Models\Viaje::where('doc_us', $this->doc_us)
                        ->whereDate('fecha', $fechaSoloDia)
                        ->when($viajeId, function($q) use ($viajeId) {
                            $q->where('id_viaje', '!=', $viajeId);
                        })
                        ->exists();

                    if ($alreadyAssignedToday) {
                        $validator->errors()->add('doc_us', 'Este conductor ya tiene una jornada laboral asignada para esta fecha y no puede ser asignado nuevamente.');
                    }
                } catch (\Exception $e) {
                    // Si el formato de fecha es inválido, ya lo captura la regla 'date' en rules()
                }
            }
        });
    }
}
