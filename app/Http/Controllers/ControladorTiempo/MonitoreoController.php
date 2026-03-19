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
            'en_ruta'     => $todosLosBuses->where('id_estado', 1)->count(),
            'inactivos'   => $todosLosBuses->where('id_estado', 2)->count(),
            'en_taller'   => $todosLosBuses->where('id_estado', 7)->count(),
            'total'       => $todosLosBuses->count(),
        ];

        // 2. Obtener solo los buses con operación activa para el monitoreo
        // Buses con asignación activa (EN_CURSO = 12)
        $buses = Bus::with([
                'estado', 
                'asignaciones' => function($q) {
                    $q->where('id_estado', 12); // Solo viajes activos
                }, 
                'asignaciones.usuario', 
                'asignaciones.ruta.barrioOrigen', 
                'asignaciones.ruta.barrioDestino',
                'recorridos' => function($q) {
                    $q->whereDate('hora_salida', \Carbon\Carbon::today())->orderBy('hora_salida', 'desc');
                }
            ])
            ->where('NIT', $user->NIT)
            ->whereHas('asignaciones', function($q) {
                $q->where('id_estado', 12);
            })
            ->orderBy('placa')
            ->get();

        $estados = Estado::all();

        return view('controlador-tiempo.monitoreo.index', compact(
            'buses',
            'estadisticas',
            'estados'
        ));
    }
}
