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
            ->whereIn('id_tipo_usuario', [1, 3, 4, 5, 7]) // Solo roles de empresa
            ->orderBy('id_tipo_usuario')
            ->get();
        $estados = DB::table('estado')->whereIn('id_estado', [1, 2, 3])->get();

        $query = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->leftJoin('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->select('usuario.*', 'estado.nombre_estado', 'ciudad.nombre_city', 'tipo_usuario.nombre_tipo');

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

        $usuarios = $query->orderBy('usuario.doc_usuario', 'ASC')->paginate(5)->withQueryString();

        $docs_licencia = \App\Models\Documento::whereIn('doc_usuario', collect($usuarios->items())->pluck('doc_usuario'))
            ->where('id_tipo_documento', 3)
            ->where('id_estado', 1)
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

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'selectedRole', 'estados', 'docs_licencia', 'licenciasAlerta'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            // Validaciones adicionales para CONDUCTOR
            $tipoUsuario = DB::table('tipo_usuario')->where('id_tipo_usuario', $request->id_tipo_usuario)->first();
            $esConductor = $tipoUsuario && stripos($tipoUsuario->nombre_tipo, 'conductor') !== false;

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

            Usuario::create($data);

            // Si es conductor, crear el documento
            if ($esConductor && $request->hasFile('archivo_licencia')) {
                // Cálculo de vigencia en Backend según normas de Colombia
                $fecha_nac = \Carbon\Carbon::parse($request->fecha_nacimiento);
                $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
                $edad = $fecha_exp->diffInYears($fecha_nac);

                if ($edad < 60) {
                    $fecha_venc = $fecha_exp->copy()->addYears(3);
                } else {
                    $fecha_venc = $fecha_exp->copy()->addYear();
                }

                $pathLicencia = $request->file('archivo_licencia')->store('documentos', 'public');
                \App\Models\Documento::create([
                    'nombre' => 'LICENCIA CONDUCCION',
                    'archivo' => $pathLicencia,
                    'fecha_expedicion' => $request->fecha_expedicion,
                    'fecha_vencimiento' => $fecha_venc->format('Y-m-d'),
                    'id_tipo_documento' => 3, // ID de la licencia
                    'doc_usuario' => $request->doc_usuario,
                    'NIT' => Auth::user()->NIT,
                    'id_estado' => 1
                ]);
            }

            return redirect()
                ->route('admin.usuarios.index')
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
                $docData['archivo'] = $request->file('archivo_licencia')->store('documentos', 'public');
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
}
