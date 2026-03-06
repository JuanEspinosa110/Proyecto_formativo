<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\Estado;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstadoService
{
    public function getAll()
    {
        return Estado::orderBy('id_estado', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return Estado::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_estado', 'like', "%{$buscar}%");
            })
            ->orderBy('id_estado', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            Estado::class,
            'nombre_estado',
            $data['nombre_estado'],
            'nombre_estado'
        );

        return Estado::create([
            'nombre_estado' => $data['nombre_estado'],
        ]);
    }

    public function update($id, array $data)
    {
        $estado = Estado::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            Estado::class,
            'nombre_estado',
            $data['nombre_estado'],
            'nombre_estado',
            $id,
            'id_estado'
        );

        $estado->update([
            'nombre_estado' => $data['nombre_estado'],
        ]);

        return $estado;
    }

    public function exportExcel($buscar = null)
    {
        $registros = Estado::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_estado', 'like', "%{$buscar}%");
            })
            ->orderBy('id_estado', 'asc')
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
            $sheet->setCellValue('A' . $row, $reg->id_estado);
            $sheet->setCellValue('B' . $row, $reg->nombre_estado);
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
            "Content-Disposition" => "attachment; filename=estados.xlsx",
        ]);
    }
}
