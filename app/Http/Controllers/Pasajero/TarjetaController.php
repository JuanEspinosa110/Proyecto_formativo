<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\TitularidadTarjeta;
use App\Models\Recarga;
use App\Models\Tarjeta;
use App\Models\VentaViaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TarjetaController extends Controller
{
    // ── index ──────────────────────────────────────────────────
    /**
     * Mi Tarjeta: redirige al saldo si tiene tarjeta, o a sin-tarjeta si no.
     */
    public function index()
    {
        $user = auth()->user();

        $tieneTarjeta = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->where('id_estado', 1)
            ->exists();

        if ($tieneTarjeta) {
            return redirect()->route('pasajero.saldo');
        }

        return redirect()->route('pasajero.tarjeta.sin-tarjeta');
    }

    // ── sinTarjeta ─────────────────────────────────────────────
    /**
     * Vista de onboarding: el pasajero no tiene tarjeta activa.
     * Si ya tiene una redirige al dashboard.
     */
    public function sinTarjeta()
    {
        $user = auth()->user();

        $tieneTarjeta = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->where('id_estado', 1)
            ->exists();

        if ($tieneTarjeta) {
            return redirect()->route('pasajero.dashboard');
        }

        return view('pasajero.tarjeta.dashboard');
    }

    // ── registrar ──────────────────────────────────────────────
    /**
     * El pasajero ya tiene una tarjeta física y quiere vincularla
     * con su código de tarjeta.
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'codigo_tarjeta' => 'required|string|max:30',
        ], [
            'codigo_tarjeta.required' => 'El código de tarjeta es obligatorio.',
        ]);

        $user = auth()->user();

        // Buscar la tarjeta por código
        $tarjeta = Tarjeta::where('codigo_tarjeta', trim($request->codigo_tarjeta))
            ->first();

        if (!$tarjeta) {
            return back()
                ->withErrors(['codigo_tarjeta' => 'No encontramos una tarjeta con ese código. Verifica el código impreso en tu tarjeta.'])
                ->withInput();
        }

        // Verificar que no esté ya asignada a otro usuario activo
        $yaAsignada = TitularidadTarjeta::where('id_tarjeta', $tarjeta->id_tarjeta)
            ->where('id_estado', 1)
            ->where('doc_usuario', '!=', $user->doc_usuario)
            ->exists();

        if ($yaAsignada) {
            return back()
                ->withErrors(['codigo_tarjeta' => 'Esta tarjeta ya está vinculada a otro usuario activo.'])
                ->withInput();
        }

        // Crear la titularidad
        TitularidadTarjeta::create([
            'id_tarjeta' => $tarjeta->id_tarjeta,
            'doc_usuario' => $user->doc_usuario,
            'fecha_inicio' => Carbon::today(),
            'fecha_fin' => null,
            'id_estado' => 1,
            'motivo_cambio' => 'Registro inicial por el pasajero',
        ]);

        return redirect()->route('pasajero.dashboard')
            ->with('success', '¡Tarjeta vinculada correctamente! Ya puedes usar SIGU.');
    }

    // ── comprar ────────────────────────────────────────────────
    /**
     * El pasajero solicita la compra de una nueva tarjeta.
     * En una implementación real conectaría con una pasarela de pago.
     * Por ahora registra la solicitud y muestra un mensaje.
     */
    public function comprar(Request $request)
    {
        $request->validate([
            'punto_compra' => 'required|string|max:100',
        ], [
            'punto_compra.required' => 'Indica dónde deseas recoger tu tarjeta.',
        ]);

        // En producción: crear una orden de compra, enviar correo, etc.
        // Por ahora retornamos con mensaje informativo.
        return back()->with(
            'info',
            'Tu solicitud fue registrada. Dirígete al punto "' . $request->punto_compra .
            '" con tu documento de identidad para retirar tu tarjeta SIGU.'
        );
    }

    // ── saldo ──────────────────────────────────────────────────
    /**
     * Vista de saldo de la tarjeta del pasajero.
     */
    public function saldo()
    {
        $user = auth()->user();

        // En TarjetaController.php
        $titularidad = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->whereNull('fecha_fin')
            ->firstOrFail();

        $tarjeta = $titularidad->tarjeta;

        // Validación de seguridad
        if (!$tarjeta) {
            return redirect()->back()->with('error', 'No tienes una tarjeta activa vinculada.');
        }

        // Ahora es seguro acceder a id_tarjeta
        $recargas = Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $totalRecargado = Recarga::where('id_tarjeta', $tarjeta->id_tarjeta)->sum('monto');

        // Fetch recent trips (Ventas) for this card
        $viajesQuery = VentaViaje::with(['viaje.ruta.barrioOrigen', 'viaje.ruta.barrioDestino'])
            ->where('id_tarjeta', $tarjeta->id_tarjeta);

        $viajesMes = (clone $viajesQuery)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->get();

        // Calculate total spent in the month using real data from the database
        $totalGastadoMes = $viajesMes->sum('valor');

        $viajes = $viajesQuery->orderBy('fecha', 'desc')->take(3)->get();

        return view('pasajero.saldo.index', compact('tarjeta', 'titularidad', 'recargas', 'totalRecargado', 'viajes', 'viajesMes', 'totalGastadoMes'));
    }

    /**
     * Muestra el formulario para solicitar el cambio de tarjeta.
     */
    public function cambiar()
    {
        return view('pasajero.tarjeta.cambiar');
    }

    /**
     * Procesa la solicitud inicial del cambio.
     * Si requiere traspaso, envía correo y pasa a verificación.
     * Si no requiere traspaso, procesa el cambio de inmediato.
     */
    public function iniciarCambio(Request $request)
    {
        $request->validate([
            'codigo_tarjeta_nueva' => 'required|string|max:30',
            'motivo' => 'required|string|max:100',
        ], [
            'codigo_tarjeta_nueva.required' => 'Debes ingresar el código de la nueva tarjeta.',
            'motivo.required' => 'Por favor, selecciona el motivo del cambio.',
        ]);

        $user = auth()->user();

        // Verificar la nueva tarjeta
        $nuevaTarjeta = Tarjeta::where('codigo_tarjeta', trim($request->codigo_tarjeta_nueva))->first();

        if (!$nuevaTarjeta) {
            return back()->withErrors(['codigo_tarjeta_nueva' => 'No encontramos una tarjeta con ese código.'])->withInput();
        }

        // Verificar que la tarjeta nueva esté activa (id_estado == 1)
        if ($nuevaTarjeta->id_estado != 1) {
            return back()->withErrors(['codigo_tarjeta_nueva' => 'Esta tarjeta no se encuentra activa.'])->withInput();
        }

        // Verificar que no esté asignada a ningún usuario en la tabla Tarjeta
        if (!empty($nuevaTarjeta->doc_usuario)) {
            return back()->withErrors(['codigo_tarjeta_nueva' => 'Esta tarjeta ya está asignada a un usuario.'])->withInput();
        }

        // Verificar que no esté ya asignada a otro usuario activo en TitularidadTarjeta
        $yaAsignada = TitularidadTarjeta::where('id_tarjeta', $nuevaTarjeta->id_tarjeta)
            ->where('id_estado', 1)
            ->exists();

        if ($yaAsignada) {
            return back()->withErrors(['codigo_tarjeta_nueva' => 'Esta tarjeta ya está vinculada a un usuario.'])->withInput();
        }

        // Obtener la tarjeta actual activa del usuario
        $titularidadActual = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->whereNull('fecha_fin')
            ->where('id_estado', 1)
            ->first();

        if (!$titularidadActual) {
            return back()->with('error', 'No tienes una tarjeta activa para cambiar.');
        }

        $traspasoSaldo = $request->has('traspaso_saldo');

        if ($traspasoSaldo) {
            // Generar y enviar OTP por correo
            $codigoOTP = random_int(100000, 999999);
            $correo = $user->correo;

            Cache::put('traspaso_' . $correo, $codigoOTP, now()->addMinutes(10));

            Mail::raw("Tu código para confirmar el traspaso de saldo de tu tarjeta SIGU es: $codigoOTP", function ($message) use ($correo) {
                $message->to($correo)
                    ->subject('Código de confirmación - Traspaso de saldo SIGU');
            });

            // Guardar datos temporales en sesión para completar el proceso después
            session([
                'cambio_tarjeta_temp' => [
                    'id_tarjeta_nueva' => $nuevaTarjeta->id_tarjeta,
                    'motivo' => $request->motivo,
                    'traspaso' => true
                ]
            ]);

            return redirect()->route('pasajero.tarjeta.verificar-cambio')
                ->with('success', 'Te hemos enviado un código de 6 dígitos a tu correo registrado (' . $correo . ') para autorizar el traspaso.');
        }

        // Si NO hay traspaso, ejecutar el cambio inmediatamente
        DB::beginTransaction();
        try {
            $this->ejecutarCambioTarjeta($user, $titularidadActual, $nuevaTarjeta->id_tarjeta, $request->motivo, false);
            DB::commit();

            return redirect()->route('pasajero.dashboard')->with('success', '¡Tarjeta cambiada exitosamente! Tu tarjeta anterior ha sido inactivada.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al procesar el cambio: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para ingresar el OTP de traspaso de saldo.
     */
    public function verificarCambioForm()
    {
        if (!session()->has('cambio_tarjeta_temp')) {
            return redirect()->route('pasajero.tarjeta.cambiar')->with('error', 'No hay ningún proceso de cambio de tarjeta pendiente.');
        }
        return view('pasajero.tarjeta.verificar-cambio');
    }

    /**
     * Valida el OTP y ejecuta el cambio con traspaso.
     */
    public function confirmarCambio(Request $request)
    {
        $request->validate([
            'codigo' => 'required|numeric'
        ]);

        $user = auth()->user();
        $correo = $user->correo;
        $sessionKey = 'intentos_otp_' . $correo;

        // Obtener intentos actuales
        $intentos = session($sessionKey, 0);

        if ($intentos >= 3) {
            session()->forget($sessionKey);
            Cache::forget('traspaso_' . $correo);
            session()->forget('cambio_tarjeta_temp');

            return redirect()->route('pasajero.tarjeta.cambiar')
                ->with('error', 'Has superado el límite de 3 intentos fallidos. Por seguridad, el proceso fue cancelado. Por favor, solicita de nuevo el cambio.');
        }

        $codigoGuardado = Cache::get('traspaso_' . $correo);

        if (!$codigoGuardado || $codigoGuardado != $request->codigo) {
            $intentos++;
            session([$sessionKey => $intentos]);

            if ($intentos >= 3) {
                session()->forget($sessionKey);
                Cache::forget('traspaso_' . $correo);
                session()->forget('cambio_tarjeta_temp');

                return redirect()->route('pasajero.tarjeta.cambiar')
                    ->with('error', 'Has superado el límite de 3 intentos fallidos. Por seguridad, el código de traspaso fue invalidado. Por favor, solicita un nuevo traspaso.');
            }

            $intentosRestantes = 3 - $intentos;
            return back()->withErrors(['codigo' => "Código incorrecto. Te quedan {$intentosRestantes} intento(s)."]);
        }

        // Éxito: limpiar contador de intentos
        session()->forget($sessionKey);

        $tempData = session('cambio_tarjeta_temp');

        if (!$tempData) {
            return redirect()->route('pasajero.tarjeta.cambiar')->with('error', 'La sesión ha expirado.');
        }

        $titularidadActual = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->whereNull('fecha_fin')
            ->where('id_estado', 1)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $this->ejecutarCambioTarjeta($user, $titularidadActual, $tempData['id_tarjeta_nueva'], $tempData['motivo'], true);

            // Limpieza
            Cache::forget('traspaso_' . $correo);
            session()->forget('cambio_tarjeta_temp');

            DB::commit();
            return redirect()->route('pasajero.dashboard')->with('success', '¡Traspaso autorizado y tarjeta cambiada exitosamente!');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al procesar el traspaso: ' . $e->getMessage());
        }
    }

    /**
     * Lógica compartida para cambiar la tarjeta en BD.
     */
    private function ejecutarCambioTarjeta($user, $titularidadAntigua, $idTarjetaNueva, $motivo, $realizarTraspaso)
    {
        $tarjetaAntigua = $titularidadAntigua->tarjeta;
        $tarjetaNueva = Tarjeta::findOrFail($idTarjetaNueva);

        // 1. Inactivar titularidad actual
        $titularidadAntigua->update([
            'fecha_fin' => Carbon::today(),
            'id_estado' => 2 // Inactiva
        ]);

        // 2. Inactivar plástico tarjeta antigua
        $tarjetaAntigua->update(['id_estado' => 2]);

        // 3. Crear nueva titularidad
        TitularidadTarjeta::create([
            'id_tarjeta' => $idTarjetaNueva,
            'doc_usuario' => $user->doc_usuario,
            'fecha_inicio' => Carbon::today(),
            'fecha_fin' => null,
            'id_estado' => 1,
            'motivo_cambio' => $motivo,
        ]);

        // 4. Activar plástico tarjeta nueva y asignar documento
        $tarjetaNueva->update([
            'id_estado' => 1,
            'doc_usuario' => $user->doc_usuario
        ]);

        // 5. Traspaso de saldo (Historial de Recargas + Actualización de campo)
        if ($realizarTraspaso && $tarjetaAntigua->saldo > 0) {
            $saldoATraspasar = $tarjetaAntigua->saldo;

            // Generar un ID de recarga específico para traspasos (iniciando con 99 para identificar traspaso)
            $baseId = '99' . time();
            $idRecargaNegativa = (int)($baseId . '1');
            $idRecargaPositiva = (int)($baseId . '2');

            // Restar en tarjeta vieja (Historial negativo)
            Recarga::create([
                'id_recarga' => $idRecargaNegativa,
                'id_tarjeta' => $tarjetaAntigua->id_tarjeta,
                'monto' => -$saldoATraspasar
            ]);
            $tarjetaAntigua->update(['saldo' => 0]);

            // Sumar en tarjeta nueva (Historial positivo)
            Recarga::create([
                'id_recarga' => $idRecargaPositiva,
                'id_tarjeta' => $tarjetaNueva->id_tarjeta,
                'monto' => $saldoATraspasar
            ]);
            $tarjetaNueva->update(['saldo' => $saldoATraspasar]);
        }
    }
}
