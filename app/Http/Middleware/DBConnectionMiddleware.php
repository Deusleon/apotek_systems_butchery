<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DBConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $config = Config::get('database.connections.' . session()->get('db_connection'));
            $config['database'] = session()->get('db_connection');
            $config['password'] = "";
            config()->set('database.default', session()->get('db_connection'));
            config()->set('database.connections.' . session()->get('db_connection'), $config);
        }
        return $next($request);
    }
}
