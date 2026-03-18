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

        $selectedRole = $request->query('role');
        if ($selectedRole) {
            $query->where('usuario.id_tipo_usuario', $selectedRole);
        }

        $usuarios = $query->orderBy('usuario.primer_nombre')->paginate(5)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'selectedRole', 'estados'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            $passwordGenerada = Str::random(10);

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

            if ($request->hasFile('foto_usuario')) {
                $path = $request->file('foto_usuario')->store('usuarios', 'public');
                $data['foto_usuario'] = $path;
            }

            Usuario::create($data);

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
                'regex:/^[1-9][0-9]{8,11}$/'
            ],
            'primer_nombre' => 'required|string|min:2|regex:/^[\pL\s]+$/u',
            'segundo_nombre' => 'nullable|string|min:2|regex:/^[\pL\s]+$/u',
            'primer_apellido' => 'required|string|min:2|regex:/^[\pL\s]+$/u',
            'segundo_apellido' => 'required|string|min:2|regex:/^[\pL\s]+$/u',
            'correo' => 'required|email|max:150',
            'telefono' => 'required|numeric|digits:10',
            'id_tipo_usuario' => 'required|integer|exists:tipo_usuario,id_tipo_usuario',
            'id_estado' => 'required|integer|in:1,2,3',
        ], [
            'doc_usuario.required' => 'El documento es obligatorio.',
            'doc_usuario.numeric' => 'El documento solo puede contener números.',
            'doc_usuario.regex' => 'El documento debe tener mínimo 9 dígitos y no puede iniciar con 0.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.min' => 'El primer nombre debe tener mínimo 2 caracteres.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.min' => 'El primer apellido debe tener mínimo 2 caracteres.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras.',
            'segundo_apellido.required' => 'El segundo apellido es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            'id_tipo_usuario.required' => 'Debe seleccionar un rol.',
            'id_estado.required' => 'Debe seleccionar un estado.',
        ]);

        $data = $request->except(['_token', '_method', 'foto_usuario', 'form_type']);

        if ($request->hasFile('foto_usuario')) {
            // Opcional: Eliminar foto anterior si existe
            $userToUpdate = Usuario::find($doc_usuario);
            if ($userToUpdate && $userToUpdate->foto_usuario) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($userToUpdate->foto_usuario);
            }

            $path = $request->file('foto_usuario')->store('usuarios', 'public');
            $data['foto_usuario'] = $path;
        }

        Usuario::where('doc_usuario', $doc_usuario)->update($data);

        // Si el usuario editado es el que está en sesión y se inactiva o bloquea, cerrar sesión
        $usuarioEditado = Auth::user();
        if ($usuarioEditado && $usuarioEditado->doc_usuario == $doc_usuario && in_array($request->id_estado, [2, 3])) {
            Auth::logout();
            return redirect('/login')->with('error', 'Tu cuenta ha sido inactivada o bloqueada.');
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Registro actualizado correctamente');
    }
}
