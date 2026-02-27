<?php

namespace App\Services;

use App\Models\Ruta;
use App\Models\Estado;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RutaService
{
    /**
     * Obtener listado de rutas con filtros, paginación y eager loading
     */
    public function getRutas(Request $request)
    {
        $query = Ruta::with(['estado', 'empresa', 'ciudad', 'barrioOrigen', 'barrioDestino']);

        // Filtro por NIT (Empresa)
        if ($request->filled('NIT')) {
            $query->where('NIT', $request->NIT);
        }

        // Búsqueda por origen o destino o ciudad
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('origen', 'like', "%{$search}%")
                  ->orWhere('destino', 'like', "%{$search}%")
                  ->orWhereHas('ciudad', function($sq) use ($search) {
                      $sq->where('nombre_city', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        return $query->orderBy('id_ruta', 'asc')->paginate(10)->withQueryString();
    }

    /**
     * Obtener estados operativos (sin IDs hardcodeados)
     */
    public function getEstadosOperativos()
    {
        return Estado::whereIn('nombre_estado', [
            'ACTIVO', 
            'INACTIVO', 
            'HABILITADA', 
            'DESHABILITADA'
        ])->get();
    }

    /**
     * Guardar nueva ruta
     */
    public function storeRuta(array $data)
    {
        return Ruta::create([
            'NIT'               => $data['NIT'],
            'id_ciudad'         => $data['id_ciudad'],
            'id_barrio_origen'  => $data['id_barrio_origen'],
            'id_barrio_destino' => $data['id_barrio_destino'],
            'origen'            => strtoupper(trim($data['origen'])),
            'destino'           => strtoupper(trim($data['destino'])),
            'id_estado'         => $data['id_estado'],
        ]);
    }

    /**
     * Actualizar ruta existente
     */
    public function updateRuta(Ruta $ruta, array $data)
    {
        return $ruta->update([
            'NIT'               => $data['NIT'] ?? $ruta->NIT,
            'id_ciudad'         => $data['id_ciudad'] ?? $ruta->id_ciudad,
            'id_barrio_origen'  => $data['id_barrio_origen'] ?? $ruta->id_barrio_origen,
            'id_barrio_destino' => $data['id_barrio_destino'] ?? $ruta->id_barrio_destino,
            'origen'            => strtoupper(trim($data['origen'])),
            'destino'           => strtoupper(trim($data['destino'])),
            'id_estado'         => $data['id_estado'],
        ]);
    }

    /**
     * Exportar rutas a Excel respetando filtros y ordenamiento ASC
     */
    public function exportExcel(Request $request)
    {
        $query = Ruta::with(['estado', 'empresa', 'ciudad', 'barrioOrigen', 'barrioDestino']);

        if ($request->filled('NIT')) {
            $query->where('NIT', $request->NIT);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('origen', 'like', "%{$search}%")
                  ->orWhere('destino', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        $rutas = $query->orderBy('id_ruta', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados Estándar
        $headers = ['ID Ruta', 'Empresa', 'Ciudad', 'Barrio Origen', 'Punto Origen', 'Barrio Destino', 'Punto Destino', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($cols as $index => $col) {
            $sheet->setCellValue($col . '1', $headers[$index]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Datos
        $row = 2;
        foreach ($rutas as $ruta) {
            $sheet->setCellValue('A' . $row, $ruta->id_ruta);
            $sheet->setCellValue('B' . $row, optional($ruta->empresa)->nombre_empresa ?? $ruta->NIT);
            $sheet->setCellValue('C' . $row, optional($ruta->ciudad)->nombre_city ?? '—');
            $sheet->setCellValue('D' . $row, optional($ruta->barrioOrigen)->nombre ?? '—');
            $sheet->setCellValue('E' . $row, $ruta->origen);
            $sheet->setCellValue('F' . $row, optional($ruta->barrioDestino)->nombre ?? '—');
            $sheet->setCellValue('G' . $row, $ruta->destino);
            $sheet->setCellValue('H' . $row, optional($ruta->estado)->nombre_estado ?? '—');
            $row++;
        }

        // Estilos: AutoSize + Bordes Finos
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:H' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_Rutas_' . date('Ymd_His') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
