<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$scope): Response
    {
        $user = $request->user();
        $abilities = $user->currentAccessToken()->abilities;
        foreach ($scope as $sc) {
            if (in_array($sc, $abilities)) {
                return $next($request);
            }
        }
        return response()->json(['message' => 'forbidden'],403);
    }
}
