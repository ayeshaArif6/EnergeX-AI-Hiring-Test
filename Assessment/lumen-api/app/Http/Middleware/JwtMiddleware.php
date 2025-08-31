<?php
namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtMiddleware {
  public function handle($request, Closure $next) {
    $auth = $request->header('Authorization');
    if (!$auth || !str_starts_with($auth, 'Bearer ')) {
      return response()->json(['error'=>'Unauthorized'],401);
    }
    try {
      $payload = JWT::decode(substr($auth,7), new Key(env('JWT_SECRET'), 'HS256'));
      $user = User::find($payload->sub ?? 0);
      if (!$user) return response()->json(['error'=>'Unauthorized'],401);
      $request->attributes->set('user', $user);
      return $next($request);
    } catch (\Throwable $e) {
      return response()->json(['error'=>'Invalid token'],401);
    }
  }
}
