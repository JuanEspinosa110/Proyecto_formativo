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

        $roles = DB::table('tipo_usuario')->orderBy('id_tipo_usuario')->get();
        $estados = DB::table('estado')->whereIn('id_estado', [1,2,3])->get();

        $query = DB::table('usuario')
            ->leftJoin('estado', 'usuario.id_estado', '=', 'estado.id_estado')
            ->leftJoin('ciudad', 'usuario.id_ciudad', '=', 'ciudad.id_ciudad')
            ->leftJoin('tipo_usuario', 'usuario.id_tipo_usuario', '=', 'tipo_usuario.id_tipo_usuario')
            ->where('usuario.NIT', $nit)
            ->select('usuario.*', 'estado.nombre_estado', 'ciudad.nombre_city', 'tipo_usuario.nombre_tipo');

        $selectedRole = $request->query('role');
        if ($selectedRole) {
            $query->where('usuario.id_tipo_usuario', $selectedRole);
        }

        $usuarios = $query->orderBy('usuario.primer_nombre')->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'selectedRole', 'estados'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $passwordGenerada = Str::random(10);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'doc_usuario' => $request->doc_usuario,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'estado' => $request->estado,
            'password' => Hash::make($passwordGenerada)
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('password_generada', $passwordGenerada, 'success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $doc_usuario)
    {
        $request->validate([
            'doc_usuario' => [
                'required',
                'digits_between:7,10',
                'regex:/^[1-9][0-9]*$/'
            ],
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'correo' => 'required|email|max:150',
            'telefono' => 'required|string|size:10',
            'id_tipo_usuario' => 'required|integer|exists:tipo_usuario,id_tipo_usuario',
            'id_estado' => 'required|integer|in:1,2,3',
        ], [
            'doc_usuario.required' => 'El documento es obligatorio.',
            'doc_usuario.digits_between' => 'El documento debe tener entre 7 y 10 dígitos.',
            'doc_usuario.regex' => 'El documento no puede iniciar con 0.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'id_tipo_usuario.required' => 'Debe seleccionar un rol.',
            'id_estado.required' => 'Debe seleccionar un estado.',
        ]);

        Usuario::where('doc_usuario', $doc_usuario)->update($request->except(['_token', '_method']));

        // Si el usuario editado es el que está en sesión y se inactiva o bloquea, cerrar sesión
        $usuarioEditado = Auth::user();
        if ($usuarioEditado && $usuarioEditado->doc_usuario == $doc_usuario && in_array($request->id_estado, [2,3])) {
            Auth::logout();
            return redirect('/login')->with('error', 'Tu cuenta ha sido inactivada o bloqueada.');
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }
}
