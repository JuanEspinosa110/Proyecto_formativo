<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\TipoEmpresa;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TipoEmpresaService
{
    public function getAll()
    {
        return TipoEmpresa::orderBy('id_tipo_empresa', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return TipoEmpresa::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_tipo', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_empresa', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            TipoEmpresa::class,
            'nombre_tipo',
            $data['nombre_tipo'],
            'nombre_tipo'
        );

        return TipoEmpresa::create([
            'nombre_tipo' => $data['nombre_tipo'],
        ]);
    }

    public function update($id, array $data)
    {
        $tipo = TipoEmpresa::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            TipoEmpresa::class,
            'nombre_tipo',
            $data['nombre_tipo'],
            'nombre_tipo',
            $id,
            'id_tipo_empresa'
        );

        $tipo->update([
            'nombre_tipo' => $data['nombre_tipo'],
        ]);

        return $tipo;
    }

    public function exportExcel($buscar = null)
    {
        $registros = TipoEmpresa::when($buscar, function ($query, $buscar) {
                return $query->where('nombre_tipo', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_empresa', 'asc')
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
            $sheet->setCellValue('A' . $row, $reg->id_tipo_empresa);
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
            "Content-Disposition" => "attachment; filename=tipo_empresa.xlsx",
        ]);
    }
}
