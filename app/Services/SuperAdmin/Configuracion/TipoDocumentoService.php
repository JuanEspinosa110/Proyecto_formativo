<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\TipoDocumento;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TipoDocumentoService
{
    public function getAll()
    {
        return TipoDocumento::orderBy('id_tipo_documento', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return TipoDocumento::when($buscar, function ($query, $buscar) {
            return $query->where('nombre', 'like', "%{$buscar}%");
        })
            ->orderBy('id_tipo_documento', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            TipoDocumento::class,
            'nombre',
            $data['nombre'],
            'nombre'
        );

        return TipoDocumento::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'id_estado' => $data['id_estado'] ?? 1, // Asumiendo 1 como activo
            'requiere_doc_usuario' => $data['requiere_doc_usuario'] ?? 0,
            'requiere_placa' => $data['requiere_placa'] ?? 0,
        ]);
    }

    public function update($id, array $data)
    {
        $tipo = TipoDocumento::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            TipoDocumento::class,
            'nombre',
            $data['nombre'],
            'nombre', // El campo en el request es 'nombre'
            $id,
            'id_tipo_documento'
        );

        $tipo->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? $tipo->descripcion,
            'id_estado' => $data['id_estado'] ?? $tipo->id_estado,
            'requiere_doc_usuario'  => $data['requiere_doc_usuario'] ?? 0,
            'requiere_placa'        => $data['requiere_placa'] ?? 0,
        ]);

        return $tipo;
    }

    public function exportExcel($buscar = null)
    {
        $registros = TipoDocumento::when($buscar, function ($query, $buscar) {
            return $query->where('nombre', 'like', "%{$buscar}%");
        })
            ->orderBy('id_tipo_documento', 'asc')
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
            $sheet->setCellValue('A' . $row, $reg->id_tipo_documento);
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
            "Content-Disposition" => "attachment; filename=tipo_documento.xlsx",
        ]);
    }
}
