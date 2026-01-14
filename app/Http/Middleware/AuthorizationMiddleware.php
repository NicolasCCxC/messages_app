<?php

namespace App\Http\Middleware;

use App\Infrastructure\Persistence\GateEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizationMiddleware
{
    use ResponseApiTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::manager()->getJWTProvider()->decode($request->bearerToken());
            $path = GateEloquent::getPath($payload['service']);
            if ($request->bearerToken() !== $path->token && !isset($payload['company_id'])) {
                return $this->errorResponse(
                    Module::SECURITY,
                    Response::HTTP_UNAUTHORIZED,
                    Response::$statusTexts[Response::HTTP_UNAUTHORIZED]);
            }
        } catch (\Exception $e) {
            \Log::info("Error on Authorization-handler: " . $e->getMessage());
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_UNAUTHORIZED,
                $e->getMessage());
        }
        return $next($request);
    }
}
