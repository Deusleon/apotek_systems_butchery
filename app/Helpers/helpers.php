<?php

use App\Store;

if (!function_exists('current_store_id')) {
    function current_store_id() {
        $id = session('current_store_id', null);
        if ($id !== null) {
            return (int)$id;
        }

        if (function_exists('auth') && auth()->check()) {
            $user = auth()->user();

            $store = $user->store;

            if ($store) {
                $derived = (int) $store->id;

                session([
                    'current_store_id' => $derived,
                    'store' => $store->name,
                ]);

                return $derived;
            }
        }

        return null;
    }
}

if (!function_exists('is_all_store')) {
    function is_all_store() {
        $id = current_store_id();
        if ($id === null) return false;

        $allId = 1;
        return $id === $allId;
    }
}

if (!function_exists('current_store')) {
    function current_store() {
        $id = current_store_id();
        if ($id === null) return null;

        return Store::find($id);
    }
}
