<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CharsetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $contentType = $response->headers->get('Content-Type');

        if (str_contains($contentType, 'application/json') || str_contains($contentType, 'text/')) {
            $response->headers->set('Content-Type', $contentType . '; charset=UTF-8');
        }

        return $response;
    }
}
