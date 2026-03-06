
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
        $query = Ruta::with(['estado', 'ciudad', 'barrioOrigen', 'barrioDestino']);

<<<<<<< HEAD
        // Búsqueda por ciudad, barrios o código de ruta
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_ruta', 'like', "%{$search}%")
                  ->orWhereHas('ciudad', function($sq) use ($search) {
=======
        // Búsqueda por ciudad o barrios
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('ciudad', function($sq) use ($search) {
>>>>>>> origin/develop
                      $sq->where('nombre_city', 'like', "%{$search}%");
                  })
                  ->orWhereHas('barrioOrigen', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('barrioDestino', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
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
            'INACTIVO'
        ])->get();
    }

    /**
     * Guardar nueva ruta
     */
    public function storeRuta(array $data)
    {
        // Generar ID aleatorio de 6 dígitos único
        do {
            $id = random_int(100000, 999999);
        } while (Ruta::where('id_ruta', $id)->exists());

        return Ruta::create([
            'id_ruta'           => $id,
            'id_ciudad'         => $data['id_ciudad'],
            'codigo_ruta'       => $data['codigo_ruta'],
            'id_barrio_origen'  => $data['id_barrio_origen'],
            'id_barrio_destino' => $data['id_barrio_destino'],
            'id_estado'         => $data['id_estado'],
        ]);
    }

    /**
     * Actualizar ruta existente
     */
    public function updateRuta(Ruta $ruta, array $data)
    {
        return $ruta->update([
            'id_ciudad'         => $data['id_ciudad'] ?? $ruta->id_ciudad,
            'codigo_ruta'       => $data['codigo_ruta'] ?? $ruta->codigo_ruta,
            'id_barrio_origen'  => $data['id_barrio_origen'] ?? $ruta->id_barrio_origen,
            'id_barrio_destino' => $data['id_barrio_destino'] ?? $ruta->id_barrio_destino,
            'id_estado'         => $data['id_estado'],
        ]);
    }

    /**
     * Exportar rutas a Excel respetando filtros y ordenamiento ASC
     */
    public function exportExcel(Request $request)
    {
        $query = Ruta::with(['estado', 'ciudad', 'barrioOrigen', 'barrioDestino']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('ciudad', function($sq) use ($search) {
                      $sq->where('nombre_city', 'like', "%{$search}%");
                  })
                  ->orWhereHas('barrioOrigen', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('barrioDestino', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        $rutas = $query->orderBy('id_ruta', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados Estándar
<<<<<<< HEAD
        $headers = ['ID Ruta', 'Código', 'Ciudad', 'Barrio Origen', 'Barrio Destino', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
=======
        $headers = ['ID Ruta', 'Ciudad', 'Barrio Origen', 'Barrio Destino', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E'];
>>>>>>> origin/develop

        foreach ($cols as $index => $col) {
            $sheet->setCellValue($col . '1', $headers[$index]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Datos
        $row = 2;
        foreach ($rutas as $ruta) {
            $sheet->setCellValue('A' . $row, $ruta->id_ruta);
<<<<<<< HEAD
            $sheet->setCellValue('B' . $row, $ruta->codigo_ruta);
            $sheet->setCellValue('C' . $row, optional($ruta->ciudad)->nombre_city ?? '—');
            $sheet->setCellValue('D' . $row, optional($ruta->barrioOrigen)->nombre ?? '—');
            $sheet->setCellValue('E' . $row, optional($ruta->barrioDestino)->nombre ?? '—');
            $sheet->setCellValue('F' . $row, optional($ruta->estado)->nombre_estado ?? '—');
=======
            $sheet->setCellValue('B' . $row, optional($ruta->ciudad)->nombre_city ?? '—');
            $sheet->setCellValue('C' . $row, optional($ruta->barrioOrigen)->nombre ?? '—');
            $sheet->setCellValue('D' . $row, optional($ruta->barrioDestino)->nombre ?? '—');
            $sheet->setCellValue('E' . $row, optional($ruta->estado)->nombre_estado ?? '—');
>>>>>>> origin/develop
            $row++;
        }

        // Estilos: AutoSize + Bordes Finos
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
<<<<<<< HEAD
        $sheet->getStyle('A1:F' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
=======
        $sheet->getStyle('A1:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
>>>>>>> origin/develop

        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_Rutas_' . date('Ymd_His') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
