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


class DashboardController extends Controller
{

public function superAdminStats()
{
    $hoy = Carbon::now();

    return response()->json([
        'usuarios' => [
            'activos' => User::where('id_estado', 1)->count(),
            'inactivos' => User::where('id_estado', 2)->count(),
        ],

        'empresas' => [
            'activos' => Empresa::where('id_estado', 1)->count(),
            'inactivos' => Empresa::where('id_estado', 2)->count(),
        ],

        'tarjetas' => [
            'activos' => Tarjeta::where('id_estado', 1)->count(),
            'inactivos' => Tarjeta::where('id_estado', 2)->count(),
        ],

        'documentos' => [
            'activos' => Documento::where('fecha_vencimiento', '>', $hoy->addDays(30))->count(),
            'por_vencer' => Documento::whereBetween('fecha_vencimiento', [$hoy, $hoy->addDays(30)])->count(),
            'vencidos' => Documento::where('fecha_vencimiento', '<', $hoy)->count(),
        ]
    ]);
}


}
