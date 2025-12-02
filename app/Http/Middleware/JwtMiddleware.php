<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
  public function handle($request, Closure $next)
  {
    $token = $request->bearerToken();

    if (!$token) {
      return response()->json([
        'error' => 'Token not provided',
        'message' => 'Token not provided message',
      ], 401);
    }

    try {
      $decoded = JWT::decode($token, new Key(config('onlyoffice.secret'), 'HS256'));

      // Attach user to request
      $request->attributes->add(['jwt_user' => (array)$decoded]);

    } catch (Exception $e) {
      return response()->json([
        'error' => 'Invalid or expired token',
        'message' => $e->getMessage()
      ], 401);
    }

    return $next($request);
  }
}
