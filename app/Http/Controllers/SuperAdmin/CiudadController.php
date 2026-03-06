<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\Departamento;

class CiudadController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Vista principal unificada
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Ciudad::with('departamento')
            ->orderBy('nombre_city');

        // Buscador por ciudad o departamento
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_city', 'LIKE', "%$buscar%")
                  ->orWhereHas('departamento', function ($sub) use ($buscar) {
                      $sub->where('nombre_departamento', 'LIKE', "%$buscar%");
                  });
            });
        }

        $ciudades = $query->paginate(5)->withQueryString();

        $departamentos = Departamento::orderBy('nombre_departamento')->get();

        return view('superadmin.configuracion.ciudades.index',
            compact('ciudades', 'departamentos'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR CIUDAD
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_city' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $exists = Ciudad::whereRaw('LOWER(nombre_city) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail('La ciudad "' . $value . '" ya se encuentra registrada en el sistema.');
                    }
                }
            ],
            'id_departamento' => 'required|exists:departamento,id_departamento'
        ], [
            'nombre_city.required' => 'El nombre de la ciudad es obligatorio.',
            'nombre_city.max' => 'La ciudad no puede superar los 100 caracteres.',
            'id_departamento.required' => 'Debe seleccionar un departamento.',
            'id_departamento.exists' => 'El departamento seleccionado no es válido.'
        ]);

        // Generar ID único de 5 dígitos
        do {
            $id_ciudad = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Ciudad::where('id_ciudad', $id_ciudad)->exists());

        Ciudad::create([
            'id_ciudad' => $id_ciudad,
            'nombre_city' => strtoupper($request->nombre_city),
            'id_departamento' => $request->id_departamento
        ]);

        return redirect()->back()->with('success',
            'Ciudad creada correctamente con el código: ' . $id_ciudad);
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR CIUDAD
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $ciudad = Ciudad::findOrFail($id);

        $request->validate([
            'nombre_city' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = Ciudad::whereRaw('LOWER(nombre_city) = ?', [strtolower($value)])
                        ->where('id_ciudad', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('La ciudad "' . $value . '" ya existe registrada en el sistema.');
                    }
                }
            ],
            'id_departamento' => 'required|exists:departamento,id_departamento'
        ], [
            'nombre_city.required' => 'El nombre de la ciudad es obligatorio.',
            'nombre_city.max' => 'La ciudad no puede superar los 100 caracteres.',
            'id_departamento.required' => 'Debe seleccionar un departamento.',
        ]);

        $ciudad->update([
            'nombre_city' => strtoupper($request->nombre_city),
            'id_departamento' => $request->id_departamento
        ]);

        return redirect()->back()->with('success',
            'Ciudad actualizada correctamente.');
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR DEPARTAMENTO (Desde mismo módulo)
    |--------------------------------------------------------------------------
    */
    public function storeDepartamento(Request $request)
    {
        $request->validate([
            'nombre_departamento' => 'required|string|max:100|unique:departamento,nombre_departamento'
        ], [
            'nombre_departamento.required' => 'El nombre del departamento es obligatorio.',
            'nombre_departamento.unique' => 'Este departamento ya existe.',
            'nombre_departamento.max' => 'Máximo 100 caracteres permitidos.'
        ]);

        Departamento::create([
            'nombre_departamento' => strtoupper($request->nombre_departamento)
        ]);

        return redirect()->back()->with('success',
            'Departamento creado correctamente.');
    }

    
}