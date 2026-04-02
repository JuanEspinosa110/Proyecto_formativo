<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'doc_us' => 'required|integer',
            'password' => 'required',
        ]);

        /*
         |----------------------------------
         |  INTENTO USUARIOS (web) - ADMIN 2FA
         |----------------------------------
         */
        $user = \App\Models\Usuario::where('doc_usuario', $request->doc_us)->where('id_estado', 1)->first();
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            
            $cookieName = '2fa_device_admin_' . $user->doc_usuario;

            if ($user->id_tipo_usuario == 1 && !$request->hasCookie($cookieName)) { // ADMINISTRADOR requiere 2FA
                
                app(\App\Http\Controllers\Auth\TwoFactorController::class)->generateAndSendCode(
                    $user->doc_usuario, 
                    'admin', 
                    $user->correo, 
                    $user->primer_nombre . ' ' . $user->primer_apellido
                );
                
                $request->session()->put('2fa_pending', [
                    'documento' => $user->doc_usuario,
                    'tipo_usuario' => 'admin',
                ]);
                return redirect()->route('2fa.index');

            } else {
                // NORMAL LOGIN para otros roles o Admin de confianza
                Auth::guard('web')->login($user);
                $request->session()->regenerate();

                switch ($user->id_tipo_usuario) {
                    case 1: return redirect()->route('admin.dashboard');
                    case 2: return redirect()->route('pasajero.saldo');
                    case 3: return redirect()->route('conductor.dashboard');
                    case 4: return redirect()->route('empresa.dashboard', ['tab' => 'personal']);
                    case 5: return redirect()->route('propietario.dashboard');
                    case 6: return redirect()->route('gestor-setp.dashboard');
                    case 7: return redirect()->route('controlador-tiempo.dashboard');
                    case 8: return redirect()->route('gestor-recargas.dashboard');
                    case 9: return redirect()->route('jefemantenimiento.dashboard');
                    case 10: return redirect()->route('gestor-recargas.dashboard');
                    default:
                        Auth::guard('web')->logout();
                        return back()->withErrors(['documento' => 'Rol no autorizado']);
                }
            }
        }

        /*
         |----------------------------------
         | INTENTO SUPERADMIN - 2FA
         |----------------------------------
         */
        $superAdmin = \App\Models\SuperAdministrador::where('doc_super_admin', $request->doc_us)->where('id_estado', 1)->first();
        if ($superAdmin && \Illuminate\Support\Facades\Hash::check($request->password, $superAdmin->password)) {
            
            $cookieName = '2fa_device_superadmin_' . $superAdmin->doc_super_admin;

            if (!$request->hasCookie($cookieName)) {
                app(\App\Http\Controllers\Auth\TwoFactorController::class)->generateAndSendCode(
                    $superAdmin->doc_super_admin, 
                    'superadmin', 
                    $superAdmin->correo, 
                    $superAdmin->nombre
                );
                
                $request->session()->put('2fa_pending', [
                    'documento' => $superAdmin->doc_super_admin,
                    'tipo_usuario' => 'superadmin',
                ]);
                return redirect()->route('2fa.index');
            } else {
                // SUPERADMIN ya confiaba en el dispositivo
                Auth::guard('superadmin')->loginUsingId($superAdmin->doc_super_admin);
                $request->session()->regenerate();
                return redirect()->route('superadmin.dashboard');
            }
        }

        return back()->with([
            'error' => 'Documento o contraseña incorrectos']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
