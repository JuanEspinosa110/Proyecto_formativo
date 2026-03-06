<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Documento;
use App\Models\VentaViaje;
use App\Models\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Ruta;
use App\Models\Viaje;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Devuelve toda la información relevante para el dashboard del administrador logueado.
     */
    public function stats(Request $request)
    {
        $user = Auth::guard('web')->user();
        $doc_usuario = $user->doc_usuario;
        $nit = $user->NIT ?? null;

        // Empresa
        $empresa = $nit ? Empresa::where('NIT', $nit)->first() : null;

        // Usuarios de la empresa
        $usuarios = $nit ? Usuario::where('NIT', $nit)->get() : collect();

        // Documentos de la empresa
        $documentos = $nit ? Documento::where('NIT', $nit)->get() : collect();

        // Buses de la empresa
        $buses = $nit ? Bus::where('NIT', $nit)->get() : collect();

        // Rutas de la empresa
        $rutas = $nit ? Ruta::whereHas('ciudad', function($q) use ($nit) {
            $q->where('NIT', $nit);
        })->get() : collect();

        // Asignaciones (viajes) donde el usuario logueado es conductor
        $asignaciones = Viaje::where('doc_us', $doc_usuario)->get();

        // Viajes de la empresa
        $viajes = $nit ? Viaje::whereHas('ruta', function($q) use ($nit) {
            $q->where('NIT', $nit);
        })->get() : collect();

        // Ventas de viajes de la empresa
        $ventas = $nit ? VentaViaje::whereHas('viaje.ruta', function($q) use ($nit) {
            $q->where('NIT', $nit);
        })->get() : collect();

        // Totales para la gráfica
        $totalUsuarios = $usuarios->count();
        $totalDocumentos = $documentos->count();

        return response()->json([
            'empresa' => $empresa,
            'usuarios' => $usuarios,
            'documentos' => $documentos,
            'buses' => $buses,
            'rutas' => $rutas,
            'asignaciones' => $asignaciones,
            'viajes' => $viajes,
            'ventas' => $ventas,
            'totales' => [
                'usuarios' => $totalUsuarios,
                'documentos' => $totalDocumentos
            ]
        ]);
    }
}
