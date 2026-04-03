<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUsuarioRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Usuario;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevoUsuarioCreado;

class UsuarioController extends Controller
{
    /**
     * Mostrar lista de usuarios de la empresa del admin autenticado
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->NIT ?? null;

        if (!$nit) {
            return redirect()->route('admin.dashboard')->with('error', 'Empresa no asociada a este usuario.');
        }

        $roles = DB::table('tipo_usuario')
            ->whereIn('id_tipo_usuario', [1, 3, 4, 5, 7]) // Solo roles de empresa (Coordinador Bus 7)
            ->orderBy('id_tipo_usuario')
            ->when($user->id_tipo_usuario != 1, function ($q) {
                $q->where(function($sub) {
                    $sub->where('nombre_tipo', 'like', '%conductor%')
                        ->orWhere('nombre_tipo', 'like', '%propietario%');
                });
            })->get();
        $estados = DB::table('estado')->whereIn('id_estado', [1, 2, 3])->get();

        $query = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->leftJoin('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->where('usuario.id_tipo_usuario', '!=', 8) // El admin no debe manejar este rol
            ->select('usuario.*', 'estado.nombre_estado', 'ciudad.nombre_city', 'tipo_usuario.nombre_tipo');

        if ($user->id_tipo_usuario != 1) {
            $query->where(function($q) {
                $q->where('tipo_usuario.nombre_tipo', 'like', '%conductor%')
                  ->orWhere('tipo_usuario.nombre_tipo', 'like', '%propietario%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('usuario.doc_usuario', 'like', "%$search%")
                  ->orWhere('usuario.primer_nombre', 'like', "%$search%")
                  ->orWhere('usuario.primer_apellido', 'like', "%$search%")
                  ->orWhere('usuario.correo', 'like', "%$search%");
            });
        }

        $selectedRole = $request->query('role');
        if ($selectedRole) {
            $query->where('usuario.id_tipo_usuario', $selectedRole);
        }

        $usuarios = $query->orderBy('usuario.doc_usuario', 'ASC')->paginate(10)->withQueryString();

        $docs_licencia = \App\Models\Documento::whereIn('doc_usuario', collect($usuarios->items())->pluck('doc_usuario'))
            ->where('id_tipo_documento', 3)
            ->whereIn('id_estado', [1, 6])
            ->get()->keyBy('doc_usuario');

        // Alertas globales de licencias para el Admin
        $licenciasAlerta = \App\Models\Documento::where('NIT', $nit)
            ->where('id_tipo_documento', 3)
            ->where('id_estado', 1)
            ->with(['usuario'])
            ->get()
            ->filter(function($doc) {
                return $doc->estado_expiracion !== 'VIGENTE';
            });

        if ($request->ajax()) {
            return view('admin.usuarios.partials.table', compact('usuarios', 'estados', 'docs_licencia'));
        }

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'selectedRole', 'estados', 'docs_licencia', 'licenciasAlerta'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            // Validaciones adicionales para CONDUCTOR
            $tipoUsuario = DB::table('tipo_usuario')->where('id_tipo_usuario', $request->id_tipo_usuario)->first();
            $esConductor = $tipoUsuario && stripos($tipoUsuario->nombre_tipo, 'conductor') !== false;
            $esPropietario = $tipoUsuario && stripos($tipoUsuario->nombre_tipo, 'propietario') !== false;

            if (Auth::user()->id_tipo_usuario != 1) { 
                if (!$esConductor && !$esPropietario) {
                    return redirect()->back()->with('error', 'Solo tiene permisos para crear Conductores y Propietarios.');
                }
            }

            if ($esConductor) {
                $request->validate([
                    'fecha_nacimiento' => 'required|date',
                    'fecha_expedicion' => 'required|date',
                    'fecha_vencimiento' => 'required|date',
                    'archivo_licencia' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048'
                ], [
                    'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria para conductores.',
                    'fecha_expedicion.required' => 'La fecha de expedición es obligatoria para conductores.',
                    'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria para conductores.',
                    'archivo_licencia.required' => 'El archivo de la licencia es obligatorio para conductores.',
                ]);
            }

            // Si viene una contraseña en el request, se usa esa, sino se genera una aleatoria
            $passwordGenerada = $request->filled('password') ? $request->password : Str::random(10);

            // Usamos los nombres de campos que vienen del formulario
            $data = [
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'doc_usuario' => $request->doc_usuario,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'id_tipo_usuario' => $request->id_tipo_usuario,
                'id_estado' => 1,
                'password' => Hash::make($passwordGenerada),
                'NIT' => Auth::user()->NIT
            ];

            if ($request->filled('fecha_nacimiento')) {
                $data['fecha_nacimiento'] = $request->fecha_nacimiento;
            }

            if ($request->hasFile('foto_usuario')) {
                $path = $request->file('foto_usuario')->store('usuarios', 'public');
                $data['foto_usuario'] = $path;
            }

            // Validar licencia de conducción si es conductor
            if ($esConductor) {
                $fecha_nac = \Carbon\Carbon::parse($request->fecha_nacimiento);
                $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
                $edad = $fecha_exp->diffInYears($fecha_nac);
                $fecha_venc = ($edad < 60) ? $fecha_exp->copy()->addYears(3) : $fecha_exp->copy()->addYear();

                if ($fecha_venc->isPast()) {
                    $data['id_estado'] = 2; // INACTIVO
                    session()->flash('warning', 'El conductor tiene la licencia vencida, por lo tanto su estado se ha definido como INACTIVO.');
                }
            }

            Usuario::create($data);

            // Enviar correo de notificación al nuevo usuario
            try {
                Mail::to($request->correo)->send(new NuevoUsuarioCreado(
                    $request->primer_nombre . ' ' . $request->primer_apellido,
                    $request->doc_usuario,
                    $passwordGenerada,
                    Auth::user()->NIT
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
            }

            // Si es conductor, crear el documento
            if ($esConductor && $request->hasFile('archivo_licencia')) {
                // Cálculo de vigencia en Backend (Precalculado arriba)

                $pathLicencia = $request->file('archivo_licencia')->store('uploads/documentos', 'uploads');
                \App\Models\Documento::create([
                    'nombre' => 'LICENCIA CONDUCCION',
                    'archivo' => $pathLicencia,
                    'fecha_expedicion' => $request->fecha_expedicion,
                    'fecha_vencimiento' => $fecha_venc->format('Y-m-d'),
                    'id_tipo_documento' => 3, // ID de la licencia
                    'doc_usuario' => $request->doc_usuario,
                    'NIT' => Auth::user()->NIT,
                    'id_estado' => $fecha_venc->isPast() ? 6 : 1
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Registro creado correctamente. Contraseña: ' . $passwordGenerada);

        }
        catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $doc_usuario)
    {
        $request->validate([
            'doc_usuario' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{5,9}$/'
            ],
            'correo' => 'required|email|max:150',
            'telefono' => 'required|numeric|digits:10',
            'id_tipo_usuario' => 'required|integer|exists:tipo_usuario,id_tipo_usuario',
            'id_estado' => 'required|integer|in:1,2,3',
        ], [
            'doc_usuario.required' => 'El documento es obligatorio.',
            'doc_usuario.numeric' => 'El documento solo puede contener números.',
            'doc_usuario.regex' => 'El documento debe tener entre 6 y 10 dígitos y no puede iniciar en 0.',
            'correo.required' => 'El correo es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            'id_tipo_usuario.required' => 'Debe seleccionar un rol.',
            'id_estado.required' => 'Debe seleccionar un estado.',
        ]);

        $tipoUsuario = DB::table('tipo_usuario')->where('id_tipo_usuario', $request->id_tipo_usuario)->first();
        $esConductor = $tipoUsuario && stripos($tipoUsuario->nombre_tipo, 'conductor') !== false;

        if ($esConductor && $request->filled('fecha_expedicion')) {
            $request->validate([
                'fecha_expedicion' => 'required|date',
                'archivo_licencia' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048'
            ]);
        }

        // Los campos de nombre y apellidos no deben ser modificables
        $data = $request->except([
            '_token', '_method', 'foto_usuario', 'form_type',
            'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido',
            'fecha_expedicion', 'fecha_vencimiento', 'archivo_licencia'
        ]);

        $userToUpdate = Usuario::find($doc_usuario);

        if ($request->hasFile('foto_usuario')) {
            // Opcional: Eliminar foto anterior si existe
            if ($userToUpdate && $userToUpdate->foto_usuario) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($userToUpdate->foto_usuario);
            }

            $path = $request->file('foto_usuario')->store('usuarios', 'public');
            $data['foto_usuario'] = $path;
        }

        Usuario::where('doc_usuario', $doc_usuario)->update($data);

        // Actualizar o crear licencia
        if ($esConductor && $request->filled('fecha_expedicion')) {
            $fecha_nac = \Carbon\Carbon::parse($userToUpdate->fecha_nacimiento);
            $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
            $edad = $fecha_exp->diffInYears($fecha_nac);

            $fecha_venc = ($edad < 60) ? $fecha_exp->copy()->addYears(3) : $fecha_exp->copy()->addYear();

            $docLicencia = \App\Models\Documento::where('doc_usuario', $doc_usuario)
                ->where('id_tipo_documento', 3)
                ->where('id_estado', 1)
                ->first();

            $docData = [
                'fecha_expedicion' => $request->fecha_expedicion,
                'fecha_vencimiento' => $fecha_venc->format('Y-m-d'),
                'NIT' => Auth::user()->NIT
            ];

            if ($request->hasFile('archivo_licencia')) {
                if ($docLicencia && $docLicencia->archivo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($docLicencia->archivo);
                }
                $docData['archivo'] = $request->file('archivo_licencia')->store('uploads/documentos', 'uploads');
            }

            if ($docLicencia) {
                $docLicencia->update($docData);
            } else {
                if ($request->hasFile('archivo_licencia')) {
                    $docData['nombre'] = 'LICENCIA CONDUCCION';
                    $docData['id_tipo_documento'] = 3;
                    $docData['doc_usuario'] = $doc_usuario;
                    $docData['id_estado'] = 1;
                    \App\Models\Documento::create($docData);
                }
            }
        }

        // Si el usuario editado es el que está en sesión y se inactiva o bloquea, cerrar sesión
        $usuarioEditado = Auth::user();
        if ($usuarioEditado && $usuarioEditado->doc_usuario == $doc_usuario && in_array($request->id_estado, [2, 3])) {
            Auth::logout();
            return redirect('/login')->with('error', 'Tu cuenta ha sido inactivada o bloqueada.');
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Registro actualizado correctamente');
    }

    /**
     * Exportar Usuarios (Conductores/Propietarios) a Excel o PDF.
     */
    public function export(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->getActiveNit();
        $format = $request->query('format', 'excel');

        $usuarios = Usuario::with(['tipoUsuario', 'estado'])
            ->where('NIT', $nit)
            ->whereIn('id_tipo_usuario', [3, 4, 5, 7])
            ->orderBy('primer_nombre', 'asc')
            ->get();

        $empresa = \App\Models\Empresa::where('NIT', $nit)->first();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.usuarios.pdf', compact('usuarios', 'empresa'));
            return $pdf->download("Informe_Personal_{$nit}.pdf");
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Personal ' . ($empresa ? $empresa->nombre_empresa : 'Empresa'));

        $headers = ['Doc. Usuario', 'Primer Nombre', 'Segundo Nombre', 'Primer Apellido', 'Segundo Apellido', 'Correo', 'Teléfono', 'Rol', 'Estado'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . '1', $headers[$idx]);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
        }

        $row = 2;
        foreach ($usuarios as $u) {
            $sheet->setCellValue('A' . $row, $u->doc_usuario);
            $sheet->setCellValue('B' . $row, $u->primer_nombre);
            $sheet->setCellValue('C' . $row, $u->segundo_nombre);
            $sheet->setCellValue('D' . $row, $u->primer_apellido);
            $sheet->setCellValue('E' . $row, $u->segundo_apellido);
            $sheet->setCellValue('F' . $row, $u->correo);
            $sheet->setCellValue('G' . $row, $u->telefono);
            $sheet->setCellValue('H' . $row, $u->tipoUsuario->nombre_tipo ?? 'N/A');
            $sheet->setCellValue('I' . $row, $u->estado->nombre_estado ?? 'N/A');
            $row++;
        }

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Reporte_Personal_{$nit}_" . date('Ymd_His') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
