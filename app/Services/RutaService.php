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

        // Búsqueda por ciudad, barrios o número de ruta
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
                    })
                    ->orWhere('codigo_ruta', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        // Filtro por código de ruta
        if ($request->filled('codigo_ruta')) {
            $query->where('codigo_ruta', 'like', "%{$request->codigo_ruta}%");
        }

        // Filtro por ciudad
        if ($request->filled('id_ciudad')) {
            $query->where('id_ciudad', $request->id_ciudad);
        }

        // Filtro por barrio origen
        if ($request->filled('id_barrio_origen')) {
            $query->where('id_barrio_origen', $request->id_barrio_origen);
        }

        // Filtro por barrio destino
        if ($request->filled('id_barrio_destino')) {
            $query->where('id_barrio_destino', $request->id_barrio_destino);
        }

        // Filtro Pro: Trayecto (Origen -> Destino)
        if ($request->filled('trayecto')) {
            $trayecto = trim($request->trayecto);
            $parts = explode(' ', $trayecto);

            if (count($parts) >= 2) {
                // Si hay 2 o más palabras, asumimos que intenta Origen y Destino
                $origenSearch = $parts[0];
                $destinoSearch = $parts[1];

                $query->where(function($q) use ($origenSearch, $destinoSearch) {
                    $q->whereHas('barrioOrigen', function($sq) use ($origenSearch) {
                        $sq->where('nombre', 'like', "%{$origenSearch}%");
                    })->whereHas('barrioDestino', function($sq) use ($destinoSearch) {
                        $sq->where('nombre', 'like', "%{$destinoSearch}%");
                    });
                });
            } else {
                // Si es solo una palabra, buscamos en ambos campos (O en cualquiera)
                $query->where(function($q) use ($trayecto) {
                    $q->whereHas('barrioOrigen', function($sq) use ($trayecto) {
                        $sq->where('nombre', 'like', "%{$trayecto}%");
                    })->orWhereHas('barrioDestino', function($sq) use ($trayecto) {
                        $sq->where('nombre', 'like', "%{$trayecto}%");
                    });
                });
            }
        }

        return $query->orderBy('codigo_ruta', 'asc')->paginate(5)->withQueryString();
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

        // Filtro por código de ruta
        if ($request->filled('codigo_ruta')) {
            $query->where('codigo_ruta', 'like', "%{$request->codigo_ruta}%");
        }

        // Filtro por ciudad
        if ($request->filled('id_ciudad')) {
            $query->where('id_ciudad', $request->id_ciudad);
        }

        // Filtro por barrio origen
        if ($request->filled('id_barrio_origen')) {
            $query->where('id_barrio_origen', $request->id_barrio_origen);
        }

        // Filtro por barrio destino
        if ($request->filled('id_barrio_destino')) {
            $query->where('id_barrio_destino', $request->id_barrio_destino);
        }

        // Filtro Pro: Trayecto (Origen -> Destino) en Exportación
        if ($request->filled('trayecto')) {
            $trayecto = trim($request->trayecto);
            $parts = explode(' ', $trayecto);

            if (count($parts) >= 2) {
                // Si hay 2 o más palabras, asumimos que intenta Origen y Destino
                $origenSearch = $parts[0];
                $destinoSearch = $parts[1];

                $query->where(function($q) use ($origenSearch, $destinoSearch) {
                    $q->whereHas('barrioOrigen', function($sq) use ($origenSearch) {
                        $sq->where('nombre', 'like', "%{$origenSearch}%");
                    })->whereHas('barrioDestino', function($sq) use ($destinoSearch) {
                        $sq->where('nombre', 'like', "%{$destinoSearch}%");
                    });
                });
            } else {
                // Si es solo una palabra, buscamos en ambos campos (O en cualquiera)
                $query->where(function($q) use ($trayecto) {
                    $q->whereHas('barrioOrigen', function($sq) use ($trayecto) {
                        $sq->where('nombre', 'like', "%{$trayecto}%");
                    })->orWhereHas('barrioDestino', function($sq) use ($trayecto) {
                        $sq->where('nombre', 'like', "%{$trayecto}%");
                    });
                });
            }
        }

        $rutas = $query->orderBy('id_ruta', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados Estándar
        $headers = ['ID Ruta', 'Ciudad', 'Barrio Origen', 'Barrio Destino', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E'];

        foreach ($cols as $index => $col) {
            $sheet->setCellValue($col . '1', $headers[$index]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Datos
        $row = 2;
        foreach ($rutas as $ruta) {
            $sheet->setCellValue('A' . $row, $ruta->id_ruta);
            $sheet->setCellValue('B' . $row, optional($ruta->ciudad)->nombre_city ?? '—');
            $sheet->setCellValue('C' . $row, optional($ruta->barrioOrigen)->nombre ?? '—');
            $sheet->setCellValue('D' . $row, optional($ruta->barrioDestino)->nombre ?? '—');
            $sheet->setCellValue('E' . $row, optional($ruta->estado)->nombre_estado ?? '—');
            $row++;
        }

        // Estilos: AutoSize + Bordes Finos
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_Rutas_' . date('Ymd_His') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
