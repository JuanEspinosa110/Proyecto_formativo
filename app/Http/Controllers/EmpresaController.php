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

class EmpresaController extends Controller
{
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

        $section = $request->query('section', 'dashboard');
        
        $data = [
            'section' => $section,
        ];

        if ($section == 'usuarios') {
            $query = Usuario::where('NIT', $nit)->whereIn('id_tipo_usuario', [3, 6]); // Conductor y Propietario
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('primer_nombre', 'like', "%{$search}%")
                      ->orWhere('primer_apellido', 'like', "%{$search}%")
                      ->orWhere('doc_usuario', 'like', "%{$search}%");
                });
            }
            $data['usuarios'] = $query->paginate(10, ['*'], 'page_usr');
            $data['tiposUsuario'] = \Illuminate\Support\Facades\DB::table('tipo_usuario')->whereIn('id_tipo_usuario', [3, 6])->get();
            $data['estados'] = Estado::whereIn('id_estado', [1, 2])->get();
        } 
        elseif ($section == 'buses') {
            $query = Bus::where('NIT', $nit)->with('estado');
            if ($request->filled('placa')) {
                $query->where('placa', 'like', '%' . $request->placa . '%');
            }
            $data['buses'] = $query->paginate(10, ['*'], 'page_bus');
            $data['propietarios'] = Usuario::where('NIT', $nit)->where('id_tipo_usuario', 6)->get();
        } 
        elseif ($section == 'documentos') {
            $query = Documento::where('NIT', $nit)->with(['tipoDocumento', 'estado', 'bus'])->whereNotNull('placa');
            if ($request->filled('placa')) {
                $query->where('placa', 'like', '%' . $request->placa . '%');
            }
            $data['documentos'] = $query->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page_doc');
        } 
        elseif ($section == 'asignaciones') {
            $query = Viaje::whereHas('bus', function($q) use ($nit) { $q->where('NIT', $nit); })->with(['ruta', 'conductor', 'estado']);
            if ($request->filled('placa')) {
                $query->where('placa', $request->placa);
            }
            $data['asignaciones'] = $query->orderBy('fecha', 'desc')->paginate(10, ['*'], 'page_asig');
            $data['busesDisponibles'] = Bus::where('NIT', $nit)->get()->filter(fn(Bus $b) => $b->isOperable());
            $data['rutas'] = Ruta::get();
            $data['conductores'] = Usuario::where('NIT', $nit)->where('id_tipo_usuario', 3)->get();
        } 
        else {
            // Stats para dashboard principal
            $data['stats'] = [
                'usuarios' => Usuario::where('NIT', $nit)->whereIn('id_tipo_usuario', [3, 6])->count(),
                'buses' => Bus::where('NIT', $nit)->count(),
                'documentos_pendientes' => Documento::where('NIT', $nit)->where('id_estado', 5)->count(),
                'asignaciones_hoy' => Viaje::whereHas('bus', function($q) use ($nit) { $q->where('NIT', $nit); })->whereDate('fecha', now()->toDateString())->count(),
            ];
        }

        return view('empresa.dashboard', $data);
    }

    /**
     * Devuelve estadísticas en JSON para las gráficas del Dashboard del Auxiliar.
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::user();
            $nit  = $user->NIT ?? null;

            if (!$nit) {
                return response()->json(['error' => 'Sin empresa asociada'], 400);
            }

            $empresa    = \App\Models\Empresa::where('NIT', $nit)->first();
            $usuarios   = Usuario::where('NIT', $nit)->get();
            $documentos = Documento::where('NIT', $nit)->get();
            $buses      = Bus::with('estado')->where('NIT', $nit)->get();
            $viajes     = Viaje::with('ruta')
                ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
                ->get();

            return response()->json([
                'empresa'    => $empresa,
                'usuarios'   => $usuarios,
                'documentos' => $documentos,
                'buses'      => $buses,
                'viajes'     => $viajes,
                'totales'    => [
                    'usuarios'   => $usuarios->count(),
                    'documentos' => $documentos->count(),
                    'buses'      => $buses->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('EmpresaController@stats: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Crear nuevo usuario (Conductor/Propietario)
     */
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'doc_usuario' => 'required|digits_between:6,15|numeric|unique:usuario,doc_usuario',
            'id_tipo_usuario' => 'required|in:3,6', // 3=Conductor, 6=Propietario ONLY
            'id_estado' => 'required|exists:estado,id_estado',
            'correo' => 'required|email|unique:usuario,correo',
            'password' => 'required|min:6',
        ]);

        $data = $request->except(['password']);
        $data['password'] = bcrypt($request->password);
        $data['NIT'] = Auth::user()->getActiveNit();

        Usuario::create($data);

        return redirect()->back()->with('success', 'Usuario creado exitosamente.');
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
     * Descargar Reporte
     */
    public function descargarReporte(Request $request)
    {
        return redirect()->back()->with('success', 'Descarga de reporte en desarrollo.');
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
 
