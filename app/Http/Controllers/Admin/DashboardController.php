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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Ruta;
use App\Models\Viaje;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $nit = $user->NIT ?? null;

        $documentosPendientes = 0;
        if ($nit) {
            $documentosPendientes = Documento::where('NIT', $nit)
                ->whereNotNull('placa')
                ->whereNotIn('id_estado', [24, 25])
                ->count();
        }

        return view('admin.dashboard', compact('documentosPendientes'));
    }

    /**
     * Devuelve toda la información relevante para el dashboard del administrador logueado.
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }
            $nit = $user->NIT ?? null;

            // Empresa
            $empresa = $nit ? Empresa::where('NIT', $nit)->first() : null;

            // Usuarios de la empresa
            $usuarios = $nit ? Usuario::where('NIT', $nit)->get() : collect();

            // Documentos de la empresa
            $documentos = $nit ? Documento::where('NIT', $nit)->get() : collect();

            // Buses de la empresa (con estado)
            $buses = $nit ? Bus::with('estado')->where('NIT', $nit)->get() : collect();

            // Viajes de la empresa (con ruta)
            $viajes = $nit ? Viaje::with('ruta')->whereHas('ruta', function($q) use ($nit) {
                $q->where('NIT', $nit);
            })->get() : collect();

            $totalUsuarios = $usuarios->count();
            $totalDocumentos = $documentos->count();
            $totalBuses = $buses->count();

            return response()->json([
                'empresa' => $empresa,
                'usuarios' => $usuarios,
                'documentos' => $documentos,
                'buses' => $buses,
                'viajes' => $viajes,
                'totales' => [
                    'usuarios' => $totalUsuarios,
                    'documentos' => $totalDocumentos,
                    'buses' => $totalBuses
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error en DashboardController@stats: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error interno: '.$e->getMessage()], 500);
        }
    }
}
