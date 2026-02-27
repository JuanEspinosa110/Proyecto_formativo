<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Str;
use App\Models\SuperAdministrador;

class RecuperarPasswordController extends Controller
{

    public function index()
    {
        return view('auth.recuperar');
    }

    public function enviarCodigo(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $correo = $request->email;

    $usuario = Usuario::where('correo', $request->email)->first();
    $superAdmin = SuperAdministrador::where('correo', $request->email)->first();

    if (!$usuario && !$superAdmin) {
    return back()->withErrors('El correo no se encuentra registrado en el sistema.');
}

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

    session(['correo' => $request->email]);

    return redirect()->route('password.codigo.form')
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
                return back()
                    ->withErrors('Código inválido o expirado.')
                    ->with('correo', $request->correo);
                }

            session(['correo' => $request->correo]);
            return redirect()->route('password.nueva.form');


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
                ],
                [
                    'correo.required' => 'El correo es obligatorio.',
                    'correo.email' => 'El correo debe ser una direccion valida.',

                    'password.required' => 'La contraseña es obligatoria.',
                    'password.confirmed'=> 'Las contraseñas no coinciden.',
                    'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                    'password.mixed' => 'La contraseña debe contener mayusculas y minusculas.',
                    'password.numbers' => 'La contraseña debe contener al menos un numero',
                    'password.symbols' => 'La contraseña debe contener al menos un simbolo',
                ]
                );

                $correo = $request->correo;

                $usuario = Usuario::where('correo', $correo)->first();

                if ($usuario) {
                    $usuario->update([
                        'password' => Hash::make($request->password)
                    ]);
                } else {
                    $superadmin = SuperAdministrador::where('correo', $correo)->first();

                    if ($superadmin) {
                        $superadmin->update([
                            'password' => Hash::make($request->password)
                        ]);
                    } else {
                         return redirect()->route('recuperar')
                            ->withErrors('No se encontró un usuario asociado a ese correo.');
                    }
                }

                Cache::forget('recuperacion_'.$correo);

                return redirect()->route('login')
                    ->with('success', 'Contraseña actualizada correctamente');
                    
                }

    public function reenviarCodigo(Request $request)
        {
            $correo = session('correo');

            if (!$correo) {
                return redirect()->route('recuperar')
                    ->withErrors('Sesión expirada. Inicia nuevamente el proceso.');
            }

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



        public function mostrarNuevaPassword(Request $request)
        {
            if (!session()->has('correo')) {
                return redirect()->route('recuperar')
                    ->withErrors(['error' => 'Sesión inválida, inicia nuevamente el proceso.']);
            }

            return view('auth.nueva_password', [
                'correo' => session('correo')
            ]);
        }




}
