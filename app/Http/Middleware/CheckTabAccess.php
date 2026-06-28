<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTabAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $tabSlug): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->canAccess($tabSlug)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')
                ->withErrors(['access' => 'You do not have access to that section.']);
        }

        return $next($request);
    }
}
