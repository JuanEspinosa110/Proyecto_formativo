<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Recorrido;
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
        
        return view('controlador-tiempo.verificacion.show', compact('recorrido', 'minutosEnRuta'));
    }

    public function registrarCheckpoint(Request $request, $id_recorrido)
    {
        // En una app real, aquí se guardaría en una tabla de 'checkpoints' o similar.
        // Por ahora lo simularemos como una novedad o un log.
        
        return redirect()->route('controlador-tiempo.dashboard')
            ->with('success', 'Bus ' . $id_recorrido . ': Checkpoint registrado a las ' . Carbon::now()->format('H:i:s'));
    }
}
