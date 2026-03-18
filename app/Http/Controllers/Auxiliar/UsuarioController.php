<?php

namespace App\Http\Controllers\Auxiliar;

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
     * Mostrar lista de usuarios (solo Conductor y Propietario)
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $nit = $user->NIT ?? null;

        if (!$nit) {
            return redirect()->route('empresa.dashboard')->with('error', 'Empresa no asociada a este usuario.');
        }

        // 1. Filtrar roles: Solo Conductor y Propietario
        $roles = DB::table('tipo_usuario')
            ->orderBy('id_tipo_usuario')
            ->get()
            ->filter(function($r) {
                $nombre = strtolower($r->nombre_tipo);
                return str_contains($nombre, 'conductor') || str_contains($nombre, 'propietario');
            });

        $estados = DB::table('estado')->whereIn('id_estado', [1,2,3])->get();

        // 2. Filtrar query: Solo Conductor y Propietario
        $query = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->leftJoin('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->where(function($q) {
                $q->where('tipo_usuario.nombre_tipo', 'like', '%conductor%')
                  ->orWhere('tipo_usuario.nombre_tipo', 'like', '%propietario%');
            })
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

        return view('auxiliar.usuarios.index', compact('usuarios', 'roles', 'selectedRole', 'estados', 'docs_licencia'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            // Validar que el rol sea conductor o propietario
            $tipoAsignado = DB::table('tipo_usuario')->where('id_tipo_usuario', $request->id_tipo_usuario)->first();
            $nombreAsignado = strtolower($tipoAsignado->nombre_tipo ?? '');
            
            if (!str_contains($nombreAsignado, 'conductor') && !str_contains($nombreAsignado, 'propietario')) {
                return back()->withInput()->with('error', 'No tienes permisos para asignar este rol.');
            }

            $esConductor = stripos($nombreAsignado, 'conductor') !== false;

            if ($esConductor) {
                $request->validate([
                    'fecha_nacimiento' => 'required|date',
                    'fecha_expedicion' => 'required|date',
                    'fecha_vencimiento' => 'required|date',
                    'archivo_licencia' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048'
                ]);
            }

            $passwordGenerada = $request->filled('password') ? $request->password : Str::random(10);

            $data = [
                'primer_nombre'   => $request->primer_nombre,
                'segundo_nombre'  => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido'=> $request->segundo_apellido,
                'doc_usuario'     => $request->doc_usuario,
                'correo'          => $request->correo,
                'telefono'        => $request->telefono,
                'id_tipo_usuario' => $request->id_tipo_usuario,
                'id_estado'       => 1,
                'password'        => Hash::make($passwordGenerada),
                'NIT'             => Auth::user()->NIT
            ];

            if ($request->filled('fecha_nacimiento')) {
                $data['fecha_nacimiento'] = $request->fecha_nacimiento;
            }

            if ($request->hasFile('foto_usuario')) {
                $data['foto_usuario'] = $request->file('foto_usuario')->store('usuarios', 'public');
            }

            Usuario::create($data);

            if ($esConductor && $request->hasFile('archivo_licencia')) {
                $fecha_nac = \Carbon\Carbon::parse($request->fecha_nacimiento);
                $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
                $edad = $fecha_exp->diffInYears($fecha_nac);
                $fecha_venc = ($edad < 60) ? $fecha_exp->copy()->addYears(3) : $fecha_exp->copy()->addYear();

                \App\Models\Documento::create([
                    'nombre' => 'LICENCIA CONDUCCION',
                    'archivo' => $request->file('archivo_licencia')->store('documentos', 'public'),
                    'fecha_expedicion' => $request->fecha_expedicion,
                    'fecha_vencimiento' => $fecha_venc->format('Y-m-d'),
                    'id_tipo_documento' => 3,
                    'doc_usuario' => $request->doc_usuario,
                    'NIT' => Auth::user()->NIT,
                    'id_estado' => 1
                ]);
            }

            return redirect()->route('auxiliar.usuarios.index')->with('success', 'Registro creado correctamente. Contraseña: ' . $passwordGenerada);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $doc_usuario)
    {
        $tipoAsignado = DB::table('tipo_usuario')->where('id_tipo_usuario', $request->id_tipo_usuario)->first();
        $nombreAsignado = strtolower($tipoAsignado->nombre_tipo ?? '');
        
        if (!str_contains($nombreAsignado, 'conductor') && !str_contains($nombreAsignado, 'propietario')) {
            return back()->withInput()->with('error', 'No tienes permisos para asignar este rol.');
        }

        $request->validate([
            'doc_usuario' => 'required|numeric|regex:/^[1-9][0-9]{5,9}$/',
            'correo' => 'required|email|max:150',
            'telefono' => 'required|numeric|digits:10',
            'id_tipo_usuario' => 'required|integer|exists:tipo_usuario,id_tipo_usuario',
            'id_estado' => 'required|integer|in:1,2,3',
        ]);

        $esConductor = stripos($nombreAsignado, 'conductor') !== false;

        $data = $request->except(['_token', '_method', 'foto_usuario', 'form_type', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'fecha_expedicion', 'fecha_vencimiento', 'archivo_licencia']);
        
        $userToUpdate = Usuario::find($doc_usuario);

        if ($request->hasFile('foto_usuario')) {
            if ($userToUpdate && $userToUpdate->foto_usuario) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($userToUpdate->foto_usuario);
            }
            $data['foto_usuario'] = $request->file('foto_usuario')->store('usuarios', 'public');
        }

        Usuario::where('doc_usuario', $doc_usuario)->update($data);

        if ($esConductor && $request->filled('fecha_expedicion')) {
            $fecha_nac = \Carbon\Carbon::parse($userToUpdate->fecha_nacimiento);
            $fecha_exp = \Carbon\Carbon::parse($request->fecha_expedicion);
            $edad = $fecha_exp->diffInYears($fecha_nac);
            $fecha_venc = ($edad < 60) ? $fecha_exp->copy()->addYears(3) : $fecha_exp->copy()->addYear();

            $docLicencia = \App\Models\Documento::where('doc_usuario', $doc_usuario)->where('id_tipo_documento', 3)->where('id_estado', 1)->first();

            $docData = ['fecha_expedicion' => $request->fecha_expedicion, 'fecha_vencimiento' => $fecha_venc->format('Y-m-d'), 'NIT' => Auth::user()->NIT];

            if ($request->hasFile('archivo_licencia')) {
                if ($docLicencia && $docLicencia->archivo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($docLicencia->archivo);
                }
                $docData['archivo'] = $request->file('archivo_licencia')->store('documentos', 'public');
            }

            if ($docLicencia) {
                $docLicencia->update($docData);
            } else if ($request->hasFile('archivo_licencia')) {
                $docData['nombre'] = 'LICENCIA CONDUCCION';
                $docData['id_tipo_documento'] = 3;
                $docData['doc_usuario'] = $doc_usuario;
                $docData['id_estado'] = 1;
                \App\Models\Documento::create($docData);
            }
        }

        return redirect()->route('auxiliar.usuarios.index')->with('success', 'Registro actualizado correctamente');
    }
}
