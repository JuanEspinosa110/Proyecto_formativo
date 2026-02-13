<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaViaje;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class ReporteController extends Controller
{
    /**
     * Mostrar el reporte general
     */
    public function index()
    {
        /**
         * ============================
         * 1. KPIs GENERALES
         * ============================
         */

        // Total de ventas realizadas
        $totalVentas = VentaViaje::count();

        // Total de ingresos
        $totalIngresos = VentaViaje::sum('valor');

        // Ventas realizadas hoy
        $ventasHoy = VentaViaje::whereDate('fecha', now())->count();

        // Ventas realizadas en el mes actual
        $ventasMes = VentaViaje::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        /**
         * ============================
         * 2. VENTAS RECIENTES
         * ============================
         */

        $ventasRecientes = VentaViaje::orderBy('fecha', 'desc')
            ->limit(10)
            ->get();

        /**
         * ============================
         * 3. INGRESOS POR EMPRESA
         * ============================
         */

        $ingresosPorEmpresa = DB::table('venta_viaje')
            ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
            ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
            ->join('empresa', 'ruta.NIT', '=', 'empresa.NIT')
            ->select(
                'empresa.nombre_empresa as empresa',
                DB::raw('SUM(venta_viaje.valor) as total')
            )
            ->groupBy('empresa.nombre_empresa')
            ->get();

        /**
         * ============================
         * 4. INGRESOS POR DIA (GRAFICA)
         * ============================
         */

        $ingresosPorDia = DB::table('venta_viaje')
            ->select(
                DB::raw('DATE(fecha) as dia'),
                DB::raw('SUM(valor) as total')
            )
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('dia', 'asc')
            ->limit(7)
            ->get();

        /**
         * ============================
         * 5. ENVIAR DATOS A LA VISTA
         * ============================
         */

        return view('admin.reportes.index', compact(
            'totalVentas',
            'totalIngresos',
            'ventasHoy',
            'ventasMes',
            'ventasRecientes',
            'ingresosPorEmpresa',
            'ingresosPorDia'
        ));
    }

    /**
 * Descargar reporte general en PDF
 */
public function exportPdf()
{
    /**
     * ============================
     * MISMAS CONSULTAS DEL INDEX
     * ============================
     */

    $totalVentas = VentaViaje::count();

    $totalIngresos = VentaViaje::sum('valor');

    $ventasHoy = VentaViaje::whereDate('fecha', now())->count();

    $ventasMes = VentaViaje::whereMonth('fecha', now()->month)
        ->whereYear('fecha', now()->year)
        ->count();

    $ventasRecientes = VentaViaje::orderBy('fecha', 'desc')
        ->limit(10)
        ->get();

    $ingresosPorEmpresa = DB::table('venta_viaje')
        ->join('viaje', 'venta_viaje.id_viaje', '=', 'viaje.id_viaje')
        ->join('ruta', 'viaje.id_ruta', '=', 'ruta.id_ruta')
        ->join('empresa', 'ruta.NIT', '=', 'empresa.NIT')
        ->select(
            'empresa.nombre_empresa as empresa',
            DB::raw('SUM(venta_viaje.valor) as total')
        )
        ->groupBy('empresa.nombre_empresa')
        ->get();

    $ingresosPorDia = DB::table('venta_viaje')
        ->select(
            DB::raw('DATE(fecha) as dia'),
            DB::raw('SUM(valor) as total')
        )
        ->groupBy(DB::raw('DATE(fecha)'))
        ->orderBy('dia', 'asc')
        ->limit(7)
        ->get();

    /**
     * ============================
     * GENERAR PDF
     * ============================
     */

    $pdf = Pdf::loadView('admin.reportes.pdf', compact(
        'totalVentas',
        'totalIngresos',
        'ventasHoy',
        'ventasMes',
        'ventasRecientes',
        'ingresosPorEmpresa',
        'ingresosPorDia'
    ))->setPaper('A4', 'portrait');

    return $pdf->download('reporte_general.pdf');
}

}


