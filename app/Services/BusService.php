<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\Estado;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;

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
     * Crear un nuevo bus bajo el NIT del usuario autenticado
     */
    public function storeBus(array $data)
    {
        $data['NIT'] = Auth::guard('web')->user()->getActiveNit();
        return Bus::create($data);
    }

    /**
     * Actualizar bus existente
     */
    public function updateBus(Bus $bus, array $data)
    {
        // La placa no debe actualizarse para evitar conflictos con llaves foráneas (FK en tabla viaje)
        unset($data['placa']);
        
        return $bus->update($data);
    }

    /**
     * Eliminar bus
     */
    public function deleteBus(Bus $bus)
    {
        return $bus->delete();
    }

    /**
     * Exportar buses a Excel: Eager loading + Filtros + Estilo Premium
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();
        $query = Bus::with(['estado', 'empresa'])->where('NIT', $nit);

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

        $buses = $query->orderBy('placa', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Placa', 'Empresa', 'Modelo', 'Capacidad', 'Kilometraje', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($cols as $index => $col) {
            $sheet->setCellValue($col . '1', $headers[$index]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Body
        $row = 2;
        foreach ($buses as $bus) {
            $sheet->setCellValue('A' . $row, $bus->placa);
            $sheet->setCellValue('B' . $row, optional($bus->empresa)->nombre_empresa ?? $bus->NIT);
            $sheet->setCellValue('C' . $row, $bus->modelo);
            $sheet->setCellValue('D' . $row, $bus->capacidad_pasajeros);
            $sheet->setCellValue('E' . $row, $bus->kilometraje);
            $sheet->setCellValue('F' . $row, optional($bus->estado)->nombre_estado ?? 'N/A');
            $row++;
        }

        // Premium Styles
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:F' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_Buses_' . date('Ymd_His') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
