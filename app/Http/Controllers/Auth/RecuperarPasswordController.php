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
use Illuminate\Support\Facades\DB;
use App\Mail\RecuperacionMailable;
use Carbon\Carbon;

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
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El formato del correo no es válido.'
        ]);

        $correo = $request->email;

        // Verificar existencia en Usuario o SuperAdministrador
        $usuario = Usuario::where('correo', $correo)->first();
        $superAdmin = SuperAdministrador::where('correo', $correo)->first();

        if (!$usuario && !$superAdmin) {
            return back()->withErrors(['email' => 'El correo no se encuentra registrado en el sistema.']);
        }

        // Generar código aleatorio de 6 dígitos
        $codigo = (string) random_int(100000, 999999);

        // Almacenar en la tabla de recuperación (invalidando el anterior)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $correo],
            [
                'token' => $codigo,
                'created_at' => Carbon::now()
            ]
        );

        // Enviar correo con Mailable profesional
        try {
            Mail::to($correo)->send(new RecuperacionMailable($codigo));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Error al enviar el correo: ' . $e->getMessage()]);
        }

        session(['correo' => $correo]);

        return redirect()->route('password.codigo.form')
            ->with('success', 'Se ha enviado un código de 6 dígitos al correo ' . $correo);
    }


    public function verificarCodigo(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'codigo' => 'required|size:6'
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->first();

        if (!$tokenRecord || $tokenRecord->token != $request->codigo) {
            return back()
                ->withErrors('Código inválido o expirado.')
                ->with('correo', $request->correo);
        }

        // Verificar si expiró (ejm: 10 minutos)
        if (Carbon::parse($tokenRecord->created_at)->addMinutes(10)->isPast()) {
            return back()
                ->withErrors('El código ha expirado, solicita uno nuevo.')
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

        // Limpiar token una vez usado
        DB::table('password_reset_tokens')->where('email', $correo)->delete();

        return redirect()->route('login')
            ->with('success', 'Tu contraseña ha sido actualizada correctamente. Inicia sesión con tus nuevas credenciales.');
                    
                }

    public function reenviarCodigo(Request $request)
        {
            $correo = session('correo');

            if (!$correo) {
                return redirect()->route('recuperar')
                    ->withErrors('Sesión expirada. Inicia nuevamente el proceso.');
            }

        $codigo = (string) random_int(100000, 999999);

        // Actualizar el token anterior
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $correo],
            [
                'token' => $codigo,
                'created_at' => Carbon::now()
            ]
        );

        try {
            Mail::to($correo)->send(new RecuperacionMailable($codigo));
        } catch (\Exception $e) {
            return back()->withErrors('Error al enviar el nuevo código: ' . $e->getMessage());
        }

        return back()->with('success', 'Se ha enviado un nuevo código a tu correo.');
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
