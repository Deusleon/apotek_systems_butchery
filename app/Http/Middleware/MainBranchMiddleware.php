<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MainBranchMiddleware
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
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Get store_id directly from user
        $store_id = $user->store_id ?? 0;
        $routeName = $request->route()->getName();

        if($store_id == 0 && $routeName != "home") {
            DB::beginTransaction();

            $update_store = DB::table('users')
                ->where('id', $user->id)
                ->update(
                    [
                        'store_id' => 1
                    ]
                );

            $store_name = DB::table('inv_stores')->where('id',"=","1")->first()->name;

            session()->put('store', $store_name);

            DB::commit();
        }

        return $next($request);
    }
}
