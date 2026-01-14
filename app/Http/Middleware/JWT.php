<?php

namespace App\Http\Middleware;

use App\Infrastructure\Persistence\GateEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWT
{
    use ResponseApiTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            $is_a_service = Module::where('token', $token)->exists();
            if (!$is_a_service) JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                // If the payload doesn't contain usr & psw, then try to validate if is a service petition
                return (new AuthorizationMiddleware)->handle($request, $next);
            } else {
                return $this->errorResponse(
                    Module::SECURITY,
                    Response::HTTP_UNAUTHORIZED,
                    $e->getMessage());
            }
        }

        return $next($request);
    }
}
