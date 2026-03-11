<?php

namespace App\Http\Controllers\GestorSetp;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Empresa;
use App\Models\Bus;
use App\Models\Documento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Panel principal del Gestor SETP.
     * Filtra todo por la ciudad del usuario autenticado.
     */
    public function index()
    {
        auth()->user()->load('ciudad');
        $gestor    = auth()->user();
        $ciudad    = $gestor->id_ciudad;

        // ── NITs de empresas de transporte urbano de la ciudad ────
        $nitsEmpresas = Empresa::where('id_ciudad', $ciudad)
                               ->where('id_tipo_empresa', 1)  // Transporte Urbano
                               ->where('id_estado', 1)
                               ->pluck('NIT');

        // ── Placas de buses activos de esas empresas ──────────────
        $placasBuses = Bus::whereIn('NIT', $nitsEmpresas)
                          ->where('id_estado', 1)
                          ->pluck('placa');

        // ── Estadísticas principales ──────────────────────────────
        $totalRutas     = Ruta::where('id_ciudad', $ciudad)->count();
        $totalEmpresas  = $nitsEmpresas->count();
        $totalBuses     = $placasBuses->count();

        // Buses con algún documento vencido o por vencer (≤ 30 días)
        $limiteFecha    = Carbon::now()->addDays(30);
        $placasConProblemas = Documento::whereIn('placa', $placasBuses)
                                       ->where('fecha_vencimiento', '<=', $limiteFecha)
                                       ->distinct('placa')
                                       ->pluck('placa');

        $busesDocsPendientes = $placasConProblemas->count();

        // Documentos ya vencidos
        $docsVencidos = Documento::whereIn('placa', $placasBuses)
                                  ->where('fecha_vencimiento', '<', Carbon::now())
                                  ->count();

        // ── Alertas de documentación (max 8 para el dashboard) ────
        $docsAlerta = Documento::with(['tipoDocumento', 'bus.empresa'])
                               ->whereIn('placa', $placasBuses)
                               ->where('fecha_vencimiento', '<=', $limiteFecha)
                               ->orderBy('fecha_vencimiento')
                               ->take(8)
                               ->get();

        $alertasDocumentos = $docsAlerta->map(function ($doc) {
            $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
            return [
                'placa'            => $doc->placa,
                'nombre_doc'       => $doc->tipoDocumento->nombre ?? $doc->nombre,
                'empresa'          => $doc->bus->empresa->nombre_empresa ?? '—',
                'tipo'             => $dias < 0 ? 'vencido' : 'por_vencer',
                'fecha_vencimiento'=> $doc->fecha_vencimiento,
                'dias'             => $dias,
            ];
        })->toArray();

        // ── Últimas 5 rutas registradas ───────────────────────────
        $rutasRecientes = Ruta::with(['barrioOrigen', 'barrioDestino'])
                              ->where('id_ciudad', $ciudad)
                              ->orderByDesc('id_ruta')
                              ->take(5)
                              ->get();

        return view('gestor-setp.dashboard', compact(
            'totalRutas',
            'totalEmpresas',
            'totalBuses',
            'busesDocsPendientes',
            'docsVencidos',
            'alertasDocumentos',
            'rutasRecientes'
        ));
    }
}
