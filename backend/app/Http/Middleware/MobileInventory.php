<?php

namespace App\Http\Middleware;

use App\User;
use App\UserLog as AppUserLog;
use Closure;
use Browser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class MobileInventory
{
    public function handle($request, Closure $next)
    {
        if ($request->header('token') == 'BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ') {
            return $next($request);
        } else return [
            'status' => 401,
            'message' => 'Unauthorized'
        ];
    }
}
