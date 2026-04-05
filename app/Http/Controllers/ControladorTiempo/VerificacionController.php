<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Recorrido;
use Illuminate\Support\Facades\Auth;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\ReporteFallaService;

class VerificacionController extends Controller
{
    public function scanner()
    {
        return view('controlador-tiempo.verificacion.scanner');
    }

    public function show($id_recorrido)
    {
        $recorrido = Recorrido::with(['viaje.bus', 'viaje.ruta.barrioOrigen', 'viaje.ruta.barrioDestino', 'viaje.conductor'])
            ->findOrFail($id_recorrido);

        // Información adicional de pasajeros y tiempos
        $minutosEnRuta = Carbon::parse($recorrido->hora_salida)->diffInMinutes(Carbon::now());
        
        // Calcular frecuencia real: Diferencia con el bus anterior de la misma ruta
        $recorridoAnterior = Recorrido::whereHas('viaje', function($q) use ($recorrido) {
                $q->where('id_ruta', $recorrido->viaje->id_ruta);
            })
            ->where('id_recorrido', '!=', $recorrido->id_recorrido)
            ->where('hora_salida', '<', $recorrido->hora_salida)
            ->orderBy('hora_salida', 'desc')
            ->first();

        $intervaloAnterior = null;
        if ($recorridoAnterior) {
            $intervaloAnterior = Carbon::parse($recorridoAnterior->hora_salida)
                ->diffInMinutes(Carbon::parse($recorrido->hora_salida));
        }
        
        return view('controlador-tiempo.verificacion.show', compact('recorrido', 'minutosEnRuta', 'intervaloAnterior'));
    }

    public function registrarCheckpoint(Request $request, $id_recorrido)
    {
        $recorrido = \App\Models\Recorrido::findOrFail($id_recorrido);
        $controlador = Auth::user();

        // 1. Registrar el Checkpoint en la base de datos
        \App\Models\NovedadRecorrido::create([
            'id_recorrido' => $id_recorrido,
            'doc_controlador' => $controlador->doc_usuario,
            'tipo' => 'CHECKPOINT',
            'descripcion' => 'Punto de control validado mediante escaneo QR.'
        ]);
        
        return redirect()->route('controlador-tiempo.dashboard')
            ->with('success', 'Bus ' . ($recorrido->viaje->placa ?? 'N/A') . ': Checkpoint registrado exitosamente.');
    }

    public function registrarIncidencia(Request $request, $id_recorrido)
    {
        $request->validate([
            'descripcion' => 'required|string|min:5'
        ]);

        $recorrido = \App\Models\Recorrido::findOrFail($id_recorrido);
        $controlador = Auth::user();

        // 1. Registrar la Incidencia en la base de datos
        \App\Models\NovedadRecorrido::create([
            'id_recorrido' => $id_recorrido,
            'doc_controlador' => $controlador->doc_usuario,
            'tipo' => 'INCIDENCIA',
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('controlador-tiempo.dashboard')
            ->with('warning', 'Bus ' . ($recorrido->viaje->placa ?? 'N/A') . ': Incidencia reportada y documentada.');
    }

    public function reportarFalla(Request $request, $id_recorrido, ReporteFallaService $service)
    {
        $request->validate([
            'descripcion' => 'required|string|min:5',
            'nivel_urgencia' => 'required|in:Bajo,Medio,Alto'
        ]);

        $recorrido = Recorrido::with('viaje')->findOrFail($id_recorrido);
        $controlador = Auth::user();

        $service->registrarFalla(
            $recorrido->viaje->placa,
            $controlador->doc_usuario,
            $request->descripcion,
            $request->nivel_urgencia
        );

        $msg = 'Falla mecánica reportada exitosamente.';
        if ($request->nivel_urgencia === 'Alto') {
            $msg .= ' La operación del bus y el turno del conductor han sido suspendidos.';
            return redirect()->route('controlador-tiempo.dashboard')->with('error', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }
}
