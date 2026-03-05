<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Mostrar la página de inicio (Landing Page)
     */
    public function index()
    {
        // Obtener planes públicos activos
        $planesPublicos = DB::table('planes_licencia')
            ->where('id_estado', 1)
            ->orderBy('precio', 'asc')
            ->get();

        // Obtener un super administrador aleatorio para el contacto
        $superAdmin = DB::table('super_administrador')
            ->where('id_estado', 1)
            ->inRandomOrder()
            ->first();

        // Si no hay superAdmin, crear un objeto dummy para evitar errores en la vista
        if (!$superAdmin) {
            $superAdmin = (object) [
                'nombre' => 'Equipo de Soporte SIGU',
                'correo' => 'soporte@sigu.com',
                'telefono' => '+57 300 123 4567',
            ];
        }

        return view('index', compact('planesPublicos', 'superAdmin'));
    }
}
