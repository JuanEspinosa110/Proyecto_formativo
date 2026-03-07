<?php

namespace App\Services\SuperAdmin\Configuracion;

use App\Models\Ciudad;
use App\Models\Departamento;
use App\Services\ConfiguracionValidationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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

        // Creamos el registro con los datos que vienen del controlador
        return Departamento::create([
            'id_departamento' => $data['id_departamento'],
            'nombre_departamento' => strtoupper($data['nombre_departamento'])
        ]);
    }

    public function getDepartamentos()
    {
        return Departamento::orderBy('nombre_departamento', 'asc')->get();
    }

    public function exportExcel($filtroCiudad = null, $filtroDepto = null)
    {
        // 1. Obtener los datos filtrados
        $query = Ciudad::with('departamento');

        if ($filtroCiudad) {
            $query->where(function ($q) use ($filtroCiudad) {
                $q->where('nombre_city', 'like', "%{$filtroCiudad}%")
                    ->orWhere('id_ciudad', 'like', "%{$filtroCiudad}%");
            });
        }

        if ($filtroDepto) {
            $query->whereHas('departamento', function ($q) use ($filtroDepto) {
                $q->where('nombre_departamento', 'like', "%{$filtroDepto}%")
                    ->orWhere('id_departamento', 'like', "%{$filtroDepto}%");
            });
        }

        $ciudades = $query->orderBy('id_ciudad', 'asc')->get();

        // 2. Crear el Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'ID Ciudad');
        $sheet->setCellValue('B1', 'Nombre Ciudad');
        $sheet->setCellValue('C1', 'ID Departamento');
        $sheet->setCellValue('D1', 'Nombre Departamento');

        $row = 2;
        foreach ($ciudades as $ciudad) {
            // USAMOS setCellValueExplicit para forzar el formato de TEXTO y mantener los ceros
            $sheet->setCellValueExplicit('A' . $row, $ciudad->id_ciudad, DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $ciudad->nombre_city);
            $sheet->setCellValueExplicit('C' . $row, $ciudad->id_departamento, DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $ciudad->departamento->nombre_departamento ?? 'N/A');
            $row++;
        }

        // 3. Preparar la descarga en el navegador
        $fileName = 'reporte_ciudades_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Enviamos las cabeceras HTTP para forzar la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
