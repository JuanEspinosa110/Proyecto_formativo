<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $nit = $user->NIT ?? null;

        if (!$nit) {
            return redirect()->route('login')->with('error', 'Empresa no asociada a este usuario.');
        }

        // 1. Contar Conductores
        $totalConductores = DB::table('usuario')
            ->join('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->where('tipo_usuario.nombre_tipo', 'like', '%conductor%')
            ->count();

        // 2. Contar Propietarios
        $totalPropietarios = DB::table('usuario')
            ->join('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->where('tipo_usuario.nombre_tipo', 'like', '%propietario%')
            ->count();

        // 3. Contar Asignaciones (Viajes)
        // Se asume que la tabla 'viaje' o 'asignacion' existe. 
        // Viendo App\Models\Viaje en Admin\DashboardController se usa Viaje.
        $totalAsignaciones = DB::table('viaje')
            ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
            ->where('ruta.NIT', $nit)
            ->count();

        // 4. Documentos Vencidos y Próximos
        $documentos = DB::table('documento')
            ->where('NIT', $nit)
            ->where('id_estado', 1) // Activo
            ->get();

        $docsVencidos = 0;
        $docsProximos = 0;
        $fechaHoy = Carbon::now();

        foreach ($documentos as $doc) {
            if ($doc->fecha_vencimiento) {
                $vencimiento = Carbon::parse($doc->fecha_vencimiento);
                if ($vencimiento->isPast()) {
                    $docsVencidos++;
                } elseif ($vencimiento->diffInDays($fechaHoy) <= 15) {
                    $docsProximos++;
                }
            }
        }

        // 5. Alertas para el Dashboard
        // Documentos vencidos o rechazados
        $alertasDocumentos = DB::table('documento')
            ->where('NIT', $nit)
            ->where(function($q) {
                $q->where('fecha_vencimiento', '<', Carbon::now())
                  ->orWhere('id_estado', 3); // 3 = Rechazado (Asumiendo)
            })
            ->get();

        // Buses inactivos por documentación
        // Se asume que si un bus está INACTIVO (2) y tiene documentos vencidos
        $busesInactivos = DB::table('bus')
            ->where('NIT', $nit)
            ->where('id_estado', 2) // Inactivo
            ->get();

        $documentosPendientes = DB::table('documento')
            ->where('NIT', $nit)
            ->whereNotNull('placa')
            ->whereNotIn('id_estado', [24, 25])
            ->count();

        return view('auxiliar.dashboard', compact(
            'totalConductores', 
            'totalPropietarios', 
            'totalAsignaciones', 
            'docsVencidos', 
            'docsProximos',
            'alertasDocumentos',
            'busesInactivos',
            'documentosPendientes'
        ));
    }
}
