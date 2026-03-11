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
        
        // Filtro por Hora (Ej: '06:00')
        if ($request->filled('hora')) {
            $query->whereTime('fecha', '=', $request->hora);
        }

        // Orden ID ASC y paginación
        $asignaciones = $query->orderBy('id_viaje', 'asc')
            ->paginate(5)
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

        // Generar ID aleatorio único
        do {
            $id = random_int(100000, 999999);
        } while (Viaje::where('id_viaje', $id)->exists());

        $data['id_viaje'] = $id;

        Viaje::create($data);

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro creado correctamente');
    }

    /**
     * Actualizar una asignación existente.
     */
    public function update(AsignacionRequest $request, $id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);
        $viaje->update($request->validated());

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro actualizado correctamente');
    }

    /**
     * Eliminar una asignación.
     */
    public function destroy($id_viaje)
    {
        $viaje = Viaje::findOrFail($id_viaje);
        $viaje->delete();

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Registro eliminado correctamente');
    }
}
