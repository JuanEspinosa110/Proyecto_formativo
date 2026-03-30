<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Ciudad;
use Illuminate\Http\Request;

/**
 * Nota: La BD actual no tiene una tabla `punto_recarga` dedicada.
 * Los puntos de recarga se obtienen de las empresas habilitadas
 * (tipos de empresa que operan puntos de venta/recarga).
 *
 * Si en el futuro se crea la tabla `punto_recarga`, actualizar
 * este controlador para usarla.
 */
class RecargaController extends Controller
{
    // Tipos de empresa que actúan como puntos de recarga
    // Ajustar según la configuración real del proyecto.
    private const TIPOS_RECARGA = [3]; // Empresa de Recarga (ID 3 en tabla tipo_empresa)

    public function index(Request $request)
    {
        $ciudad = auth()->user()->id_ciudad;

        $query = Empresa::with('ciudad')
                        ->whereIn('id_tipo_empresa', self::TIPOS_RECARGA)
                        ->where('id_estado', 1);

        // Filtro: buscar por nombre o NIT
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('nombre_empresa', 'like', "%{$q}%")
                   ->orWhere('NIT', 'like', "%{$q}%");
            });
        }

        // Filtro: ciudad (por defecto la ciudad del pasajero)
        $ciudadFiltro = $request->filled('ciudad') ? $request->ciudad : $ciudad;
        $query->where('id_ciudad', $ciudadFiltro);

        $puntos = $query->orderBy('nombre_empresa')->paginate(12);

        // Lista de ciudades para el filtro
        $ciudades = Ciudad::orderBy('nombre_city')->get();

        return view('pasajero.recargas.index', compact('puntos', 'ciudades', 'ciudadFiltro'));
    }
}
