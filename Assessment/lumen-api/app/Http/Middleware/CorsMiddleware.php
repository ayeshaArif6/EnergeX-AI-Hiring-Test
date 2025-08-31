<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        // Handle preflight
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type')
                ->header('Access-Control-Expose-Headers', 'Authorization, Content-Type')
                ->header('Vary', 'Origin');
        }

        // Call the next middleware / controller
        $response = $next($request);

        // If controller returned a string/array etc, wrap it as a Response
        if (!$response instanceof SymfonyResponse) {
            $response = response($response);
        }

        // Add CORS headers to the actual response
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization, Content-Type');
        $response->headers->set('Vary', 'Origin');

        return $response;
    }
}
