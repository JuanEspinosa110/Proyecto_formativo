<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Empresa;
use App\Services\RutaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $empresas = Empresa::orderBy('nombre_empresa')->get();
        $ciudades = \App\Models\Ciudad::orderBy('nombre_city')->get();
        $barrios = \App\Models\Barrio::orderBy('nombre')->get();

        return view('superadmin.rutas.index', compact('rutas', 'estados', 'empresas', 'ciudades', 'barrios'));
    }

    /**
     * Guardar nueva ruta
     */
    public function store(RutaRequest $request)
    {
        $this->rutaService->storeRuta($request->validated());

        return response()->json(['message' => 'La ruta ha sido creada exitosamente.']);
    }

    /**
     * Actualizar ruta existente
     */
    public function update(RutaRequest $request, Ruta $ruta)
    {
        $this->rutaService->updateRuta($ruta, $request->validated());

        return response()->json(['message' => 'La ruta ha sido actualizada correctamente.']);
    }

    /**
     * Exportar a Excel con filtros activos
     */
    public function export(Request $request)
    {
        return $this->rutaService->exportExcel($request);
    }
}
