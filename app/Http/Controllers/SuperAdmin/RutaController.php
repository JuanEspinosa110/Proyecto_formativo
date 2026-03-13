<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
// use App\Models\Empresa;
use App\Services\RutaService;
use App\Http\Requests\Admin\RutaRequest as AdminRutaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\SuperAdmin\RutaRequest;

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

        return view('superadmin.rutas.index', compact('rutas', 'estados', 'ciudades', 'barrios'));
    }

    /**
     * Guardar nueva ruta
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_ruta'       => 'required|numeric',
            'id_ciudad'         => 'required|exists:ciudad,id_ciudad',
            'id_barrio_origen'  => 'required|exists:barrio,id_barrio',
            'id_barrio_destino' => 'required|exists:barrio,id_barrio|different:id_barrio_origen',
            'id_estado'         => 'required|numeric',
        ], [
            'codigo_ruta.required' => 'El código de la ruta es obligatorio.',
            'id_barrio_destino.different' => 'El barrio de destino debe ser diferente al de origen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Verificar si ya existe la ruta original
        $existeOriginal = \App\Models\Ruta::where('codigo_ruta', $data['codigo_ruta'])
            ->where('id_ciudad', $data['id_ciudad'])
            ->where('id_barrio_origen', $data['id_barrio_origen'])
            ->where('id_barrio_destino', $data['id_barrio_destino'])
            ->exists();

        if (!$existeOriginal) {
            $this->rutaService->storeRuta($data);
        }

        // Verificar si ya existe la ruta inversa
        $existeInversa = \App\Models\Ruta::where('codigo_ruta', $data['codigo_ruta'])
            ->where('id_ciudad', $data['id_ciudad'])
            ->where('id_barrio_origen', $data['id_barrio_destino'])
            ->where('id_barrio_destino', $data['id_barrio_origen'])
            ->exists();

        if (!$existeInversa) {
            $this->rutaService->storeRuta([
                'codigo_ruta'       => $data['codigo_ruta'],
                'id_ciudad'         => $data['id_ciudad'],
                'id_barrio_origen'  => $data['id_barrio_destino'],
                'id_barrio_destino' => $data['id_barrio_origen'],
                'id_estado'         => $data['id_estado'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ruta y su inversa creadas con éxito.'
        ]);
    }

    /**
     * Actualizar ruta existente
     */
    public function update(AdminRutaRequest $request, Ruta $ruta)
    {
        $this->rutaService->updateRuta($ruta, $request->validated());

        return response()->json(['message' => 'La ruta ha sido actualizada correctamente.']);
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
