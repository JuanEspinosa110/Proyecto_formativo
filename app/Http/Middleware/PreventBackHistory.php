<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /**
         * Meticulosidad: Solo aplicamos el escudo de no-caché si la respuesta es HTML.
         * Esto garantiza que:
         * 1. Las descargas de PDF/Excel (BinaryFileResponse) no se rompan.
         * 2. Las respuestas de API/AJAX (JsonResponse) no tengan cabeceras innecesarias.
         * 3. Los dashboards (HTML) queden totalmente blindados ante el botón "Atrás".
         */
        if (!($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) && 
            !($response instanceof \Illuminate\Http\JsonResponse)) {
            
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
