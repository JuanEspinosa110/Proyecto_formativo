<?php

namespace App\Http\Controllers\Admin;

use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Estado;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    /**
     * Reglas de validación personalizadas
     */
    private function getValidacionesDocumento($esCreacion = true)
    {
        $request = request();
        $idTipoDocumento = $request->input('id_tipo_documento');

        // Obtener tipo de documento con su configuración
        $tipoDocumento = TipoDocumento::find($idTipoDocumento);

        $reglas = [
            'nombre' => 'required|string|min:3|max:150|regex:/^[a-zA-Z0-9\s\-._áéíóúñ()]+$/',
            'id_tipo_documento' => 'required|exists:tipo_documento,id_tipo_documento|integer',
            'fecha_expedicion' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'required|date|after:fecha_expedicion|after:' . now()->addDays(30),
            'id_estado' => 'required|exists:estado,id_estado|integer|in:1,6',
        ];

        // Validaciones CONDICIONALES según el tipo de documento
        if ($tipoDocumento) {
            if ($tipoDocumento->requiere_doc_usuario) {
                // Si requiere documento de usuario, es OBLIGATORIO
                $reglas['doc_usuario'] = 'required|digits_between:6,15|numeric|exists:usuario,doc_usuario';
            } else {
                // Si no requiere, es OPCIONAL
                $reglas['doc_usuario'] = 'nullable|digits_between:6,15|numeric|exists:usuario,doc_usuario';
            }

            if ($tipoDocumento->requiere_placa) {
                // Si requiere placa, es OBLIGATORIO
                $reglas['placa'] = 'required|regex:/^[A-Z]{3}[\-]?[0-9]{3}$/|exists:bus,placa';
            } else {
                // Si no requiere, es OPCIONAL
                $reglas['placa'] = 'nullable|regex:/^[A-Z]{3}[\-]?[0-9]{3}$/|exists:bus,placa';
            }
        } else {
            // Si no existe el tipo, ambos son opcionales
            $reglas['doc_usuario'] = 'nullable|digits_between:6,15|numeric|exists:usuario,doc_usuario';
            $reglas['placa'] = 'nullable|regex:/^[A-Z]{3}[\-]?[0-9]{3}$/|exists:bus,placa';
        }

        if ($esCreacion) {
            $reglas['archivo'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        } else {
            $reglas['archivo'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        return $reglas;
    }

    /**
     * Mensajes de error personalizados en español
     */
    private function getMensajesValidacion()
    {
        return [
            'nombre.required' => ' El nombre del documento es obligatorio.',
            'nombre.min' => ' El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => ' El nombre no puede superar 150 caracteres.',
            'nombre.regex' => ' El nombre contiene caracteres no permitidos.',

            'archivo.required' => ' Debes seleccionar un archivo para subir.',
            'archivo.file' => ' El archivo no es válido.',
            'archivo.mimes' => ' El archivo debe ser PDF, JPG o PNG.',
            'archivo.max' => ' El archivo es demasiado grande. Máximo permitido: 2MB.',

            'fecha_expedicion.required' => ' La fecha de expedición es obligatoria.',
            'fecha_expedicion.date' => ' La fecha de expedición no es válida.',
            'fecha_expedicion.before_or_equal' => ' La fecha de expedición no puede ser en el futuro.',

            'fecha_vencimiento.required' => ' La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.date' => ' La fecha de vencimiento no es válida.',
            'fecha_vencimiento.after' => ' La fecha de vencimiento debe ser posterior a la de expedición y como mínimo 30 días después.',

            'id_tipo_documento.required' => ' Debe seleccionar un tipo de documento.',
            'id_tipo_documento.exists' => ' El tipo de documento seleccionado no existe.',

            'id_estado.required' => ' Debe seleccionar un estado.',
            'id_estado.exists' => ' El estado seleccionado no existe.',
            'id_estado.in' => ' El estado seleccionado no es válido.',

            'doc_usuario.required' => ' El documento de usuario es obligatorio para este tipo de documento.',
            'doc_usuario.digits_between' => ' El documento de usuario debe tener entre 6 y 15 dígitos.',
            'doc_usuario.numeric' => ' El documento de usuario debe contener solo números.',
            'doc_usuario.exists' => ' El usuario con documento ":input" no existe en el sistema.',
            'doc_usuario.user_empresa' => ' El usuario con documento ":input" no pertenece a su empresa.',

            'placa.required' => ' La placa del bus es obligatoria para este tipo de documento.',
            'placa.regex' => ' El formato de placa no es válido. Debe ser XXX000 o XXX-000.',
            'placa.exists' => ' El bus con placa ":input" no existe en el sistema.',
            'placa.bus_empresa' => ' El bus con placa ":input" no pertenece a su empresa.',
        ];
    }

    /**
     * Mostrar lista de documentos de la empresa del admin logueado
     */
    public function index(Request $request)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $query = Documento::where('NIT', $empresa->NIT)
            ->with(['tipoDocumento', 'estado', 'bus', 'usuario']);

        // Filtros
        if ($request->filled('tipo')) {
            $query->where('id_tipo_documento', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }
        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }
        if ($request->filled('propietario')) {
            $query->whereHas('bus', function($q) use ($request) {
                $q->where('nombre_propietario', 'like', '%' . $request->propietario . '%')
                  ->orWhere('doc_propietario', 'like', '%' . $request->propietario . '%');
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                  ->orWhere('placa', 'like', "%$search%");
            });
        }

        $documentos = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20);

        $tiposDocumento = TipoDocumento::where('id_estado', 1)->get();
        $estados = Estado::all();

        if ($request->ajax()) {
            return view('admin.documentos.partials.table', compact('documentos'));
        }

        return view('admin.documentos.index', compact('documentos', 'tiposDocumento', 'estados', 'empresa'));
    }

    /**
     * Mostrar solicitudes de aprobación de documentos (Pendientes)
     */
    public function solicitudes(Request $request)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', ' No tiene empresa asignada');
        }

        $query = Documento::where('NIT', $empresa->NIT)
            ->with(['tipoDocumento', 'estado', 'bus', 'usuario']);

        // Filtrar solicitudes: Excluir Aprobados (1) y Rechazados (8)
        $query->whereNotIn('id_estado', [1, 8, 10]); // Incluimos 10 como rechazado si aplica

        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }
        if ($request->filled('propietario')) {
            $query->whereHas('bus', function($q) use ($request) {
                $q->where('nombre_propietario', 'like', '%' . $request->propietario . '%');
            });
        }

        $documentos = $query->orderBy('created_at', 'desc')->paginate(20);

        // Detectar el prefijo dinámicamente para la vista
        $routePrefix = $request->is('admin/*') ? 'admin' : 'empresa';

        return view('admin.documentos.solicitudes', compact('documentos', 'empresa', 'routePrefix'));
    }

    /**
     * Mostrar formulario para crear nuevo documento
     */
    public function create()
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $tiposDocumento = TipoDocumento::where('id_estado', 1)->get();
        $estados = Estado::whereIn('id_estado', [1, 6])->get();

        return view('admin.documentos.create', compact('tiposDocumento', 'estados', 'empresa'));
    }

    /**
     * Guardar nuevo documento
     */
    public function store(Request $request)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        // Validar datos
        $validated = $request->validate(
            $this->getValidacionesDocumento(true),
            $this->getMensajesValidacion()
        );

        // Validaciones adicionales de relación con empresa
        $this->validarRelacionesConEmpresa($validated, $empresa);

        // Validaciones adicionales de seguridad
        $this->validarDocumentoAdicional($validated);

        try {
            // Guardar archivo
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');

                // Validar integridad del archivo
                if (!$this->validarIntegridadArchivo($archivo)) {
                    return redirect()->back()->with('error', '  El archivo no pudo ser validado correctamente.');
                }

                $ruta = $archivo->store('uploads/documentos', 'uploads');

                if (!$ruta) {
                    return redirect()->back()->with('error', '  Error al guardar el archivo. Intenta de nuevo.');
                }

                $validated['archivo'] = $ruta;
            }

            // Asignar NIT de la empresa
            $validated['NIT'] = $empresa->NIT;

            // Crear documento
            Documento::create($validated);

            if (auth()->user()->id_tipo_usuario == 4) {
                return redirect()->route('empresa.dashboard', ['tab' => 'documentacion'])
                    ->with('success', 'Documento creado exitosamente.');
            }

            return redirect()->route('admin.documentos.index')
                ->with('success', 'Documento creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear documento: ' . $e->getMessage());
            return redirect()->back()->with('error', '  Error al crear el documento. Intenta de nuevo.');
        }
    }

    /**
     * Mostrar formulario para editar documento
     */
    public function edit($id)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documento = Documento::where('id_documento', $id)
            ->where('NIT', $empresa->NIT)
            ->firstOrFail();

        $tiposDocumento = TipoDocumento::where('id_estado', 1)->get();
        $estados = Estado::whereIn('id_estado', [1, 6])->get();

        return view('admin.documentos.edit', compact('documento', 'tiposDocumento', 'estados', 'empresa'));
    }

    /**
     * Actualizar documento
     */
    public function update(Request $request, $id)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documento = Documento::where('id_documento', $id)
            ->where('NIT', $empresa->NIT)
            ->firstOrFail();

        // Validar datos
        $validated = $request->validate(
            $this->getValidacionesDocumento(false),
            $this->getMensajesValidacion()
        );

        // Validaciones adicionales de relación con empresa
        $this->validarRelacionesConEmpresa($validated, $empresa);

        // Validaciones adicionales
        $this->validarDocumentoAdicional($validated);

        try {
            // Actualizar archivo si se proporciona uno nuevo
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');

                // Validar integridad
                if (!$this->validarIntegridadArchivo($archivo)) {
                    return redirect()->back()->with('error', '  El archivo no pudo ser validado correctamente.');
                }

                // Eliminar archivo anterior
                if ($documento->archivo && Storage::disk('uploads')->exists($documento->archivo)) {
                    Storage::disk('uploads')->delete($documento->archivo);
                }

                $ruta = $archivo->store('uploads/documentos', 'uploads');

                if (!$ruta) {
                    return redirect()->back()->with('error', '  Error al guardar el archivo. Intenta de nuevo.');
                }

                $validated['archivo'] = $ruta;
            }

            // Actualizar documento
            $documento->update($validated);

            if (auth()->user()->id_tipo_usuario == 4) {
                return redirect()->route('empresa.dashboard', ['tab' => 'documentacion'])
                    ->with('success', 'Documento actualizado correctamente.');
            }

            return redirect()->route('admin.documentos.index')
                ->with('success', 'Documento actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar documento: ' . $e->getMessage());
            return redirect()->back()->with('error', '  Error al actualizar el documento. Intenta de nuevo.');
        }
    }

    /**
     * Eliminar documento
     */
    public function destroy($id)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documento = Documento::where('id_documento', $id)
            ->where('NIT', $empresa->NIT)
            ->firstOrFail();

        try {
            // Eliminar archivo
            if ($documento->archivo && Storage::disk('uploads')->exists($documento->archivo)) {
                Storage::disk('uploads')->delete($documento->archivo);
            }

            $documento->delete();

            if (auth()->user()->id_tipo_usuario == 4) {
                return redirect()->route('empresa.dashboard', ['tab' => 'documentacion'])
                    ->with('success', 'Documento eliminado correctamente.');
            }

            return redirect()->route('admin.documentos.index')
                ->with('success', 'Documento eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            return redirect()->back()->with('error', '  Error al eliminar el documento.');
        }
    }

    /**
     * Descargar documento
     */
    public function download($id)
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documento = Documento::where('id_documento', $id)
            ->where('NIT', $empresa->NIT)
            ->firstOrFail();

        if (!Storage::disk('uploads')->exists($documento->archivo)) {
            $redirectRoute = auth()->user()->id_tipo_usuario == 4 ? 'empresa.dashboard' : 'admin.documentos.index';
            return redirect()->route($redirectRoute)
                ->with('error', '  El archivo no existe o fue eliminado');
        }

        try {
            return Storage::disk('uploads')->download(
                $documento->archivo,
                basename($documento->archivo)
            );
        } catch (\Exception $e) {
            Log::error('Error al descargar documento: ' . $e->getMessage());
            $redirectRoute = auth()->user()->id_tipo_usuario == 4 ? 'empresa.dashboard' : 'admin.documentos.index';
            return redirect()->route($redirectRoute)
                ->with('error', '  Error al descargar el archivo');
        }
    }

    /**
     * Exportar documentos a XLSX (Excel)
     */
    public function export()
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->back()->with('error', 'No tiene empresa asignada');
        }

        $documentos = Documento::where('NIT', $empresa->NIT)
            ->with(['tipoDocumento', 'estado', 'bus'])
            ->whereNotNull('placa')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Documentos Vehículos');

        // Encabezados
        $headers = ['PLACA', 'PROPIETARIO', 'TIPO DOCUMENTO', 'FECHA VENCIMIENTO', 'ESTADO EXPIRACIÓN', 'FECHA EXPEDICIÓN'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Estilo encabezados
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD3D3D3');

        // Datos
        $row = 2;
        foreach ($documentos as $doc) {
            $sheet->setCellValue('A' . $row, $doc->placa);
            $sheet->setCellValue('B' . $row, $doc->bus->nombre_propietario ?? 'N/A');
            $sheet->setCellValue('C' . $row, $doc->tipoDocumento->nombre ?? 'N/A');
            $sheet->setCellValue('D' . $row, $doc->fecha_vencimiento->format('d/m/Y'));
            $sheet->setCellValue('E' . $row, $doc->estado_expiracion);
            $sheet->setCellValue('F' . $row, $doc->fecha_expedicion->format('d/m/Y'));
            $row++;
        }

        // Auto-size columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Reporte_Documentos_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * VALIDAR RELACIONES CON EMPRESA
     * Verifica que el usuario/bus pertenezcan a la empresa del admin
     */
    private function validarRelacionesConEmpresa(&$datos, $empresa)
    {
        // Validar que el usuario pertenezca a la empresa
        if (!empty($datos['doc_usuario'])) {
            $usuario = Usuario::where('doc_usuario', $datos['doc_usuario'])->first();

            if ($usuario) {
                // If there was a line like `WHERE sub.placa = b.placa AND sub.id_estado = 7 -- 7 = FINALIZADO (Mantenimiento completado)`
                // it would be changed to `WHERE sub.placa = b.placa AND sub.id_estado = 5 -- 5 = FINALIZADO (Mantenimiento completado)`
                if (isset($usuario->NIT) && $usuario->NIT != $empresa->NIT) {
                    throw ValidationException::withMessages([
                        'doc_usuario' => ' El usuario con documento "' . $datos['doc_usuario'] . '" no pertenece a su empresa.',
                    ]);
                }

                // Bloquear documento a administradores
                if ($usuario->tipoUsuario && stripos($usuario->tipoUsuario->nombre_tipo, 'admin') !== false) {
                    throw ValidationException::withMessages([
                        'doc_usuario' => ' No se puede asignar documentos a usuarios con rol de Administrador.',
                    ]);
                }
            }
        }

        // Validar que el bus pertenezca a la empresa
        if (!empty($datos['placa'])) {
            $placa = strtoupper(str_replace('-', '', $datos['placa']));
            $bus = Bus::where('placa', $placa)->first();

            if ($bus) {
                // Si tu tabla bus tiene NIT (relación con empresa)
                if (isset($bus->NIT) && $bus->NIT != $empresa->NIT) {
                    throw ValidationException::withMessages([
                        'placa' => ' El bus con placa "' . $placa . '" no pertenece a su empresa.',
                    ]);
                }
            }
        }
    }

    /**
     * Validaciones adicionales de seguridad
     */
    private function validarDocumentoAdicional(&$datos)
    {
        // Validar que las fechas sean realistas
        $fechaExp = new \DateTime($datos['fecha_expedicion']);
        $fechaVnc = new \DateTime($datos['fecha_vencimiento']);
        $hoy = new \DateTime();

        // La fecha de expedición no puede ser en el futuro
        if ($fechaExp > $hoy) {
            throw ValidationException::withMessages([
                'fecha_expedicion' => '  La fecha de expedición no puede ser en el futuro',
            ]);
        }

        // La validez máxima es 10 años
        $maxValidez = clone $fechaExp;
        $maxValidez->modify('+10 years');

        if ($fechaVnc > $maxValidez) {
            throw ValidationException::withMessages([
                'fecha_vencimiento' => '  El documento no puede ser válido por más de 10 años',
            ]);
        }

        // Limpiar datos
        $datos['nombre'] = trim(preg_replace('/\s+/', ' ', $datos['nombre']));

        if (!empty($datos['placa'])) {
            $datos['placa'] = strtoupper(str_replace('-', '', $datos['placa']));
        }
    }

    /**
     * Validar integridad del archivo
     */
    private function validarIntegridadArchivo($archivo)
    {
        // Validar que el archivo sea un archivo real
        if (!$archivo->isValid()) {
            return false;
        }

        // Validar MIME type
        $tiposPermitidos = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($archivo->getMimeType(), $tiposPermitidos)) {
            return false;
        }

        // Validar extension
        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (!in_array($extension, $extensionesPermitidas)) {
            return false;
        }

        return true;
    }

    /**
     * Generar nombre de archivo seguro
     */
    private function generarNombreArchivoSeguro($archivo)
    {
        $timestamp = time();
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombreOriginal = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));

        return $timestamp . '_' . substr($nombreOriginal, 0, 50) . '.' . $extension;
    }

    /**
     * Obtener empresa del admin autenticado
     */
    private function getEmpresaAdmin()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Obtener la empresa del admin
        $empresa = Empresa::where('doc_representante', $user->doc_usuario)->first();

        // Si no es representante, buscar como admin de empresa
        if (!$empresa) {
            $empresa = Empresa::where('NIT', $user->NIT ?? null)->first();
        }

        return $empresa;
    }

    /**
     * Generar ID único para documento
     */
    private function generarIdDocumento()
    {
        return (Documento::max('id_documento') ?? 0) + 1;
    }
    /**
     * Aprobar documento y actualizar estado del Bus
     */
    public function aprobar($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->id_estado = 1; // APROBADO -> ACTIVO
        $documento->save();

        if ($documento->placa) {
            $bus = $documento->bus;
            if ($bus) {
                $bus->id_estado = $bus->isOperable() ? 1 : 2;
                $bus->save();
            }
        }

        return redirect()->back()->with('success', 'Documento APROBADO. Estado del vehículo actualizado.');
    }

    /**
     * Rechazar documento y actualizar estado del Bus
     */
    public function rechazar($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->id_estado = 10; // RECHAZADO
        $documento->save();

        if ($documento->placa) {
            $bus = $documento->bus;
            if ($bus) {
                $bus->id_estado = 2; // Inactivo
                $bus->save();
            }
        }

        return redirect()->back()->with('success', 'Documento RECHAZADO. El vehículo ha sido INACTIVADO.');
    }
}
