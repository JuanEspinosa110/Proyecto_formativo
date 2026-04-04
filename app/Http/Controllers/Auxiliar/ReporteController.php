<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Viaje;
use App\Models\Documento;
use App\Models\FallaMecanica;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        return view('auxiliar.reportes.index');
    }

    /**
     * Exportar Reportes (Excel o PDF)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $nit = $user->NIT ?? null;

        if (!$nit) {
            return redirect()->back()->with('error', 'Usuario sin empresa asociada.');
        }

        $tipo = $request->input('tipo_reporte');
        $format = $request->input('formato', 'excel'); // Por defecto Excel

        if ($tipo === 'conductores') {
            return $this->exportConductores($nit, $format);
        } elseif ($tipo === 'recorridos') {
            return $this->exportRecorridos($nit, $request, $format);
        } elseif ($tipo === 'documentos') {
            return $this->exportDocumentos($nit, $format);
        } elseif ($tipo === 'fallas') {
            return $this->exportFallas($nit, $request, $format);
        }

        return redirect()->back()->with('error', 'Tipo de reporte no soportado.');
    }

    private function exportConductores($nit, $format)
    {
        $conductores = Usuario::where('NIT', $nit)
            ->whereHas('tipoUsuario', function($q) {
                $q->where('nombre_tipo', 'like', '%conductor%');
            })->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('auxiliar.reportes.pdf_conductores', compact('conductores'));
            return $pdf->download('reporte_conductores.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Conductores');

        $headers = ['DOCUMENTO', 'NOMBRE', 'CORREO', 'TELÉFONO', 'ESTADO'];
        $row = 5;
        foreach ($conductores as $c) {
            $sheet->setCellValue('A' . $row, $c->doc_usuario);
            $sheet->setCellValue('B' . $row, $c->primer_nombre . ' ' . $c->primer_apellido);
            $sheet->setCellValue('C' . $row, $c->correo);
            $sheet->setCellValue('D' . $row, $c->telefono);
            $sheet->setCellValue('E' . $row, $c->id_estado == 1 ? 'Activo' : 'Inactivo');
            $row++;
        }

        $this->formatExcelReport($sheet, 'Reporte de Conductores', $headers, count($conductores));

        return $this->downloadExcel($spreadsheet, 'Reporte_Conductores');
    }

    private function exportRecorridos($nit, Request $request, $format)
    {
        $query = Viaje::with(['bus', 'ruta', 'conductor'])
            ->whereHas('bus', function($q) use ($nit) {
                $q->where('NIT', $nit);
            });

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $recorridos = $query->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('auxiliar.reportes.pdf_recorridos', compact('recorridos'));
            return $pdf->download('reporte_recorridos.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Recorridos');

        $headers = ['FECHA', 'PLACA', 'RUTA', 'CONDUCTOR', 'SALIDA', 'LLEGADA'];
        $row = 5;
        foreach ($recorridos as $r) {
            $sheet->setCellValue('A' . $row, $r->fecha);
            $sheet->setCellValue('B' . $row, $r->placa);
            $sheet->setCellValue('C' . $row, $r->ruta->nombre_ruta ?? $r->id_ruta);
            $sheet->setCellValue('D' . $row, $r->conductor ? ($r->conductor->primer_nombre . ' ' . $r->conductor->primer_apellido) : 'N/A');
            $salida = $r->fecha_asignacion ? \Carbon\Carbon::parse($r->fecha_asignacion)->format('d/m/Y h:i A') : 'N/A';
            $llegada = $r->fecha_asignacion ? \Carbon\Carbon::parse($r->fecha_asignacion)->addHours(8)->format('d/m/Y h:i A') : 'N/A';
            
            $sheet->setCellValue('E' . $row, $salida);
            $sheet->setCellValue('F' . $row, $llegada);
            $row++;
        }

        $this->formatExcelReport($sheet, 'Itinerario de Viajes', $headers, count($recorridos));

        return $this->downloadExcel($spreadsheet, 'Reporte_Recorridos');
    }

    private function exportDocumentos($nit, $format)
    {
        $documentos = Documento::where('NIT', $nit)
            ->with(['tipoDocumento', 'estado', 'usuario.tipoUsuario'])
            ->get();

        $documentosBus = $documentos->whereNotNull('placa');
        $documentosUser = $documentos->whereNotNull('doc_usuario')->whereNull('placa');

        if ($format === 'pdf') {
            $empresa = \App\Models\Empresa::where('NIT', $nit)->first();
            $pdf = Pdf::loadView('auxiliar.reportes.pdf_documentos', compact('documentosBus', 'documentosUser', 'empresa'));
            return $pdf->download('reporte_documentos.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Estado Documental');

        // --- SECCIÓN 1: DOCUMENTOS DE VEHÍCULOS ---
        $headersBus = ['PLACA', 'TIPO', 'DOCUMENTO', 'NOMBRE', 'VENCIMIENTO', 'ESTADO'];
        $row = 5;
        
        // Título de sección (Opcional, pero ayuda a la claridad)
        $sheet->setCellValue('A4', 'DOCUMENTACIÓN DE VEHÍCULOS (FLOTA)');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);

        $rowStartBus = 6;
        $row = $rowStartBus;
        foreach ($documentosBus as $d) {
            $sheet->setCellValue('A' . $row, $d->placa);
            $sheet->setCellValue('B' . $row, $d->tipoDocumento->nombre ?? 'N/A');
            $sheet->setCellValue('C' . $row, $d->id_documento);
            $sheet->setCellValue('D' . $row, $d->nombre);
            $sheet->setCellValue('E' . $row, $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('Y-m-d') : 'N/A');
            $sheet->setCellValue('F' . $row, $d->estado->nombre_estado ?? 'N/A');
            $row++;
        }
        
        $this->formatExcelReport($sheet, 'Estado Documental - Flota', $headersBus, count($documentosBus), 5);

        // --- SECCIÓN 2: DOCUMENTOS DE PERSONAL ---
        $rowPersonalTitle = $row + 2;
        $rowHeaderPersonal = $row + 3;
        $rowStartPersonal = $row + 4;
        
        $headersUser = ['DOCUMENTO', 'NOMBRE', 'ROL', 'TIPO DOC', 'VENCIMIENTO', 'ESTADO'];
        
        $sheet->setCellValue('A' . $rowPersonalTitle, 'DOCUMENTACIÓN DE PERSONAL (CONDUCTORES/PROPIETARIOS)');
        $sheet->getStyle('A' . $rowPersonalTitle)->getFont()->setBold(true)->setSize(12);

        $row = $rowStartPersonal;
        foreach ($documentosUser as $d) {
            $sheet->setCellValue('A' . $row, $d->doc_usuario);
            $sheet->setCellValue('B' . $row, $d->usuario ? ($d->usuario->primer_nombre . ' ' . $d->usuario->primer_apellido) : $d->nombre);
            $sheet->setCellValue('C' . $row, $d->usuario->tipoUsuario->nombre_tipo ?? 'N/A');
            $sheet->setCellValue('D' . $row, $d->tipoDocumento->nombre ?? 'N/A');
            $sheet->setCellValue('E' . $row, $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('Y-m-d') : 'N/A');
            $sheet->setCellValue('F' . $row, $d->estado->nombre_estado ?? 'N/A');
            $row++;
        }

        $this->formatExcelSubSection($sheet, $headersUser, count($documentosUser), $rowHeaderPersonal);

        return $this->downloadExcel($spreadsheet, 'Reporte_Documental');
    }

    /**
     * Versión adaptada de formatExcelReport para subsecciones en la misma hoja
     */
    private function formatExcelSubSection($sheet, $headers, $dataCount, $headerRow)
    {
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        
        // Headers (Fila especificada)
        $sheet->fromArray($headers, NULL, 'A' . $headerRow);
        $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3799'] // Azul institucional
            ]
        ]);

        // Bordes y Zebra Striping
        $lastRow = $headerRow + $dataCount;
        if ($dataCount > 0) {
            $sheet->getStyle("A{$headerRow}:{$lastColumn}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            for ($i = $headerRow + 1; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle("A{$i}:{$lastColumn}{$i}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                }
            }
        }

        // Autoajuste de columnas
        foreach (range(1, count($headers)) as $index) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportFallas($nit, Request $request, $format)
    {
        $query = FallaMecanica::with(['bus', 'estado'])
            ->whereHas('bus', function($q) use ($nit) {
                $q->where('NIT', $nit);
            });

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [$request->fecha_inicio . ' 00:00:00', $request->fecha_fin . ' 23:59:59']);
        }

        $fallas = $query->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('auxiliar.reportes.pdf_fallas', compact('fallas'));
            return $pdf->download('reporte_fallas.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fallas Mecánicas');

        $headers = ['FECHA', 'PLACA', 'DESCRIPCIÓN', 'URGENCIA', 'ESTADO'];
        $row = 5;
        foreach ($fallas as $f) {
            $sheet->setCellValue('A' . $row, $f->created_at);
            $sheet->setCellValue('B' . $row, $f->placa);
            $sheet->setCellValue('C' . $row, $f->descripcion);
            $sheet->setCellValue('D' . $row, $f->nivel_urgencia);
            $sheet->setCellValue('E' . $row, optional($f->estado)->nombre_estado ?? 'Desconocido');
            $row++;
        }

        $this->formatExcelReport($sheet, 'Reporte de Fallas Mecánicas', $headers, count($fallas));

        return $this->downloadExcel($spreadsheet, 'Reporte_Fallas');
    }

    private function formatExcelReport($sheet, $title, $headers, $dataCount, $startRow = 4)
    {
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        
        // 1. Título
        $sheet->setCellValue('A1', strtoupper($title));
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 2. Fecha Generación
        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 3. Headers (Fila especificada)
        $sheet->fromArray($headers, NULL, 'A' . $startRow);
        $headerRange = "A{$startRow}:{$lastColumn}{$startRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E17EB'] 
            ]
        ]);

        // 4. Bordes y Zebra Striping
        $lastRow = $startRow + $dataCount;
        if ($dataCount > 0) {
            $sheet->getStyle("A{$startRow}:{$lastColumn}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            for ($i = $startRow + 1; $i <= $lastRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle("A{$i}:{$lastColumn}{$i}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                }
            }
        }

        // Autoajuste de columnas
        foreach (range(1, count($headers)) as $index) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function downloadExcel($spreadsheet, $name)
    {
        $filename = $name . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
