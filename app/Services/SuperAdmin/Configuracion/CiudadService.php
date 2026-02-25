<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\Ciudad;
use App\Models\Departamento;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CiudadService
{
    public function getAll()
    {
        return Ciudad::with('departamento')->orderBy('id_ciudad', 'asc')->get();
    }

    public function paginate($perPage = 5, $buscar = null)
    {
        return Ciudad::with('departamento')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('nombre_city', 'like', "%{$buscar}%")
                             ->orWhereHas('departamento', function ($q) use ($buscar) {
                                 $q->where('nombre_departamento', 'like', "%{$buscar}%");
                             });
            })
            ->orderBy('id_ciudad', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            Ciudad::class,
            'nombre_city',
            $data['nombre_city'],
            'nombre_city'
        );

        return Ciudad::create([
            'nombre_city' => $data['nombre_city'],
            'id_departamento' => $data['id_departamento']
        ]);
    }

    public function update($id, array $data)
    {
        $ciudad = Ciudad::findOrFail($id);

        ConfiguracionValidationService::validarNombreUnico(
            Ciudad::class,
            'nombre_city',
            $data['nombre_city'],
            'nombre_city',
            $id,
            'id_ciudad'
        );

        $ciudad->update([
            'nombre_city' => $data['nombre_city'],
            'id_departamento' => $data['id_departamento']
        ]);

        return $ciudad;
    }

    public function storeDepartamento(array $data)
    {
        ConfiguracionValidationService::validarNombreUnico(
            Departamento::class,
            'nombre_departamento',
            $data['nombre_departamento'],
            'nombre_departamento'
        );

        return Departamento::create([
            'nombre_departamento' => $data['nombre_departamento']
        ]);
    }

    public function getDepartamentos()
    {
        return Departamento::orderBy('nombre_departamento', 'asc')->get();
    }

    public function exportExcel($buscar = null)
    {
        $registros = Ciudad::with('departamento')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('nombre_city', 'like', "%{$buscar}%")
                             ->orWhereHas('departamento', function ($q) use ($buscar) {
                                 $q->where('nombre_departamento', 'like', "%{$buscar}%");
                             });
            })
            ->orderBy('id_ciudad', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Ciudad');
        $sheet->setCellValue('C1', 'Departamento');

        // Estilos para encabezados
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        
        $row = 2;
        foreach ($registros as $reg) {
            $sheet->setCellValue('A' . $row, $reg->id_ciudad);
            $sheet->setCellValue('B' . $row, $reg->nombre_city);
            $sheet->setCellValue('C' . $row, $reg->departamento->nombre_departamento ?? 'N/A');
            $row++;
        }

        // AutoSize y Bordes
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:C' . ($row - 1))->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=ciudades.xlsx",
        ]);
    }
}
