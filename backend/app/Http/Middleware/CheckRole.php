<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role1 = '', $role2 = '', $role3 = '', $role4 = '')
    {
        $user = Auth::user();

        if (!!$user) {
            if ($role1 !== '' && $user->hasRole($role1))
                return $next($request);
            if ($role2 !== '' && $user->hasRole($role2))
            return $next($request);
            if ($role3 !== '' && $user->hasRole($role3))
            return $next($request);
            if ($role4 !== '' && $user->hasRole($role4))
            return $next($request);

            return response("Access forbitten!",403);
        } else {
            return response("Unauthorized Action", 401);
        }
    }
}
