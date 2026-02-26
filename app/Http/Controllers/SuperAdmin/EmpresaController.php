<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Estado;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmpresaController extends Controller
{
    /**
     * Listado de empresas
     */
    public function index(Request $request)
    {
        $query = Empresa::with(['ciudad.departamento', 'estado']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nombre_empresa', 'like', "%$search%")
                  ->orWhere('NIT', 'like', "%$search%")
                  ->orWhere('correo_corporativo', 'like', "%$search%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        if ($request->filled('ciudad')) {
            $query->where('id_ciudad', $request->ciudad);
        }

        $empresas = $query->orderBy('fecha_creacion', 'desc')->paginate(5);
        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();
        $ciudades = Ciudad::orderBy('nombre_city')->get();

        return view('superadmin.empresas.index', compact('empresas','estados','ciudades'));
    }

    /**
     * Formulario crear empresa
     */
    public function create()
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();

        return view('superadmin.empresas.create', compact('departamentos','estados'));
    }

    /**
     * Guardar empresa
     */
    public function store(Request $request)
    {
    $validated = $request->validate([

    // EMPRESA
    'NIT' => 'required|digits:10|unique:empresa,NIT',
    'nombre_empresa' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
    'telefono_empresa' => 'required|digits_between:7,15',
    'correo_corporativo' => 'required|email',

    // REPRESENTANTE
    'doc_representante' => 'required|digits_between:7,10',
    'primer_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
    'segundo_nombre_repre' => ['nullable','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
    'primer_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
    'segundo_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
    'telefono_representante' => 'required|digits_between:7,15',
    'correo_representante' => 'required|email',

    // UBICACIÓN
    'id_ciudad' => 'required|exists:ciudad,id_ciudad',

    ], [

    // NIT
    'NIT.required' => 'El NIT es obligatorio.',
    'NIT.digits' => 'El NIT debe tener exactamente 10 dígitos.',
    'NIT.unique' => 'Ya existe una empresa registrada con este NIT.',

    // Empresa
    'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
    'nombre_empresa.regex' => 'El nombre solo puede contener letras y espacios.',
    'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
    'telefono_empresa.digits_between' => 'El teléfono debe tener entre 7 y 15 números.',
    'correo_corporativo.required' => 'El correo corporativo es obligatorio.',
    'correo_corporativo.email' => 'El correo corporativo no es válido.',

    // Representante
    'doc_representante.required' => 'El documento del representante es obligatorio.',
    'doc_representante.digits_between' => 'El documento debe tener entre 7 y 10 dígitos.',
    'primer_nombre_repre.required' => 'El primer nombre es obligatorio.',
    'primer_apellido_repre.required' => 'El primer apellido es obligatorio.',
    'segundo_apellido_repre.required' => 'El segundo apellido es obligatorio.',
    'telefono_representante.required' => 'El teléfono del representante es obligatorio.',
    'telefono_representante.digits_between' => 'El teléfono del representante no es válido.',
    'correo_representante.required' => 'El correo del representante es obligatorio.',
    'correo_representante.email' => 'El correo del representante no es válido.',

    // Ubicación
    'id_ciudad.required' => 'Debe seleccionar una ciudad.',
    'id_ciudad.exists' => 'La ciudad seleccionada no es válida.',
    ]);


    //  ASIGNACIONES AUTOMÁTICAS
    $validated['id_estado'] = 1;
    $validated['id_tipo_empresa'] = 1;

    Empresa::create($validated);

    return redirect()
        ->route('superadmin.empresas.index')
        ->with('success', 'La empresa ha sido creada exitosamente.');
    }



    public function edit($nit)
    {
        $empresa = Empresa::with('ciudad')->findOrFail($nit);

        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();

        // Cargar ciudades del departamento actual
        $ciudades = Ciudad::where(
            'id_departamento',
            optional($empresa->ciudad)->id_departamento
        )->get();

        return view('superadmin.empresas.edit', compact(
            'empresa',
            'departamentos',
            'ciudades',
            'estados'
        ));
    }

    /**
     * Actualizar empresa
     */
    public function update(Request $request, $nit)
    {
        $empresa = Empresa::findOrFail($nit);

        $validated = $request->validate([

            'nombre_empresa' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'doc_representante' => 'required|numeric',
            'primer_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'primer_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'telefono_representante' => 'required|numeric',
            'telefono_empresa' => 'required|numeric',
            'correo_corporativo' => ['required','regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'correo_representante' => ['required','regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],

            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'id_estado' => 'required|exists:estado,id_estado',

        ]);

        $empresa->update($validated);

        return redirect()
            ->route('superadmin.empresas.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Mostrar detalles
     */
    public function show($nit)
    {
        $empresa = Empresa::with(['ciudad.departamento','estado','usuarios.tipoUsuario'])
            ->findOrFail($nit);

        return view('superadmin.empresas.show', compact('empresa'));
    }

    /**
     * Cargar ciudades por departamento (AJAX)
     */
    public function getCiudadesByDepartamento($id_departamento)
        {
            $ciudades = Ciudad::where('id_departamento', $id_departamento)
                ->orderBy('nombre_city')
                ->get();

            return response()->json($ciudades);
        }

        public function exportCsv()
    {
        $fileName = 'empresas.csv';

        $empresas = Empresa::with(['ciudad', 'estado'])->get();

        $response = new StreamedResponse(function () use ($empresas) {

            $handle = fopen('php://output', 'w');

            // Encabezados
            fputcsv($handle, [
                'NIT',
                'Nombre Empresa',
                'Teléfono',
                'Correo',
                'Ciudad',
                'Estado'
            ]);

            foreach ($empresas as $empresa) {
                fputcsv($handle, [
                    $empresa->NIT,
                    $empresa->nombre_empresa,
                    $empresa->telefono_empresa,
                    $empresa->correo_corporativo,
                    optional($empresa->ciudad)->nombre_city,
                    optional($empresa->estado)->nombre_estado,
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=$fileName");

        return $response;
    }

    /**
     * Exportar empresas a Excel
     */
    public function exportExcel()
    {
        $fileName = 'empresas_' . date('Y-m-d_H-i-s') . '.xlsx';

        $empresas = Empresa::with(['ciudad', 'estado'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['NIT', 'Nombre Empresa', 'Teléfono', 'Correo', 'Ciudad', 'Estado'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Estilos para encabezados
        $headerStyle = $sheet->getStyle('A1:F1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FF5E548E'); // Color SIGU
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Datos
        $row = 2;
        foreach ($empresas as $empresa) {
            $sheet->setCellValue('A' . $row, $empresa->NIT);
            $sheet->setCellValue('B' . $row, $empresa->nombre_empresa);
            $sheet->setCellValue('C' . $row, $empresa->telefono_empresa);
            $sheet->setCellValue('D' . $row, $empresa->correo_corporativo);
            $sheet->setCellValue('E' . $row, optional($empresa->ciudad)->nombre_city);
            $sheet->setCellValue('F' . $row, optional($empresa->estado)->nombre_estado);
            $row++;
        }

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Guardar en archivo temporal y descargar
        $writer = new Xlsx($spreadsheet);
        $temp = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp);

        return response()->download($temp, $fileName)->deleteFileAfterSend(true);
    }


}