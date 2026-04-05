<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Documento;
use App\Models\Bus;
use App\Models\Viaje;
use Illuminate\Support\Facades\Log;

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
                  ->orWhere('id_estado', 8); // 8 = RECHAZADO
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
            ->where('id_estado', 5) // PENDIENTE
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

    /**
     * Devuelve la información para las gráficas del auxiliar.
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }
            $nit = $user->NIT ?? null;

            if (!$nit) {
                return response()->json(['error' => 'Usuario no tiene un NIT de empresa asociado'], 400);
            }

            // Validar que el usuario sea Auxiliar o Administrador (Rol 4 o 1)
            if (!in_array((int)$user->id_tipo_usuario, [1, 4, 8])) {
                return response()->json(['error' => 'Rol no autorizado para ver estadísticas'], 403);
            }

            // Empresa
            $empresa = Empresa::where('NIT', $nit)->first();

            // Usuarios de la empresa (Filtrar por NIT)
            $usuarios = Usuario::where('NIT', $nit)->get();

            // Documentos de la empresa
            $documentos = Documento::where('NIT', $nit)->get();

            // Buses de la empresa (con estado)
            $buses = Bus::with('estado')->where('NIT', $nit)->get();

            // Viajes de la empresa (con ruta)
            $viajes = Viaje::with('ruta')->whereHas('ruta', function($q) use ($nit) {
                $q->where('NIT', $nit);
            })->get();

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
            Log::error('Error en Auxiliar\DashboardController@stats: '.$e->getMessage());
            return response()->json(['error' => 'Error interno: '.$e->getMessage()], 500);
        }
    }
}
