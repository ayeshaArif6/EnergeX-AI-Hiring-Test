<?php
namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Throwable;

class JwtMiddleware {
  public function handle($request, Closure $next) {
    $auth = $request->header('Authorization', '');
    if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
      return response()->json(['error'=>'Missing Bearer token'],401);
    }
    $token = trim($m[1]);
    try {
      JWT::$leeway = 60; // tolerate 60s skew during dev
      $payload = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

      $user = isset($payload->sub) ? User::find((int)$payload->sub) : null;
      if (!$user) return response()->json(['error'=>'Invalid token subject'],401);

      $request->attributes->set('user', $user);
      return $next($request);
    } catch (Throwable $e) {
      // While APP_DEBUG=true, return the real reason to help debug.
      if (env('APP_DEBUG', false)) {
        return response()->json(['error'=>'Invalid token', 'reason'=>$e->getMessage()],401);
      }
      return response()->json(['error'=>'Invalid token'],401);
    }
  }
}
