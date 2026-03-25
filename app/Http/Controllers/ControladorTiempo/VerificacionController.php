<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Recorrido;
use Illuminate\Support\Facades\Auth;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerificacionController extends Controller
{
    public function scanner()
    {
        return view('controlador-tiempo.verificacion.scanner');
    }

    public function show($id_recorrido)
    {
        $recorrido = Recorrido::with(['bus', 'ruta.barrioOrigen', 'ruta.barrioDestino', 'conductor'])
            ->findOrFail($id_recorrido);

        // Información adicional de pasajeros y tiempos
        $minutosEnRuta = Carbon::parse($recorrido->hora_salida)->diffInMinutes(Carbon::now());
        
        // Calcular frecuencia real: Diferencia con el bus anterior de la misma ruta
        $recorridoAnterior = Recorrido::where('id_ruta', $recorrido->id_ruta)
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
            ->with('success', 'Bus ' . ($recorrido->placa ?? 'N/A') . ': Checkpoint registrado exitosamente.');
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
            ->with('warning', 'Bus ' . ($recorrido->placa ?? 'N/A') . ': Incidencia reportada y documentada.');
    }
}
