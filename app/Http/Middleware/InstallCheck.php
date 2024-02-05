<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstallCheck
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
        if (empty(env('DB_HOST')) || empty(env('DB_CONNECTION')) || empty(env('DB_PORT')) || empty(env('DB_DATABASE')) || empty(env('DB_USERNAME')) || empty(env('DB_PASSWORD'))) {
            return redirect()->route('install.step.one');
        }

        return $next($request);
    }
}
