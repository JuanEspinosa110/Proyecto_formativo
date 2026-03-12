<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    // ── edit ──────────────────────────────────────────────────
    public function edit()
    {
        return view('pasajero.perfil.edit', ['user' => auth()->user()]);
    }

    // ── update ────────────────────────────────────────────────
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'primer_nombre'    => 'required|string|max:50',
            'segundo_nombre'   => 'nullable|string|max:50',
            'primer_apellido'  => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'required|email|max:150|unique:usuario,correo,' . $user->doc_usuario . ',doc_usuario',
        ], [
            'primer_nombre.required'   => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'correo.required'          => 'El correo es obligatorio.',
            'correo.email'             => 'Ingresa un correo válido.',
            'correo.unique'            => 'Este correo ya está registrado por otro usuario.',
        ]);

        Usuario::where('doc_usuario', $user->doc_usuario)->update($data);

        return back()->with('success', 'Información actualizada correctamente.');
    }

    // ── foto ──────────────────────────────────────────────────
    public function foto(Request $request)
    {
        $request->validate([
            'foto_usuario' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_usuario.required' => 'Selecciona una imagen.',
            'foto_usuario.image'    => 'El archivo debe ser una imagen.',
            'foto_usuario.mimes'    => 'La imagen debe ser JPG, PNG o WebP.',
            'foto_usuario.max'      => 'La imagen no puede superar los 2 MB.',
        ]);

        $user = auth()->user();

        // Eliminar foto anterior
        if ($user->foto_usuario && Storage::disk('public')->exists($user->foto_usuario)) {
            Storage::disk('public')->delete($user->foto_usuario);
        }

        $path = $request->file('foto_usuario')->store('fotos/pasajeros', 'public');

        Usuario::where('doc_usuario', $user->doc_usuario)
               ->update(['foto_usuario' => $path]);

        return back()->with('success', 'Foto de perfil actualizada.');
    }

    // ── password ──────────────────────────────────────────────
    public function password(Request $request)
    {
        $request->validate([
            'password_actual'  => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
            'password.required'        => 'La nueva contraseña es obligatoria.',
            'password.min'             => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->password_actual, $user->password)) {
            return back()
                ->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])
                ->withInput();
        }

        Usuario::where('doc_usuario', $user->doc_usuario)
               ->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
