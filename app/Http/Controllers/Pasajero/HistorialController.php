<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Recarga;
use App\Models\VentaViaje;
use App\Models\TitularidadTarjeta;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    private function tarjeta()
    {
        return TitularidadTarjeta::with('tarjeta')
            ->where('doc_usuario', auth()->user()->doc_usuario)
            ->where('id_estado', 1)
            ->latest('fecha_inicio')
            ->firstOrFail()
            ->tarjeta;
    }

    // ── index: ambos historials ────────────────────────────────
    public function index(Request $request)
    {
        $tarjeta = $this->tarjeta();
        $tab = $request->get('tab', 'viajes');

        $viajes = VentaViaje::with(['viaje.ruta.barrioOrigen', 'viaje.ruta.barrioDestino'])
            ->where('id_tarjeta', $tarjeta->id_tarjeta)
            ->orderByDesc('fecha')
            ->paginate(15, ['*'], 'pv');

        $recargas = Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'pr');

        $totalGastado  = VentaViaje::where('id_tarjeta', $tarjeta->id_tarjeta)->sum('valor');
        $totalRecargado = Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)->sum('monto');

        return view('pasajero.historial.index', compact(
            'tarjeta', 'viajes', 'recargas', 'tab', 'totalGastado', 'totalRecargado'
        ));
    }

    // ── viajes (alias con tab fijo) ────────────────────────────
    public function viajes(Request $request)
    {
        return redirect()->route('pasajero.historial.index', ['tab' => 'viajes']);
    }

    // ── recargas (alias con tab fijo) ──────────────────────────
    public function recargas(Request $request)
    {
        return redirect()->route('pasajero.historial.index', ['tab' => 'recargas']);
    }
}
