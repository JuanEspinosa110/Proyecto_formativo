<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use App\Models\TitularidadTarjeta;
use App\Models\Recarga;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TarjetaController extends Controller
{
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

        return view('pasajero.tarjeta.sin-tarjeta');
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

        if (! $tarjeta) {
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
            'id_tarjeta'   => $tarjeta->id_tarjeta,
            'doc_usuario'  => $user->doc_usuario,
            'fecha_inicio' => Carbon::today(),
            'fecha_fin'    => null,
            'id_estado'    => 1,
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

        return view('pasajero.saldo.index', compact('tarjeta', 'titularidad', 'recargas', 'totalRecargado'));
    }
}
