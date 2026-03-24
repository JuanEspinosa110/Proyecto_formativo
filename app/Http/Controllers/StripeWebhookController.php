<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
        } catch(\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response('Webhook Error: ' . $e->getMessage(), 400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $id_tarjeta = $session->metadata->id_tarjeta ?? null;
            $monto = ($session->amount_total ?? 0) / 100;

            if ($id_tarjeta && $monto > 0) {
                $tarjeta = \App\Models\Tarjeta::where('id_tarjeta', $id_tarjeta)->first();
                if ($tarjeta) {
                    $tarjeta->saldo += $monto;
                    $tarjeta->save();

                    // Registrar la recarga
                    \App\Models\Recarga::create([
                        'id_tarjeta' => $id_tarjeta,
                        'monto' => $monto,
                        // Puedes agregar más campos si tu modelo lo requiere
                    ]);
                }
            }
        }

        return response('Webhook handled', 200);
    }
}
