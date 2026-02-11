<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AlertaController extends Controller
{
    public function index()
    {

        /*
        ===============================
        DOCUMENTOS VENCIDOS
        ===============================
        */

        $documentosVencidos = DB::table('documentos')
            ->where('fecha_vencimiento', '<', now())
            ->count();


        /*
        ===============================
        MANTENIMIENTOS PRÓXIMOS
        ===============================
        */

        $mantenimientosProximos = DB::table('mantenimiento')
            ->whereDate('fecha_proximo', '<=', now()->addDays(7))
            ->count();


        /*
        ===============================
        USUARIOS BLOQUEADOS
        ===============================
        */

        $usuariosBloqueados = DB::table('usuario')
            ->where('id_estado', '!=', 1)
            ->count();


        /*
        ===============================
        TARJETAS SUSPENDIDAS
        ===============================
        */

        $tarjetasSuspendidas = DB::table('tarjeta')
        ->where('id_estado', 2)
        ->count();



        /*
        ===============================
        VIAJES ACTIVOS
        ===============================
        */

        $viajesActivos = DB::table('viaje')
             ->where('id_estado', 1)
              ->count();



        return view('superadmin.alertas.index', compact(
            'documentosVencidos',
            'mantenimientosProximos',
            'usuariosBloqueados',
            'tarjetasSuspendidas',
            'viajesActivos'
        ));
    }
}


