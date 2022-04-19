<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $addHttpCookie = true;

    protected $except = [
        'http://192.168.20.26:8000/api/file',
        'http://192.168.20.26:8000/api/file/status',
        'http://192.168.20.28:8000/api/file',
        'http://192.168.20.28:8000/api/file/status',
        'http://192.168.20.41:8000/api/file',
        'http://192.168.20.41:8000/api/file/status'
    ];
}
