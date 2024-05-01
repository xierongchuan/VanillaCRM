<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
		/**
		 * Get the path the admin should be redirected to when they are not authenticated.
		 */
		protected function redirectTo(Request $request): ?string
		{
				return $request->expectsJson() ? null : route('auth.sign_in');
		}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)
     */
    public function handle(Request $request, Closure $next): Response
    {
				if (Auth::check() && Auth::user()->role === 'admin') {
						return $next($request);
				}

				abort(403, 'Доступ запрещен');
    }
}
