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

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Devuelve métricas básicas para el dashboard (JSON)
     */
    public function stats(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        $empresa = $nit ? Empresa::where('NIT', $nit)->first() : null;

        // Totales actuales
        $totalUsuarios = $nit ? Usuario::where('NIT', $nit)->count() : 0;
        $totalDocumentos = $nit ? Documento::where('NIT', $nit)->count() : 0;

        // Serie de tiempo: últimos N días
        $days = 14;
        $end = Carbon::today()->endOfDay();
        $start = Carbon::today()->subDays($days - 1)->startOfDay();

        // Nota: el modelo `Usuario` no define timestamps (timestamps = false),
        // por lo que no hay un campo `created_at` fiable para series de usuarios.
        // Devolvemos series para `documentos` y `ventas`; para `usuarios` incluimos
        // la lista reciente en `latest.usuarios` y dejamos la serie de usuarios en null.

        $documentosByDay = $nit ? Documento::where('NIT', $nit)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total','day')
            ->toArray() : [];

        // Ventas: relacionar venta_viaje -> viaje -> ruta -> empresa (ruta.NIT)
        $ventasByDay = [];
        $ventasCount = 0;
        $ventasTotal = 0.0;
        if ($nit) {
            $ventasByDay = DB::table('venta_viaje')
                ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
                ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
                ->where('ruta.NIT', $nit)
                ->whereBetween('venta_viaje.fecha', [$start, $end])
                ->selectRaw('DATE(venta_viaje.fecha) as day, SUM(venta_viaje.valor) as total')
                ->groupBy('day')
                ->pluck('total','day')
                ->toArray();

            $ventasCount = DB::table('venta_viaje')
                ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
                ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
                ->where('ruta.NIT', $nit)
                ->count();

            $ventasTotal = DB::table('venta_viaje')
                ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
                ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
                ->where('ruta.NIT', $nit)
                ->sum('venta_viaje.valor');
        }

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $series[] = [
                'day' => $d,
                'usuarios' => null,
                'documentos' => isset($documentosByDay[$d]) ? (int)$documentosByDay[$d] : 0,
                'ventas' => isset($ventasByDay[$d]) ? (float)$ventasByDay[$d] : 0,
            ];
        }

        // Periodo anterior para calcular % change
        $prevStart = $start->copy()->subDays($days);
        $prevEnd = $start->copy()->subSecond();

        // Sumar series (tener en cuenta nulls)
        $currentUsuarios = 0; // no disponible por falta de timestamps
        $currentDocumentos = array_sum(array_column($series, 'documentos'));
        $currentVentas = array_sum(array_column($series, 'ventas'));

        $prevUsuarios = 0; // no disponible
        $prevDocumentos = $nit ? Documento::where('NIT', $nit)
            ->whereBetween('created_at', [$prevStart, $prevEnd])->count() : 0;

        $prevVentas = 0;
        if ($nit) {
            $prevVentas = DB::table('venta_viaje')
                ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
                ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
                ->where('ruta.NIT', $nit)
                ->whereBetween('venta_viaje.fecha', [$prevStart, $prevEnd])
                ->selectRaw('SUM(venta_viaje.valor) as total')
                ->pluck('total')
                ->first() ?: 0;
        }

        // Percent change helper: if previous is 0 and current > 0, return null
        // so frontend can show an absolute increase (no misleading %).
        $pct = function($curr, $prev) {
            if ($prev == 0) {
                if ($curr == 0) return 0;
                return null; // indicates previous was zero and current > 0
            }
            return round((($curr - $prev) / max(1,$prev)) * 100, 1);
        };


        $busesCount = 0;
        // comprobar nombres comunes de tabla para buses
        if ($nit) {
            if (Schema::hasTable('bus')) {
                $busesCount = DB::table('bus')->where('NIT', $nit)->count();
            } elseif (Schema::hasTable('buses')) {
                $busesCount = DB::table('buses')->where('NIT', $nit)->count();
            } elseif (Schema::hasTable('vehiculo')) {
                $busesCount = DB::table('vehiculo')->where('NIT', $nit)->count();
            }
        }

        return response()->json([
            'empresa' => $empresa ? [
                'NIT' => $empresa->NIT ?? $empresa->NIT,
                'nombre' => $empresa->nombre ?? $empresa->nombre_empresa ?? null,
            ] : null,
            'totales' => [
                'usuarios' => $totalUsuarios,
                'documentos' => $totalDocumentos,
                'ventas_count' => $ventasCount,
                'ventas_total' => (float)$ventasTotal,
                'buses' => $busesCount,
            ],
            'trends' => [
                'usuarios' => ['current' => $currentUsuarios, 'previous' => $prevUsuarios, 'pct' => $pct($currentUsuarios,$prevUsuarios)],
                'documentos' => ['current' => $currentDocumentos, 'previous' => $prevDocumentos, 'pct' => $pct($currentDocumentos,$prevDocumentos)],
                'ventas' => ['current' => $currentVentas, 'previous' => $prevVentas, 'pct' => $pct($currentVentas,$prevVentas)],
            ],
            'series' => $series,
            'latest' => [
                'usuarios' => $nit ? Usuario::where('NIT', $nit)->select('doc_usuario','primer_nombre','primer_apellido','correo','telefono')->limit(10)->get() : [],
                'documentos' => $nit ? Documento::where('NIT', $nit)->orderBy('created_at','desc')->limit(10)->get() : [],
                'ventas' => $nit ? DB::table('venta_viaje')
                    ->join('viaje','venta_viaje.id_viaje','=','viaje.id_viaje')
                    ->join('ruta','viaje.id_ruta','=','ruta.id_ruta')
                    ->where('ruta.NIT', $nit)
                    ->select('venta_viaje.*','viaje.id_ruta')
                    ->orderBy('venta_viaje.fecha','desc')
                    ->limit(10)
                    ->get() : [],
            ],
        ]);
    }
}
