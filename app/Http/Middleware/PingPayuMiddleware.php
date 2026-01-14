<?php

namespace App\Http\Middleware;

use App\Helpers\Utils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PingPayuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $newRequest = $request->duplicate();
        $dataRequest = $request->all();

        if(isset($dataRequest['transaction']['ipAddress'])) $dataRequest['transaction']['ipAddress'] = $request->ip();
        if(isset($dataRequest['ip'])) $dataRequest['ip'] = $request->ip();

        $newRequest->merge($dataRequest);

        if (!Utils::ping_payu()) {
            return response('PayU is not available', Response::HTTP_BAD_REQUEST);
        }

        return $next($newRequest);
    }
}
