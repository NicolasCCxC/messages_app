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

class MembershipIpAddress
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
            $clientIpAddress = $request->ip();
            $request->merge([
                'payu_data' => [
                    'transaction' => array_merge($request->input('payu_data.transaction', []), [
                    'ipAddress' => $clientIpAddress,
                    ]),
                ],
            ]);
            return $next($request);
        } catch (\Exception $e) {
            return $next($request);
        }
    }
}
