<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DBConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Dynamically switch the default DB connection for the authenticated user.
        if (Auth::check() && session()->has('db_connection')) {
            $connection = session('db_connection');

            // Only switch if the connection is defined in config/database.php
            if (Config::has("database.connections.$connection")) {
                Config::set('database.default', $connection);

                // Purge so the new connection config is picked up immediately.
                DB::purge($connection);
            }
        }

        return $next($request);
    }
}
