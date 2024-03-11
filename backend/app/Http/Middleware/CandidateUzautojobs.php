<?php

namespace App\Http\Middleware;

use App\User;
use App\UserLog as AppUserLog;
use Closure;
use Browser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class CandidateUzautojobs
{
    public function handle($request, Closure $next)
    {
        if ($request->header('token') == 'BraveLion%TgHNSY(AZZAny*7cx3yV(3jp7dhV3Afu*Q%RE2XGerzhJsmM') {
            return $next($request);
        } else return [
            'status' => 401,
            'message' => 'Unauthorized'
        ];
    }
}
