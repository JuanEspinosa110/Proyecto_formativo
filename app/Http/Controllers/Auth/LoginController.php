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
            'doc_us'   => 'required|integer',
            'password' => 'required',
        ]);

        /*
        |----------------------------------
        |  INTENTO USUARIOS (web)
        |----------------------------------
        */
        if (Auth::guard('web')->attempt([
            'doc_usuario' => $request->doc_us,
            'password'   => $request->password,
            'id_estado'  => 1,
        ])) {

            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            switch ($user->id_tipo_usuario) {
                case 1:
                    return redirect()->route('admin.dashboard');

                case 2:
                    return redirect()->route('pasajero.dashboard');

                case 3:
                    return redirect()->route('conductor.dashboard');

                case 4:
                    return redirect()->route('empresa.dashboard');
                
                case 5:
                    return redirect()->route('empresa.dashboard'); // Auxiliar

                case 6:
                    return redirect()->route('propietario.dashboard'); // Propietario

                default:
                    Auth::guard('web')->logout();
                    return back()->withErrors([
                        'documento' => 'Rol no autorizado'
                    ]);
            }
        }

        /*
        |----------------------------------
        | 2️⃣ INTENTO SUPERADMIN
        |----------------------------------
        */
        if (Auth::guard('superadmin')->attempt([
            'doc_super_admin' => $request->doc_us,
            'password'  => $request->password,
            'id_estado' => 1,
        ])) {

            $request->session()->regenerate();
            return redirect()->route('superadmin.dashboard');
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
