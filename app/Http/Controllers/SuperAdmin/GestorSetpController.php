<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Ciudad;

/**
 * Controlador SuperAdmin → Gestores SETP
 * Gestiona la creación y edición de usuarios con rol Gestor SETP (id_tipo_usuario = 11),
 * asignados a empresas de tipo Setp (id_tipo_empresa = 5).
 */
class GestorSetpController extends Controller
{
    // id del tipo de empresa Setp
    const ID_TIPO_EMPRESA_SETP  = 5;
    // id del tipo de usuario Gestor Setp
    const ID_TIPO_USUARIO_GESTOR = 6;

    // ── index ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Usuario::where('id_tipo_usuario', self::ID_TIPO_USUARIO_GESTOR)
                        ->with(['empresa', 'ciudad']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('primer_nombre',    'like', "%{$q}%")
                   ->orWhere('primer_apellido','like', "%{$q}%")
                   ->orWhere('doc_usuario',     $q);
            });
        }
        if ($request->filled('nit'))    $query->where('NIT', $request->nit);
        if ($request->filled('estado')) $query->where('id_estado', $request->estado);

        $gestores      = $query->latest('doc_usuario')->paginate(15);
        $empresasSetp  = Empresa::where('id_tipo_empresa', self::ID_TIPO_EMPRESA_SETP)
                                ->where('id_estado', 1)->get();

        return view('superadmin.gestor-setp.index', compact('gestores', 'empresasSetp'));
    }

    // ── create ────────────────────────────────────────────────────
    public function create()
    {
        $empresasSetp = Empresa::where('id_tipo_empresa', self::ID_TIPO_EMPRESA_SETP)
                               ->where('id_estado', 1)->with('ciudad')->get();
        $ciudades     = Ciudad::orderBy('nombre_city')->get();

        return view('superadmin.gestor-setp.create', compact('empresasSetp', 'ciudades'));
    }

    // ── store ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'doc_usuario'      => 'required|integer|min:1000000|unique:usuario,doc_usuario',
            'NIT'              => 'required|exists:empresa,NIT',
            'primer_nombre'    => 'required|string|max:50',
            'segundo_nombre'   => 'nullable|string|max:50',
            'primer_apellido'  => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'correo'           => 'required|email|max:150|unique:usuario,correo',
            'telefono'         => 'nullable|string|max:20',
            'id_ciudad'        => 'required|exists:ciudad,id_ciudad',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'doc_usuario.required'   => 'El número de documento es obligatorio.',
            'doc_usuario.unique'     => 'Ya existe un usuario con ese documento.',
            'NIT.required'           => 'Debe seleccionar una empresa SETP.',
            'NIT.exists'             => 'La empresa seleccionada no existe.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.required'        => 'El correo electrónico es obligatorio.',
            'correo.unique'          => 'Ya existe un usuario con ese correo.',
            'id_ciudad.required'     => 'Debe seleccionar una ciudad.',
            'password.required'      => 'La contraseña es obligatoria.',
            'password.min'           => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'     => 'Las contraseñas no coinciden.',
        ]);

        // Verificar que la empresa sea de tipo Setp
        $empresa = Empresa::findOrFail($data['NIT']);
        if ($empresa->id_tipo_empresa !== self::ID_TIPO_EMPRESA_SETP) {
            return back()->withErrors(['NIT' => 'La empresa seleccionada no es de tipo Setp.'])->withInput();
        }

        Usuario::create([
            'doc_usuario'      => $data['doc_usuario'],
            'NIT'              => $data['NIT'],
            'primer_nombre'    => $data['primer_nombre'],
            'segundo_nombre'   => $data['segundo_nombre'] ?? null,
            'primer_apellido'  => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] ?? null,
            'correo'           => $data['correo'],
            'telefono'         => $data['telefono'] ?? null,
            'id_ciudad'        => $data['id_ciudad'],
            'id_tipo_usuario'  => self::ID_TIPO_USUARIO_GESTOR,
            'id_estado'        => 1,
            'password'         => Hash::make($data['password']),
        ]);

        return redirect()->route('superadmin.gestores-setp.index')
                         ->with('success', 'Gestor SETP creado exitosamente.');
    }

    // ── edit ──────────────────────────────────────────────────────
    public function edit($doc)
    {
        $gestor = Usuario::where('doc_usuario', $doc)
                         ->where('id_tipo_usuario', self::ID_TIPO_USUARIO_GESTOR)
                         ->firstOrFail();

        $empresasSetp = Empresa::where('id_tipo_empresa', self::ID_TIPO_EMPRESA_SETP)
                               ->where('id_estado', 1)->with('ciudad')->get();
        $ciudades     = Ciudad::orderBy('nombre_city')->get();

        return view('superadmin.gestor-setp.edit', compact('gestor', 'empresasSetp', 'ciudades'));
    }

    // ── update ────────────────────────────────────────────────────
    public function update(Request $request, $doc)
    {
        $gestor = Usuario::where('doc_usuario', $doc)
                         ->where('id_tipo_usuario', self::ID_TIPO_USUARIO_GESTOR)
                         ->firstOrFail();

        $rules = [
            'NIT'              => 'required|exists:empresa,NIT',
            'primer_nombre'    => 'required|string|max:50',
            'segundo_nombre'   => 'nullable|string|max:50',
            'primer_apellido'  => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'correo'           => "required|email|max:150|unique:usuario,correo,{$doc},doc_usuario",
            'telefono'         => 'nullable|string|max:20',
            'id_ciudad'        => 'required|exists:ciudad,id_ciudad',
            'id_estado'        => 'required|in:1,2',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $messages = [
            'NIT.required'           => 'Debe seleccionar una empresa SETP.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.unique'          => 'Ya existe otro usuario con ese correo.',
            'id_ciudad.required'     => 'Debe seleccionar una ciudad.',
            'password.min'           => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'     => 'Las contraseñas no coinciden.',
        ];

        $data = $request->validate($rules, $messages);

        $updateData = [
            'NIT'              => $data['NIT'],
            'primer_nombre'    => $data['primer_nombre'],
            'segundo_nombre'   => $data['segundo_nombre'] ?? null,
            'primer_apellido'  => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] ?? null,
            'correo'           => $data['correo'],
            'telefono'         => $data['telefono'] ?? null,
            'id_ciudad'        => $data['id_ciudad'],
            'id_estado'        => $data['id_estado'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $gestor->update($updateData);

        return redirect()->route('superadmin.gestores-setp.index')
                         ->with('success', 'Gestor SETP actualizado correctamente.');
    }

    // ── toggleEstado ──────────────────────────────────────────────
    public function toggleEstado($doc)
    {
        $gestor = Usuario::where('doc_usuario', $doc)
                         ->where('id_tipo_usuario', self::ID_TIPO_USUARIO_GESTOR)
                         ->firstOrFail();

        $gestor->update([
            'id_estado' => $gestor->id_estado == 1 ? 2 : 1,
        ]);

        $accion = $gestor->id_estado == 1 ? 'activado' : 'inactivado';
        return back()->with('success', "Gestor SETP {$accion} exitosamente.");
    }

    // ── destroy (opcional) ────────────────────────────────────────
    public function destroy($doc)
    {
        $gestor = Usuario::where('doc_usuario', $doc)
                         ->where('id_tipo_usuario', self::ID_TIPO_USUARIO_GESTOR)
                         ->firstOrFail();

        $gestor->delete();

        return redirect()->route('superadmin.gestores-setp.index')
                         ->with('success', 'Gestor SETP eliminado del sistema.');
    }
}
