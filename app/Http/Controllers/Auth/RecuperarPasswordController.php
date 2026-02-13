<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Str;


class RecuperarPasswordController extends Controller
{

    public function index()
    {
        return view('auth.recuperar');
    }

    public function enviarCodigo(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:usuario,correo'
    ], [
        'email.exists' => 'El correo no se encuentra registrado en el sistema.'
    ]);

    $codigo = random_int(100000, 999999);

    Cache::put(
        'recuperacion_'.$request->email,
        $codigo,
        now()->addMinutes(10)
    );

    Mail::raw("Tu código de recuperación es: $codigo", function ($message) use ($request) {
        $message->to($request->email)
                ->subject('Código de recuperación');
    });

    return redirect()->route('password.codigo.form')
        ->with('correo', $request->email)
        ->with('success', 'Se envió un código al correo '.$request->email);

}


    public function verificarCodigo(Request $request)
        {
                $request->validate([
                    'correo' => 'required|email',
                    'codigo' => 'required'
                ]);

                $codigoGuardado = Cache::get('recuperacion_'.$request->correo);

                if (!$codigoGuardado || $codigoGuardado != $request->codigo) {
                    return back()->withErrors([
                        'codigo' => 'Código inválido o expirado'
                    ]);
            }

            return redirect()->route('password.nueva.form')
                ->with('correo', $request->correo);
        }

            public function actualizarPassword(Request $request)
                {
                    $request->validate([
                        'correo' => 'required|email',
                        'password' => [
                            'required',
                            'confirmed',
                            \Illuminate\Validation\Rules\Password::min(8)
                                ->mixedCase()
                                ->numbers()
                                ->symbols()
                        ]
                    ]);

                   $usuario = Usuario::where('correo', $request->correo)->first();

                    if (!$usuario) {
                        return redirect()->route('recuperar');
                    }

                    $usuario->update([
                        'password' => Hash::make($request->password)
                    ]);

                    Cache::forget('recuperacion_'.$request->correo);

                    return redirect()->route('login')
                        ->with('success', 'Contraseña actualizada correctamente.');
                }

    public function reenviarCodigo(Request $request)
        {
            $correo = $request->correo;

            $codigo = random_int(100000, 999999);

            Cache::put(
                'recuperacion_'.$correo,
                $codigo,
                now()->addMinutes(10)
            );

            Mail::raw("Tu nuevo código es: $codigo", function ($message) use ($correo) {
                $message->to($correo)
                        ->subject('Nuevo código de recuperación');
            });

            return back()->with('success', 'Nuevo código enviado');
        }
    



}
