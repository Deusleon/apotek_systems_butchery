<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('userCan')) {
    function userCan($permission_name)
    {
        $user = auth()->user();
        if (!$user) return false;

        $permissions = DB::table('permissions as p')
            ->join('role_has_permissions as rhp', 'p.id', '=', 'rhp.permission_id')
            ->join('roles as r', 'r.id', '=', 'rhp.role_id')
            ->whereRaw('LOWER(r.name) = ?', [strtolower($user->position)])
            ->pluck('p.name')
            ->toArray();

        return in_array($permission_name, $permissions);
    }
}
