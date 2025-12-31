<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;  // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AdminMiddleware: check=' . Auth::check() . ', user=' . (Auth::check() ? Auth::user()->role : 'none'));

        if (!Auth::check() || !Auth::user()->isAdmin()) {
            Log::info('AdminMiddleware: redirecting');
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/')->with('error', 'Access denied. Admin only.');
        }

        return $next($request);
    }
}
