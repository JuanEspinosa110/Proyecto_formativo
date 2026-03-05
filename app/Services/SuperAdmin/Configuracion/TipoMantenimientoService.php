<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\TipoMantenimiento;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TipoMantenimientoService
{
    public function getAll()
    {
        return TipoMantenimiento::orderBy('id_tipo_mantenimiento', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return TipoMantenimiento::when($buscar, function ($query, $buscar) {
                return $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_mantenimiento', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            TipoMantenimiento::class,
            'nombre',
            $data['nombre'],
            'nombre'
        );

        return TipoMantenimiento::create([
            'nombre' => $data['nombre'],
        ]);
    }

    public function update($id, array $data)
    {
        $tipo = TipoMantenimiento::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            TipoMantenimiento::class,
            'nombre',
            $data['nombre'],
            'nombre',
            $id,
            'id_tipo_mantenimiento'
        );

        $tipo->update([
            'nombre' => $data['nombre'],
        ]);

        return $tipo;
    }

    public function exportExcel($buscar = null)
    {
        $registros = TipoMantenimiento::when($buscar, function ($query, $buscar) {
                return $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_mantenimiento', 'asc')
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
            $sheet->setCellValue('A' . $row, $reg->id_tipo_mantenimiento);
            $sheet->setCellValue('B' . $row, $reg->nombre);
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
            "Content-Disposition" => "attachment; filename=tipo_mantenimiento.xlsx",
        ]);
    }
}
