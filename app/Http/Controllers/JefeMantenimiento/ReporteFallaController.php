<?php

namespace App\Http\Controllers\JefeMantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReporteFalla;

class ReporteFallaController extends Controller
{
    // ─── Jefe de Mantenimiento ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = ReporteFalla::with(['bus', 'conductor', 'estado']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_reporte', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_reporte', '<=', $request->fecha_hasta);
        }
        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }
        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        $reportes = $query->orderBy('created_at', 'desc')->paginate(10);

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

    public function indexAdmin(Request $request)
    {
        $query = ReporteFalla::with(['bus', 'conductor', 'estado']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_reporte', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_reporte', '<=', $request->fecha_hasta);
        }
        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }
        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        $reportes = $query->orderBy('created_at', 'desc')->paginate(10);

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
