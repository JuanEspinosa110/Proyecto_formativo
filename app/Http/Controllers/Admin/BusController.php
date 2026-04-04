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
        
        if ($request->ajax()) {
            return view('admin.buses.partials.table', compact('buses', 'estados'));
        }

        return view('admin.buses.index', compact('buses', 'estados'));
    }

    /**
     * Guardar nuevo bus
     */
    public function store(BusRequest $request)
    {
        $this->busService->storeBus($request->validated());
        
        if (auth()->user()->id_tipo_usuario == 4) {
            return redirect()->back()->with('success', 'Vehículo registrado correctamente.');
        }
        
        return redirect()->route('admin.buses.index')
            ->with('success', 'Registro creado correctamente');
    }

    /**
     * Actualizar bus
     */
    public function update(BusRequest $request, Bus $bus)
    {
        try {
            $this->busService->updateBus($bus, $request->validated());
            
            if (auth()->user()->id_tipo_usuario == 4) {
                return redirect()->back()->with('success', 'Vehículo actualizado correctamente.');
            }
            
            return redirect()->route('admin.buses.index')
                ->with('success', 'Registro actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar bus
     */
    public function destroy(Bus $bus)
    {
        $this->busService->deleteBus($bus);
        
        if (auth()->user()->id_tipo_usuario == 4) {
            return redirect()->back()->with('success', 'Vehículo eliminado correctamente.');
        }
        
        return redirect()->route('admin.buses.index')
            ->with('success', 'Registro eliminado correctamente');
    }

    /**
     * Exportar a Excel o PDF con filtros activos
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        return $this->busService->export($request, $format);
    }

    /**
     * Obtener gastos del vehículo (AJAX)
     */
    public function getGastos($placa)
    {
        $user = auth()->user();
        $nit = $user->NIT ?? null;

        $busExists = Bus::where('placa', $placa)->where('NIT', $nit)->exists();

        if (!$busExists) {
            return response()->json(['error' => 'Vehículo no encontrado o sin acceso'], 404);
        }

        $gastos = \App\Models\Gasto::where('placa', $placa)->orderBy('fecha', 'desc')->get();
        return response()->json($gastos);
    }

    /**
     * Obtener detalles completos para ficha técnica (AJAX)
     */
    public function show($placa)
    {
        $detalles = $this->busService->getBusDetails($placa);
        return response()->json($detalles);
    }

    /**
     * Obtener datos del propietario por documento (AJAX)
     */
    public function getPropietario($doc_propietario)
    {
        $datos = $this->busService->getOwnerData($doc_propietario);
        return response()->json($datos);
    }

    /**
     * Devuelve el historial completo (bóveda) de documentos para un vehículo.
     */
    public function historialDocumental($placa)
    {
        $user = auth()->user();
        $nit = $user->NIT ?? null;

        $bus = Bus::where('placa', $placa)->where('NIT', $nit)->first();
        if (!$bus) {
            return response()->json(['error' => 'Vehículo no encontrado o sin acceso'], 404);
        }

        $documentos = \App\Models\Documento::with('tipoDocumento')
            ->where('placa', $placa)
            ->orderBy('id_estado', 'asc') // 1 primero
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($doc) {
                return [
                    'id_documento' => $doc->id_documento,
                    'nombre' => $doc->nombre ?? 'Sin nombre',
                    'tipo_nombre' => $doc->tipoDocumento->nombre ?? 'Documento',
                    'fecha_carga' => $doc->created_at->format('d/m/Y'),
                    'fecha_vencimiento' => $doc->fecha_vencimiento->format('d/m/Y'),
                    'status_vigencia' => $doc->estado_expiracion,
                    'status_color' => $doc->status_color,
                    'es_archivado' => $doc->id_estado == 2,
                    'url_archivo' => $doc->archivo ? asset('storage/' . $doc->archivo) : null
                ];
            });

        // Agrupar por tipo para renderizar secciones
        $grupos = [];
        foreach ($documentos as $doc) {
            $grupos[$doc['tipo_nombre']][] = $doc;
        }

        return response()->json([
            'placa' => $placa,
            'grupos' => $grupos
        ]);
    }
}
