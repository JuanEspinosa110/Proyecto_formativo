<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Bus;
use App\Models\Ruta;
use App\Models\Usuario;
use App\Models\Estado;
use App\Models\ConcesionRuta;
use App\Models\HistorialBus;
use App\Http\Requests\AsignacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsignacionController extends Controller
{
    /**
     * Listado de asignaciones vinculado a la empresa del administrador.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        $query = Viaje::with(['bus', 'ruta', 'conductor', 'estado'])
            ->whereHas('bus', function($q) use ($nit) {
                $q->where('NIT', $nit);
            });

        // Filtrado por buscador (General)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('id_viaje', 'like', "%{$search}%")
                  ->orWhereHas('conductor', function($sq) use ($search) {
                      $sq->where('primer_nombre', 'like', "%{$search}%")
                        ->orWhere('primer_apellido', 'like', "%{$search}%")
                        ->orWhere('doc_usuario', 'like', "%{$search}%");
                  });
            });
        }

        // Filtros específicos
        if ($request->filled('id_viaje')) {
            $query->where('id_viaje', $request->id_viaje);
        }
        if ($request->filled('placa')) {
            $query->where('placa', $request->placa);
        }
        if ($request->filled('id_ruta')) {
            $query->where('id_ruta', $request->id_ruta);
        }
        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }
        if ($request->filled('conductor')) {
            $cSearch = $request->conductor;
            $query->whereHas('conductor', function($sq) use ($cSearch) {
                $sq->where(function($ssq) use ($cSearch) {
                    $ssq->where('primer_nombre', 'like', "%{$cSearch}%")
                        ->orWhere('primer_apellido', 'like', "%{$cSearch}%")
                        ->orWhere('doc_usuario', 'like', "%{$cSearch}%");
                });
            });
        }
        
        // Filtro por Fecha (Ej: '2026-03-30')
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', '=', $request->fecha);
        }

        // Orden ID ASC y paginación
        $asignaciones = $query->orderBy('id_viaje', 'asc')
            ->paginate(5)
            ->withQueryString();

        // Datos para los modales
        $buses = Bus::where('NIT', $nit)->get();
        // Rutas disponibles: Las autorizadas para esta empresa O las que no tienen NINGUNA concesión activa (Públicas)
        $rutas = Ruta::where(function($query) use ($nit) {
            $query->whereHas('concesiones', function($q) use ($nit) {
                $q->where('NIT', '=', (string)$nit)->where('id_estado', 1);
            })
            ->orWhereDoesntHave('concesiones', function($q) {
                $q->where('id_estado', 1);
            });
        })
        ->with(['concesiones' => function($q) {
            $q->where('id_estado', 1);
        }])
        ->orderBy('id_ruta', 'asc')
        ->get();
        $adminRoleIds = \Illuminate\Support\Facades\DB::table('tipo_usuario')
            ->where('nombre_tipo', 'like', '%admin%')
            ->pluck('id_tipo_usuario');

        $licenciasVigentes = \App\Models\Documento::where('id_tipo_documento', 3)
            ->where('id_estado', 1) // Activo/Vigente
            ->whereDate('fecha_vencimiento', '>=', now()->format('Y-m-d'))
            ->pluck('doc_usuario');

        $conductores = Usuario::where('NIT', $nit)
            ->where('id_estado', 1) // ACTIVO
            ->whereIn('doc_usuario', $licenciasVigentes)
            ->whereNotIn('id_tipo_usuario', $adminRoleIds)
            ->get();
        $estados = Estado::whereIn('nombre_estado', ['ACTIVO', 'INACTIVO'])->get();

        if ($request->has('ajax_options')) {
            return response()->json([
                'rutas' => $rutas
            ]);
        }

        if ($request->ajax()) {
            return view('admin.asignaciones.partials.table', compact(
                'asignaciones',
                'buses',
                'rutas',
                'conductores',
                'estados'
            ));
        }

        return view('admin.asignaciones.index', compact(
            'asignaciones',
            'buses',
            'rutas',
            'conductores',
            'estados'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva asignación.
     */
    public function create()
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        $buses = Bus::where('NIT', $nit)->get();
        // Rutas disponibles: Las autorizadas para esta empresa O las que no tienen NINGUNA concesión activa (Públicas)
        $rutas = Ruta::where(function($query) use ($nit) {
            $query->whereHas('concesiones', function($q) use ($nit) {
                $q->where('NIT', '=', (string)$nit)->where('id_estado', 1);
            })
            ->orWhereDoesntHave('concesiones', function($q) {
                $q->where('id_estado', 1);
            });
        })
        ->with(['concesiones' => function($q) {
            $q->where('id_estado', 1);
        }])
        ->orderBy('id_ruta', 'asc')
        ->get();
        
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
            ->get();

        $estados = Estado::whereIn('nombre_estado', ['ACTIVO', 'INACTIVO'])->get();

        return view('admin.asignaciones.create', compact('buses', 'rutas', 'conductores', 'estados'));
    }

    /**
     * Guardar una nueva asignación con ID generado manualmente.
     */
    public function store(AsignacionRequest $request)
    {
        $data = $request->validated();

        // Generar ID aleatorio único
        do {
            $id = random_int(100000, 999999);
        } while (Viaje::where('id_viaje', $id)->exists());

        $data['id_viaje'] = $id;

        Viaje::create($data);

        // Registrar en historial
        $ruta = Ruta::find($data['id_ruta']);
        $conductor = Usuario::where('doc_usuario', $data['doc_us'])->first();
        
        HistorialBus::create([
            'placa' => $data['placa'],
            'id_ruta' => $data['id_ruta'],
            'doc_us' => $data['doc_us'],
            'tipo_cambio' => 'ASIGNACION',
            'detalle' => "Nueva asignación. Ruta: " . ($ruta->nombre_ruta ?? $data['id_ruta']) . ". Conductor: " . ($conductor ? ($conductor->primer_nombre . ' ' . $conductor->primer_apellido) : $data['doc_us'])
        ]);

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro creado correctamente');
    }

    /**
     * Actualizar una asignación existente.
     */
    public function update(AsignacionRequest $request, $id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);
        
        // No permitir editar si ya está finalizado (ID 5)
        if ($viaje->id_estado == 5) {
            return redirect()->route('admin.asignaciones.index')
                ->with('error', 'No se puede editar una asignación que ya ha sido finalizada.');
        }
        
        // Registrar en historial
        $oldConductor = $viaje->getOriginal('doc_us');
        $oldRuta = $viaje->getOriginal('id_ruta');
        $data = $request->validated();
        
        $viaje->update($data);

        $tipoCambio = 'REASIGNACION';
        $detalles = [];

        if ($oldConductor != $viaje->doc_us) {
            $prevCond = Usuario::where('doc_usuario', $oldConductor)->first();
            $newCond = Usuario::where('doc_usuario', $viaje->doc_us)->first();
            $detalles[] = "Conductor: " . ($prevCond ? ($prevCond->primer_nombre . ' ' . $prevCond->primer_apellido) : $oldConductor) . " → " . ($newCond ? ($newCond->primer_nombre . ' ' . $newCond->primer_apellido) : $viaje->doc_us);
            $tipoCambio = 'CAMBIO_CONDUCTOR';
        }

        if ($oldRuta != $viaje->id_ruta) {
            $prevR = Ruta::find($oldRuta);
            $newR = Ruta::find($viaje->id_ruta);
            $detalles[] = "Ruta: " . ($prevR->nombre_ruta ?? $oldRuta) . " → " . ($newR->nombre_ruta ?? $viaje->id_ruta);
            $tipoCambio = ($tipoCambio == 'CAMBIO_CONDUCTOR') ? 'REASIGNACION' : 'CAMBIO_RUTA';
        }

        HistorialBus::create([
            'placa' => $viaje->placa,
            'id_ruta' => $viaje->id_ruta,
            'doc_us' => $viaje->doc_us,
            'tipo_cambio' => $tipoCambio,
            'detalle' => implode(". ", $detalles) ?: 'Actualización general de datos'
        ]);

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro actualizado correctamente');
    }
    
    /**
     * Eliminar una asignación.
     */
    public function destroy($id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);

        // No permitir eliminar si ya está finalizado (ID 5)
        if ($viaje->id_estado == 5) {
            return redirect()->route('admin.asignaciones.index')
                ->with('error', 'No se puede eliminar una asignación que ya ha sido finalizada.');
        }
        // Registrar en historial antes de borrar
        HistorialBus::create([
            'placa' => $viaje->placa,
            'id_ruta' => $viaje->id_ruta,
            'doc_us' => $viaje->doc_us,
            'tipo_cambio' => 'ELIMINACION',
            'detalle' => 'Se eliminó la asignación del viaje #' . $viaje->id_viaje
        ]);

        $viaje->delete();

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro eliminado correctamente');
    }

    /**
     * Consulta disponibilidad de conductores y buses vía AJAX.
     */
    public function getDisponibilidad(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();
        
        $fecha = $request->fecha;
        $hora = $request->hora_salida;

        if (!$fecha) {
            return response()->json(['conductores' => [], 'buses' => []]);
        }

        try {
            // Si vienen separados (Modal Auxiliar), los unimos. 
            // Si viene uno solo con la fecha completa (Create Admin), lo usamos tal cual.
            $fechaFull = ($hora && !str_contains($fecha, 'T') && !str_contains($fecha, ' ')) 
                ? "{$fecha} {$hora}" 
                : $fecha;

            $fechaObj = \Carbon\Carbon::parse($fechaFull);
            $fechaSoloDia = $fechaObj->toDateString();
            $proposedStart = $fechaObj->toDateTimeString();
            $proposedEnd = $fechaObj->copy()->addHours(8)->toDateTimeString();

            // 1. Filtrar Conductores
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
                ->get();

            $conductores = $conductores->map(function($c) {
                return [
                    'doc_usuario' => $c->doc_usuario,
                    'nombre_completo' => "{$c->primer_nombre} {$c->primer_apellido} ({$c->doc_usuario})"
                ];
            });

            // 2. Filtrar Buses
            $buses = Bus::where('NIT', $nit)
                ->get()
                ->filter(function(Bus $bus) use ($proposedStart, $proposedEnd) {
                    if (!$bus->isOperable()) return false;
                    
                    $conflict = Viaje::where('placa', $bus->placa)
                        ->where(function($q) use ($proposedStart, $proposedEnd) {
                            $q->where('fecha', '<', $proposedEnd)
                              ->whereRaw('DATE_ADD(fecha, INTERVAL 8 HOUR) > ?', [$proposedStart]);
                        })
                        ->exists();
                    
                    return !$conflict;
                })
                ->map(function($b) {
                    return [
                        'placa' => $b->placa,
                        'modelo' => $b->modelo,
                        'label' => "{$b->placa} - {$b->modelo}"
                    ];
                })
                ->values();

            return response()->json([
                'conductores' => $conductores,
                'buses' => $buses
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
