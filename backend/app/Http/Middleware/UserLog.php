<?php

namespace App\Http\Middleware;

use App\User;
use App\Http\Models\UserLog\Action;
use App\Http\Models\UserLog\Controller;
use App\Http\Models\UserLog\IpAddress;
use App\Http\Models\UserLog\Log as Logs;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class UserLog
{
    public function handle($request, Closure $next)
    {
        $user_id = Auth::id();
        $now = date("Y-m-d H:i:s");
        try {
            User::where('id', $user_id)->update(['online_at' => $now]);
            $currentAction = Route::currentRouteAction();
            if ($currentAction) {
                list($controller_name, $method) = explode('@', $currentAction);
                $controller_name = preg_replace('/.*\\\/', '', $controller_name);
            }

            // if (Auth::id() == 1) 
            {
                $controller = Controller::firstOrCreate(['name' => $controller_name]);
                $action = Action::firstOrCreate(['name' => $method]);
                $ip_address = IpAddress::firstOrCreate(['name' => $request->ip()]);
                Logs::create(
                    [
                        'user_id' => $user_id,
                        'ip_address_id' => $ip_address->id,
                        'controller_id' => $controller->id,
                        'action_id' => $action->id,
                        'created_at' => $now
                    ]
                );
            }
        } catch (\Throwable $th) {
            //throw $th;
            if (Auth::id() == 1) {
                dd($th->getMessage());
            }
        }
        return $next($request);
    }
}
