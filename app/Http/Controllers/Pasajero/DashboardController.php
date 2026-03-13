<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\TitularidadTarjeta;
use App\Models\Recarga;
use App\Models\VentaViaje;
use App\Models\Viaje;
use App\Models\Ruta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Titularidad activa y su tarjeta
        $titularidad = TitularidadTarjeta::with('tarjeta')
            ->where('doc_usuario', $user->doc_usuario)
            ->where('id_estado', 1)
            ->latest('fecha_inicio')
            ->first();

        $tarjeta = $titularidad?->tarjeta;

        // ── Estadísticas ──────────────────────────────────────────
        $totalRecargas = $tarjeta
            ? Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)->count()
            : 0;

        $totalViajes = $tarjeta
            ? VentaViaje::where('id_tarjeta', $tarjeta->id_tarjeta)->count()
            : 0;

        $gastoMes = $tarjeta
            ? VentaViaje::where('id_tarjeta', $tarjeta->id_tarjeta)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('valor')
            : 0;

        // ── Últimos 5 movimientos (recargas + viajes combinados) ──
        $ultimasRecargas = $tarjeta
            ? Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(fn($r) => [
                    'tipo'   => 'recarga',
                    'desc'   => 'Recarga de saldo',
                    'monto'  => $r->monto,
                    'fecha'  => $r->created_at,
                ])
            : collect();

        $ultimosViajes = $tarjeta
            ? VentaViaje::with(['viaje.ruta.barrioOrigen', 'viaje.ruta.barrioDestino'])
                ->where('id_tarjeta', $tarjeta->id_tarjeta)
                ->orderByDesc('fecha')
                ->take(5)
                ->get()
                ->map(fn($v) => [
                    'tipo'   => 'viaje',
                    'desc'   => $v->viaje?->ruta
                        ? 'Ruta #' . $v->viaje->ruta->codigo_ruta
                        : 'Viaje',
                    'monto'  => $v->valor,
                    'fecha'  => $v->fecha,
                ])
            : collect();

        $movimientos = $ultimasRecargas->concat($ultimosViajes)
            ->sortByDesc('fecha')
            ->take(6)
            ->values();

        // ── Rutas de la ciudad del usuario ────────────────────────
        $rutasDisponibles = Ruta::where('id_ciudad', $user->id_ciudad)
            ->where('id_estado', 1)
            ->count();

        $viajesQuery = VentaViaje::with(['viaje.ruta.barrioOrigen', 'viaje.ruta.barrioDestino'])
            ->where('id_tarjeta', $tarjeta?->id_tarjeta);

        $viajesMes = (clone $viajesQuery)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->get();
            
        // Calculate total spent in the month using real data from the database
        $totalGastadoMes = $viajesMes->sum('valor');

        $viajes = $viajesQuery->orderBy('fecha', 'desc')->take(3)->get();
        // Usar la colección ya sacada
        $recargas = $tarjeta
            ? Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)
                ->orderByDesc('created_at')
                ->take(10)
                ->get()
            : collect();
            
        $totalRecargado = $tarjeta ? Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)->sum('monto') : 0;

        return view('pasajero.saldo.index', compact('tarjeta', 'titularidad', 'recargas', 'totalRecargado', 'viajes', 'viajesMes', 'totalGastadoMes'));
    }
}
