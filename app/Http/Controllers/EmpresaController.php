<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Usuario;
use App\Models\Documento;
use App\Models\Viaje;
use App\Models\Ruta;
use App\Models\TipoDocumento;
use App\Models\Estado;
use App\Models\HistorialBus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\BusService;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevoUsuarioCreado;

class EmpresaController extends Controller
{
    protected $busService;

    public function __construct(BusService $busService)
    {
        $this->busService = $busService;
    }
    /**
     * Muestra el dashboard de la empresa para el Auxiliar.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $nit = $user->getActiveNit();

        if (!$nit) {
            return redirect()->route('home')->with('error', 'No tienes empresa asociada.');
        }

        $tab = $request->query('tab', 'personal');
        
        // --- 1. DATOS COMPARTIDOS Y METADATOS ---
        $data = [
            'tab' => $tab,
            'section' => $tab,
            'roles' => \Illuminate\Support\Facades\DB::table('tipo_usuario')->whereIn('id_tipo_usuario', [3, 4, 5])->get(),
            'estados' => Estado::whereIn('id_estado', [1, 2])->get(),
            'estadosBus' => Estado::whereIn('id_estado', [1, 2, 9])->get(), // Activo, Inactivo, En mantenimiento
            'rutas' => Ruta::all(),
            'tiposDocumento' => TipoDocumento::all(),
            'propietarios' => Usuario::where('NIT', $nit)->where('id_tipo_usuario', 5)->get(), // Solo para selección de bus
            'conductores' => Usuario::where('NIT', $nit)->where('id_tipo_usuario', 3)->get(),
            'busesDisponibles' => Bus::where('NIT', $nit)->get()->filter(function(Bus $b) { return $b->isOperable(); }),
        ];

        // --- 2. CARGA DE LISTADOS (PAGINADOS) ---
        // Inicializamos las variables con paginación base para evitar errores en las vistas de pestañas no activas
        
        // Pestaña Personal (Solo Conductores, Auxiliares y Propietarios por requerimiento)
        $queryUsr = Usuario::with(['tipoUsuario', 'estado'])->where('NIT', $nit)->whereIn('id_tipo_usuario', [3, 4, 5]);
        if ($tab == 'personal') {
            if ($request->filled('search')) {
                $search = $request->search;
                $queryUsr->where(function($q) use ($search) {
                    $q->where('primer_nombre', 'like', "%{$search}%")
                      ->orWhere('primer_apellido', 'like', "%{$search}%")
                      ->orWhere('doc_usuario', 'like', "%{$search}%");
                });
            }
            if ($request->filled('role')) {
                $queryUsr->where('id_tipo_usuario', $request->role);
                $data['selectedRole'] = $request->role;
            }
        }
        $data['usuarios'] = $queryUsr->paginate(10, ['*'], 'page_usr');

        // Pestaña Flota
        $queryBus = Bus::where('NIT', $nit)->with('estado');
        if ($tab == 'flota') {
            if ($request->filled('search_bus')) {
                $queryBus->where('placa', 'like', '%' . $request->search_bus . '%');
            }
            if ($request->filled('estado_bus')) {
                $queryBus->where('id_estado', $request->estado_bus);
            }
        }
        $data['buses'] = $queryBus->paginate(10, ['*'], 'page_bus');

        // Pestaña Documentos
        $queryDoc = Documento::where('NIT', $nit)->with(['tipoDocumento', 'estado', 'bus'])->whereNotNull('placa');
        if ($tab == 'documentacion') {
            if ($request->filled('placa')) {
                $queryDoc->where('placa', 'like', '%' . $request->placa . '%');
            }
        }
        $data['documentos'] = $queryDoc->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page_doc');

        // Pestaña Asignaciones
        $queryAsig = Viaje::whereHas('bus', function($q) use ($nit) { $q->where('NIT', $nit); })->with(['ruta', 'conductor', 'estado', 'bus.propietario']);
        if ($tab == 'asignaciones') {
            if ($request->filled('search_asignacion')) {
                $s = $request->search_asignacion;
                $queryAsig->where(function($q) use ($s) {
                    $q->where('placa', 'like', "%$s%")
                      ->orWhereHas('conductor', fn($sq) => $sq->where('primer_nombre', 'like', "%$s%")->orWhere('primer_apellido', 'like', "%$s%"));
                });
            }
            if ($request->filled('id_ruta')) {
                $queryAsig->where('id_ruta', $request->id_ruta);
            }
        }
        $data['asignaciones'] = $queryAsig->orderBy('fecha', 'desc')->paginate(10, ['*'], 'page_asig');

        // --- 3. ESTADÍSTICAS Y ALERTAS ---
        $data['licenciasAlerta'] = Documento::where('id_tipo_documento', 3) // Licencias
            ->whereHas('usuario', fn($q) => $q->where('NIT', $nit))
            ->where('id_estado', '!=', 1) // No aprobadas o por vencer
            ->get();

        $data['stats'] = [
            'total_usuarios' => Usuario::where('NIT', $nit)->count(),
            'total_buses' => Bus::where('NIT', $nit)->count(),
            'pendientes_doc' => Documento::where('NIT', $nit)->where('id_estado', 5)->count(),
            'viajes_hoy' => Viaje::whereHas('bus', fn($q) => $q->where('NIT', $nit))->whereDate('fecha', now()->toDateString())->count(),
        ];

        return view('empresa.dashboard', $data);
    }

    /**
     * Devuelve estadísticas en JSON para las gráficas del Dashboard del Auxiliar.
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::user();
            $nit  = $user->getActiveNit();

            if (!$nit) {
                return response()->json(['error' => 'Sin empresa asociada'], 400);
            }

            $usuarios   = Usuario::where('NIT', $nit)->get();
            $documentos = Documento::where('NIT', $nit)->get();
            $buses      = Bus::with('estado')->where('NIT', $nit)->get();
            $asignaciones_hoy = Viaje::whereHas('bus', fn($q) => $q->where('NIT', $nit))
                ->whereDate('fecha', now()->toDateString())
                ->count();

            return response()->json([
                'totales' => [
                    'usuarios'   => $usuarios->count(),
                    'buses'      => $buses->count(),
                    'documentos_pendientes' => $documentos->where('id_estado', 5)->count(),
                    'asignaciones_hoy' => $asignaciones_hoy
                ],
                'buses_estado' => [
                    'operables' => $buses->filter(function(Bus $b) { return $b->isOperable(); })->count(),
                    'proximos_vencer' => $buses->filter(function(Bus $b) { return $b->hasDocumentsExpiringSoon(); })->count()
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('EmpresaController@stats: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Devuelve detalles de un bus para el modal Expediente.
     */
    public function showBus($placa)
    {
        try {
            $nit = Auth::user()->getActiveNit();
            // Verificar pertenencia
            $bus = Bus::where('placa', $placa)->where('NIT', $nit)->firstOrFail();
            
            $detalles = $this->busService->getBusDetails($placa);
            return response()->json($detalles);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se encontró el vehículo o no pertenece a su empresa.'], 404);
        }
    }

