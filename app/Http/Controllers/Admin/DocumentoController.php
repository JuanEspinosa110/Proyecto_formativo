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
            'id_estado' => 'required|exists:estado,id_estado|integer|in:1,20,21',
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
            $reglas['archivo'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
        } else {
            $reglas['archivo'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
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
            'archivo.mimes' => ' El archivo debe ser PDF, JPG, PNG, DOC o DOCX.',
            'archivo.max' => ' El archivo es demasiado grande. Máximo permitido: 5MB.',

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
    public function index()
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documentos = Documento::where('NIT', $empresa->NIT)
            ->with(['tipoDocumento', 'estado'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $tiposDocumento = TipoDocumento::where('id_estado', 1)->get();
        $estados = Estado::all();

        return view('admin.documentos.index', compact('documentos', 'tiposDocumento', 'estados', 'empresa'));
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
        $estados = Estado::whereIn('id_estado', [1, 20, 21])->get();

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

                $nombreArchivo = $this->generarNombreArchivoSeguro($archivo);
                $ruta = $archivo->storeAs(
                    'documentos/' . $empresa->NIT,
                    $nombreArchivo,
                    'public'
                );

                if (!$ruta) {
                    return redirect()->back()->with('error', '  Error al guardar el archivo. Intenta de nuevo.');
                }

                $validated['archivo'] = $ruta;
            }

            // Asignar NIT de la empresa
            $validated['NIT'] = $empresa->NIT;

            // Generar ID único
            $validated['id_documento'] = $this->generarIdDocumento();

            // Crear documento
            Documento::create($validated);

            return redirect()->route('admin.documentos.index')
                ->with('success', '  Documento creado exitosamente');
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
        $estados = Estado::whereIn('id_estado', [1, 20, 21])->get();

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
                if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                    Storage::disk('public')->delete($documento->archivo);
                }

                $nombreArchivo = $this->generarNombreArchivoSeguro($archivo);
                $ruta = $archivo->storeAs(
                    'documentos/' . $empresa->NIT,
                    $nombreArchivo,
                    'public'
                );

                if (!$ruta) {
                    return redirect()->back()->with('error', '  Error al guardar el archivo. Intenta de nuevo.');
                }

                $validated['archivo'] = $ruta;
            }

            // Actualizar documento
            $documento->update($validated);

            return redirect()->route('admin.documentos.index')
                ->with('success', '  Documento actualizado exitosamente');
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
            if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                Storage::disk('public')->delete($documento->archivo);
            }

            $documento->delete();

            return redirect()->route('admin.documentos.index')
                ->with('success', '  Documento eliminado exitosamente');
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

        if (!Storage::disk('public')->exists($documento->archivo)) {
            return redirect()->route('admin.documentos.index')
                ->with('error', '  El archivo no existe o fue eliminado');
        }

        try {
            return Storage::disk('public')->download(
                $documento->archivo,
                basename($documento->archivo)
            );
        } catch (\Exception $e) {
            Log::error('Error al descargar documento: ' . $e->getMessage());
            return redirect()->route('admin.documentos.index')
                ->with('error', '  Error al descargar el archivo');
        }
    }

    /**
     * Exportar documentos a CSV
     */
    public function export()
    {
        $empresa = $this->getEmpresaAdmin();

        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', '  No tiene empresa asignada');
        }

        $documentos = Documento::where('NIT', $empresa->NIT)
            ->with(['tipoDocumento', 'estado'])
            ->get();

        $filename = 'documentos_' . $empresa->NIT . '_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($documentos) {
            $file = fopen('php://output', 'w');

            // Headers del CSV
            fputcsv($file, [
                'ID Documento',
                'Nombre',
                'Tipo de Documento',
                'Fecha Expedición',
                'Fecha Vencimiento',
                'Estado',
                'Usuario',
                'Placa Bus',
                'Creado en'
            ]);

            // Datos
            foreach ($documentos as $doc) {
                fputcsv($file, [
                    $doc->id_documento,
                    $doc->nombre,
                    $doc->tipoDocumento->nombre ?? 'N/A',
                    $doc->fecha_expedicion,
                    $doc->fecha_vencimiento,
                    $doc->estado->nombre_estado ?? 'N/A',
                    $doc->doc_usuario ?? 'N/A',
                    $doc->placa ?? 'N/A',
                    $doc->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
                // Si tu tabla usuario tiene NIT (relación con empresa)
                if (isset($usuario->NIT) && $usuario->NIT != $empresa->NIT) {
                    throw ValidationException::withMessages([
                        'doc_usuario' => ' El usuario con documento "' . $datos['doc_usuario'] . '" no pertenece a su empresa.',
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
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($archivo->getMimeType(), $tiposPermitidos)) {
            return false;
        }

        // Validar extension
        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
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
}
