<?php

namespace App\Http\Controllers\JefeMantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReporteFalla;
use App\Models\Bus;
use Illuminate\Support\Facades\Auth;

class ReporteFallaController extends Controller
{
    // ─── Jefe de Mantenimiento ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        $query = ReporteFalla::with(['bus', 'conductor', 'estado'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit));

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
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        $reporte = ReporteFalla::where('id_reporte', $id)
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
            ->firstOrFail();

        return redirect()->route('jefemantenimiento.create', [
            'placa'      => $reporte->placa,
            'reporte_id' => $reporte->id_reporte,
            'origen'     => 'jefe',
        ]);
    }

    // ─── Admin de Empresa ─────────────────────────────────────────────────────

    public function indexAdmin(Request $request)
    {
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        $query = ReporteFalla::with(['bus', 'conductor', 'estado'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit));

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
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        $reporte = ReporteFalla::where('id_reporte', $id)
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
            ->firstOrFail();

        return redirect()->route('admin.mantenimiento.create', [
            'placa'      => $reporte->placa,
            'reporte_id' => $reporte->id_reporte,
            'origen'     => 'admin',
        ]);
    }

    public function getPendingByBus($placa)
    {
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        $reportes = ReporteFalla::where('placa', $placa)
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
            ->whereIn('id_estado', [1, 6]) // 1: No Atendido, 6: Pendiente
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'id_reporte' => $r->id_reporte,
                    'nivel'      => $r->nivel_urgencia,
                    'descripcion' => $r->descripcion,
                    'fecha'      => $r->created_at->format('d/m/Y'),
                ];
            });

        return response()->json($reportes);
    }
}
