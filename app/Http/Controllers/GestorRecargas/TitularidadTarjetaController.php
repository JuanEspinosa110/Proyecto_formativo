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
            $cooldown = 60; // segundos
            \Log::info('Sesión antes de validar cooldown', [
                'last_sent' => session('codigo_verificacion_last_sent_' . $doc_usuario),
                'codigo' => session('codigo_verificacion_' . $doc_usuario)
            ]);
            $lastSent = session('codigo_verificacion_last_sent_' . $doc_usuario);
            if ($lastSent && now()->diffInSeconds($lastSent) < $cooldown) {
                $wait = $cooldown - now()->diffInSeconds($lastSent);
                $wait = max(0, min($cooldown, ceil($wait)));
                \Log::info('Cooldown activo', [
                    'wait' => $wait,
                    'last_sent' => $lastSent,
                    'now' => now()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => "Debes esperar $wait segundos para reenviar el código.",
                    'wait' => $wait,
                    'cooldown_active' => true
                ]);
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
            \Log::info('Sesión después de guardar código', [
                'codigo' => session('codigo_verificacion_' . $doc_usuario),
                'last_sent' => session('codigo_verificacion_last_sent_' . $doc_usuario),
            ]);
            \Log::info('Intentando enviar correo a: ' . $usuario->correo);
        try {
            Mail::raw("Su código de verificación para cambio de tarjeta es: $codigo", function($message) use ($usuario) {
                $message->to($usuario->correo)->subject('Código de verificación - Cambio de Tarjeta ' . time());
            });
            \Log::info('Correo enviado correctamente a: ' . $usuario->correo);
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo enviar el correo: ' . $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Código enviado al correo del usuario.', 'wait' => $cooldown, 'cooldown_active' => false]);
    }

    /**
     * Consultar el tiempo restante del cooldown para un usuario (sin enviar código)
     */
    public function consultarCooldown(Request $request)
    {
        $doc_usuario = $request->input('doc_usuario');
        $cooldown = 60; // segundos
        $lastSent = session('codigo_verificacion_last_sent_' . $doc_usuario);
        if ($lastSent && now()->diffInSeconds($lastSent) < $cooldown) {
            $wait = $cooldown - now()->diffInSeconds($lastSent);
            $wait = max(0, min($cooldown, ceil($wait)));
            return response()->json([
                'cooldown_active' => true,
                'wait' => $wait
            ]);
        }
        return response()->json([
            'cooldown_active' => false,
            'wait' => 0
        ]);
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

        if (!preg_match('/^\d{6}$/', $codigo)) {
            return response()->json(['success' => false, 'message' => 'El código debe tener exactamente 6 dígitos numéricos.']);
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
        // Iniciar transacción para asegurar coherencia
        \DB::beginTransaction();
        try {
            // Suspender titularidad anterior si existe
            $titularidad_anterior = $usuario->titularidadesTarjeta()->where('id_estado', 1)->first();
            if ($titularidad_anterior) {
                $titularidad_anterior->id_estado = 3; // SUSPENDIDO
                $titularidad_anterior->fecha_fin = now();
                $titularidad_anterior->motivo_cambio = 'Cambio de tarjeta por gestión';
                $titularidad_anterior->save();

                // Poner la tarjeta anterior en SUSPENDIDO
                $tarjeta_anterior = \App\Models\Tarjeta::find($titularidad_anterior->id_tarjeta);
                if ($tarjeta_anterior) {
                    $tarjeta_anterior->id_estado = 3; // SUSPENDIDA
                    $tarjeta_anterior->save();
                }
            }

            // ACTIVAR TARJETA Y USUARIO Y ASOCIAR NIT
            $gestor = \Illuminate\Support\Facades\Auth::user();
            if ($usuario && is_null($usuario->NIT)) {
                $usuario->NIT = $gestor->NIT;
                $usuario->save();
            }

            // Crear nueva titularidad
            $nueva = new \App\Models\TitularidadTarjeta();
            $nueva->id_tarjeta = $id_tarjeta;
            $nueva->doc_usuario = $doc_usuario;
            $nueva->fecha_inicio = now();
            $nueva->id_estado = 1; // ACTIVO
            $nueva->motivo_cambio = 'Asignación/cambio por gestión';
            $nueva->save();

            // ACTIVAR TARJETA Y USUARIO
            $tarjeta = \App\Models\Tarjeta::find($id_tarjeta);
            $saldo_transferido = 0;
            if ($tarjeta) {
                $tarjeta->id_estado = 1; // ACTIVO
                
                // Traspasar saldo si había tarjeta anterior
                if ($titularidad_anterior) {
                    $t_ant = \App\Models\Tarjeta::find($titularidad_anterior->id_tarjeta);
                    if ($t_ant && $t_ant->saldo > 0) {
                        $saldo_transferido = $t_ant->saldo;
                        $tarjeta->saldo += $t_ant->saldo;
                        $t_ant->saldo = 0;
                        $t_ant->save();
                    }
                }
                $tarjeta->save();
            }

            \DB::commit();

            // Limpiar código y datos de sesión
            session()->forget([
                'codigo_verificacion_' . $doc_usuario,
                'codigo_verificacion_last_sent_' . $doc_usuario,
                'codigo_verificacion_intentos_' . $doc_usuario,
                'codigo_verificacion_expira_' . $doc_usuario,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Cambio de titularidad realizado correctamente.',
                'nueva_titularidad' => [
                    'id_tarjeta' => $nueva->id_tarjeta,
                    'fecha_inicio' => $nueva->fecha_inicio ? $nueva->fecha_inicio->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
                ],
                'saldo_transferido' => $saldo_transferido
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar el cambio: ' . $e->getMessage()]);
        }
    }



    /**
     * Endpoint temporal para resetear el cooldown del código de verificación (solo pruebas)
     */
    public function resetearCooldown(Request $request)
    {
        $doc_usuario = $request->input('doc_usuario');
        session()->forget([
            'codigo_verificacion_' . $doc_usuario,
            'codigo_verificacion_last_sent_' . $doc_usuario,
            'codigo_verificacion_intentos_' . $doc_usuario,
            'codigo_verificacion_expira_' . $doc_usuario,
        ]);
        return response()->json(['success' => true, 'message' => 'Cooldown reseteado para ' . $doc_usuario]);
    }
}
