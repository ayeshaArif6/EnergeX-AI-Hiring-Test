<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        // Handle preflight early
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders($this->headers($request));
        }

        $response = $next($request);
        foreach ($this->headers($request) as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }

    private function headers($request): array
    {
        $origin = $request->headers->get('Origin');
        $allowed = env('CORS_ALLOWED_ORIGINS', '*');

        $allowOrigin = $allowed === '*' ? '*' : (in_array($origin, array_map('trim', explode(',', $allowed))) ? $origin : '');

        return [
            'Access-Control-Allow-Origin'      => $allowOrigin ?: '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With, Accept, Origin',
            'Vary'                             => 'Origin',
        ];
    }
}
