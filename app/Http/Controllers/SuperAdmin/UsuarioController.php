<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        $usuarios = User::paginate(12);

        return view('superadmin.usuarios.index', compact('usuarios'));
    }

    // =========================
    // PERFIL COMPLETO
    // =========================
    public function show($doc)
    {
        $user = User::findOrFail($doc);

        return view('superadmin.usuarios.show', compact('user'));
    }

    // =========================
    // CAMBIAR PASSWORD
    // =========================
    public function updatePassword(Request $request, $doc)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::findOrFail($doc);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente');
    }

    // =========================
    // DOCUMENTOS
    // =========================
    public function documentos($doc)
    {
        $user = User::findOrFail($doc);

        return view('superadmin.usuarios.documentos', compact('user'));
    }

    // =========================
    // AFILIACIONES
    // =========================
    public function afiliaciones($doc)
    {
        $user = User::findOrFail($doc);

        return view('superadmin.usuarios.afiliaciones', compact('user'));
    }

    // =========================
    // BUSES (PROPIETARIO)
    // =========================
    public function buses($doc)
    {
        $user = User::findOrFail($doc);

        return view('superadmin.usuarios.buses', compact('user'));
    }

    // =========================
    // ASIGNACIONES (CONDUCTOR)
    // =========================
    public function asignaciones($doc)
    {
        $user = User::findOrFail($doc);

        return view('superadmin.usuarios.asignaciones', compact('user'));
    }
}
