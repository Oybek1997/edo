<?php

namespace App\Http\Middleware;

use App\User;
use App\UserLog as AppUserLog;
use Closure;
use Browser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class UserLog
{
    public function handle($request, Closure $next)
    {
        // if(parse_url($_SERVER['HTTP_REFERER'])['host'] != 'testedo.uzautomotors.com'){
        //     return false;
        // }
        //---------------------------------------------------
        // $token = Auth::user()->token();
        // $token->expires_at = now()->addminute();
        // $token->save();
        //---------------------------------------------------
        User::where('id', Auth::id())->update(['online_at' => date("Y-m-d H:i:s")]);
        $currentAction = Route::currentRouteAction();
        if ($currentAction) {
            list($controller, $method) = explode('@', $currentAction);
            $controller = preg_replace('/.*\\\/', '', $controller);
            if (
                // $method != 'getNotification' &&
                in_array($controller, [
                    'DocumentController',
                    'DocumentSignerController',
                    // 'DocumentTemplateController',
                    // 'EmployeeController',
                    // 'DocumentSignerController',
                    // 'EmployeeRelativeController',
                ])
                // &&
                // in_array($method, [
                //     'update',
                //     'indexView',
                //     'indexFilter',
                //     'showDocumentNew',
                //     'publish',
                //     'reaction',
                // ])
            ) {
                // $user->save();
                switch (true) {
                    case Browser::isDesktop():
                        $device_type = 'Desktop';
                        break;
                    case Browser::isMobile():
                        $device_type = 'Mobile';
                        break;
                    case Browser::isTablet():
                        $device_type = 'Desktop';
                        break;
                    default:
                        $device_type = 'Undefined';
                        break;
                }
                $json = json_encode($request->all());
                // if (
                //     in_array($controller, [
                //         'DocumentController',
                //         'DocumentTemplateController',
                //         'EmployeeController',
                //         'DocumentSignerController',
                //         'EmployeeRelativeController',
                //     ])
                //     &&
                //     in_array($method, [
                //         'update',
                //         'setBase64',
                //         'indexView',
                //         'indexFilter',
                //         'showDocumentNew',
                //     ])
                // ) {
                //     // Ma'lumot ko'pligi uchun baza ishib ketmasligi uchun
                //     $json = '';
                // }
                AppUserLog::create([
                    'user_id' => Auth::id(),
                    // 'operation' => Browser::platformFamily(),
                    'controller' => "-",//$controller,
                    'action' => $method,
                    'object_id' => $request->input('id'),
                    'user_ip' => $request->ip(),
                    // 'user_browser' => Browser::browserFamily(),
                    // 'user_agent' => Browser::userAgent(),
                    // 'user_browser_version' => Browser::browserVersion(),
                    // 'user_platform' => Browser::platformVersion(),
                    // 'user_device_type' => $device_type,
                    // 'request_json' => $json,
                    'url' => str_replace('https://b-edo.uzautomotors.com','',$request->url()),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return $next($request);
    }
}
