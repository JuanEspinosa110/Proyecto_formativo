<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\TipoAsignacion;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TipoAsignacionService
{
    public function getAll()
    {
        return TipoAsignacion::orderBy('id_tipo_asignacion', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return TipoAsignacion::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_tipo', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_asignacion', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            TipoAsignacion::class,
            'nombre_tipo',
            $data['nombre_tipo'],
            'nombre_tipo'
        );

        return TipoAsignacion::create([
            'nombre_tipo' => $data['nombre_tipo'],
        ]);
    }

    public function update($id, array $data)
    {
        $tipo = TipoAsignacion::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            TipoAsignacion::class,
            'nombre_tipo',
            $data['nombre_tipo'],
            'nombre_tipo',
            $id,
            'id_tipo_asignacion'
        );

        $tipo->update([
            'nombre_tipo' => $data['nombre_tipo'],
        ]);

        return $tipo;
    }

    public function exportExcel($buscar = null)
    {
        $registros = TipoAsignacion::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_tipo', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_asignacion', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');

        // Estilos para encabezados
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        
        $row = 2;
        foreach ($registros as $reg) {
            $sheet->setCellValue('A' . $row, $reg->id_tipo_asignacion);
            $sheet->setCellValue('B' . $row, $reg->nombre_tipo);
            $row++;
        }

        // AutoSize y Bordes
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:B' . ($row - 1))->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=tipo_asignacion.xlsx",
        ]);
    }
}
