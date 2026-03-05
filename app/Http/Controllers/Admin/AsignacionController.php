<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Bus;
use App\Models\Ruta;
use App\Models\Usuario;
use App\Models\Estado;
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

        // Filtrado por buscador
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhereHas('conductor', function($sq) use ($search) {
                      $sq->where('primer_nombre', 'like', "%{$search}%")
                        ->orWhere('primer_apellido', 'like', "%{$search}%");
                  });
            });
        }

        // Orden ID ASC y paginación
        $asignaciones = $query->orderBy('id_viaje', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Datos para los modales
        $buses = Bus::where('NIT', $nit)->get();
        $rutas = Ruta::orderBy('id_ruta', 'asc')->get();
        $conductores = Usuario::where('NIT', $nit)->get();
        $estados = Estado::whereIn('nombre_estado', ['ACTIVO', 'INACTIVO'])->get();

        return view('admin.asignaciones.index', compact(
            'asignaciones',
            'buses',
            'rutas',
            'conductores',
            'estados'
        ));
    }

    /**
     * Guardar una nueva asignación con ID generado manualmente.
     */
    public function store(AsignacionRequest $request)
    {
        $data = $request->validated();

        // Generar ID aleatorio único (Manual como en arquitectura base)
        do {
            $id = random_int(100000, 999999);
        } while (Viaje::where('id_viaje', $id)->exists());

        $data['id_viaje'] = $id;

        Viaje::create($data);

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Asignación creada exitosamente.');
    }

    /**
     * Actualizar una asignación existente.
     */
    public function update(AsignacionRequest $request, Viaje $asignacion)
    {
        $asignacion->update($request->validated());

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Eliminar una asignación.
     */
    public function destroy(Viaje $asignacion)
    {
        $asignacion->delete();

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }
}
