<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Services\SuperAdmin\Configuracion\CiudadService;

class CiudadController extends Controller
{
    // Inyectamos el servicio de Ciudad para manejar la lógica de negocio
    protected $ciudadService;

    public function __construct(CiudadService $ciudadService)
    {
        $this->ciudadService = $ciudadService;
    }

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

        return view(
            'superadmin.configuracion.ciudades.index',
            compact('ciudades', 'departamentos')
        );
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

        return redirect()->back()->with(
            'success',
            'Ciudad creada correctamente con el código: ' . $id_ciudad
        );
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

        return redirect()->back()->with(
            'success',
            'Ciudad actualizada correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR DEPARTAMENTO (Desde mismo módulo)
    |--------------------------------------------------------------------------
    */

    public function storeDepartamentoTEST(Request $request)
    {
        dd($request->all()); // Esto detendrá el programa y mostrará qué datos llegan
        // 1. Validar que ambos campos lleguen desde el modal
        $validated = $request->validate([
            'id_departamento' => 'required|string|size:2|unique:departamento,id_departamento',
            'nombre_departamento' => 'required|string|max:100|unique:departamento,nombre_departamento'
        ], [
            'id_departamento.required' => 'El código del departamento es obligatorio.',
            'id_departamento.size' => 'El código debe tener exactamente 2 dígitos.',
            'id_departamento.unique' => 'Este código ya existe.',
            'nombre_departamento.required' => 'El nombre del departamento es obligatorio.',
        ]);

        // 2. Pasar TODO el arreglo $validated (que ya incluye el ID) al Service
        $this->ciudadService->storeDepartamento($validated);

        return redirect()->back()->with('success', 'Departamento creado correctamente.');
    }
}
