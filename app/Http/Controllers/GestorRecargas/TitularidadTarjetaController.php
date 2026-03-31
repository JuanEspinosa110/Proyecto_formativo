<?php


namespace App\Http\Controllers\GestorRecargas;
use Illuminate\Support\Facades\Mail;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Tarjeta;
use App\Models\TitularidadTarjeta;

class TitularidadTarjetaController extends Controller
{
    /**
     * Vista principal del módulo de titularidad de tarjeta
     */
    public function index(Request $request)
    {
        // Aquí irá la lógica de búsqueda y despliegue de usuarios/tarjetas
        return view('empresa-recargas.titularidad.index');
    }

    /**
     * Búsqueda AJAX de usuario por documento o correo
     */
    public function buscarUsuario(Request $request)
    {
        $busqueda = $request->input('busqueda');
        $usuario = \App\Models\Usuario::where('doc_usuario', $busqueda)
            ->orWhere('correo', $busqueda)
            ->first();

        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.']);
        }

        // Buscar titularidad activa
        $titularidad = $usuario->titularidadesTarjeta()->where('id_estado', 1)->first();
        $tarjeta = null;
        $saldo = null;
        // SIEMPRE buscar tarjetas inactivas y sin titularidad
        $tarjetas_disponibles = \App\Models\Tarjeta::where('id_estado', 2)
            ->whereDoesntHave('titularidades')
            ->get(['id_tarjeta', 'codigo_tarjeta']);
        if ($titularidad) {
            $tarjeta = \App\Models\Tarjeta::where('id_tarjeta', $titularidad->id_tarjeta)->first();
            $saldo = $tarjeta ? $tarjeta->saldo : null;
        }

