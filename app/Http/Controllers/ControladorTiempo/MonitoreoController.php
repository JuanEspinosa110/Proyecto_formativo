<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Estado;
use Illuminate\Support\Facades\Auth;

class MonitoreoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Todos los buses de la empresa con su estado y asignación activa
        $buses = Bus::with(['estado', 'asignaciones.usuario', 'asignaciones.ruta.barrioOrigen', 'asignaciones.ruta.barrioDestino'])
            ->where('NIT', $user->NIT)
            ->orderBy('placa')
            ->get();

        // Resumen por estado
        $estadisticas = [
            'en_ruta'     => $buses->where('id_estado', 1)->count(),
            'inactivos'   => $buses->where('id_estado', 2)->count(),
            'en_taller'   => $buses->where('id_estado', 7)->count(),
            'total'       => $buses->count(),
        ];

        $estados = Estado::all();

        return view('controlador-tiempo.monitoreo.index', compact(
            'buses',
            'estadisticas',
            'estados'
        ));
    }
}
