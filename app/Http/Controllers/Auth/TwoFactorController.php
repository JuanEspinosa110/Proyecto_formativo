<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TwoFactorCode;
use App\Models\Usuario;
use App\Models\SuperAdministrador;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    /**
     * Muestra el formulario para ingresar el código 2FA.
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('2fa_pending')) {
            return redirect()->route('login');
        }

        return view('auth.2fa');
    }

    /**
     * Procesa la verificación del código ingresado.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        if (!$request->session()->has('2fa_pending')) {
            return redirect()->route('login');
        }

        $pending = $request->session()->get('2fa_pending');
        
        $twoFactor = TwoFactorCode::where('documento', $pending['documento'])
            ->where('tipo_usuario', $pending['tipo_usuario'])
            ->where('codigo', $request->codigo)
            ->first();

        // Validar código
        if (!$twoFactor || $twoFactor->isExpired()) {
            return back()->with('error', 'El código de verificación es incorrecto o ha expirado.');
        }

        // Eliminar códigos usados
        TwoFactorCode::where('documento', $pending['documento'])->delete();

        // Iniciar sesión y limpiar variable de sesión pending
        $request->session()->forget('2fa_pending');
        $request->session()->regenerate();

        // Opcional: Recordar dispositivo por 1 día
        if ($request->has('remember_device')) {
            $cookieName = '2fa_device_' . $pending['tipo_usuario'] . '_' . $pending['documento'];
            cookie()->queue($cookieName, 'trusted', 60 * 24); // 1 día (minutos)
        }

        if ($pending['tipo_usuario'] == 'superadmin') {
            Auth::guard('superadmin')->loginUsingId($pending['documento']);
            return redirect()->route('superadmin.dashboard');
        } else {
            // Es admin
            Auth::guard('web')->loginUsingId($pending['documento']);
            return redirect()->route('admin.dashboard');
        }
    }

    /**
     * Reenviar código
     */
    public function resend(Request $request)
    {
        if (!$request->session()->has('2fa_pending')) {
            return redirect()->route('login');
        }

        $pending = $request->session()->get('2fa_pending');
        
        // Obtener la info del usuario
        if ($pending['tipo_usuario'] == 'superadmin') {
            $user = SuperAdministrador::find($pending['documento']);
            $nombre = $user->nombre;
            $correo = $user->correo;
        } else {
            $user = Usuario::find($pending['documento']);
            $nombre = $user->primer_nombre . ' ' . $user->primer_apellido;
            $correo = $user->correo;
        }

        $this->generateAndSendCode($pending['documento'], $pending['tipo_usuario'], $correo, $nombre);

        return back()->with('status', 'Un nuevo código ha sido enviado a tu correo electrónico.');
    }

    /**
     * Genera un nuevo código 2FA y lo envía por correo.
     */
    public function generateAndSendCode($documento, $tipoUsuario, $correo, $nombre)
    {
        // Limpiar códigos anteriores de este usuario
        TwoFactorCode::where('documento', $documento)
            ->where('tipo_usuario', $tipoUsuario)
            ->delete();

        // Generar código aleatorio de 6 dígitos
        $codigo = sprintf('%06d', mt_rand(100000, 999999));

        // Guardar en la base de datos
        TwoFactorCode::create([
            'documento' => $documento,
            'tipo_usuario' => $tipoUsuario,
            'codigo' => $codigo,
            'expires_at' => now()->addMinutes(10), // Expira en 10 minutos
        ]);

        // Enviar correo
        Mail::to($correo)->send(new TwoFactorCodeMail($codigo, $nombre));
    }
}
