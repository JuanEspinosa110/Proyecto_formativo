<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Viaje;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Estado;
use App\Models\Usuario;
use App\Models\HistorialBus;
use App\Models\VentaViaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropietarioController extends Controller
{
    /**
     * Muestra la vista principal del propietario con toda su información.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Validar que el usuario sea propietario
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Información de los buses (Filtrados por doc_propietario)
        $buses = Bus::with('estado')->where('doc_propietario', $user->doc_usuario)->get();
        $busIds = $buses->pluck('placa')->toArray();
        $viajesOwnerIds = Viaje::whereIn('placa', $busIds)->pluck('id_viaje')->toArray();

        // 2. Asignaciones, Ingresos y Gastos
        $queryAsignaciones = Viaje::query()->with(['ruta', 'conductor', 'estado', 'ventas']);
        $ultimaAsignacion = null;
        $conteoAsignaciones = 0;
        $conteoDocumentos = 0;
        $conteoPasajeros = 0;
        $ingresosTotales = 0;
        $precioPasaje = 3300;
        $gananciasHoy = 0;
        $gananciasSemana = 0;
        $gananciasMes = 0;
        $ingresosPorBus = collect();

        if ($buses->isNotEmpty()) {
            $queryAsignaciones->whereIn('placa', $busIds);
            
            // Obtener última ruta asignada
            $ultimaAsignacion = Viaje::whereIn('placa', $busIds)
                ->with('ruta')
                ->orderBy('fecha', 'desc')
                ->first();

            // Conteos consolidados basados en Recorridos Reales
            // Conteos consolidados basados en Recorridos Reales
            $conteoAsignaciones = \App\Models\Viaje::whereIn('placa', $busIds)->count();
            $conteoDocumentos = Documento::whereIn('placa', $busIds)->count();
            
            // Ingresos y pasajeros desde la tabla recorridos
            $conteoPasajeros = \App\Models\VentaViaje::whereHas('viaje', function($q) use ($busIds) {
                $q->whereIn('placa', $busIds);
            })->count();
            
            // Filtro por Mes para Ganancias (Captura inicial)
            $mesFiltro = $request->query('mes_seleccionado'); 
            $mesBase = $mesFiltro ?: \Carbon\Carbon::now()->format('Y-m');

            // 1. Ingresos Totales del Mes Seleccionado
            $parts = explode('-', $mesBase);
            $year = $parts[0];
            $month = $parts[1];

            $ingresosTotales = \App\Models\VentaViaje::whereHas('viaje', function($q) use ($busIds) {
                $q->whereIn('placa', $busIds);
            })->whereYear('fecha', $year)->whereMonth('fecha', $month)->sum('valor');

            $hoy = \Carbon\Carbon::today();
            $semana = \Carbon\Carbon::now()->startOfWeek();

            // 2. Cálculo de tarjetas resumen
            $gananciasHoy = \App\Models\VentaViaje::whereHas('viaje', function($q) use ($busIds) {
                $q->whereIn('placa', $busIds);
            })->whereDate('fecha', $hoy)->sum('valor');
            
            $gananciasSemana = \App\Models\VentaViaje::whereHas('viaje', function($q) use ($busIds) {
                $q->whereIn('placa', $busIds);
            })->where('fecha', '>=', $semana)->sum('valor');
            
            // Si hay un mes seleccionado diferente al actual, 'gananciasMes' mostrará ese mes.
            $gananciasMes = $ingresosTotales;

            // 3. Ingresos individuales por bus (Filtrados por los 3 periodos: Hoy, Semana, Mes)
            $viajesTotalesBus = \App\Models\Viaje::whereIn('placa', $busIds)
                // Mes Actual (o seleccionado)
                ->withCount(['ventas as count_mes' => function($q) use ($year, $month) {
                    $q->whereYear('fecha', $year)->whereMonth('fecha', $month);
                }])
                ->withSum(['ventas as sum_mes' => function($q) use ($year, $month) {
                    $q->whereYear('fecha', $year)->whereMonth('fecha', $month);
                }], 'valor')
                // Hoy
                ->withCount(['ventas as count_hoy' => function($q) use ($hoy) {
                    $q->whereDate('fecha', $hoy);
                }])
                ->withSum(['ventas as sum_hoy' => function($q) use ($hoy) {
                    $q->whereDate('fecha', $hoy);
                }], 'valor')
                // Semana
                ->withCount(['ventas as count_semana' => function($q) use ($semana) {
                    $q->where('fecha', '>=', $semana);
                }])
                ->withSum(['ventas as sum_semana' => function($q) use ($semana) {
                    $q->where('fecha', '>=', $semana);
                }], 'valor')
                ->get();
                
            $ingresosPorBus = $viajesTotalesBus->groupBy('placa')->map(function ($viajes, $placa) {
                $mesIngresos = $viajes->sum('sum_mes') ?? 0;
                $mesPasajeros = $viajes->sum('count_mes') ?? 0;
                
                return (object)[
                    'placa' => $placa,
                    'mes_ingresos' => $mesIngresos,
                    'mes_pasajeros' => $mesPasajeros,
                    'total_ingresos' => $mesIngresos, // Alias para compatibilidad con la vista
                    'total_pasajeros' => $mesPasajeros, // Alias para compatibilidad con la vista
                    'hoy_ingresos' => $viajes->sum('sum_hoy') ?? 0,
                    'hoy_pasajeros' => $viajes->sum('count_hoy') ?? 0,
                    'semana_ingresos' => $viajes->sum('sum_semana') ?? 0,
                    'semana_pasajeros' => $viajes->sum('count_semana') ?? 0,
                ];
            })->values();

            // 4. Filtros Independientes para Asignaciones
            if ($request->filled('fecha')) {
                $queryAsignaciones->whereDate('fecha', $request->fecha);
            }
            if ($request->filled('conductor')) {
                $queryAsignaciones->whereHas('conductor', function($q) use ($request) {
                    $q->where(function($group) use ($request) {
                        $group->where('primer_nombre', 'like', '%' . $request->conductor . '%')
                              ->orWhere('primer_apellido', 'like', '%' . $request->conductor . '%')
                              ->orWhere('segundo_nombre', 'like', '%' . $request->conductor . '%')
                              ->orWhere('segundo_apellido', 'like', '%' . $request->conductor . '%');
                    });
                });
            }
            if ($request->filled('placa')) {
                $queryAsignaciones->where('placa', 'like', '%' . $request->placa . '%');
            }
            if ($request->filled('ruta')) {
                $queryAsignaciones->whereHas('ruta', function($q) use ($request) {
                    $q->whereHas('barrioOrigen', function($qb) use ($request) {
                        $qb->where('nombre', 'like', '%' . $request->ruta . '%');
                    })->orWhereHas('barrioDestino', function($qb) use ($request) {
                        $qb->where('nombre', 'like', '%' . $request->ruta . '%');
                    });
                });
            }
            // 5. Lógica de Filtro por Hora (Dentro del turno de 8h)
            if ($request->filled('horario')) {
                $searchTime = $request->horario;
                $queryAsignaciones->whereRaw("
                    CASE 
                        WHEN TIME(DATE_ADD(fecha, INTERVAL 8 HOUR)) >= TIME(fecha) 
                        THEN ? BETWEEN TIME(fecha) AND TIME(DATE_ADD(fecha, INTERVAL 8 HOUR))
                        ELSE ? >= TIME(fecha) OR ? <= TIME(DATE_ADD(fecha, INTERVAL 8 HOUR))
                    END
                ", [$searchTime, $searchTime, $searchTime]);
            }
            if ($request->filled('estado')) {
                $queryAsignaciones->where('id_estado', $request->estado);
            }
        } else {
            $queryAsignaciones->whereRaw('1 = 0');
        }

        // 6. Separación de Asignaciones (Simples) y Historial (Filtrado)
        $asignacionesRecientes = $queryAsignaciones->orderBy('fecha', 'desc')->paginate(5, ['*'], 'page_rec');
        $historialAsignaciones = $queryAsignaciones->orderBy('fecha', 'desc')->paginate(5, ['*'], 'page_hist');

        // Asignaciones para el módulo de "Ganancias" (Filtradas por el mes seleccionado)
        $queryGanancias = Viaje::with(['ruta', 'conductor', 'ventas'])
            ->whereIn('placa', $busIds);
        
        if ($mesFiltro) {
             $queryGanancias->whereYear('fecha', $year)->whereMonth('fecha', $month);
        }

        // Si es AJAX y viene periodo, sobreescribimos filtros de tiempo
        if ($request->ajax() && $request->filled('periodo')) {
            $periodo = $request->periodo;
            if ($periodo == 'hoy') $queryGanancias->whereDate('fecha', $hoy);
            elseif ($periodo == 'semana') $queryGanancias->where('fecha', '>=', $semana);
            elseif ($periodo == 'mes') $queryGanancias->whereYear('fecha', $year)->whereMonth('fecha', $month);
        }

        $asignacionesGanancias = $queryGanancias->orderBy('fecha', 'desc')
            ->paginate(5, ['*'], 'page_gan');
        
        $busesPaginated = Bus::with('estado')->where('doc_propietario', $user->doc_usuario)->paginate(5, ['*'], 'page_bus');
        
        $documentos = $buses->isNotEmpty() 
            ? Documento::whereIn('placa', $busIds)->with(['tipoDocumento', 'estado'])->orderBy('created_at', 'desc')->paginate(5, ['*'], 'page_doc') 
            : Documento::whereRaw('1 = 0')->paginate(5, ['*'], 'page_doc');
            
        $historialCambios = $buses->isNotEmpty() 
            ? HistorialBus::whereIn('placa', $busIds)->with(['ruta', 'conductor'])->orderBy('created_at', 'desc')->paginate(5, ['*'], 'page_camb') 
            : HistorialBus::whereRaw('1 = 0')->paginate(5, ['*'], 'page_camb');
        
        // Datos adicionales
        $tiposDocumento = TipoDocumento::where('id_estado', 1)->where('id_tipo_documento', '!=', 3)->get();
        $estados = Estado::all();

        // 4. Alertas Documentales para el Propietario
        $documentosAlerta = [];
        if ($buses->isNotEmpty()) {
            $todosLosDocs = Documento::whereIn('placa', $busIds)->where('id_estado', 1)->get();
            $documentosAlerta = $todosLosDocs->filter(function($doc) {
                return $doc->estado_expiracion !== 'VIGENTE';
            });
        }
        $conteoVencidos = collect($documentosAlerta)->filter(fn($d) => $d->estado_expiracion === 'VENCIDO')->count();
        $conteoProximos = collect($documentosAlerta)->filter(fn($d) => $d->estado_expiracion === 'PRÓXIMO A VENCER')->count();

        // Respuesta para AJAX (Paginación sin recarga)
        if ($request->ajax()) {
            if ($request->query('section') === 'ganancias') {
                return view('propietario.partials.ganancias_table', compact('asignacionesGanancias', 'precioPasaje'))->render();
            }
        }

        return view('propietario.dashboard', compact(
            'buses', 
            'busesPaginated',
            'asignacionesRecientes', 
            'historialAsignaciones',
            'asignacionesGanancias',
            'documentos', 
            'historialCambios',
            'tiposDocumento', 
            'estados', 
            'conteoAsignaciones', 
            'conteoDocumentos',
            'conteoPasajeros',
            'ingresosTotales',
            'ultimaAsignacion',
            'precioPasaje',
            'documentosAlerta',
            'conteoVencidos',
            'conteoProximos',
            'gananciasHoy',
            'gananciasSemana',
            'gananciasMes',
            'ingresosPorBus'
        ));
    }

    /**
     * Sube un nuevo documento para el bus del propietario.
     */
    public function subirDocumento(Request $request)
    {
        $user = Auth::user();
        $buses = Bus::where('doc_propietario', $user->doc_usuario)->pluck('placa')->toArray();

        if (empty($buses)) {
            return redirect()->back()->with('error', 'No tienes un vehículo asociado para subir documentos.');
        }

        $rules = [
            'placa' => 'required|in:' . implode(',', $buses),
            'id_tipo_documento' => 'required|exists:tipo_documento,id_tipo_documento|not_in:3',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'fecha_expedicion' => 'required|date|before_or_equal:today',
        ];

        if ($request->id_tipo_documento != 6) {
            $rules['fecha_vencimiento'] = 'required|date|after_or_equal:today';
        }

        $request->validate($rules, [
            'fecha_vencimiento.after_or_equal' => 'El documento que intentas subir ya se encuentra vencido. Por favor, sube un documento vigente.',
            'fecha_expedicion.before_or_equal' => 'La fecha de expedición no puede ser una fecha futura.'
        ]);

        $bus = Bus::where('placa', $request->placa)->first();

        // Calcular Vencimiento Inteligente
        $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
        $fecha_venc = null;

        if ($request->id_tipo_documento == 1 || $request->id_tipo_documento == 4) {
            // SOAT y Pólizas: 1 año estricto
            $fecha_venc = $fecha_exp->copy()->addYear();
        } elseif ($request->id_tipo_documento == 2) {
            // Tecnomecánica
            $modeloInt = (int)(preg_replace('/[^0-9]/', '', $bus->modelo) ?: date('Y'));
            if ($fecha_exp->year <= $modeloInt + 5) {
                // Primeros 5 años
                $fecha_venc = \Carbon\Carbon::create($modeloInt + 5, $fecha_exp->month, $fecha_exp->day);
                // Si la fecha resultante ya pasó, aplicamos 1 año normal
                if ($fecha_venc->isPast()) {
                    $fecha_venc = $fecha_exp->copy()->addYear();
                }
            } else {
                // Período anual
                $fecha_venc = $fecha_exp->copy()->addYear();
            }
        } else {
            // Otros (o si viene en el request)
            if ($request->id_tipo_documento == 6) {
                $fecha_venc = \Carbon\Carbon::parse('2099-12-31');
            } else {
                $fecha_venc = $request->fecha_vencimiento ? \Carbon\Carbon::parse($request->fecha_vencimiento) : $fecha_exp->copy()->addYear();
            }
        }

        try {
            if ($request->hasFile('archivo')) {
                // Archivar documento anterior si existe del mismo tipo y vehículo
                Documento::where('placa', $request->placa)
                        ->where('id_tipo_documento', $request->id_tipo_documento)
                        ->where('id_estado', 1)
                        ->update(['id_estado' => 11]); // 11 = Archivado

                // Almacenar el archivo en public/uploads/documentos
                $path = $request->file('archivo')->store('uploads/documentos', 'uploads');

                if ($path) {
                    $tipoDoc = TipoDocumento::find($request->id_tipo_documento);
                    
                    // Crear el registro en la base de datos
                    Documento::create([
                        'nombre' => $tipoDoc->nombre ?? 'Documento sin nombre',
                        'archivo' => $path,
                        'fecha_expedicion' => $fecha_exp,
                        'fecha_vencimiento' => $fecha_venc,
                        'id_tipo_documento' => $request->id_tipo_documento,
                        'placa' => $bus->placa,
                        'doc_usuario' => $user->doc_usuario,
                        'NIT' => $bus->NIT,
                        'id_estado' => 6, // 6 = PENDIENTE de aprobación
                    ]);

                    // Inactivar el bus preventivamente ya que el documento requiere aprobación
                    if ($bus) {
                        $bus->id_estado = 2; // Inactivo
                        $bus->save();
                    }

                    return redirect()->back()->with('success', 'Documento almacenado correctamente.');
                }
            }
            
            return redirect()->back()->with('error', 'No se pudo subir el archivo.');
        } catch (\Exception $e) {
            Log::error('Error al subir documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al procesar el documento.');
        }
    }

    /**
     * Actualiza un documento existente.
     */
    public function actualizarDocumento(Request $request, $id)
    {
        $user = Auth::user();
        $busIds = Bus::where('doc_propietario', $user->doc_usuario)->pluck('placa')->toArray();

        if (empty($busIds)) {
            return redirect()->back()->with('error', 'No tienes un vehículo asociado.');
        }

        $documento = Documento::where('id_documento', $id)
            ->whereIn('placa', $busIds)
            ->firstOrFail();

        $rules = [
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'fecha_expedicion' => 'required|date|before_or_equal:today',
        ];

        if ($documento->id_tipo_documento != 6) {
            $rules['fecha_vencimiento'] = 'required|date|after:fecha_expedicion';
        }

        $request->validate($rules);

        try {
            $data = [
                'doc_usuario' => $user->doc_usuario,
                'fecha_expedicion' => $request->fecha_expedicion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'id_estado' => 6, // 6 = PENDIENTE de aprobación
            ];

            // Inactivar el bus preventivamente ya que el documento requiere aprobación
            if ($documento->bus) {
                $documento->bus->update(['id_estado' => 2]); // 2 = Inactivo
            }

            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior si existe
                if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                    Storage::disk('public')->delete($documento->archivo);
                }

                // Guardar usando el método store estándar en la carpeta pública
                $ruta = $request->file('archivo')->store('uploads/documentos', 'uploads');
                
                if ($ruta) {
                    $data['archivo'] = $ruta;
                }
            }

            $documento->update($data);

            return redirect()->back()->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar documento propietario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al actualizar el documento.');
        }
    }

    /**
     * Obtiene los detalles de un vehículo para la ficha técnica.
     */
    public function verVehiculo($placa)
    {
        $user = Auth::user();
        
        // Cargar bus con su estado
        $bus = Bus::with('estado')
            ->where('placa', $placa)
            ->where('doc_propietario', $user->doc_usuario)
            ->firstOrFail();

        // Obtener último conductor asignado (último viaje registrado)
        $ultimoViaje = Viaje::where('placa', $placa)
            ->with('conductor')
            ->orderBy('fecha', 'desc')
            ->first();

        // Obtener documentos del vehículo ACTIVOS
        $documentos = Documento::where('placa', $placa)
            ->where('id_estado', 1)
            ->with(['tipoDocumento', 'estado'])
            ->get()
            ->map(function($doc) {
                return [
                    'id_tipo_documento' => $doc->id_tipo_documento,
                    'tipo_documento' => $doc->tipoDocumento,
                    'fecha_vencimiento' => $doc->fecha_vencimiento->format('Y-m-d'),
                    'created_at' => $doc->created_at->format('Y-m-d H:i:s'),
                    'status_vigencia' => $doc->estado_expiracion,
                    'status_color' => $doc->status_color,
                    'url_archivo' => $doc->archivo ? asset($doc->archivo) : null
                ];
            });

        return response()->json([
            'bus' => $bus,
            'conductor' => $ultimoViaje ? [
                'nombre' => $ultimoViaje->conductor->primer_nombre . ' ' . $ultimoViaje->conductor->primer_apellido,
                'documento' => $ultimoViaje->conductor->doc_usuario,
                'licencia' => 'LC-' . $ultimoViaje->conductor->doc_usuario // Placeholder
            ] : null,
            'ruta' => ($ultimoViaje && $ultimoViaje->ruta) ? $ultimoViaje->ruta->nombre_ruta : 'Sin ruta asignada',
            'documentos' => $documentos
        ]);
    }

    /**
     * Devuelve el historial completo (bóveda) de documentos para un vehículo.
     */
    public function historialDocumental($placa)
    {
        $user = Auth::user();
        
        $bus = Bus::where('placa', $placa)->where('doc_propietario', $user->doc_usuario)->first();
        if (!$bus) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $documentos = Documento::with('tipoDocumento')
            ->where('placa', $placa)
            ->orderBy('id_estado', 'asc') // 1 primero
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($doc) {
                return [
                    'id_documento' => $doc->id_documento,
                    'nombre' => $doc->nombre ?? 'Sin nombre',
                    'tipo_nombre' => $doc->tipoDocumento->nombre ?? 'Documento',
                    'fecha_carga' => $doc->created_at->format('d/m/Y'),
                    'fecha_vencimiento' => $doc->fecha_vencimiento->format('d/m/Y'),
                    'status_vigencia' => $doc->estado_expiracion,
                    'status_color' => $doc->status_color,
                    'es_archivado' => $doc->id_estado == 11,
                    'url_archivo' => $doc->archivo ? asset($doc->archivo) : null
                ];
            });

        // Agrupar por tipo para renderizar secciones
        $grupos = [];
        foreach ($documentos as $doc) {
            $grupos[$doc['tipo_nombre']][] = $doc;
        }

        return response()->json([
            'placa' => $placa,
            'grupos' => $grupos
        ]);
    }

    /**
     * Obtiene el detalle de una asignación específica, incluyendo los viajes (recorridos) realizados.
     */
    public function getDetalleAsignacion($id)
    {
        $user = Auth::user();
        
        // Cargar la asignación (viaje)
        $asignacion = Viaje::with(['ruta.barrioOrigen', 'ruta.barrioDestino', 'conductor', 'bus'])
            ->where('id_viaje', $id)
            ->whereIn('placa', function($query) use ($user) {
                $query->select('placa')->from('bus')->where('doc_propietario', $user->doc_usuario);
            })
            ->firstOrFail();

        // El turno dura 8 horas
        $horaInicio = \Carbon\Carbon::parse($asignacion->fecha);
        $horaFin = $horaInicio->copy()->addHours(8);

        // Obtener los recorridos realizados durante este turno
        // Un detalle importante: los recorridos deben ser del mismo bus, conductor y ruta? 
        // El requerimiento dice "viajes realizados durante ese turno".
        // Generalmente un turno está asociado a un bus y un conductor.
        $recorridos = \App\Models\Recorrido::where('id_viaje', $asignacion->id_viaje)
            ->orderBy('hora_salida', 'asc')
            ->get();

        // Totales referenciando tarjetas/ventas directamente a LA ASIGNACIÓN (Viaje), NO por recorrido
        // Totales referenciando tarjetas/ventas directamente a LA ASIGNACIÓN (Viaje)
        $ventasViaje = \App\Models\VentaViaje::where('id_viaje', $asignacion->id_viaje)->get();
        $totalPasajeros = $ventasViaje->count();
        $totalIngresos = $ventasViaje->sum('valor');
        
        // Contar trayectos
        // Trayecto Origen -> Destino vs Destino -> Origen
        $ruta = $asignacion->ruta;
        $totalOrigenDestino = 0;
        $totalDestinoOrigen = 0;

        foreach ($recorridos as $rec) {
            // Lógica simple: si es el primer viaje, asumimos O->D, luego alternamos?
            // O mejor, si tenemos barrios de origen/destino en la ruta.
            // Para este ejemplo, usaremos un contador alternado o buscaremos algo en el modelo.
            // El requerimiento pide "Origen -> Destino" y "Destino -> Origen".
            // Implementaremos una lógica basada en el índice para este desarrollo formativo
            // a menos que encontremos un campo.
        }

        // Si no hay campo de sentido, usaremos alternancia para la visualización si es necesario,
        // pero idealmente deberíamos tenerlo. Vamos a chequear el modelo Ruta.
        
        return response()->json([
            'asignacion' => [
                'id_viaje' => $asignacion->id_viaje,
                'placa' => $asignacion->placa,
                'conductor' => $asignacion->conductor->primer_nombre . ' ' . $asignacion->conductor->primer_apellido,
                'ruta' => $asignacion->ruta->nombre_ruta,
                'inicio' => $horaInicio->format('H:i'),
                'fin' => $horaFin->format('H:i'),
                'fecha' => $horaInicio->format('d/m/Y')
            ],
            'recorridos' => $recorridos->map(function($r, $index) use ($ruta, $recorridos, $ventasViaje) {
                // Sentido del recorrido (ida o vuelta)
                $esIda = ($index % 2 == 0);
                
                $horaSalida = \Carbon\Carbon::parse($r->hora_salida);
                
                // Si no tiene hora_llegada, el torniquete asume los pasajes hasta el inicio del siguiente recorrido, o hasta el final del turno
                $horaLlegada = $r->hora_llegada ? \Carbon\Carbon::parse($r->hora_llegada) : null;
                if (!$horaLlegada && isset($recorridos[$index + 1])) {
                    $horaLlegada = \Carbon\Carbon::parse($recorridos[$index + 1]->hora_salida);
                }
                
                // Filtrar ventas por el rango de tiempo de este recorrido
                $ventasRecorrido = $ventasViaje->filter(function($venta) use ($horaSalida, $horaLlegada) {
                    $fechaVenta = \Carbon\Carbon::parse($venta->fecha);
                    if ($horaLlegada) {
                        return $fechaVenta->between($horaSalida, $horaLlegada);
                    }
                    return $fechaVenta->greaterThanOrEqualTo($horaSalida);
                });
                
                $pasajerosRec = $ventasRecorrido->count();
                $ingresosRec = $ventasRecorrido->sum('valor');

                return [
                    'trayecto' => !$esIda 
                        ? ($ruta->barrioDestino->nombre ?? 'Destino') . ' → ' . ($ruta->barrioOrigen->nombre ?? 'Origen')
                        : ($ruta->barrioOrigen->nombre ?? 'Origen') . ' → ' . ($ruta->barrioDestino->nombre ?? 'Destino'),
                    'hora_salida' => $horaSalida->format('H:i'),
                    'hora_llegada' => $r->hora_llegada ? \Carbon\Carbon::parse($r->hora_llegada)->format('H:i') : 'En curso',
                    'cantidad_pasajeros' => $pasajerosRec,
                    'ingresos' => $ingresosRec,
                    'evidencia' => $r->foto_torniquete ? asset('storage/' . $r->foto_torniquete) : null,
                    'es_regreso' => !$esIda
                ];
            }),
            'resumen' => [
                'total_origen_destino' => $recorridos->filter(fn($r, $i) => $i % 2 == 0)->count(),
                'total_destino_origen' => $recorridos->filter(fn($r, $i) => $i % 2 != 0)->count(),
                'total_pasajeros' => $totalPasajeros,
                'total_ingresos' => $totalIngresos
            ]
        ]);
    }


}
