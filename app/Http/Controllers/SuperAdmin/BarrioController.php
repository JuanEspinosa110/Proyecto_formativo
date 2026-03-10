<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Barrio;
use App\Models\Ciudad;
use App\Http\Requests\BarrioRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarrioController extends Controller
{
    /**
     * Listado de barrios paginado
     */
    public function index(Request $request)
    {
        $query = Barrio::with('ciudad');

        // Filtro básico por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        // Orden ASC por ID según requerimiento
        $barrios = $query->orderBy('id_barrio', 'asc')->paginate(5);

        // Ciudades para los select en modales
        $ciudades = Ciudad::orderBy('nombre_city', 'asc')->get();

        return view('superadmin.barrios.index', compact('barrios', 'ciudades'));
    }

    /**
     * Almacenar un nuevo barrio
     */
    public function store(BarrioRequest $request)
    {
        Barrio::create($request->validated());

        return redirect()->route('superadmin.configuracion.barrios.index')
            ->with('success', 'Barrio creado exitosamente.');
    }

    /**
     * Actualizar un barrio existente
     */
    public function update(BarrioRequest $request, $id)
    {
        $barrio = Barrio::findOrFail($id);
        $barrio->update($request->validated());

        return redirect()->route('superadmin.configuracion.barrios.index')
            ->with('success', 'Barrio actualizado exitosamente.');
    }


    /**
     * Exportar a Excel usando PhpSpreadsheet
     */
    public function export()
    {
        $fileName = 'barrios_' . date('Y-m-d_H-i-s') . '.xlsx';
        $barrios = Barrio::with('ciudad')->orderBy('id_barrio', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados claros
        $headers = ['ID', 'Nombre del Barrio', 'Ciudad'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Estilos para encabezados (Estándar SIGU)
        $headerStyle = $sheet->getStyle('A1:C1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FF5E548E'); // Color Púrpura SIGU
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Datos
        $row = 2;
        foreach ($barrios as $barrio) {
            $sheet->setCellValue('A' . $row, $barrio->id_barrio);
            $sheet->setCellValue('B' . $row, $barrio->nombre);
            $sheet->setCellValue('C' . $row, optional($barrio->ciudad)->nombre_city);
            $row++;
        }

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);

        // Guardar en archivo temporal y descargar
        $writer = new Xlsx($spreadsheet);
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $fileName)->deleteFileAfterSend(true);
    }
}
