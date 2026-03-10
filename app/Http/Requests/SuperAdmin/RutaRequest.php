<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Barrio;

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

        $idCiudad = $this->input('id_ciudad');

        return [

            'codigo_ruta' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
                'unique:ruta,codigo_ruta,' . $idRuta . ',id_ruta',
                'integer',
                'min:1',
                'max:99',
                function ($attribute, $value, $fail) use ($idRuta) {
                    $exists = DB::table('ruta')
                        ->where('codigo_ruta', $value)
                        ->when($idRuta, function ($q) use ($idRuta) {
                            return $q->where('id_ruta', '!=', $idRuta);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('Este código de ruta ya está registrado.');
                    }
                }
            ],

            'id_ciudad' => [
                'required',
                'string',
                'size:6',
                'regex:/^[0-9]+$/',
                'exists:ciudad,id_ciudad'
            ],
            'id_barrio_origen' => [
                'required',
                'integer',
                'min:1',
                'regex:/^[0-9]+$/',
                'exists:barrio,id_barrio',
                function ($attribute, $value, $fail) use ($idCiudad, $idRuta) {
                    if ($idCiudad) {
                        $barrio = Barrio::where('id_barrio', $value)->first();
                        if ($barrio && $barrio->id_ciudad !== $idCiudad) {
                            $fail('El barrio de origen debe pertenecer a la ciudad seleccionada.');
                        }
                        
                        // Check for duplicate route
                        $exists = DB::table('ruta')
                            ->where('id_ciudad', $idCiudad)
                            ->where('id_barrio_origen', $value)
                            ->where('id_barrio_destino', $this->id_barrio_destino)
                            ->when($idRuta, function ($q) use ($idRuta) {
                                return $q->where('id_ruta', '!=', $idRuta);
                            })
                            ->exists();
                        
                        if ($exists) {
                            $fail("Esta ruta (mismo origen y destino en esta ciudad) ya está registrada.");
                        }
                    }
                }
            ],
            'id_barrio_destino' => [
                'required',
                'integer',
                'min:1',
                'regex:/^[0-9]+$/',
                'exists:barrio,id_barrio',
                'different:id_barrio_origen',
                function ($attribute, $value, $fail) use ($idCiudad) {
                    if ($idCiudad) {
                        $barrio = Barrio::where('id_barrio', $value)->first();
                        if ($barrio && $barrio->id_ciudad !== $idCiudad) {
                            $fail('El barrio de destino debe pertenecer a la ciudad seleccionada.');
                        }
                    }
                }
            ],
            'id_estado' => 'required|exists:estado,id_estado',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_ruta.required' => 'El código de ruta es obligatorio.',
            'codigo_ruta.integer' => 'El código debe ser numérico.',
            'codigo_ruta.regex' => 'El código debe ser numérico y no puede iniciar en 0.',
            'codigo_ruta.unique' => 'Este código de ruta ya está registrado.',
            'codigo_ruta.max' => 'El código de ruta no puede ser mayor a 99.',
            'codigo_ruta.min' => 'El código debe ser mayor a 0.',
            'id_ciudad.required' => 'La ciudad es obligatoria.',
            'id_ciudad.size' => 'La ciudad debe tener exactamente 6 caracteres.',
            'id_ciudad.regex' => 'La ciudad solo puede contener números.',
            'id_ciudad.exists' => 'La ciudad seleccionada no es válida.',
            'codigo_ruta.required' => 'El código de ruta es obligatorio.',
            'codigo_ruta.regex' => 'El código de ruta solo permite números (0-9) sin caracteres especiales.',
            'codigo_ruta.integer' => 'El código de ruta debe ser un número válido.',
            'codigo_ruta.between' => 'El código de ruta debe estar entre 1 y 90.',
            'id_barrio_origen.required' => 'El barrio de origen es obligatorio.',
            'id_barrio_origen.integer' => 'El barrio de origen debe ser un número entero.',
            'id_barrio_origen.min' => 'El barrio de origen no es válido.',
            'id_barrio_origen.regex' => 'El barrio de origen solo puede contener números.',
            'id_barrio_origen.exists' => 'El barrio de origen no existe.',
            'id_barrio_destino.required' => 'El barrio de destino es obligatorio.',
            'id_barrio_destino.different' => 'El barrio de destino debe ser diferente al de origen.',
            'id_barrio_destino.regex' => 'El barrio de destino solo puede contener números.',
            'id_barrio_destino.exists' => 'El barrio de destino no existe.',
            'id_estado.required' => 'El estado es obligatorio.',
            'id_estado.exists' => 'El estado seleccionado no es válido.',
        ];
    }
}
