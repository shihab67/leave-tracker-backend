<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Access
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if ($request->user()->status === 'inactive' || $request->user()->status === 'suspended') {
            return response()->json(['message' => 'Your account is inactive or suspended.'], 403);
        }

        if (!in_array($request->user()->role->id, $roles)) {
            return response()->json(['message' => 'You don\'t have permission to access this resource.'], 403);
        }
        return $next($request);
    }
}
