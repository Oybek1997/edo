<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    
    protected $except = [
    'get-file',
    'get-captcha',
    'backend-mobile-verify',
    'documents/transport-request-completed',
    'inventory/mobile'
  ];
}
