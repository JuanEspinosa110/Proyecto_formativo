<?php

namespace App\Http\Controllers\JefeMantenimiento;

use App\Http\Controllers\Controller;
use App\Models\ReporteFalla;

class ReporteFallaController extends Controller
{
    // ─── Jefe de Mantenimiento ────────────────────────────────────────────────

    public function index()
    {
        $reportes = ReporteFalla::with(['bus', 'conductor', 'estado'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('jefemantenimiento.reportes', compact('reportes'));
    }

    public function attend($id)
    {
        $reporte = ReporteFalla::findOrFail($id);

        return redirect()->route('jefemantenimiento.create', [
            'placa'      => $reporte->placa,
            'reporte_id' => $reporte->id_reporte,
            'origen'     => 'jefe',
        ]);
    }

    // ─── Admin de Empresa ─────────────────────────────────────────────────────

    public function indexAdmin()
    {
        $reportes = ReporteFalla::with(['bus', 'conductor', 'estado'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.mantenimiento.reportes', compact('reportes'));
    }

    public function attendAdmin($id)
    {
        $reporte = ReporteFalla::findOrFail($id);

        return redirect()->route('admin.mantenimiento.create', [
            'placa'      => $reporte->placa,
            'reporte_id' => $reporte->id_reporte,
            'origen'     => 'admin',
        ]);
    }
}
