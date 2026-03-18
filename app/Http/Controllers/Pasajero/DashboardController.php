<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\TitularidadTarjeta;
use App\Models\Recarga;
use App\Models\VentaViaje;
use App\Models\Ruta;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Titularidad activa y su tarjeta (puede ser null para usuarios sin tarjeta)
        $titularidad = TitularidadTarjeta::with('tarjeta')
            ->where('doc_usuario', $user->doc_usuario)
            ->where('id_estado', 1)
            ->latest('fecha_inicio')
            ->first();

        $tarjeta      = $titularidad?->tarjeta;
        $tieneTarjeta = (bool) $tarjeta;

        // ── Estadísticas (solo si tiene tarjeta) ────────────────
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

        // ── Rutas de la ciudad del usuario ───────────────────────
        $rutasDisponibles = Ruta::where('id_ciudad', $user->id_ciudad)
            ->where('id_estado', 1)
            ->count();

        return view('pasajero.dashboard', compact(
            'user',
            'tarjeta',
            'titularidad',
            'tieneTarjeta',
            'totalRecargas',
            'totalViajes',
            'gastoMes',
            'rutasDisponibles'
        ));
    }
}
