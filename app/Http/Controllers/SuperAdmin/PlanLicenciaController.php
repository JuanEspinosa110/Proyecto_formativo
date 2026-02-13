<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanLicenciaController extends Controller
{
    /**
     * Mostrar listado de planes
     */
    public function index()
    {
        $planes = DB::table('planes_licencia')
            ->join('estado', 'planes_licencia.id_estado', '=', 'estado.id_estado')
            ->select(
                'planes_licencia.*',
                'estado.nombre_estado'
            )
            ->orderBy('planes_licencia.precio', 'asc')
            ->get();

        // Contar licencias por plan
        foreach ($planes as $plan) {
            $plan->total_licencias = DB::table('licencias')
                ->where('id_plan', $plan->id_plan)
                ->count();
            
            $plan->licencias_activas = DB::table('licencias')
                ->where('id_plan', $plan->id_plan)
                ->whereIn('id_estado', [1, 22]) // VIGENTE o RENOVADA
                ->count();
        }

        // Estadísticas
        $stats = [
            'total_planes' => $planes->count(),
            'planes_activos' => $planes->where('id_estado', 1)->count(),
            'planes_inactivos' => $planes->where('id_estado', 2)->count(),
            'total_licencias_vendidas' => DB::table('licencias')->count(),
        ];

        return view('superadmin.planes.index', compact('planes', 'stats'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('superadmin.planes.crear');
    }

    /**
     * Guardar nuevo plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_plan' => 'required|string|max:50|unique:planes_licencia,nombre_plan',
            'duracion_meses' => 'required|integer|min:1|max:120',
            'precio' => 'required|numeric|min:0|max:999999999.99',
            'descripcion' => 'required|string|max:500',
        ], [
            'nombre_plan.required' => 'El nombre del plan es obligatorio',
            'nombre_plan.unique' => 'Ya existe un plan con este nombre',
            'duracion_meses.required' => 'La duración es obligatoria',
            'duracion_meses.min' => 'La duración debe ser al menos 1 mes',
            'duracion_meses.max' => 'La duración no puede superar 120 meses',
            'precio.required' => 'El precio es obligatorio',
            'precio.min' => 'El precio debe ser mayor a 0',
            'descripcion.required' => 'La descripción es obligatoria',
        ]);

        try {
            DB::table('planes_licencia')->insert([
                'nombre_plan' => $validated['nombre_plan'],
                'duracion_meses' => $validated['duracion_meses'],
                'precio' => $validated['precio'],
                'descripcion' => $validated['descripcion'],
                'id_estado' => 1, // Activo
            ]);

            return redirect()
                ->route('superadmin.planes.index')
                ->with('success', 'Plan de licencia creado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el plan: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $plan = DB::table('planes_licencia')
            ->where('id_plan', $id)
            ->first();

        if (!$plan) {
            return redirect()
                ->route('superadmin.planes.index')
                ->with('error', 'Plan no encontrado');
        }

        // Contar licencias asociadas
        $plan->total_licencias = DB::table('licencias')
            ->where('id_plan', $id)
            ->count();

        return view('superadmin.planes.editar', compact('plan'));
    }

    /**
     * Actualizar plan
     */
    public function update(Request $request, $id)
    {
        $plan = DB::table('planes_licencia')->where('id_plan', $id)->first();

        if (!$plan) {
            return redirect()
                ->route('superadmin.planes.index')
                ->with('error', 'Plan no encontrado');
        }

        $validated = $request->validate([
            'nombre_plan' => 'required|string|max:50|unique:planes_licencia,nombre_plan,' . $id . ',id_plan',
            'duracion_meses' => 'required|integer|min:1|max:120',
            'precio' => 'required|numeric|min:0|max:999999999.99',
            'descripcion' => 'required|string|max:500',
            'id_estado' => 'required|in:1,2', // 1=Activo, 2=Inactivo
        ]);

        try {
            DB::table('planes_licencia')
                ->where('id_plan', $id)
                ->update([
                    'nombre_plan' => $validated['nombre_plan'],
                    'duracion_meses' => $validated['duracion_meses'],
                    'precio' => $validated['precio'],
                    'descripcion' => $validated['descripcion'],
                    'id_estado' => $validated['id_estado'],
                ]);

            return redirect()
                ->route('superadmin.planes.index')
                ->with('success', 'Plan actualizado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar plan
     */
    public function destroy($id)
    {
        $plan = DB::table('planes_licencia')->where('id_plan', $id)->first();

        if (!$plan) {
            return redirect()
                ->route('superadmin.planes.index')
                ->with('error', 'Plan no encontrado');
        }

        // Verificar si hay licencias asociadas
        $licenciasAsociadas = DB::table('licencias')
            ->where('id_plan', $id)
            ->count();

        if ($licenciasAsociadas > 0) {
            return redirect()
                ->route('superadmin.planes.index')
                ->with('error', "No se puede eliminar el plan. Hay {$licenciasAsociadas} licencia(s) asociada(s). Desactívelo en su lugar.");
        }

        try {
            DB::table('planes_licencia')
                ->where('id_plan', $id)
                ->delete();

            return redirect()
                ->route('superadmin.planes.index')
                ->with('success', 'Plan eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->route('superadmin.planes.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del plan (Activar/Desactivar)
     */
    public function toggleEstado($id)
    {
        $plan = DB::table('planes_licencia')->where('id_plan', $id)->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan no encontrado'], 404);
        }

        $nuevoEstado = $plan->id_estado == 1 ? 2 : 1; // Toggle entre activo/inactivo

        DB::table('planes_licencia')
            ->where('id_plan', $id)
            ->update(['id_estado' => $nuevoEstado]);

        $accion = $nuevoEstado == 1 ? 'activado' : 'desactivado';

        return response()->json([
            'success' => true,
            'message' => "Plan {$accion} exitosamente",
            'nuevo_estado' => $nuevoEstado
        ]);
    }

    /**
     * Registrar actividad
     */

}
