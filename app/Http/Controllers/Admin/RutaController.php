<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Services\RutaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\RutaRequest;

class RutaController extends Controller
{
    protected $rutaService;

    public function __construct(RutaService $rutaService)
    {
        $this->rutaService = $rutaService;
    }

    /**
     * Listado de rutas
     */
    public function index(Request $request)
    {
        $rutas = $this->rutaService->getRutas($request);
        $estados = $this->rutaService->getEstadosOperativos();
        $ciudades = \App\Models\Ciudad::orderBy('nombre_city')->get();
        $barrios = \App\Models\Barrio::orderBy('nombre')->get();

        return view('admin.rutas.index', compact('rutas', 'estados', 'ciudades', 'barrios'));
    }

    /**
     * Guardar nueva ruta
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_ruta'       => 'required|numeric|unique:ruta,codigo_ruta',
            'id_ciudad'         => 'required|exists:ciudad,id_ciudad',
            'id_barrio_origen'  => 'required|exists:barrio,id_barrio',
            'id_barrio_destino' => 'required|exists:barrio,id_barrio|different:id_barrio_origen',
            'id_estado'         => 'required|numeric',
        ], [
            'codigo_ruta.required' => 'El código de la ruta es obligatorio.',
            'codigo_ruta.numeric' => 'El código debe ser numérico.',
            'codigo_ruta.unique'   => 'Este código de ruta ya está registrado.',
            'id_ciudad.required'   => 'La ciudad es obligatoria.',
            'id_barrio_origen.required' => 'El barrio de origen es obligatorio.',
            'id_barrio_destino.required' => 'El barrio de destino es obligatorio.',
            'id_barrio_destino.different' => 'El barrio de destino debe ser diferente al de origen.',
            'id_estado.required'   => 'El estado es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $this->rutaService->storeRuta($validator->validated());
        session()->flash('success', 'Registro creado correctamente');

        return response()->json([
            'success' => true,
            'message' => 'Registro creado correctamente'
        ]);
    }

    /**
     * Actualizar ruta existente
     */
    public function update(RutaRequest $request, Ruta $ruta)
    {
        $this->rutaService->updateRuta($ruta, $request->validated());
        session()->flash('success', 'Registro actualizado correctamente');

        return response()->json(['message' => 'Registro actualizado correctamente']);
    }

    /**
     * Obtener barrios por ciudad (AJAX)
     */
    public function getBarriosByCiudad($id_ciudad)
    {
        $barrios = \App\Models\Barrio::where('id_ciudad', $id_ciudad)
            ->orderBy('nombre')
            ->get(['id_barrio', 'nombre']);

        return response()->json($barrios);
    }

    /**
     * Exportar a Excel con filtros activos
     */
    public function export(Request $request)
    {
        return $this->rutaService->exportExcel($request);
    }
}
