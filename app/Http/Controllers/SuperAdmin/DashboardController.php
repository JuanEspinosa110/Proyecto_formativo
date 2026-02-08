<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Documento;
use App\Models\Tarjeta;
use Carbon\Carbon;


class DashboardController extends Controller
{

public function superAdminStats()
{
    $hoy = Carbon::now();

    return response()->json([
        'usuario' => User::count(),
        'empresa' => Empresa::count(),
        'tarjeta' => Tarjeta::count(),

        'documentos' => [
            'activos' => Documento::where('fecha_vencimiento', '>', $hoy->addDays(30))->count(),
            'por_vencer' => Documento::whereBetween('fecha_vencimiento', [$hoy, $hoy->addDays(30)])->count(),
            'vencidos' => Documento::where('fecha_vencimiento', '<', $hoy)->count(),
        ]
    ]);
}


}
