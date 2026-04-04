<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\Estado;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BusService
{
    /**
     * Obtener listado de buses con filtros, paginación y ordenamiento ASC obligatorio
     */
    public function getBuses(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        $query = Bus::with(['estado', 'empresa'])
            ->where('NIT', $nit);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        return $query->orderBy('placa', 'asc')->paginate(5)->withQueryString();
    }

    /**
     * Obtener estados operativos (Lógica centralizada)
     */
    public function getEstadosOperativos()
    {
        return Estado::whereIn('nombre_estado', [
            'ACTIVO', 
            'INACTIVO', 
            'EN_MANTENIMIENTO', 
            'FUERA_DE_SERVICIO'
        ])->get();
    }

    /**
     * Crear un nuevo bus bajo el NIT del usuario autenticado.
     * Inicia como INACTIVO (2) hasta validar documentos.
     */
    public function storeBus(array $data)
    {
        $data['NIT'] = Auth::guard('web')->user()->getActiveNit();
        $data['id_estado'] = 2; // INACTIVO por defecto
        
        return Bus::create($data);
    }

    /**
     * Actualizar bus existente
     */
    public function updateBus(Bus $bus, array $data)
    {
        $oldStatus = $bus->id_estado;
        // La placa no debe actualizarse para evitar conflictos con llaves foráneas (FK en tabla viaje)
        unset($data['placa']);

        // Bloqueo de seguridad: No permitir ACTIVO si hay fallas ALTO pendientes
        if (isset($data['id_estado']) && (int)$data['id_estado'] === 1) {
            if ($bus->hasPendingHighLevelFaults()) {
                throw new \Exception("El vehículo no puede marcarse como ACTIVO porque aún tiene reportes de falla de nivel ALTO pendientes de resolución.");
            }
        }
        
        $updated = $bus->update($data);

        if ($updated && isset($data['id_estado']) && $data['id_estado'] != $oldStatus) {
            $nuevoEstado = \App\Models\Estado::find($data['id_estado'])->nombre_estado;
            $viejoEstado = \App\Models\Estado::find($oldStatus)->nombre_estado;
            \App\Models\HistorialBus::create([
                'placa' => $bus->placa,
                'tipo_cambio' => 'ACTUALIZACION_ESTADO',
                'detalle' => "{$viejoEstado} → {$nuevoEstado}"
            ]);
        }
        
        return $updated;
    }

    /**
     * Eliminar bus
     */
    public function deleteBus(Bus $bus)
    {
        return $bus->delete();
    }

    /**
     * Obtener detalles completos de un bus incluyendo su asignación actual
     */
    public function getBusDetails($placa)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        $bus = Bus::with('estado')->where('placa', $placa)->where('NIT', $nit)->firstOrFail();
        
        // Obtener la última asignación (viaje)
        $ultimaAsignacion = \App\Models\Viaje::where('placa', $placa)
            ->with(['conductor', 'ruta'])
            ->orderBy('fecha', 'desc')
            ->first();

        // Obtener documentos del vehículo
        $documentos = \App\Models\Documento::where('placa', $placa)
            ->with(['tipoDocumento', 'estado'])
            ->get()
            ->map(function($doc) {
                return [
                    'id_documento' => $doc->id_documento,
                    'id_tipo_documento' => $doc->id_tipo_documento,
                    'tipo_documento' => $doc->tipoDocumento,
                    'fecha_vencimiento' => $doc->fecha_vencimiento->format('Y-m-d'),
                    'created_at' => $doc->created_at->format('Y-m-d H:i:s'),
                    'status_vigencia' => $doc->estado_expiracion,
                    'status_color' => $doc->status_color,
                    'url_archivo' => $doc->archivo ? asset('storage/' . $doc->archivo) : null
                ];
            });

        return [
            'bus' => $bus,
            'asignacion' => $ultimaAsignacion ? [
                'conductor' => $ultimaAsignacion->conductor->primer_nombre . ' ' . $ultimaAsignacion->conductor->primer_apellido,
                'doc_conductor' => $ultimaAsignacion->conductor->doc_usuario,
                'licencia' => 'LC-' . $ultimaAsignacion->conductor->doc_usuario,
                'ruta' => $ultimaAsignacion->ruta ? $ultimaAsignacion->ruta->nombre_ruta : 'Sin ruta'
            ] : null,
            'documentos' => $documentos
        ];
    }

    /**
     * Obtener datos del propietario por documento para autocompletar
     */
    public function getOwnerData($doc_propietario)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();

        return Bus::where('doc_propietario', $doc_propietario)
            ->where('NIT', $nit)
            ->select('nombre_propietario', 'telefono', 'correo', 'doc_propietario')
            ->first();
    }

    /**
     * Exportar buses: Soporta Excel y PDF con Estilo Premium
     */
    public function export(Request $request, $format = 'excel')
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();
        
        $query = Bus::with(['estado', 'empresa', 'propietario'])->where('NIT', $nit);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('nombre_propietario', 'like', "%{$search}%")
                  ->orWhere('doc_propietario', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        $buses = $query->orderBy('placa', 'asc')->get();

        if ($format === 'pdf') {
            $empresa = \App\Models\Empresa::where('NIT', $nit)->first();
            $pdf = Pdf::loadView('admin.buses.pdf', compact('buses', 'empresa'));
            return $pdf->download("Inventario_Flota_{$nit}_" . date('Ymd_His') . ".pdf");
        }

        // Exportación a EXCEL
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario Flota');

        // Header
        $headers = ['Placa', 'Modelo', 'Capacidad', 'Kilometraje', 'Propietario', 'Teléfono Prop', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($cols as $index => $col) {
            $sheet->setCellValue($col . '1', $headers[$index]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDDEBF7');
        }

        // Body
        $row = 2;
        foreach ($buses as $bus) {
            $sheet->setCellValue('A' . $row, $bus->placa);
            $sheet->setCellValue('B' . $row, $bus->modelo);
            $sheet->setCellValue('C' . $row, $bus->capacidad_pasajeros);
            $sheet->setCellValue('D' . $row, $bus->kilometraje . ' KM');
            $sheet->setCellValue('E' . $row, $bus->nombre_propietario ?? ($bus->propietario ? ($bus->propietario->primer_nombre . ' ' . $bus->propietario->primer_apellido) : 'PARTICULAR'));
            $sheet->setCellValue('F' . $row, $bus->telefono ?? ($bus->propietario ? $bus->propietario->telefono : '---'));
            $sheet->setCellValue('G' . $row, optional($bus->estado)->nombre_estado ?? 'N/A');
            $row++;
        }

        // Auto-fit y bordes
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:G' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Reporte_Flota_' . $nit . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
