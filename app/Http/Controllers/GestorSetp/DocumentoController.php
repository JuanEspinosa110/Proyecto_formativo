<?php

namespace App\Http\Controllers\GestorSetp;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Bus;
use App\Models\Empresa;
use App\Models\TipoDocumento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DocumentoController extends Controller
{
    private const DIAS_ALERTA = 30;

    /**
     * NITs de empresas de transporte de la ciudad del gestor autenticado.
     */
    private function nitsEmpresasCiudad(): \Illuminate\Support\Collection
    {
        return Empresa::where('id_ciudad', auth()->user()->id_ciudad)
                      ->where('id_tipo_empresa', 1) // Transporte Urbano
                      ->pluck('NIT');
    }

    /**
     * Placas de buses de las empresas de la ciudad del gestor.
     */
    private function placasBusesCiudad(): \Illuminate\Support\Collection
    {
        return Bus::whereIn('NIT', $this->nitsEmpresasCiudad())->pluck('placa');
    }

    // ── index ─────────────────────────────────────────────────────
    /**
     * Lista los buses con sus documentos, agrupados por bus.
     * Permite filtrar por placa, empresa y estado de documento.
     */
    public function index(Request $request)
    {
        $nits  = $this->nitsEmpresasCiudad();

        // Empresas para el select del filtro
        $empresas = Empresa::whereIn('NIT', $nits)->orderBy('nombre_empresa')->get();

        $queryBuses = Bus::with([
                'empresa',
                'documentos.tipoDocumento',
            ])
            ->whereIn('NIT', $nits);

        // Filtro por placa
        if ($request->filled('placa')) {
            $queryBuses->where('placa', 'like', '%' . strtoupper($request->placa) . '%');
        }

        // Filtro por empresa
        if ($request->filled('nit')) {
            $queryBuses->where('NIT', $request->nit);
        }

        // Filtro por estado de documentos
        if ($request->filled('estado_doc')) {
            $limite = Carbon::now()->addDays(self::DIAS_ALERTA);

            $queryBuses->whereHas('documentos', function ($q) use ($request, $limite) {
                if ($request->estado_doc === 'vencidos') {
                    $q->where('fecha_vencimiento', '<', Carbon::now());
                } elseif ($request->estado_doc === 'por_vencer') {
                    $q->whereBetween('fecha_vencimiento', [Carbon::now(), $limite]);
                } elseif ($request->estado_doc === 'vigentes') {
                    $q->where('fecha_vencimiento', '>', $limite);
                }
            });
        }

        $buses = $queryBuses->orderBy('placa')->paginate(10);

        return view('gestor-setp.documentos.index', compact('buses', 'empresas'));
    }

    // ── show ──────────────────────────────────────────────────────
    /**
     * Detalle de un documento específico.
     * Solo accesible si pertenece a un bus de la ciudad del gestor.
     */
    public function show($id)
    {
        $placas = $this->placasBusesCiudad();

        $documento = Documento::with(['tipoDocumento', 'bus.empresa'])
                              ->whereIn('placa', $placas)
                              ->findOrFail($id);

        $diasRestantes = Carbon::now()->diffInDays(
            Carbon::parse($documento->fecha_vencimiento), false
        );

        return view('gestor-setp.documentos.show', compact('documento', 'diasRestantes'));
    }

    // ── enviarAviso ───────────────────────────────────────────────
    /**
     * Envía un aviso a la empresa notificando documentos pendientes o vencidos.
     * Registra la notificación como sesión flash para trazabilidad visual.
     */
    public function enviarAviso(Request $request, $placa)
    {
        $placas = $this->placasBusesCiudad();

        // Verificar que el bus pertenece a la ciudad del gestor
        abort_unless($placas->contains($placa), 403, 'No tiene permiso sobre este bus.');

        $bus = Bus::with('empresa')->where('placa', $placa)->firstOrFail();

        $data = $request->validate([
            'plazo_regularizacion' => 'required|date|after:today',
            'mensaje'              => 'nullable|string|max:500',
        ], [
            'plazo_regularizacion.required' => 'El plazo de regularización es obligatorio.',
            'plazo_regularizacion.after'    => 'El plazo debe ser una fecha futura.',
            'mensaje.max'                   => 'El mensaje no puede superar los 500 caracteres.',
        ]);

        $empresa = $bus->empresa;

        if (! $empresa || ! $empresa->correo_corporativo) {
            return back()->with('error', 'La empresa no tiene correo corporativo registrado. No se pudo enviar el aviso.');
        }

        // Obtener documentos con problemas del bus
        $limite = Carbon::now()->addDays(self::DIAS_ALERTA);
        $docsProblematicos = Documento::with('tipoDocumento')
                                      ->where('placa', $placa)
                                      ->where('fecha_vencimiento', '<=', $limite)
                                      ->get();

        // ── Envío de correo ───────────────────────────────────────
        // Opción A: Mail::to() con Mailable dedicado (recomendado para producción)
        // Mail::to($empresa->correo_corporativo)
        //     ->send(new \App\Mail\AvisoDocumentosEmail($bus, $empresa, $data, $docsProblematicos));

        // Opción B: Mail raw (útil para pruebas rápidas)
        try {
            $plazo = Carbon::parse($data['plazo_regularizacion'])->format('d/m/Y');
            $gestor = auth()->user();
            $listaDocumentos = $docsProblematicos->map(function ($d) {
                $dias = Carbon::now()->diffInDays(Carbon::parse($d->fecha_vencimiento), false);
                $estado = $dias < 0 ? 'VENCIDO' : "Por vencer en {$dias} día(s)";
                return "  - {$d->tipoDocumento->nombre} ({$d->nombre}): {$estado}";
            })->implode("\n");

            $cuerpo = "Estimados señores {$empresa->nombre_empresa},\n\n"
                . "El sistema SIGU - Gestor SETP informa que el bus con placa "
                . "{$bus->placa} tiene los siguientes documentos con novedad:\n\n"
                . "{$listaDocumentos}\n\n"
                . "Plazo para regularizar: {$plazo}.\n\n"
                . ($data['mensaje'] ? "Observación del gestor:\n{$data['mensaje']}\n\n" : '')
                . "De no regularizar la documentación en el plazo indicado, el bus podrá ser inactivado.\n\n"
                . "Atentamente,\n"
                . "{$gestor->primer_nombre} {$gestor->primer_apellido}\n"
                . "Gestor SETP - SIGU";

            Mail::raw($cuerpo, function ($message) use ($empresa, $bus) {
                $message->to($empresa->correo_corporativo, $empresa->nombre_empresa)
                        ->subject("SIGU - Aviso documentación pendiente bus {$bus->placa}");

                // Copiar al representante si tiene correo diferente
                if ($empresa->correo_representante
                    && $empresa->correo_representante !== $empresa->correo_corporativo) {
                    $message->cc($empresa->correo_representante);
                }
            });

            return back()->with('success',
                "Aviso enviado correctamente a {$empresa->nombre_empresa} ({$empresa->correo_corporativo}).");

        } catch (\Exception $e) {
            return back()->with('error',
                'No se pudo enviar el correo: ' . $e->getMessage() .
                '. Verifique la configuración SMTP en .env');
        }
    }

    // ── inactivarBus ──────────────────────────────────────────────
    /**
     * Inactiva un bus por incumplimiento documental.
     * Solo permitido si el bus pertenece a la ciudad del gestor.
     * Registra el motivo como sesión flash (en producción considerar
     * guardarlo en una tabla de auditoría).
     */
    public function inactivarBus(Request $request, $placa)
    {
        $placas = $this->placasBusesCiudad();

        abort_unless($placas->contains($placa), 403, 'No tiene permiso sobre este bus.');

        $bus = Bus::with('empresa')->where('placa', $placa)->firstOrFail();

        $data = $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'El motivo de inactivación es obligatorio.',
            'motivo.min'      => 'El motivo debe tener al menos 10 caracteres.',
            'motivo.max'      => 'El motivo no puede superar los 500 caracteres.',
        ]);

        if ($bus->id_estado === 2) {
            return back()->with('warning', 'El bus ya se encuentra inactivo.');
        }

        // Inactivar el bus
        $bus->update(['id_estado' => 2]);

        // ── Notificar a la empresa del cambio ─────────────────────
        $empresa = $bus->empresa;
        if ($empresa && $empresa->correo_corporativo) {
            try {
                $gestor = auth()->user();
                $cuerpo = "Estimados señores {$empresa->nombre_empresa},\n\n"
                    . "Le informamos que el bus con placa {$bus->placa} ha sido inactivado en el "
                    . "sistema SIGU por incumplimiento en la documentación requerida.\n\n"
                    . "Motivo registrado:\n{$data['motivo']}\n\n"
                    . "Para reactivar el bus deberá regularizar los documentos pendientes y "
                    . "contactar al Gestor SETP de su ciudad.\n\n"
                    . "Atentamente,\n"
                    . "{$gestor->primer_nombre} {$gestor->primer_apellido}\n"
                    . "Gestor SETP - SIGU";

                Mail::raw($cuerpo, function ($message) use ($empresa, $bus) {
                    $message->to($empresa->correo_corporativo, $empresa->nombre_empresa)
                            ->subject("SIGU - Bus {$bus->placa} inactivado por incumplimiento documental");
                });
            } catch (\Exception $e) {
                // El bus ya fue inactivado; solo informar que el correo falló
                return redirect()->route('gestor.documentos.index')
                    ->with('warning',
                        "Bus {$placa} inactivado, pero no se pudo enviar la notificación por correo: "
                        . $e->getMessage());
            }
        }

        return redirect()->route('gestor.documentos.index')
                         ->with('success',
                             "Bus {$placa} inactivado correctamente. Se notificó a "
                             . ($empresa->nombre_empresa ?? 'la empresa') . '.');
    }
}
