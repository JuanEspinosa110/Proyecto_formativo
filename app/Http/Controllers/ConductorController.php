<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Viaje;
use App\Models\Recorrido;
use App\Models\Documento;
use App\Models\FallaMecanica;
use App\Models\Tarjeta;
use App\Models\VentaViaje;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConductorController extends Controller
{
    public function dashboard()
    {
        $conductor = Auth::guard('web')->user();
        $hoy = Carbon::today();

        // 1. Obtener todas las asignaciones del conductor
        $asignaciones = Viaje::with(['bus', 'ruta', 'estado'])
            ->where('doc_us', $conductor->doc_usuario)
            ->orderBy('fecha', 'desc')
            ->get();
            
        $ahora = Carbon::now();

        // 2. Estado de la jornada actual
        // Buscar si tiene un turno activo o en curso programado para HOY dentro del rango horario
        $asignacionActiva = $asignaciones->filter(function($asig) use ($hoy, $ahora) {
            $esHoy = Carbon::parse($asig->fecha)->isSameDay($hoy);
            if (!$esHoy) return false;

            $fecha = Carbon::parse($asig->fecha);
            $inicio = $fecha->copy()->subMinutes(30);
            $fin = $fecha->copy()->addHours(8); // Duración asumida de jornada

            return in_array($asig->id_estado, [1, 12, 8]) && $ahora->between($inicio, $fin);
        })->first();

        // **Auto-vencer turno si pasan más de 4 horas sin iniciar**
        if ($asignacionActiva && $asignacionActiva->id_estado == 1) {
            $horaProgramada = Carbon::parse($asignacionActiva->fecha);
            if (Carbon::now()->greaterThan($horaProgramada->copy()->addHours(4))) {
                $asignacionActiva->id_estado = 8; // Vencida
                $asignacionActiva->save();
            }
        }

        // Buscar si ya finalizó su turno hoy (8 = FUERA DE SERVICIO)
        $turnoFinalizadoHoy = $asignaciones->filter(function($asig) use ($hoy) {
            return $asig->id_estado == 8 && Carbon::parse($asig->fecha)->isSameDay($hoy);
        })->isNotEmpty();

        // 3. Documentos y validaciones (Licencia)
        $documentos = Documento::with('tipoDocumento')
            ->where('doc_usuario', $conductor->doc_usuario)
            ->get();

        $licenciaVencida = false;
        $licenciaProxima = false;
        foreach($documentos as $doc) {
            if ($doc->estado_expiracion == 'VENCIDO') $licenciaVencida = true;
            if ($doc->estado_expiracion == 'PRÓXIMO A VENCER') $licenciaProxima = true;
        }

        // 4. Estado del vehículo (Fallas activas)
        $fallasBus = collect();
        if ($asignacionActiva) {
            $fallasBus = FallaMecanica::where('placa', $asignacionActiva->placa)
                ->where('id_estado', 19) // 19 = PENDIENTE
                ->get();
        }

        // 5. Seguimiento del Recorrido Actual
        $recorridoActivo = Recorrido::where('doc_us', $conductor->doc_usuario)
            ->whereNull('hora_llegada')
            ->first();

        // **Auto-finalizar recorrido si lleva más de 30 minutos**
        if ($recorridoActivo) {
            $horaSalida = Carbon::parse($recorridoActivo->hora_salida);
            if ($horaSalida->addMinutes(30)->isPast()) {
                $recorridoActivo->hora_llegada = $horaSalida->copy()->addMinutes(30);
                $recorridoActivo->save();
                $recorridoActivo = null; // Limpiar
            }
        }

        // 6. Historial de Recorridos (Trazabilidad dia actual y generales)
        // Calcular Totales de Hoy desde VentaViaje (Ventas Reales)
        $viajesHoyIds = Viaje::where('doc_us', $conductor->doc_usuario)
            ->whereDate('fecha', $hoy)
            ->pluck('id_viaje')
            ->toArray();

        $pasajerosTotalesHoy = VentaViaje::whereIn('id_viaje', $viajesHoyIds)->count();
        $ingresosTotalesHoy = VentaViaje::whereIn('id_viaje', $viajesHoyIds)->sum('valor');

        // Primero obtener TODOS los recorridos de hoy para los contadores totales
        $recorridosHoy = Recorrido::where('doc_us', $conductor->doc_usuario)
            ->whereDate('hora_salida', $hoy)
            ->get();

        // Calcular Tiempo Trabajado
        $minutosTrabajados = 0;
        foreach ($recorridosHoy as $rec) {
            if ($rec->hora_llegada) {
                $salida = Carbon::parse($rec->hora_salida);
                $llegada = Carbon::parse($rec->hora_llegada);
                $minutosTrabajados += $salida->diffInMinutes($llegada);
            }
        }
        $horasTrabajadas = floor($minutosTrabajados / 60);
        $minutosRestantes = $minutosTrabajados % 60;
        $tiempoTrabajadoFormato = "{$horasTrabajadas}h {$minutosRestantes}m";

        // Resumen para el Dashboard (máximo 5 registros)
        $historialRecorridos = Recorrido::with(['bus', 'ruta'])
            ->where('doc_us', $conductor->doc_usuario)
            ->orderBy('hora_salida', 'desc')
            ->limit(5)
            ->get();

        // 7. Historial de Fallas (Resumen para el Dashboard)
        $historialFallas = FallaMecanica::with('bus')
            ->where('doc_usuario', $conductor->doc_usuario)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        // 8. Opciones ENUM para modal de fallas
        $type = DB::select('SHOW COLUMNS FROM reportes_fallas WHERE Field = "nivel_urgencia"')[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $nivelesUrgencia = array();
        foreach(explode(',', $matches[1]) as $value){
            $nivelesUrgencia[] = trim($value, "'");
        }

        return view('conductor.dashboard', compact(
            'conductor', 
            'asignaciones', 
            'asignacionActiva', 
            'turnoFinalizadoHoy',
            'documentos',
            'licenciaVencida',
            'licenciaProxima',
            'fallasBus',
            'recorridoActivo',
            'historialRecorridos',
            'recorridosHoy',
            'tiempoTrabajadoFormato',
            'historialFallas',
            'hoy',
            'nivelesUrgencia',
            'pasajerosTotalesHoy',
            'ingresosTotalesHoy'
        ));
    }

    public function reportarFalla(Request $request)
    {
        $request->validate([
            'placa' => 'required',
            'descripcion' => 'required|string',
            'nivel_urgencia' => 'nullable|in:Bajo,Medio,Alto'
        ]);

        FallaMecanica::create([
            'placa' => $request->placa,
            'doc_usuario' => Auth::guard('web')->id(),
            'descripcion' => $request->descripcion,
            'nivel_urgencia' => $request->nivel_urgencia ?? 'Bajo',
            'id_estado' => 19 // PENDIENTE
        ]);

        // Cierre automático de turno si hay uno activo
        $conductor = Auth::guard('web')->user();
        $hoy = Carbon::today();
        
        $asignacionActiva = Viaje::where('doc_us', $conductor->doc_usuario)
            ->whereIn('id_estado', [1, 12]) // EN_CURSO o PROGRAMADO
            ->where('placa', $request->placa)
            ->first();

        if ($asignacionActiva) {
            $asignacionActiva->id_estado = 8; // FUERA_DE_SERVICIO
            $asignacionActiva->save();

            // También finalizar recorrido activo en pista si existe
            $recorridoActivo = Recorrido::where('doc_us', $conductor->doc_usuario)
                ->whereNull('hora_llegada')
                ->first();
            if ($recorridoActivo) {
                $recorridoActivo->hora_llegada = Carbon::now();
                $recorridoActivo->save();
            }
        }

        return redirect()->back()->with('success', 'Reporte de falla enviado exitosamente. Su turno ha sido FINALIZADO preventivamente.');
    }

    // LÓGICA DE JORNADA
    public function iniciarTurno($id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);

        // **Validación de Estado para iniciar turno**
        if ($viaje->id_estado != 1) { // 1 = Activo/Programado
            return redirect()->back()->with('error', 'El turno no se encuentra en estado programado o ya fue procesado.');
        }
        
        // Validación de Horario (30 min antes - 4h después)
        $horaProgramada = Carbon::parse($viaje->fecha);
        $ahora = Carbon::now();
        $puedeIniciar = $ahora->between($horaProgramada->copy()->subMinutes(30), $horaProgramada->copy()->addHours(4));

        if (!$puedeIniciar) {
            return redirect()->back()->with('error', 'No puede iniciar el turno fuera del horario permitido (30 min antes o hasta 4h después de la hora programada).');
        }
        
        // Bloqueo de seguridad si la licencia está vencida (validar de nuevo backend)
        $docs = Documento::where('doc_usuario', Auth::guard('web')->id())->get();
        foreach($docs as $doc) {
            if($doc->estado_expiracion == 'VENCIDO') {
                return redirect()->back()->with('error', 'Licencias vencidas. Autorización negada por el sistema.');
            }
        }

        // 12 = EN_CURSO
        $viaje->id_estado = 12;
        $viaje->save();

        return redirect()->back()->with('success', 'Turno iniciado. Conduzca con precaución.');
    }

    public function finalizarTurno($id_viaje)
    {
        $recorridoA = Recorrido::where('doc_us', Auth::guard('web')->id())->whereNull('hora_llegada')->first();
        if ($recorridoA) {
            return redirect()->back()->with('error', 'Debe finalizar el recorrido activo en pista antes de terminar el turno.');
        }

        $viaje = Viaje::findOrFail($id_viaje);
        // 8 = FUERA_DE_SERVICIO
        $viaje->id_estado = 8;
        $viaje->save();

        return redirect()->back()->with('success', 'Turno finalizado y registrado.');
    }

    // LÓGICA DE RECORRIDOS
    public function iniciarRecorrido(Request $request, $id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);

        // Validar que el turno esté en curso (12) antes de iniciar recorrido
        if ($viaje->id_estado != 12) {
            return redirect()->back()->with('error', 'Debe iniciar su turno antes de comenzar un recorrido.');
        }
        
        $request->validate([
            'sentido' => 'required|string|in:IDA,VUELTA'
        ]);

        // Evitar duplicidades de inicio
        $activo = Recorrido::where('doc_us', Auth::guard('web')->id())->whereNull('hora_llegada')->first();
        if ($activo) {
            return redirect()->back();
        }

        Recorrido::create([
            'placa' => $viaje->placa,
            'id_ruta' => $viaje->id_ruta,
            'sentido' => $request->sentido,
            'doc_us' => $viaje->doc_us,
            'hora_salida' => Carbon::now(),
            'hora_llegada' => null,
            'cantidad_pasajeros' => 0,
            'ingresos' => 0
        ]);

        return redirect()->back()->with('success', 'Ruta iniciada en sentido ' . $request->sentido . '.');
    }

    public function finalizarRecorrido(Request $request, $id_recorrido)
    {
        $recorrido = Recorrido::findOrFail($id_recorrido);

        $recorrido->hora_llegada = Carbon::now();
        $recorrido->save();

        return redirect()->back()->with('success', 'Recorrido (Sentido ' . $recorrido->sentido . ') finalizado exitosamente.');
    }

    // REGISTRO TARJETAS (Soporte AJAX para Scanner)
    public function registrarPasajero(Request $request, $id_recorrido)
    {
        $request->validate([
            'codigo_tarjeta' => 'required|numeric'
        ]);

        $recorrido = Recorrido::findOrFail($id_recorrido);
        $viaje = Viaje::where('doc_us', $recorrido->doc_us)
            ->whereIn('id_estado', [12]) // EN_CURSO
            ->where('placa', $recorrido->placa)
            ->first();

        if (!$viaje) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No hay viaje activo o el turno finalizó.']);
            }
            return redirect()->back()->with('error', 'No hay viaje activo o el turno finalizó.');
        }

        $tarjeta = Tarjeta::where('codigo_tarjeta', $request->codigo_tarjeta)->first();
        if (!$tarjeta) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tarjeta inválida o no registrada en el sistema.']);
            }
            return redirect()->back()->with('error', 'Tarjeta inválida o no registrada en el sistema.');
        }

        if ($tarjeta->id_estado != 1 && $tarjeta->id_estado != 20) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'La tarjeta se encuentra inactiva o bloqueada.']);
            }
            return redirect()->back()->with('error', 'La tarjeta se encuentra inactiva o bloqueada.');
        }

        $costoPasaje = 3300; 

        if ($tarjeta->saldo < $costoPasaje) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'SALDO INSUFICIENTE. Saldo actual: $' . number_format($tarjeta->saldo)]);
            }
            return redirect()->back()->with('error', 'SALDO INSUFICIENTE. Saldo actual: $' . number_format($tarjeta->saldo));
        }

        // Debitar Saldo
        $tarjeta->saldo -= $costoPasaje;
        $tarjeta->save();

        // Mapear Venta Viaje (Pasajero)
        VentaViaje::create([
            'id_viaje' => $viaje->id_viaje,
            'id_tarjeta' => $tarjeta->id_tarjeta,
            'valor' => $costoPasaje,
            'fecha' => Carbon::now(),
            'id_estado' => 18 // PAGADO
        ]);

        // Aumentar pasajeros e ingresos en recorrido
        $recorrido->cantidad_pasajeros += 1;
        $recorrido->ingresos += 3300;
        $recorrido->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Pasaje cobrado con éxito. Pasajeros: ' . $recorrido->cantidad_pasajeros,
                'cantidad_pasajeros' => $recorrido->cantidad_pasajeros
            ]);
        }
        return redirect()->back()->with('success', 'Pasaje cobrado con éxito. Pasajeros: ' . $recorrido->cantidad_pasajeros);
    }

    public function historialRecorridos(Request $request)
    {
        $conductor = Auth::guard('web')->user();
        $filtro = $request->get('filtro', 'todos');
        
        $query = Recorrido::with(['bus', 'ruta'])
            ->where('doc_us', $conductor->doc_usuario)
            ->orderBy('hora_salida', 'desc');

        if ($filtro == 'hoy') {
            $query->whereDate('hora_salida', Carbon::today());
        } elseif ($filtro == 'semana') {
            $query->whereBetween('hora_salida', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filtro == 'mes') {
            $query->whereMonth('hora_salida', Carbon::now()->month)
                  ->whereYear('hora_salida', Carbon::now()->year);
        }

        $recorridos = $query->paginate(5);

        return view('conductor.recorridos.index', compact('recorridos', 'filtro'));
    }

    public function historialFallas(Request $request)
    {
        $conductor = Auth::guard('web')->user();
        $filtro = $request->get('filtro', 'todos');
        
        $query = FallaMecanica::with('bus')
            ->where('doc_usuario', $conductor->doc_usuario)
            ->orderBy('created_at', 'desc');

        if ($filtro == 'hoy') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filtro == 'semana') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filtro == 'mes') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $fallas = $query->paginate(5);

        // Para el modal de reporte
        $asignaciones = Viaje::where('doc_us', $conductor->doc_usuario)->get();
        $hoy = Carbon::today();
        $asignacionActiva = $asignaciones->filter(function($asig) use ($hoy) {
            return in_array($asig->id_estado, [1, 12]) && Carbon::parse($asig->fecha)->isSameDay($hoy);
        })->first();

        $type = DB::select('SHOW COLUMNS FROM reportes_fallas WHERE Field = "nivel_urgencia"')[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $nivelesUrgencia = array();
        foreach(explode(',', $matches[1]) as $value){
            $nivelesUrgencia[] = trim($value, "'");
        }

        return view('conductor.fallas.index', compact('fallas', 'nivelesUrgencia', 'asignaciones', 'asignacionActiva', 'filtro'));
    }
}
