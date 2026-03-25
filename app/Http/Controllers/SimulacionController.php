<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Viaje;
use App\Models\Tarjeta;
use App\Models\VentaViaje;
use Carbon\Carbon;

class SimulacionController extends Controller
{
    public function index()
    {
        // Obtener viajes de HOY para simular
        $viajes = Viaje::with(['bus', 'ruta', 'conductor', 'estado'])
            ->whereDate('fecha', Carbon::today())
            ->whereIn('id_estado', [1, 4]) // Solo Programado (1) o En Curso (4)
            ->whereHas('ruta', function ($q) {
                $q->where('id_estado', 1); // 1 = ACTIVO en la tabla estado para Rutas
            })
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function($viaje) {
                $fecha = Carbon::parse($viaje->fecha);
                $ahora = Carbon::now();
                
                // Rango horario: 30 min antes hasta 8 horas después
                $inicio = $fecha->copy()->subMinutes(30);
                $fin = $fecha->copy()->addHours(8);

                $viaje->esta_en_horario = $ahora->between($inicio, $fin);
                $viaje->esta_completo = $viaje->bus && $viaje->conductor && $viaje->ruta;
                $viaje->puede_simular = $viaje->esta_en_horario && $viaje->esta_completo;

                return $viaje;
            })
            ->filter(function($viaje) {
                return $viaje->puede_simular;
            });
            
        return view('simulacion.index', compact('viajes'));
    }

    public function validar(Request $request)
    {
        $request->validate([
            'id_viaje' => 'required',
            'codigo_tarjeta' => 'required|numeric'
        ]);

        $tarjeta = Tarjeta::where('codigo_tarjeta', $request->codigo_tarjeta)->first();
        if (!$tarjeta) {
            return response()->json(['success' => false, 'message' => 'Tarjeta inválida o no registrada.']);
        }

        if ($tarjeta->id_estado != 1) { // 1 = Activa
            return response()->json(['success' => false, 'message' => 'La tarjeta se encuentra inactiva o bloqueada.']);
        }

        $costoPasaje = 3300; 
        if ($tarjeta->saldo < $costoPasaje) {
            return response()->json(['success' => false, 'message' => 'SALDO INSUFICIENTE. Saldo: $' . number_format($tarjeta->saldo)]);
        }

        // Debitar Saldo
        $tarjeta->saldo -= $costoPasaje;
        $tarjeta->save();

        // Registrar Venta
        VentaViaje::create([
            'id_viaje' => $request->id_viaje,
            'id_tarjeta' => $tarjeta->id_tarjeta,
            'valor' => $costoPasaje,
            'fecha' => Carbon::now(),
            'id_estado' => 1 // PAGADO -> ACTIVO
        ]);

        $viaje = Viaje::find($request->id_viaje);
        if ($viaje) {
            $recorridoActivo = \App\Models\Recorrido::where('doc_us', $viaje->doc_us)
                ->where('placa', $viaje->placa)
                ->whereNull('hora_llegada')
                ->first();

            if ($recorridoActivo) {
                $recorridoActivo->cantidad_pasajeros += 1;
                $recorridoActivo->ingresos += $costoPasaje;
                $recorridoActivo->save();
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Pasaje cobrado con éxito. Buen viaje.',
            'saldo_restante' => '$' . number_format($tarjeta->saldo)
        ]);
    }
}
