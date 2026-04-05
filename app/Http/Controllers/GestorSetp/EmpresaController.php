<?php

namespace App\Http\Controllers\GestorSetp;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Bus;
use App\Models\Asignacion;
use App\Models\ConcesionRuta;
use App\Models\Documento;
use App\Models\Ruta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    // id_tipo_empresa = 1 → Transporte Urbano
    private const TIPO_EMPRESA_TRANSPORTE = 1;
    // id_tipo_asignacion = 3 → BUS A RUTA
    private const TIPO_ASIGNACION_RUTA = 3;
    // Días de margen para documentos "por vencer"
    private const DIAS_ALERTA = 30;

    // ── index ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $ciudad = auth()->user()->id_ciudad;

        $query = Empresa::with('ciudad')
                        ->where('id_ciudad', $ciudad)
                        ->where('id_tipo_empresa', self::TIPO_EMPRESA_TRANSPORTE);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('nombre_empresa', 'like', "%{$q}%")
                   ->orWhere('NIT', 'like', "%{$q}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        // Conteos en memoria tras paginar para evitar subqueries complejos
        $empresas = $query->orderBy('nombre_empresa')->paginate(12);

        // Calcular rutas de "Uso Público" (sin ninguna empresa asignada actualmente)
        $rutasLibresCount = Ruta::where('id_ciudad', $ciudad)
            ->where('id_estado', 1)
            ->whereDoesntHave('concesiones', function ($q) {
                $q->where('id_estado', 1);
            })->count();

        // Añadir conteos y documentos pendientes a cada empresa
        $empresas->getCollection()->transform(function ($empresa) use ($rutasLibresCount) {
            $placas = Bus::where('NIT', $empresa->NIT)->pluck('placa');

            $empresa->buses_count = $placas->count();

            $rutasPropias = ConcesionRuta::where('NIT', $empresa->NIT)
                ->where('id_estado', 1)
                ->count();
            
            $empresa->rutas_count = $rutasPropias + $rutasLibresCount;

            $empresa->docs_pendientes = $placas->isNotEmpty()
                ? Documento::whereIn('placa', $placas)
                            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(self::DIAS_ALERTA))
                            ->count()
                : 0;

            return $empresa;
        });

        return view('gestor-setp.empresas.index', compact('empresas'));
    }

    // ── show ──────────────────────────────────────────────────────
    public function show($nit)
    {
        // Solo permitir ver empresas de la ciudad del gestor
        $empresa = Empresa::with('ciudad')
                          ->where('NIT', $nit)
                          ->where('id_ciudad', auth()->user()->id_ciudad)
                          ->where('id_tipo_empresa', self::TIPO_EMPRESA_TRANSPORTE)
                          ->firstOrFail();

        // Buses de la empresa con conteo de docs pendientes
        $buses = Bus::where('NIT', $empresa->NIT)
                    ->orderBy('placa')
                    ->get()
                    ->map(function ($bus) {
                        $bus->docs_pendientes = Documento::where('placa', $bus->placa)
                            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(self::DIAS_ALERTA))
                            ->count();
                        return $bus;
                    });

        // Rutas operativas (Asignadas a ella O de Uso Público)
        $rutas = Ruta::with(['barrioOrigen', 'barrioDestino'])
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->where('id_estado', 1)
            ->where(function($q) use ($empresa) {
                $q->whereHas('concesiones', function($sq) use ($empresa) {
                    $sq->where('NIT', $empresa->NIT)->where('id_estado', 1);
                })
                ->orDoesntHave('concesiones');
            })
            ->paginate(10);

        // Total de documentos pendientes de todos los buses
        $placas = $buses->pluck('placa');
        $docsPendientes = $placas->isNotEmpty()
            ? Documento::whereIn('placa', $placas)
                        ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(self::DIAS_ALERTA))
                        ->count()
            : 0;

        return view('gestor-setp.empresas.show', compact('empresa', 'buses', 'rutas', 'docsPendientes'));
    }
}
