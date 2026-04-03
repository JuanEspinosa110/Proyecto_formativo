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
        $documentos = Documento::where('NIT', $nit)->with(['tipoDocumento', 'estado'])->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('auxiliar.reportes.pdf_documentos', compact('documentos'));
            return $pdf->download('reporte_documentos.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Documentos');

        $headers = ['PLACA', 'TIPO', 'NOMBRE', 'VENCIMIENTO', 'ESTADO'];
        $row = 5;
        foreach ($documentos as $d) {
            $sheet->setCellValue('A' . $row, $d->placa);
            $sheet->setCellValue('B' . $row, $d->tipoDocumento->nombre ?? 'N/A');
            $sheet->setCellValue('C' . $row, $d->nombre);
            $sheet->setCellValue('D' . $row, $d->fecha_vencimiento);
            $sheet->setCellValue('E' . $row, $d->estado->nombre_estado ?? 'N/A');
            $row++;
        }

        $this->formatExcelReport($sheet, 'Reporte de Documentos', $headers, count($documentos));

        return $this->downloadExcel($spreadsheet, 'Reporte_Documentos');
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

    private function formatExcelReport($sheet, $title, $headers, $dataCount)
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

        // 3. Headers (Fila 4)
        $sheet->fromArray($headers, NULL, 'A4');
        $headerRange = "A4:{$lastColumn}4";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E17EB'] // Púrpura institucional razonable
            ]
        ]);

        // 4. Bordes y Zebra Striping (Fila 5 en adelante)
        $lastRow = 4 + $dataCount;
        if ($dataCount > 0) {
            $sheet->getStyle("A4:{$lastColumn}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            for ($i = 5; $i <= $lastRow; $i++) {
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
