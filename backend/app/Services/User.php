<?php

namespace App\Services;

use App\MailQueue;
use Illuminate\Support\Facades\Auth;

class User
{
    public static function hrRoles()
    {
        $branches = [1,2,3,4,5];
        $user = Auth::user();
        if (!$user->hasRole('hr')) {
            if (!$user->hasRole('hr_asaka')) {
                if (($key = array_search(1, $branches)) !== false) {
                    unset($branches[$key]);
                }
            }
            if (!$user->hasRole('hr_toshkent_skd')) {
                if (($key = array_search(2, $branches)) !== false) {
                    unset($branches[$key]);
                }
            }
            if (!$user->hasRole('hr_xorazm')) {
                if (($key = array_search(3, $branches)) !== false) {
                    unset($branches[$key]);
                }
            }
            if (!$user->hasRole('hr_toshkent_ofis')) {
                if (($key = array_search(4, $branches)) !== false) {
                    unset($branches[$key]);
                }
            }
            if (!$user->hasRole('hr_angren')) {
                if (($key = array_search(5, $branches)) !== false) {
                    unset($branches[$key]);
                }
            }
        }
        return $branches;
    }
}