    /**
     * Devuelve el historial completo (bóveda) de documentos para un vehículo (Auxiliar).
     */
    public function historialDocumental($placa)
    {
        try {
            $nit = Auth::user()->getActiveNit();
            $bus = Bus::where('placa', $placa)->where('NIT', $nit)->firstOrFail();

            $documentos = \App\Models\Documento::with('tipoDocumento')
                ->where('placa', $placa)
                ->orderBy('id_estado', 'asc')
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
                        'es_archivado' => $doc->id_estado == 2,
                        'url_archivo' => $doc->archivo ? asset('storage/' . $doc->archivo) : null
                    ];
                });

            $grupos = [];
            foreach ($documentos as $doc) {
                $grupos[$doc['tipo_nombre']][] = $doc;
            }

            return response()->json([
                'placa' => $placa,
                'grupos' => $grupos
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Acceso denegado o vehículo no encontrado.'], 403);
        }
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'doc_usuario' => 'required|digits_between:6,15|numeric|unique:usuario,doc_usuario',
            'id_tipo_usuario' => 'required|in:3,4,5', // 3=Conductor, 4=Auxiliar, 5=Propietario ONLY
            'correo' => 'required|email|unique:usuario,correo',
            'password' => 'nullable|min:6',
        ]);

        $data = $request->except(['password', 'fecha_expedicion', 'fecha_vencimiento', 'archivo_licencia', 'id_estado']);
        $data['id_estado'] = 1; // Activo por defecto
        $passGenerada = $request->filled('password') ? $request->password : \Illuminate\Support\Str::random(10);
        $data['password'] = bcrypt($passGenerada);
        $data['NIT'] = Auth::user()->getActiveNit();
        $data['id_ciudad'] = Auth::user()->id_ciudad;

        $user = Usuario::create($data);

        // Enviar correo de notificación al nuevo usuario
        try {
            Mail::to($request->correo)->send(new NuevoUsuarioCreado(
                $request->primer_nombre . ' ' . $request->primer_apellido,
                $request->doc_usuario,
                $passGenerada,
                $data['NIT']
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de bienvenida (Aux): ' . $e->getMessage());
        }

        // Si es conductor, crear el documento de licencia
        if ($request->id_tipo_usuario == 3 && $request->hasFile('archivo_licencia')) {
            $pathLicencia = $request->file('archivo_licencia')->store('documentos', 'public');
            Documento::create([
                'nombre' => 'LICENCIA CONDUCCION',
                'archivo' => $pathLicencia,
                'fecha_expedicion' => $request->fecha_expedicion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'id_tipo_documento' => 3, 
                'doc_usuario' => $user->doc_usuario,
                'NIT' => $data['NIT'],
                'id_estado' => 1
            ]);
        }

        return redirect()->back()->with('success', 'Usuario creado exitosamente. Contraseña enviada/generada: ' . $passGenerada);
    }

    /**
     * Actualiza un usuario existente
     */
    public function updateUsuario(Request $request, $doc_usuario)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'id_tipo_usuario' => 'required|in:3,4,5',
            'correo' => 'required|email|unique:usuario,correo,'.$doc_usuario.',doc_usuario',
            'telefono' => 'required|numeric'
        ]);

        $user = Usuario::where('doc_usuario', $doc_usuario)->where('NIT', Auth::user()->getActiveNit())->firstOrFail();
        
        $data = $request->only(['primer_nombre', 'primer_apellido', 'segundo_nombre', 'segundo_apellido', 'correo', 'telefono', 'id_tipo_usuario', 'id_estado']);
        $user->update($data);

        return redirect()->back()->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Crear nuevo vehículo
     */
    public function storeBus(Request $request)
    {
        $request->validate([
            'placa' => 'required|regex:/^[A-Z]{3}[0-9]{3}$/|unique:bus,placa',
            'modelo' => 'required|string|max:4',
            'capacidad_pasajeros' => 'required|integer|min:1',
            'doc_propietario' => 'required|exists:usuario,doc_usuario',
            'linc_transito' => 'required|string|max:50',
        ]);

        $data = $request->all();
        $data['NIT'] = Auth::user()->getActiveNit();
        $data['id_estado'] = 2; // Inactivo por defecto

        Bus::create($data);

        return redirect()->back()->with('success', 'Vehículo registrado exitosamente. Está INACTIVO hasta que se aprueben documentos.');
    }

    /**
     * Aprobar documento
     */
    public function aprobarDocumento($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->id_estado = 1; // APROBADO (ACTIVO)
        $documento->save();

        if ($documento->placa) {
            $bus = $documento->bus;
            if ($bus) {
                $bus->id_estado = $bus->isOperable() ? 1 : 2; 
                $bus->save();
            }
        }

        return redirect()->back()->with('success', 'Documento APROBADO. Estado del vehículo actualizado.');
    }

    /**
     * Rechazar documento
     */
    public function rechazarDocumento($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->id_estado = 10; // RECHAZADO
        $documento->save();

        if ($documento->placa) {
            $bus = $documento->bus;
            if ($bus) {
                $bus->id_estado = 2; // Inactivo
                $bus->save();
            }
        }

        return redirect()->back()->with('success', 'Documento RECHAZADO.');
    }

    /**
     * Crear Asignación (Viaje)
     */
    public function storeAsignacion(Request $request)
    {
        $request->validate([
            'placa' => 'required|exists:bus,placa',
            'id_ruta' => 'required|exists:ruta,id_ruta',
            'doc_us' => 'required|exists:usuario,doc_usuario',
            'fecha' => 'required|date',
        ]);

        $bus = Bus::where('placa', $request->placa)->first();
        if ($bus && !$bus->isOperable()) {
            return redirect()->back()->withErrors(['placa' => 'El bus no es operable por documentos vencidos.']);
        }

        // Generar ID
        do {
            $id = random_int(100000, 999999);
        } while (Viaje::where('id_viaje', $id)->exists());

        $data = $request->all();
        $data['id_viaje'] = $id;
        $data['id_estado'] = 1; // Programado

        $viaje = Viaje::create($data);

        HistorialBus::create([
            'placa' => $data['placa'],
            'id_ruta' => $data['id_ruta'],
            'doc_us' => $data['doc_us'],
            'tipo_cambio' => 'ASIGNACION',
            'detalle' => "Nueva asignación. Auxiliar."
        ]);

        return redirect()->back()->with('success', 'Asignación creada exitosamente.');
    }

    /**
     * Inactiva una asignación (Cambiar a Estado 2), solo si es Programada y no ha iniciado.
     */
    public function inactivarViaje($id_viaje)
    {
        try {
            $viaje = Viaje::findOrFail($id_viaje);
            $nit = Auth::user()->getActiveNit();

            // Verificar pertenencia (a través del bus)
            if ($viaje->bus->NIT != $nit) {
                return back()->with('error', 'Acceso denegado.');
            }

            // Solo inactivar si es Programada (1) y no tiene recorridos
            if ($viaje->id_estado != 1 || $viaje->recorridos()->exists()) {
                return back()->with('error', 'Solo se pueden inactivar viajes programados que no hayan iniciado.');
            }

            $viaje->id_estado = 2; // INACTIVO
            $viaje->save();

            HistorialBus::create([
                'placa' => $viaje->placa,
                'id_ruta' => $viaje->id_ruta,
                'doc_us' => $viaje->doc_us,
                'tipo_cambio' => 'INACTIVACION',
                'detalle' => "Viaje #$id_viaje inactivado por Auxiliar."
            ]);

            return back()->with('success', 'Asignación inactivada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Descargar Reporte Completo de Operación (CSV).
     */
    public function descargarReporte(Request $request)
    {
        try {
            $nit = Auth::user()->getActiveNit();
            $viajes = Viaje::with(['ruta', 'conductor', 'bus', 'estado'])
                ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
                ->orderBy('fecha', 'desc')
                ->get();

            $filename = "Reporte_Operacion_" . now()->format('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['ID_Viaje', 'Fecha', 'Placa', 'Ruta', 'Conductor', 'Documento_Cond', 'Estado'];

            $callback = function() use ($viajes, $columns) {
                $file = fopen('php://output', 'w');
                // BOM para UTF-8 en Excel
                fputs($file, (chr(0xEF) . chr(0xBB) . chr(0xBF)));
                fputcsv($file, $columns, ';');

                foreach ($viajes as $v) {
                    fputcsv($file, [
                        $v->id_viaje,
                        $v->fecha,
                        $v->placa,
                        $v->ruta->nombre_ruta ?? 'N/A',
                        ($v->conductor->primer_nombre ?? '---') . ' ' . ($v->conductor->primer_apellido ?? ''),
                        $v->doc_us,
                        $v->estado->nombre_estado ?? '---'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte.');
        }
    }

    /**
     * Consulta disponibilidad de conductores y buses vía AJAX para el panel de Auxiliar.
     */
    public function getDisponibilidad(Request $request)
    {
        $user = Auth::user();
        $nit = $user->getActiveNit();
        
        $fecha = $request->fecha;
        $hora = $request->hora_salida;

        if (!$fecha) {
            return response()->json(['conductores' => [], 'buses' => []]);
        }

        try {
            // Combinar fecha y hora
            $datetimeStr = $fecha . ($hora ? ' ' . $hora : ' 00:00:00');
            $fechaObj = \Carbon\Carbon::parse($datetimeStr);

            $fechaSoloDia = $fechaObj->toDateString();
            $proposedStart = $fechaObj->toDateTimeString();
            $proposedEnd = $fechaObj->copy()->addHours(8)->toDateTimeString();

            // 1. Filtrar Conductores (Mismas reglas que Admin)
            $adminRoleIds = \Illuminate\Support\Facades\DB::table('tipo_usuario')
                ->where('nombre_tipo', 'like', '%admin%')
                ->pluck('id_tipo_usuario');

            $licenciasVigentes = \App\Models\Documento::where('id_tipo_documento', 3)
                ->where('id_estado', 1)
                ->whereDate('fecha_vencimiento', '>=', now()->format('Y-m-d'))
                ->pluck('doc_usuario');

            $conductores = Usuario::where('NIT', $nit)
                ->where('id_estado', 1)
                ->whereIn('doc_usuario', $licenciasVigentes)
                ->whereNotIn('id_tipo_usuario', $adminRoleIds)
                ->whereDoesntHave('viajes', function($q) use ($fechaSoloDia, $proposedStart, $proposedEnd) {
                    $q->whereIn('id_estado', [1, 5]) // Solo bloquean viajes ACTIVOS o FINALIZADOS
                      ->where(function($sq) use ($fechaSoloDia, $proposedStart, $proposedEnd) {
                          $sq->whereDate('fecha', $fechaSoloDia)
                             ->orWhere(function($ssq) use ($proposedStart, $proposedEnd) {
                                 $ssq->where('fecha', '<', $proposedEnd)
                                     ->whereRaw('DATE_ADD(fecha, INTERVAL 8 HOUR) > ?', [$proposedStart]);
                             });
                      });
                })
                ->get()
                ->map(fn($c) => [
                    'doc_usuario' => $c->doc_usuario,
                    'nombre_completo' => "{$c->primer_nombre} {$c->primer_apellido} ({$c->doc_usuario})"
                ]);

            // 2. Filtrar Buses
            $buses = Bus::where('NIT', $nit)
                ->get()
                ->filter(function($bus) use ($proposedStart, $proposedEnd) {
                    /** @var Bus $bus */
                    if (!$bus->isOperable()) return false;
                    
                    $conflict = Viaje::where('placa', $bus->placa)
                        ->where(function($q) use ($proposedStart, $proposedEnd) {
                            $q->where('fecha', '<', $proposedEnd)
                              ->whereRaw('DATE_ADD(fecha, INTERVAL 8 HOUR) > ?', [$proposedStart]);
                        })
                        ->exists();
                    
                    return !$conflict;
                })
                ->values()
                ->map(fn($b) => [
                    'placa' => $b->placa,
                    'modelo' => $b->modelo,
                    'label' => "{$b->placa} - {$b->modelo}"
                ]);

            return response()->json([
                'conductores' => $conductores,
                'buses' => $buses
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
 
