<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener los orígenes permitidos desde la configuración
        $allowedOrigins = config('cors.allowed_origins', ['*']);
        $origin = $request->header('Origin');

        // Si el origen está en la lista de permitidos o se permite cualquier origen
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            $allowOrigin = in_array('*', $allowedOrigins) ? '*' : $origin;
        } else {
            $allowOrigin = $allowedOrigins[0] ?? '*';
        }

        // Manejar solicitudes OPTIONS (preflight)
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Procesar la solicitud normal
        $response = $next($request);

        // Agregar headers CORS a la respuesta
        // BinaryFileResponse usa headers->set() en lugar de header()
        if ($response instanceof BinaryFileResponse) {
            $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        } else {
            $response->header('Access-Control-Allow-Origin', $allowOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
