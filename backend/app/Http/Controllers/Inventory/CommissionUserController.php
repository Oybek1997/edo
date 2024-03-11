<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Inventory\MainUserAddress;
use App\Http\Models\Inventory\Item;
use App\Http\Models\Inventory\Warehouse;
use App\User;
use App\Http\Models\Inventory\CommissionUser;
use App\Http\Models\Employee;
use App\Http\Models\Inventory\Address;
use App\Http\Models\Inventory\Eo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class CommissionUserController extends Controller
{   
    public function changeParol(Request $request)
    {
        $mmm = CommissionUser::select()->get();
        foreach ($mmm as $value) {
            $model = CommissionUser::where('tab', $value->tab)->where('is_active', true)->first();
            $employee = Employee::where('tabel', 'like', $model->tab)->first();
            $model->employee_id = $employee ? $employee->id : '0';
            $model->firstname = $model->firstname == null ? ($employee ? $employee->firstname_uz_latin : '0') : $employee ? $employee->firstname_uz_latin : '1';
            $model->password = Hash::make($model->employee_id);
            $model->save();
        }
        return $mmm;
    }
    public function getUser(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('filter')['search'] ?? null;
        $comCuser = CommissionUser::query();
        if ($search !== null) {
            $comCuser->where(function ($query) use ($search) {
                $query->where('tab','ilike', $search)
                    ->orWhere('lastname', 'ilike',$search)
                    ->orWhere('firstname', 'ilike',$search)
                    ->orWhere('commission_number', 'ilike',$search);
            });
        }
        // ->select('id','is_active','lastname','tab','updated_at','updated_by','user_name','commission_number','created_at','created_by','employee_id','employee_id')
        $comCusers=$comCuser->get();
        $comCuserQuery = $comCuser->toBase(); // Get the query builder from the original model

        $is_active_true = $comCuserQuery->where('is_active', true)->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);
        $is_active_false = $comCusers->where('is_active', '!=', true)->values();
        
        return [
            'is_active' => $is_active_true,
            'isnot_active' => $is_active_false,
        ];
    }
    public function cheackUser(Request $request)
    {
        $employee = Employee::where('tabel', $request->input('tabel'))->first();
        if ($employee) {
            return [
                'data' => [
                    'tabel' => $employee->tabel,
                    'lastname' => $employee->lastname_uz_latin,
                    'firstname' => $employee->firstname_uz_latin,
                    'employee_id' => $employee->id,
                ],
                'message' => 'Found User',
                'code' => '200',
            ];
        } else {
            return [
                'data' => null,
                'message' => 'User not found in EDO system',
                'code' => '404',
            ];
        }
    }

    public function createUser(Request $request)
    {
        $tabN = $request->input('form')['tabel'];
        $employee = Employee::where('tabel', $tabN)->first();
        if ($employee) {
            $comUser = CommissionUser::where('employee_id', $employee->id)->where('is_active', '=', true)->first();
            if ($comUser) {
                return [
                    'data' => [
                        'tabel' => $comUser->tab,
                        'lastname' => $comUser->lastname,
                        'firstname' => $comUser->firstname,
                        'username' => $comUser->user_name,
                        'commissionnumber' => $comUser->commission_number,
                    ],
                    'message' => 'Found User',
                    'code' => '200',
                ];
            } else {
                $res = $this->UpdateUser($request);
                return [
                    'data' => [
                        'tabel' => $res['data']['tabel'],
                        'lastname' => $res['data']['lastname'],
                        'firstname' => $res['data']['firstname'],
                        'username' => $res['data']['username'],
                        'commissionnumber' => $res['data']['commissionnumber'],
                    ],
                    'message' => 'Successful Created',
                    'code' => '201',
                ];
            }
        } else {
            return [
                'data' => null,
                'message' => 'User not found in EDO system',
                'code' => '404',
            ];
        }
    }
    public function changeManual(Request $request)
    {
        $tabN = $request->input('form')['tabel'];
        $manualStatus = $request->input('form')['manual'];
        $comUser = CommissionUser::where('tab', $tabN)->where('is_active', '=', true)->first();
        $comUser->manual=$manualStatus;
        $comUser->save();
        return 1;
    }
    public function UpdateUser(Request $request)
    {
        $tabN = $request->input('form')['tabel'];
        $commissionNumber = $request->input('form')['commissionnumber'];
        $isManual = $request->input('form')['manual'];
        $isManual = $isManual!=null?$isManual:0;
        $employee = Employee::where('tabel', $tabN)->first();
        $comUser = CommissionUser::where('employee_id', $employee->id)->where('is_active', '=', true)->first();
        // $mainAddress=MainUserAddress::get();
        if ($comUser) {
            $comUser->updated_by = Auth::id();
            $comUser->updated_at = date('Y-m-d H:i:s');
            $comUser->is_active = false;
            $comUser->save();
        }
        $model = new CommissionUser();
        $model->employee_id = $employee->id;
        $model->tab = $employee->tabel;
        $model->lastname = $employee->lastname_uz_latin;
        $model->firstname = $employee->firstname_uz_latin;
        $model->commission_number = $commissionNumber;
        $model->manual = $isManual;
        $model->user_name = substr($model->firstname, 0, 1) . substr($model->lastname, 0, 1) . $model->tab;
        $model->password = $comUser->password ?? Hash::make("@12345");
        $model->created_by = Auth::id();
        $model->created_at = date('Y-m-d H:i:s');
        $model->is_active = true;
        $model->save();
        return [
            'data' => [
                'tabel' => $model->tab,
                'lastname' => $model->lastname,
                'firstname' => $model->firstname,
                'username' => $model->user_name,
                'commissionnumber' => $model->commission_number,
            ],
            'message' => 'Successful Created',
            'code' => '201',
        ];
    }
    public function changePassword(Request $request)
    {
        $filter = $request->input('filter');
        $id = $filter['itemID'];
        $password = $filter['pass'];
        $model = CommissionUser::where('id', $id)->where('is_active', '=', true)->first();
        $model->password = Hash::make($password);
        $model->save();
        return [
            'data' => $model,            
            'message' => 'Successful changed',
            'code' => '200',
        ];
    }   
    public function userCheck1(Request $request)
    {
        if ($request->header('token') == 'BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ') {
            $username = $request->input('username');
            $password = $request->input('password');
            $comUser = CommissionUser::where('user_name', 'ilike', ($username))->where('is_active', '=', true)
            ->with('PermissionsUsers.userPerr')
            ->first();
            if ($comUser) {
                $isMatch = Hash::check($password, $comUser->password);
                if ($isMatch) {
                    $resInfo['user_id'] = $comUser->id;
                    $resInfo['tabel'] = $comUser->tab;
                    $resInfo['username'] = $username;
                    $resInfo['firstname'] = $comUser->firstname;
                    $resInfo['lastname'] = $comUser->lastname;
                    $resInfo['manual'] = $comUser->manual;
                    $resInfo['middlename'] = '';
                    $resInfo['commission_number'] = $comUser->commission_number;
                    $resInfo['permission']=$comUser->PermissionsUsers;
                    return [
                        'message' => 'Successfully login',
                        'dateTime' => date('Y-m-d H:i:s'),
                        'code' => '200',
                        'data' => $resInfo
                    ];
                } else {
                    return [
                        'message' => 'Foydalanuvchi USER yoki Parol noto`g`ri',
                        'code' => '404',
                    ];
                }
            } else {
                return [
                    'message' => 'Foydalanuvchi EDO tizimidagi komissiya ro`yxatidan topilmadi',
                    'code' => '404',
                ];
            }
        }
        return [
            'message' => 'Access Forbidden',
            'code' => '403',
        ];
    }
    public function userCheck(Request $request)
    {
        if ($request->header('token') == 'BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ') {
            $username = $request->input('username');
            $password = $request->input('password');
            $comUser = CommissionUser::where('user_name', 'ilike', ($username))->where('is_active', '=', true)
            ->with('PermissionsUsers.userPerr')
            ->first();
            // return [ $comUser];
            if ($comUser) {
                $isMatch = Hash::check($password, $comUser->password);
                if ($isMatch) {
                    $resInfo['user_id'] = $comUser->id;
                    $resInfo['tabel'] = $comUser->tab;
                    $resInfo['username'] = $username;
                    $resInfo['firstname'] = $comUser->firstname;
                    $resInfo['lastname'] = $comUser->lastname;
                    $resInfo['manual'] = $comUser->manual;
                    $resInfo['middlename'] = '';
                    $resInfo['commission_number'] = $comUser->commission_number;
                    $resInfo['permission']=$comUser->PermissionsUsers;
                    return [
                        'message' => 'Successfully login',
                        'dateTime' => date('Y-m-d H:i:s'),
                        'code' => '200',
                        'data' => $resInfo
                    ];
                } else {
                    return [
                        'message' => 'Foydalanuvchi USER yoki Parol noto`g`ri',
                        'code' => '404',
                    ];
                }
            } else {
                return [
                    'message' => 'Foydalanuvchi EDO tizimidagi komissiya ro`yxatidan topilmadi',
                    'code' => '404',
                ];
            }
        }
        return [
            'message' => 'Access Forbidden',
            'code' => '403',
        ];
    }
   
    public function getAdInfo($username)
    {
        $user = Adldap::search()->findBy('sAMAccountname', $username);
        if ($user) {
            $res['username'] = $user->samaccountname[0];
            $res['fullname'] = $user->cn[0];
            $res['employer_id'] = $user->employeenumber[0];
            $res['mail'] = $user->mail[0];
            $distinguishedname = $user->distinguishedname[0];
            $distinguishedname = substr($distinguishedname, strpos($distinguishedname, 'DC'), 1000);
            $res['account_suffix'] = str_replace(',', '.', str_ireplace('dc=', '', $distinguishedname));
        } else {
            return false;
        }
        return $res;
    }
}