        return response()->json([
            'success' => true,
            'usuario' => [
                'doc_usuario' => $usuario->doc_usuario,
                'nombre' => $usuario->primer_nombre . ' ' . $usuario->primer_apellido,
                'correo' => $usuario->correo,
            ],
            'titularidad' => $titularidad,
            'tarjeta' => $tarjeta,
            'saldo' => $saldo,
            'tarjetas_disponibles' => $tarjetas_disponibles,
        ]);
    }

    /**
     * Enviar código de verificación al correo del usuario
     */
    public function enviarCodigo(Request $request)
    {
        $doc_usuario = $request->input('doc_usuario');
        $usuario = \App\Models\Usuario::find($doc_usuario);
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.']);
        }
        // Cooldown: no reenviar código más de 1 vez cada 60 segundos
        $lastSent = session('codigo_verificacion_last_sent_' . $doc_usuario);
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $wait = 60 - now()->diffInSeconds($lastSent);
            return response()->json(['success' => false, 'message' => "Debes esperar $wait segundos para reenviar el código."]);
        }
        // Generar código aleatorio de 6 dígitos
        $codigo = random_int(100000, 999999);
        // Guardar código, timestamp y resetear intentos
        session([
            'codigo_verificacion_' . $doc_usuario => $codigo,
            'codigo_verificacion_last_sent_' . $doc_usuario => now(),
            'codigo_verificacion_intentos_' . $doc_usuario => 0,
            'codigo_verificacion_expira_' . $doc_usuario => now()->addMinutes(10),
        ]);
        // Enviar correo
            try {
                Mail::raw("Su código de verificación para cambio de tarjeta es: $codigo", function($message) use ($usuario) {
                    $message->to($usuario->correo)->subject('Código de verificación - Cambio de Tarjeta');
                });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'No se pudo enviar el correo.']);
        }
        return response()->json(['success' => true, 'message' => 'Código enviado al correo del usuario.']);
    }

    /**
     * Validar código y realizar el cambio de titularidad
     */
    public function cambiarTarjeta(Request $request)
    {
        $doc_usuario = $request->input('doc_usuario');
        $id_tarjeta = $request->input('id_tarjeta');
        $codigo = $request->input('codigo_verificacion');
        $usuario = \App\Models\Usuario::find($doc_usuario);
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.']);
        }
        // Validar campos obligatorios
        if (!$id_tarjeta || !$codigo) {
            return response()->json(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        }
        // Validar código y expiración
        $codigo_guardado = session('codigo_verificacion_' . $doc_usuario);
        $expira = session('codigo_verificacion_expira_' . $doc_usuario);
        $intentos = session('codigo_verificacion_intentos_' . $doc_usuario, 0);
        if (!$codigo_guardado || !$expira || now()->gt($expira)) {
            session()->forget([
                'codigo_verificacion_' . $doc_usuario,
                'codigo_verificacion_last_sent_' . $doc_usuario,
                'codigo_verificacion_intentos_' . $doc_usuario,
                'codigo_verificacion_expira_' . $doc_usuario,
            ]);
            return response()->json(['success' => false, 'message' => 'El código ha expirado. Solicita uno nuevo.']);
        }
        if ($intentos >= 3) {
            session()->forget([
                'codigo_verificacion_' . $doc_usuario,
                'codigo_verificacion_last_sent_' . $doc_usuario,
                'codigo_verificacion_intentos_' . $doc_usuario,
                'codigo_verificacion_expira_' . $doc_usuario,
            ]);
            return response()->json(['success' => false, 'message' => 'Demasiados intentos fallidos. Solicita un nuevo código.']);
        }
        if ($codigo != $codigo_guardado) {
            session(['codigo_verificacion_intentos_' . $doc_usuario => $intentos + 1]);
            $restantes = 2 - $intentos;
            return response()->json(['success' => false, 'message' => "Código incorrecto. Intentos restantes: $restantes"]);
        }
        // Suspender titularidad anterior si existe
        $titularidad_anterior = $usuario->titularidadesTarjeta()->where('id_estado', 1)->first();
        if ($titularidad_anterior) {
            $titularidad_anterior->id_estado = 3; // SUSPENDIDO
            $titularidad_anterior->fecha_fin = now();
            $titularidad_anterior->motivo_cambio = 'Cambio de tarjeta por gestión';
            $titularidad_anterior->save();
            // Poner la tarjeta anterior en INACTIVO
            $tarjeta_anterior = \App\Models\Tarjeta::find($titularidad_anterior->id_tarjeta);
            if ($tarjeta_anterior) {
                $tarjeta_anterior->id_estado = 2; // INACTIVO
                $tarjeta_anterior->save();
            }
        }
        // Crear nueva titularidad
        $nueva = new \App\Models\TitularidadTarjeta();
        $nueva->id_tarjeta = $id_tarjeta;
        $nueva->doc_usuario = $doc_usuario;
        $nueva->fecha_inicio = now();
        $nueva->id_estado = 1; // ACTIVO
        $nueva->motivo_cambio = 'Asignación/cambio por gestión';
        $nueva->save();
        // Activar tarjeta
        $tarjeta = \App\Models\Tarjeta::find($id_tarjeta);
        if ($tarjeta) {
            $tarjeta->id_estado = 1; // ACTIVO
            // Traspasar saldo si había tarjeta anterior
            if ($titularidad_anterior) {
                $tarjeta_anterior = \App\Models\Tarjeta::find($titularidad_anterior->id_tarjeta);
                if ($tarjeta_anterior && $tarjeta_anterior->saldo > 0) {
                    $tarjeta->saldo += $tarjeta_anterior->saldo;
                    $tarjeta_anterior->saldo = 0;
                    $tarjeta_anterior->save();
                }
            }
            $tarjeta->save();
        }
        // Limpiar código y datos de sesión
        session()->forget([
            'codigo_verificacion_' . $doc_usuario,
            'codigo_verificacion_last_sent_' . $doc_usuario,
            'codigo_verificacion_intentos_' . $doc_usuario,
            'codigo_verificacion_expira_' . $doc_usuario,
        ]);
        return response()->json(['success' => true, 'message' => 'Cambio de titularidad realizado correctamente.']);
    }

    /**
     * Cambia la tarjeta activa de un usuario, validando el código y transfiriendo el saldo.
     */
    public function cambiar(Request $request)
    {
        $request->validate([
            'doc_usuario' => 'required',
            'id_tarjeta' => 'required|exists:tarjetas,id_tarjeta',
            'codigo_verificacion' => 'required|string',
        ]);

        $usuario = Usuario::where('doc_usuario', $request->doc_usuario)->first();
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.']);
        }

        // Obtener la titularidad actual y saldo
        $titularidadActual = TitularidadTarjeta::where('doc_usuario', $usuario->doc_usuario)
            ->where('activa', true)->first();
        if (!$titularidadActual) {
            return response()->json(['success' => false, 'message' => 'El usuario no tiene tarjeta activa.']);
        }
        if ($titularidadActual->id_tarjeta == $request->id_tarjeta) {
            return response()->json(['success' => false, 'message' => 'Debe seleccionar una tarjeta diferente a la actual.']);
        }
        $tarjetaActual = Tarjeta::find($titularidadActual->id_tarjeta);
        $saldo = $tarjetaActual ? $tarjetaActual->saldo : 0;

        // Validar código de verificación
        $sessionKey = 'codigo_verificacion_' . $usuario->doc_usuario;
        $codigoData = session($sessionKey);
        if (!$codigoData) {
            return response()->json(['success' => false, 'message' => 'No se ha solicitado código de verificación.']);
        }
        if (now()->gt($codigoData['expira'])) {
            session()->forget($sessionKey);
            return response()->json(['success' => false, 'message' => 'El código ha expirado. Solicite uno nuevo.']);
        }
        if ($codigoData['codigo'] !== $request->codigo_verificacion) {
            // Incrementar intentos
            $codigoData['intentos'] = ($codigoData['intentos'] ?? 0) + 1;
            session([$sessionKey => $codigoData]);
            if ($codigoData['intentos'] >= 3) {
                session()->forget($sessionKey);
                return response()->json(['success' => false, 'message' => 'Demasiados intentos fallidos. Solicite un nuevo código.']);
            }
            return response()->json(['success' => false, 'message' => 'Código incorrecto.']);
        }

        // Verificar que la tarjeta seleccionada esté disponible
        $tarjetaNueva = Tarjeta::where('id_tarjeta', $request->id_tarjeta)->where('estado', 'disponible')->first();
        if (!$tarjetaNueva) {
            return response()->json(['success' => false, 'message' => 'La tarjeta seleccionada no está disponible.']);
        }

        // Desactivar titularidad y tarjeta actual
        $titularidadActual->activa = false;
        $titularidadActual->save();
        if ($tarjetaActual) {
            $tarjetaActual->estado = 'disponible';
            $tarjetaActual->saldo = 0;
            $tarjetaActual->save();
        }

        // Asignar nueva titularidad y transferir saldo
        $titularidadNueva = new TitularidadTarjeta();
        $titularidadNueva->doc_usuario = $usuario->doc_usuario;
        $titularidadNueva->id_tarjeta = $tarjetaNueva->id_tarjeta;
        $titularidadNueva->activa = true;
        $titularidadNueva->fecha_asignacion = now();
        $titularidadNueva->save();

        $tarjetaNueva->estado = 'asignada';
        $tarjetaNueva->saldo = $saldo;
        $tarjetaNueva->save();

        // Limpiar código de sesión
        session()->forget($sessionKey);

        return response()->json(['success' => true, 'message' => 'Cambio de tarjeta realizado correctamente. El saldo ha sido transferido.']);
    }
}
