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

        // 1. Obtener estadísticas de TODA la flota de la empresa
        $todosLosBuses = Bus::where('NIT', $user->NIT)->get();

        $estadisticas = [
            'en_ruta' => $todosLosBuses->where('id_estado', 1)->count(),
            'inactivos' => $todosLosBuses->where('id_estado', 2)->count(),
            'en_taller' => $todosLosBuses->where('id_estado', 4)->count(),
            'total' => $todosLosBuses->count(),
        ];

        // 2. Obtener solo los buses con operación activa para el monitoreo
        $buses = Bus::with([
            'estado',
            'asignaciones' => function ($q) {
            $q->where('id_estado', 4); // Solo viajes activos (EN CURSO)
        },
            'asignaciones.usuario',
            'asignaciones.ruta.barrioOrigen',
            'asignaciones.ruta.barrioDestino',
            'recorridos' => function ($q) {
            $q->whereDate('hora_salida', \Carbon\Carbon::today())->orderBy('hora_salida', 'desc');
        }
        ])
            ->where('NIT', $user->NIT)
            ->whereHas('asignaciones', function ($q) {
            $q->where('id_estado', 4);
        })
            ->orderBy('placa')
            ->get();

        $buses = $buses->map(function ($bus) use ($user) {
            $ultimaSalida = $bus->recorridos->first();
            $bus->intervalo_anterior = null;

            if ($ultimaSalida && $bus->asignaciones->first()) {
                $idRuta = $bus->asignaciones->first()->id_ruta;

                // Buscar el bus que salió inmediatamente antes por la misma ruta
                $recorridoAnterior = \App\Models\Recorrido::whereHas('viaje', function($q) use ($idRuta) {
                                $q->where('id_ruta', $idRuta);
                            })
                            ->where('id_recorrido', '!=', $ultimaSalida->id_recorrido)
                            ->where('hora_salida', '<', $ultimaSalida->hora_salida)
                            ->orderBy('hora_salida', 'desc')
                            ->first();

                        if ($recorridoAnterior) {
                            $bus->intervalo_anterior = (int) round(\Carbon\Carbon::parse($recorridoAnterior->hora_salida)
                                ->diffInMinutes(\Carbon\Carbon::parse($ultimaSalida->hora_salida)));
                        }
                    }
                    return $bus;
                });

        $estados = \App\Models\Estado::all();

        return view('controlador-tiempo.monitoreo.index', compact(
            'buses',
            'estadisticas',
            'estados'
        ));
    }
}
