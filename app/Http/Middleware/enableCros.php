<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class enableCros
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
        return $next($request)
            // ->header('Access-Control-Allow-Origin', ['113.164.176.24:80', '127.0.0.1:80'])
            ->header('Accept', '*/*')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET,POST')
            ->header('Access-Control-Allow-Hearders', '*');
    }
}
