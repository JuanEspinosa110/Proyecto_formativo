<?php

namespace App\Http\Controllers\JefeMantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mantenimiento;
use App\Models\DetalleMantenimiento;
use App\Models\Bus;
use App\Models\TipoMantenimiento;
use App\Models\ReporteFalla;
use Illuminate\Support\Facades\DB;

// IDs de estado del bus
define('ESTADO_BUS_ACTIVO', 1);
define('ESTADO_BUS_EN_MANTENIMIENTO', 4);
// IDs de estado del registro de mantenimiento
define('ESTADO_MANT_EN_TALLER', 4);
define('ESTADO_MANT_FINALIZADO', 7);

class MantenimientoController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $busesEnTaller      = Bus::where('id_estado', ESTADO_BUS_EN_MANTENIMIENTO)->count();
        $reportesPendientes = ReporteFalla::whereHas('estado', fn($q) => $q->where('nombre_estado', '!=', 'Atendido'))->count();
        $trabajosEnCurso    = Mantenimiento::where('id_estado', ESTADO_MANT_EN_TALLER)->count();
        $trabajosFinalizados= Mantenimiento::where('id_estado', ESTADO_MANT_FINALIZADO)->count();

        // Últimos 5 mantenimientos en curso
        $enCurso = Mantenimiento::with(['bus'])
            ->where('id_estado', ESTADO_MANT_EN_TALLER)
            ->orderBy('fecha_mantenimiento', 'desc')
            ->take(5)
            ->get();

        // Últimos 5 reportes sin atender
        $reportesRecientes = ReporteFalla::with(['bus', 'conductor'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Alertas de mantenimiento preventivo (Próximos o Vencidos)
        // Buscamos mantenimientos finales donde su fecha_proximo o km_proximo estén cerca o vencidos
        // comparados con el kilometraje actual del bus y la fecha actual.
        $alertasMantenimiento = DB::select("
            SELECT m.id_mantenimiento, m.fecha_proximo, m.km_proximo, b.placa, b.kilometraje as km_actual
            FROM mantenimiento m
            JOIN bus b ON m.placa = b.placa
            WHERE m.id_mantenimiento = (
                SELECT sub.id_mantenimiento
                FROM mantenimiento sub
                WHERE sub.placa = b.placa AND sub.id_estado = 7 -- 7 = FINALIZADO (Mantenimiento completado)
                ORDER BY sub.fecha_mantenimiento DESC
                LIMIT 1
            )
            AND (
                (m.fecha_proximo IS NOT NULL AND m.fecha_proximo <= CURRENT_DATE + INTERVAL 7 DAY)
                OR
                (m.km_proximo IS NOT NULL AND m.km_proximo <= b.kilometraje + 500)
            )
            AND b.id_estado NOT IN (4, 9) -- Excluir buses en taller o bloqueados
        ");

        return view('jefemantenimiento.dashboard', compact(
            'busesEnTaller',
            'reportesPendientes',
            'trabajosEnCurso',
            'trabajosFinalizados',
            'enCurso',
            'reportesRecientes',
            'alertasMantenimiento'
        ));
    }

    // ─── Listados ─────────────────────────────────────────────────────────────

    /** Vista del Jefe de Mantenimiento */
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['bus', 'estado'])
            ->orderBy('fecha_mantenimiento', 'desc')
            ->paginate(10);


        return view('jefemantenimiento.index', compact('mantenimientos'));
    }

    /** Vista del Admin de Empresa */
    public function indexAdmin()
    {
        $mantenimientos = Mantenimiento::with(['bus', 'estado'])
            ->orderBy('fecha_mantenimiento', 'desc')
            ->paginate(10);

        return view('admin.mantenimiento.index', compact('mantenimientos'));
    }

    // ─── Crear registro ───────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $buses  = Bus::with('estado')->get();
        $tipos  = TipoMantenimiento::all();
        $placa_predefinida = $request->query('placa');
        $reporte_id        = $request->query('reporte_id');
        $origen            = $request->query('origen', 'jefe'); // 'admin' | 'jefe'

        $view = $origen === 'admin'
            ? 'admin.mantenimiento.create'
            : 'jefemantenimiento.create';

        return view($view, compact('buses', 'tipos', 'placa_predefinida', 'reporte_id', 'origen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'placa'                            => 'required|exists:bus,placa',
            'fecha_mantenimiento'              => 'required|date',
            'costo_total'                      => 'required|numeric|min:0',
            'descripcion_general'              => 'nullable|string|max:500',
            'detalles'                         => 'required|array|min:1',
            'detalles.*.id_tipo_mantenimiento' => 'required|exists:tipo_mantenimiento,id_tipo_mantenimiento',
            'detalles.*.descripcion'           => 'required|string',
            'detalles.*.evidencia_foto'        => 'nullable|image|max:4096',
            'detalles.*.fecha_proximo'         => 'nullable|date|after_or_equal:fecha_mantenimiento',
            'detalles.*.km_proximo'            => 'nullable|numeric|min:0',
            'origen'                           => 'nullable|in:admin,jefe',
        ], [
            'placa.required'                            => 'Debe seleccionar un bus.',
            'placa.exists'                              => 'El bus seleccionado no existe en el sistema.',
            'fecha_mantenimiento.required'              => 'La fecha de mantenimiento es obligatoria.',
            'fecha_mantenimiento.date'                  => 'La fecha de mantenimiento no es válida.',
            'costo_total.required'                      => 'Debe indicar un costo total estimado (puede ser 0).',
            'costo_total.numeric'                       => 'El costo total debe ser un número.',
            'costo_total.min'                           => 'El costo total no puede ser negativo.',
            'detalles.required'                         => 'Debe agregar al menos una tarea de mantenimiento.',
            'detalles.min'                              => 'Debe agregar al menos una tarea de mantenimiento.',
            'detalles.*.descripcion.required'           => 'Todas las tareas deben tener una descripción.',
            'detalles.*.evidencia_foto.image'           => 'El archivo subido como foto debe ser una imagen.',
            'detalles.*.evidencia_foto.max'             => 'La foto no debe pesar más de 4MB.',
            'detalles.*.fecha_proximo.after_or_equal'   => 'La fecha del próximo mantenimiento no puede ser anterior a la del mantenimiento actual.',
            'detalles.*.km_proximo.min'                 => 'El próximo kilometraje no puede ser menor a cero.'
        ]);

        try {
            DB::beginTransaction();

            $bus = Bus::where('placa', $request->placa)->firstOrFail();

            // Calcular el 'fecha_proximo' y 'km_proximo' global basado en todos los detalles
            $minFechaProximo = null;
            $minKmProximo = null;

            foreach ($request->detalles as $detalle) {
                if (!empty($detalle['fecha_proximo'])) {
                    if (is_null($minFechaProximo) || strtotime($detalle['fecha_proximo']) < strtotime($minFechaProximo)) {
                        $minFechaProximo = $detalle['fecha_proximo'];
                    }
                }
                if (!empty($detalle['km_proximo'])) {
                    if (is_null($minKmProximo) || $detalle['km_proximo'] < $minKmProximo) {
                        $minKmProximo = $detalle['km_proximo'];
                    }
                }
            }

            $mantenimiento = Mantenimiento::create([
                'placa'               => $request->placa,
                'NIT'                 => $bus->NIT,
                'kilometraje'         => $bus->kilometraje,
                'fecha_mantenimiento' => $request->fecha_mantenimiento,
                'fecha_proximo'       => $minFechaProximo,
                'km_proximo'          => $minKmProximo,
                'costo_total'         => $request->costo_total,
                'id_estado'           => ESTADO_MANT_EN_TALLER,
            ]);

            foreach ($request->detalles as $i => $detalle) {
                $fotoPath = null;
                if ($request->hasFile("detalles.{$i}.evidencia_foto")) {
                    $file = $request->file("detalles.{$i}.evidencia_foto");
                    $fotoPath = $file->store('mantenimientos', 'public');
                }

                DetalleMantenimiento::create([
                    'id_mantenimiento'      => $mantenimiento->id_mantenimiento,
                    'id_tipo_mantenimiento' => $detalle['id_tipo_mantenimiento'],
                    'descripcion'           => $detalle['descripcion'],
                    'evidencia_foto'        => $fotoPath,
                ]);
            }

            // *** Cambiar bus a "En Mantenimiento" (id=7) ***
            $bus->update(['id_estado' => ESTADO_BUS_EN_MANTENIMIENTO]);

            // Si viene de un reporte, marcarlo como atendido (4 = En Curso/Taller)
            if ($request->reporte_id) {
                ReporteFalla::find($request->reporte_id)?->update(['id_estado' => 4]);
            }

            DB::commit();

            $origen = $request->input('origen', 'jefe');
            $ruta   = $origen === 'admin' ? 'admin.mantenimiento.index' : 'jefemantenimiento.index';

            return redirect()->route($ruta)
                ->with('success', "Bus {$request->placa} enviado al taller correctamente. Estado actualizado a: En Mantenimiento.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    // ─── Detalle ──────────────────────────────────────────────────────────────

    public function show($id, Request $request)
    {
        $mantenimiento = Mantenimiento::with(['bus.estado', 'detalles.tipoMantenimiento', 'estado'])
            ->findOrFail($id);

        $origen = $request->query('origen', 'jefe');
        $view   = $origen === 'admin' ? 'admin.mantenimiento.show' : 'jefemantenimiento.show';

        return view($view, compact('mantenimiento', 'origen'));
    }

    // ─── Finalizar (Admin de Empresa) ─────────────────────────────────────────
    // Admin marca el trabajo como "Finalizado" y libera el bus.

    public function finalizar($id)
    {
        try {
            DB::beginTransaction();

            $mantenimiento = Mantenimiento::with('bus')->findOrFail($id);

            if ((int)$mantenimiento->id_estado === ESTADO_MANT_FINALIZADO) {
                return back()->with('error', 'Este mantenimiento ya está finalizado.');
            }

            $mantenimiento->update(['id_estado' => ESTADO_MANT_FINALIZADO]);
            $mantenimiento->bus?->update(['id_estado' => ESTADO_BUS_ACTIVO]);

            DB::commit();
            return redirect()->route('admin.mantenimiento.index')
                ->with('success', "Mantenimiento finalizado. Bus {$mantenimiento->placa} disponible nuevamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al finalizar: ' . $e->getMessage());
        }
    }

    // ─── Aprobar Salida (Jefe de Mantenimiento) ───────────────────────────────
    // El Jefe verifica el trabajo y libera el bus.

    public function aprobarSalida($id)
    {
        try {
            DB::beginTransaction();

            $mantenimiento = Mantenimiento::with('bus')->findOrFail($id);

            if ((int)$mantenimiento->id_estado === ESTADO_MANT_FINALIZADO) {
                return back()->with('error', 'Este mantenimiento ya fue aprobado.');
            }

            $mantenimiento->update(['id_estado' => ESTADO_MANT_FINALIZADO]);
            $mantenimiento->bus?->update(['id_estado' => ESTADO_BUS_ACTIVO]);

            DB::commit();
            return redirect()->route('jefemantenimiento.index')
                ->with('success', "Salida aprobada. Bus {$mantenimiento->placa} disponible nuevamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar salida: ' . $e->getMessage());
        }
    }

    // ─── Historial por bus (Admin) ────────────────────────────────────────────

    public function historialBus($placa)
    {
        $bus = Bus::with('estado')->where('placa', $placa)->firstOrFail();

        $mantenimientos = Mantenimiento::with(['detalles.tipoMantenimiento', 'estado'])
            ->where('placa', $placa)
            ->orderBy('fecha_mantenimiento', 'desc')
            ->get();

        $totalGastado  = $mantenimientos->sum('costo_total');
        $gastoEsteAnio = $mantenimientos
            ->where('fecha_mantenimiento', '>=', now()->startOfYear()->toDateString())
            ->sum('costo_total');
        $gastoEsteMes  = $mantenimientos
            ->where('fecha_mantenimiento', '>=', now()->startOfMonth()->toDateString())
            ->sum('costo_total');

        return view('admin.mantenimiento.historial_bus', compact(
            'bus', 'mantenimientos', 'totalGastado', 'gastoEsteAnio', 'gastoEsteMes'
        ));
    }
}
