<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Documento;
use App\Models\Tarjeta;
use Carbon\Carbon;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{


public function superAdminStats()
{
    $now = Carbon::now();
    $hace12Meses = Carbon::now()->subMonths(11)->startOfMonth();

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ CRECIMIENTO MENSUAL EMPRESAS
    |--------------------------------------------------------------------------
    */
    $empresasMensual = DB::table('empresa')
        ->selectRaw("DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(*) as total")
        ->where('fecha_creacion', '>=', $hace12Meses)
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ EMPRESAS POR ESTADO
    |--------------------------------------------------------------------------
    */
    $empresasEstado = DB::table('empresa')
        ->join('estado', 'empresa.id_estado', '=', 'estado.id_estado')
        ->select('estado.nombre_estado', DB::raw('COUNT(*) as total'))
        ->whereIn('empresa.id_estado', [1,2,3])
        ->groupBy('estado.nombre_estado')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ ESTADO DE LICENCIAS
    |--------------------------------------------------------------------------
    */

    // Activas (VIGENTE = 20)
    $licenciasActivas = DB::table('licencias')
        ->where('id_estado', 1)
        ->count();

    // Vencidas (21)
    $licenciasVencidas = DB::table('licencias')
        ->where('id_estado', 6)
        ->count();

    // Por vencer (vigentes que vencen en próximos 15 días)
    $licenciasPorVencer = DB::table('licencias')
        ->where('id_estado', 1)
        ->whereBetween('fecha_vencimiento', [
            $now,
            $now->copy()->addDays(15)
        ])
        ->count();

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ LICENCIAS EMITIDAS POR MES
    |--------------------------------------------------------------------------
    */
    $licenciasMensual = DB::table('licencias')
        ->selectRaw("DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(*) as total")
        ->where('fecha_creacion', '>=', $hace12Meses)
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ PLANES MÁS UTILIZADOS
    |--------------------------------------------------------------------------
    */
    $planesPopulares = DB::table('licencias')
        ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
        ->select('planes_licencia.nombre_plan', DB::raw('COUNT(*) as total'))
        ->groupBy('planes_licencia.nombre_plan')
        ->orderByDesc('total')
        ->get();

    return response()->json([
        'empresas_crecimiento' => $empresasMensual,
        'empresas_estado' => $empresasEstado,
        'licencias_estado' => [
            'activas' => $licenciasActivas,
            'por_vencer' => $licenciasPorVencer,
            'vencidas' => $licenciasVencidas,
        ],
        'licencias_mensual' => $licenciasMensual,
        'planes_populares' => $planesPopulares
    ]);
}



}
