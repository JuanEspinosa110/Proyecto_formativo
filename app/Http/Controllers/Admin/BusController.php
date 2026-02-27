<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Services\BusService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\BusRequest;

class BusController extends Controller
{
    protected $busService;

    public function __construct(BusService $busService)
    {
        $this->busService = $busService;
    }

    /**
     * Listado de buses
     */
    public function index(Request $request)
    {
        $buses = $this->busService->getBuses($request);
        $estados = $this->busService->getEstadosOperativos();
        
        return view('admin.buses.index', compact('buses', 'estados'));
    }

    /**
     * Guardar nuevo bus
     */
    public function store(BusRequest $request)
    {
        $this->busService->storeBus($request->validated());
        
        if ($request->ajax()) {
            return response()->json(['message' => 'El bus ha sido creado exitosamente.']);
        }

        return redirect()->route('admin.buses.index')
            ->with('success', 'El bus ha sido creado exitosamente.');
    }

    /**
     * Actualizar bus
     */
    public function update(BusRequest $request, Bus $bus)
    {
        $this->busService->updateBus($bus, $request->validated());
        
        if ($request->ajax()) {
            return response()->json(['message' => 'El bus ha sido actualizado correctamente.']);
        }

        return redirect()->route('admin.buses.index')
            ->with('success', 'El bus ha sido actualizado correctamente.');
    }

    /**
     * Exportar a Excel con filtros activos
     */
    public function export(Request $request)
    {
        return $this->busService->exportExcel($request);
    }
}
