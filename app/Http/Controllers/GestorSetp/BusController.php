<?php

namespace App\Http\Controllers\GestorSetp;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Empresa;
use App\Models\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusController extends Controller
{
    private const DIAS_ALERTA = 30;

    /**
     * Devuelve los NITs de empresas de transporte de la ciudad del gestor.
     */
    private function nitsEmpresasCiudad(): \Illuminate\Support\Collection
    {
        return Empresa::where('id_ciudad', auth()->user()->id_ciudad)
                      ->where('id_tipo_empresa', 1) // Transporte Urbano
                      ->pluck('NIT');
    }

    // ── index ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $nits = $this->nitsEmpresasCiudad();

        $query = Bus::with('empresa')
                    ->whereIn('NIT', $nits);

        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . strtoupper($request->placa) . '%');
        }

        if ($request->filled('nit')) {
            $query->where('NIT', $request->nit);
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        $buses = $query->orderBy('placa')->paginate(20);

        // Añadir conteo de documentos pendientes
        $buses->getCollection()->transform(function ($bus) {
            $limite = Carbon::now()->addDays(self::DIAS_ALERTA);
            $bus->documentos_pendientes = Documento::where('placa', $bus->placa)
                ->where('fecha_vencimiento', '<=', $limite)
                ->count();

            // Filtro adicional: solo con docs pendientes
            return $bus;
        });

        // Filtrar en colección si el usuario pide solo buses con/sin pendientes
        if ($request->filled('docs')) {
            $buses->setCollection(
                $buses->getCollection()->filter(function ($bus) use ($request) {
                    if ($request->docs === 'pendientes') return $bus->documentos_pendientes > 0;
                    if ($request->docs === 'completos')  return $bus->documentos_pendientes === 0;
                    return true;
                })->values()
            );
        }

        // Lista de empresas para el select de filtro
        $empresas = Empresa::whereIn('NIT', $nits)
                           ->orderBy('nombre_empresa')
                           ->get();

        return view('gestor-setp.buses.index', compact('buses', 'empresas'));
    }

    // ── show ──────────────────────────────────────────────────────
    public function show($placa)
    {
        $nits = $this->nitsEmpresasCiudad();

        // Solo permitir ver buses de empresas de la ciudad del gestor
        $bus = Bus::with(['empresa', 'documentos.tipoDocumento'])
                  ->whereIn('NIT', $nits)
                  ->where('placa', $placa)
                  ->firstOrFail();

        $limite = Carbon::now()->addDays(self::DIAS_ALERTA);

        // Clasificar documentos
        $docsVigentes  = $bus->documentos->filter(fn($d) => Carbon::parse($d->fecha_vencimiento)->gt($limite));
        $docsPorVencer = $bus->documentos->filter(fn($d) =>
            !Carbon::parse($d->fecha_vencimiento)->isPast()
            && Carbon::parse($d->fecha_vencimiento)->lte($limite)
        );
        $docsVencidos  = $bus->documentos->filter(fn($d) => Carbon::parse($d->fecha_vencimiento)->isPast());

        return view('gestor-setp.buses.show', compact(
            'bus',
            'docsVigentes',
            'docsPorVencer',
            'docsVencidos'
        ));
    }

    // ── cambiarEstado ─────────────────────────────────────────────
    public function cambiarEstado(Request $request, $placa)
    {
        $nits = $this->nitsEmpresasCiudad();

        $bus = Bus::whereIn('NIT', $nits)
                  ->where('placa', $placa)
                  ->firstOrFail();

        $request->validate([
            'nuevo_estado' => 'required|in:1,2,3,4,9',
        ], [
            'nuevo_estado.required' => 'Debe indicar el nuevo estado.',
            'nuevo_estado.in'       => 'El estado indicado no es válido para un bus.',
        ]);

        $estadoAnterior = $bus->id_estado;
        $bus->update(['id_estado' => $request->nuevo_estado]);

        $mensajes = [
            1 => 'Bus activado correctamente.',
            2 => 'Bus inactivado correctamente.',
            3 => 'Bus suspendido correctamente.',
            4 => 'Bus marcado en mantenimiento.',
            9 => 'Bus bloqueado.',
        ];

        return back()->with('success', $mensajes[$request->nuevo_estado] ?? 'Estado actualizado.');
    }
}
