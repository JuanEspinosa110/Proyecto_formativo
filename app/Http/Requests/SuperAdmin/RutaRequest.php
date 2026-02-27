<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class RutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idRuta = $this->route('ruta');
        if (is_object($idRuta)) {
            $idRuta = $idRuta->id_ruta;
        }

        $nit = $this->input('NIT');

        return [
            'NIT' => 'required|exists:empresa,NIT',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'id_barrio_origen' => 'required|exists:barrio,id_barrio',
            'id_barrio_destino' => 'required|exists:barrio,id_barrio|different:id_barrio_origen',
            'origen' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) use ($idRuta, $nit) {
                    if (!$nit) return; 
                    
                    $exists = DB::table('ruta')
                        ->where('NIT', $nit)
                        ->where('origen', strtoupper(trim($value)))
                        ->where('destino', strtoupper(trim($this->destino)))
                        ->where('id_ciudad', $this->id_ciudad)
                        ->when($idRuta, function ($q) use ($idRuta) {
                            return $q->where('id_ruta', '!=', $idRuta);
                        })
                        ->exists();

                    if ($exists) {
                        $fail("Ya existe una ruta con el mismo origen, destino y ciudad para esta empresa.");
                    }
                }
            ],
            'destino' => 'required|string|max:150',
            'id_estado' => 'required|exists:estado,id_estado',
        ];
    }

    public function messages(): array
    {
        return [
            'NIT.required' => 'La empresa es obligatoria.',
            'NIT.exists' => 'La empresa seleccionada no es válida.',
            'id_ciudad.required' => 'La ciudad es obligatoria.',
            'id_ciudad.exists' => 'La ciudad seleccionada no es válida.',
            'id_barrio_origen.required' => 'El barrio de origen es obligatorio.',
            'id_barrio_origen.exists' => 'El barrio de origen no es válido.',
            'id_barrio_destino.required' => 'El barrio de destino es obligatorio.',
            'id_barrio_destino.different' => 'El barrio de destino debe ser diferente al de origen.',
            'origen.required' => 'El punto de origen es obligatorio.',
            'destino.required' => 'El punto de destino es obligatorio.',
            'id_estado.required' => 'El estado es obligatorio.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'origen' => $this->origen ? strtoupper(trim($this->origen)) : null,
            'destino' => $this->destino ? strtoupper(trim($this->destino)) : null,
        ]);
    }
}
