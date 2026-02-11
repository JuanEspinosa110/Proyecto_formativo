<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('documentos')
            ->leftJoin('empresa', 'documentos.NIT', '=', 'empresa.NIT')
            ->leftJoin('bus', 'documentos.placa', '=', 'bus.placa')
            ->select(
                'documentos.*',
                'empresa.nombre as empresa_nombre',
                'bus.placa as placa_bus'
            );

        /*
        ==========================
        FILTROS
        ==========================
        */

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('empresa.nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('documentos.placa', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'vencido') {
                $query->where('fecha_vencimiento', '<', now());
            }

            if ($request->estado === 'vigente') {
                $query->where('fecha_vencimiento', '>=', now());
            }
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_documento', $request->tipo);
        }

        $documentos = $query->orderBy('fecha_vencimiento')
          ->paginate(12);

        



        return view('superadmin.documentos.index', compact('documentos'));
    }
}

