<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogFileUpload
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Request reaching preview route', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'files' => $request->allFiles(),
            'post_size' => $request->server('CONTENT_LENGTH'),
            'max_post_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize')
        ]);

        return $next($request);
    }
} 