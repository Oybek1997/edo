<?php

namespace App\Http\Controllers;

use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Department;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Employee;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //Show
    public function show(Request $request)
    {
        $valid_to = $request->input('valid_to');
        if($valid_to){
            $user = Auth::user();
            $user->eimzo_expire_date = date('Y-m-d',strtotime($valid_to));
            $user->save();
        }
        return User::with('roles.permissions:name')->with('permissions:name')
        ->with('employee.company')
        ->with('employee.country')
        ->with('employee.nationality')
        ->with('employee.region')
        ->with('employee.district')
        ->with(['employee.employeeStaff' => function ($query) {
            $query->with('tariffScale')
                ->with(['staff' => function ($staffQuery) {
                    $staffQuery->with('position')->with('department');
                }])
                ->where('is_active', '=', 1);
        }])
        ->with(['employee.employeeCoefficients' => function ($q) {
            $q->with('coefficient');
        }])
        ->with(['employee.employeeAddresses' => function ($q) {
            $q->with('country')
                ->with('region')
                ->with('district');
        }])
        ->with('employee.employeePhones')
        ->with(['employee.employeeOfficialDocument' => function ($q) {
            $q->with('officialDocumentType');
        }])
        ->where('id', Auth::id())->first();
    }

    //Eimzo Push
    public function eimzoPush(Request $request)
    {
        $user = User::find(Auth::id());
        if ($user) {
            $user->eimzo_username = $request->input('eimzo_username');
            $user->eimzo_name = $request->input('eimzo_name');
            $user->eimzo_password = $request->input('eimzo_password');
            $user->save();
            return $user;
        }
        return false;
    }

    //index
    public function index()
    {
        $users = User::with(['employee' => function ($query) {
            $query->withTrashed();
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }])
                // ->with('employee.staff')
                // ->with('employee.staff.department')
                // ->with('employee.employeeStaff.staff.department')
                // ->with('employee.employeeStaff.staff.position')
                ->with('roles')
                ->orderBy('id', 'asc')
                ->with('roles.permissions')->get();
        return ['users' => $users, 'employees' => '', 'roles' => Role::get()];
    }

    public function indexFilter(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $search = $request->input('search');
        $users = User::with(['employee' => function ($query) {
            $query->withTrashed();
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }])
                // ->with('employee.staff')
                // ->with('employee.staff.department')
                // ->with('employee.employeeStaff.staff.department')
                // ->with('employee.employeeStaff.staff.position')
                ->with('roles')->with('permissions');

        if (isset($filter['username'])) {
            $users->where('username', 'ilike', '%'.$filter['username'].'%');
        }

        if (isset($filter['department_code'])) {
            $users->whereHas('employee.employeeStaff.staff.department', function ($q) use ($filter) {
                $q->where('department_code', 'like', "%" . $filter['department_code'] . "%");
            });
        }

        if (isset($filter['department_name'])) {
            $users->whereHas('employee.employeeStaff.staff.department', function ($q) use ($filter) {
                $q->where('name_uz_latin', 'like', "%" . $filter['department_name'] . "%");
            });
        }

        if (isset($filter['role'])) {
            $users->whereHas('roles', function ($q) use ($filter) {
                $q->where('name', 'like', "%" . $filter['role'] . "%");
            });
        }

        if (isset($filter['position'])) {
            $users->whereHas('employee.employeeStaff.staff.position', function ($q) use ($filter) {
                $q->where('name_uz_latin', 'like', "%" . $filter['position'] . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $filter['position'] . "%")
                    ->orWhere('name_ru', 'like', "%" . $filter['position'] . "%");
            });
        }

        if (isset($filter['employee'])) {
            $users->whereHas('employee', function ($q) use ($filter) {
                $q->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['employee'] . "%");
            });
        }
        return ['users' => $users->select('users.*')->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['users.id'], 'page name', $page), 'roles' => Role::orderBy('name')->get(),
                'permissions' => Permission::orderBy('name')->get()];
    }
    //indexFilter for Dillers and Companies
    public function indexFilterDillers(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $search = $request->input('search');
        $users = User::with(['employee' => function ($query) {
            $query->withTrashed();
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }])->whereIn('type', ['K', 'D'])
                // ->with('employee.staff')
                // ->with('employee.staff.department')
                // ->with('employee.employeeStaff.staff.department')
                // ->with('employee.employeeStaff.staff.position')
                ->with('roles')->with('permissions');

        if (isset($filter['username'])) {
            $users->where('username', 'like', '%'.$filter['username'].'%');
        }
        if (isset($filter['type'])) {
            $users->where('type', $filter['type']);
        }

        if (isset($filter['department_code'])) {
            $users->whereHas('employee.employeeStaff.staff.department', function ($q) use ($filter) {
                $q->where('department_code', 'like', "%" . $filter['department_code'] . "%");
            });
        }

        if (isset($filter['department_name'])) {
            $users->whereHas('employee.employeeStaff.staff.department', function ($q) use ($filter) {
                $q->where('name_uz_latin', 'like', "%" . $filter['department_name'] . "%");
            });
        }

        if (isset($filter['role'])) {
            $users->whereHas('roles', function ($q) use ($filter) {
                $q->where('name', 'like', "%" . $filter['role'] . "%");
            });
        }

        if (isset($filter['position'])) {
            $users->whereHas('employee.employeeStaff.staff.position', function ($q) use ($filter) {
                $q->where('name_uz_latin', 'like', "%" . $filter['position'] . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $filter['position'] . "%")
                    ->orWhere('name_ru', 'like', "%" . $filter['position'] . "%");
            });
        }

        if (isset($filter['employee'])) {
            $users->whereHas('employee', function ($q) use ($filter) {
                $q->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['employee'] . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['employee'] . "%");
            });
        }
        return ['users' => $users->select('users.*')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['users.id'], 'page name', $page), 'roles' => Role::orderBy('name')->get(),
                'permissions' => Permission::orderBy('name')->get()];
    }
    
    //indexDeleted 26.08.2020
    public function indexDeleted(Request $request)
    {
        $filter = $request->input('filter');
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

        $employee = Employee::onlyTrashed()
                ->with('employeeStaff')
                ->with('staff.department')
                ->with('staff.position')
                ->with('employeeStaff.leavingReasons');
        return $employee->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }


    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $locale = $request->input('locale');
        $perPage = $request->input('perPage');
        $users = User::with(['employee' => function ($query) {
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }])
            ->select('users.*')
            ->leftJoin('employees', 'employees.id', '=', 'users.employee_id')
            ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
            ->leftJoin('staff', 'staff.id', '=', 'employee_staff.staff_id')
            ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
            ->where('employee_staff.deleted_at')
            ->orderByRaw('departments.department_code ASC')
            ->leftJoin('positions', 'positions.id', '=', 'staff.position_id')
            ->paginate($perPage, ['*'], 'page name', $page);
        $excel = [];
        $username = '';
        $name = '';
        $position = '';
        $department_code = '';
        $department = '';
        $eri = '';
        foreach ($users as $key => $value) {
            $username = $value->username ? $value->username : '';
            $name = $value->employee ? $value->employee->firstname_uz_cyril . ' ' . $value->employee->middlename_uz_cyril .  ' ' . $value->employee->lastname_uz_cyril : '';
            $eri = $value->eimzo_username && $value->eimzo_username != null ? 1 : 0;
            foreach ($value->employee->employeeStaff as $key_codes => $value_codes) {
                if ($value_codes->is_main_staff == 1) {
                    $department_code = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department->department_code : '';
                    $department = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department['name_' . $locale] : '';
                    $position = $value_codes->staff && $value_codes->staff->position ? $value_codes->staff->position['name_' . $locale] : "";
                }
            }
            array_push($excel, (object)[
                "№" => $key + 1 + $page * $perPage - $perPage,
                "Код подразделения" => $department_code,
                "Табелный номер" =>  $username,
                "Сотрудник" =>  $name,
                "Подразделения" => $department,
                "Должность" => $position,
                "ЭРИ" => $eri,
            ]);
        }
        return $excel;
    }



    public function indexSearch(Request $request)
    {
        //
        $filter = $request->input('filter');
        $search = $request->input('search');
        $employees = Employee::select();

        if (isset($search)) {
            $employees->where(function (Builder $query) use ($search) {
                return $query
                            ->where('firstname_uz_latin', 'like', "%".$search."%")
                            ->orWhere('firstname_uz_cyril', 'like', "%".$search."%")
                            ->orWhere('middlename_uz_latin', 'like', "%".$search."%")
                            ->orWhere('middlename_uz_cyril', 'like', "%".$search."%")
                            ->orWhere('lastname_uz_latin', 'like', "%".$search."%")
                            ->orWhere('lastname_uz_cyril', 'like', "%".$search."%")
                            // ->orWhere('gender', 'like', "%".$search."%")
                            // ->orWhere('nationality_id', $search."%")
                            // ->orWhere('INPS', 'like', '%'.$search.'%')
                            // ->orWhere('INN', 'like', '%'.$search.'%')
                            ->orWhere('born_date', 'like', '%'.$search."%")
                            ->orWhere('tabel', 'like', '%'.$search.'%');
            });
        } else {
            if (isset($filter['username'])) {
                $employees->where(function (Builder $query) use ($filter) {
                    return $query->where('firstname_uz_latin', 'like', "%".$filter['username']."%")
                                                ->orWhere('firstname_uz_cyril', 'like', "%".$filter['username']."%");
                });
            }
        }
        return $employees->paginate(20);
    }
    public function indexSearchUser(Request $request)
    {
        //
        $filter = $request->input('filter');
        $search = $request->input('search');
        $employees = User::with(['employee' => function ($query){
            $query->withTrashed();
        }]);
        if (isset($search)) {
            $employees->where(function (Builder $query) use ($search) {
                return $query
                            ->whereHas('employee', function($q) use($search){
                                $q->where(DB::raw("concat(tabel, ' ', employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
                                    ->orWhere(DB::raw("concat(tabel, ' ', employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%")
                                    ->orWhere(DB::raw("concat(tabel, ' ', employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
                                    ->orWhere(DB::raw("concat(tabel, ' ', employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%");
                            })
                            ->orWhere('username', 'like', "%".$search."%");
                            // ->orWhere('tabel', 'like', '%'.$search.'%');
            });
        } else {
            if (isset($filter['username'])) {
                $employees->where(function (Builder $query) use ($filter) {
                    return $query
                    ->where('username', 'like', "%".$filter['username']."%");
                    // ->orWhere('tabel', 'like', "%".$filter['username']."%");
                });
            }
        }
        return $employees->paginate(20);
    }

    public function update(Request $request)
    {
        $user = User::find($request->input('id'));
        if (!$user) {
            $user = new User();
            $user->created_by= Auth::id();
        } else {
            $user->updated_by = Auth::id();
        }
        $user->username = $request->input('username');
        if($request->input('password') != ''){
            $user->password = Hash::make($request->input('password'));
        }
        $user->employee_id = $request->input('employee_id');
        try {
            $adUser = Adldap::search()->where('sAMAccountname', $user->username)->first();
            if ($adUser) {
                $search = json_encode($adUser);
                $search = json_decode($search);
                $mail = $search->mail[0];
                $user->email = $mail;
            }
        } catch (\Throwable $th) {
            Log::error('AD user email not fount for username:'.$user->username);
        }
        $user->save();
        $roles = $request->input('roles');
        $permissions = $request->input('permissions');
        $user->detachPermissions();
        foreach ($permissions as $pkey => $permission) {
            $user->permissions()->attach($permission['id']);
        }
        $user->detachRoles();
        foreach ($roles as $rkey => $role) {
            $user->roles()->attach($role['id']);
        }
        return $user;
    }

    public function indexView(Request $request)
    {
        $search = $request->all();
        $users = User::with(['employee' => function ($query) {
            $query->withTrashed();
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }])
                // ->with('employee.staff')
                // ->with('employee.staff.department')
                // ->with('employee.employeeStaff.staff.department')
                // ->with('employee.employeeStaff.staff.position')
                ->with('roles')
                ->with('roles.permissions');

        if ($search['username']) {
            $users->where('username', 'like', '%'.$search['username'].'%');
        }

        return ['users' => $users->get(), 'employees' => '', 'roles' => Role::get()];



        //
        // $filter = $request->input('filter');
        // $search = $request->input('search');
        // $page = $request->input('pagination')['page'];
        // $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        // $employees = User::with('company')
        //                 ->with('employee')
        //                 ->with('country')
        //                 ->with('nationality')
        //                 ->with('region')
        //                 ->with('district')
        //                 ->with('employeeStaff')
        //                 ->with('employeeStaff.tariffScale')
        //                 ->with('employeeStaff.staff')
        //                 ->with('employeeStaff.staff.position')
        //                 ->with('employeeStaff.staff.department')
        //                 ->with('employeeCoefficients')
        //                 ->with('employeeCoefficients.coefficient')
        //                 ->with('employeeAddresses')
        //                 ->with('employeeAddresses.country')
        //                 ->with('employeeAddresses.region')
        //                 ->with('employeeAddresses.district')
        //                 ->with('employeePhones')
        //                 ->with('employeeOfficialDocument.officialDocumentType')
        //     ->select('employees.*, employee_staff.deleted_at')
        //     ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
        //     ->leftJoin('staff', 'staff.id', '=', 'employee_staff.staff_id')
        //     ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
        //     ->where('employee_staff.deleted_at')
        //     ->orderByRaw('departments.department_code ASC')
            // ->where('tabel', 'not like', "h%")
            // ->where('tabel', 'not like', "x%")
            // ->where('tabel', 'not like', "z%")
        // ;

        // if (isset($search)) {
        //     $employees->where(function (Builder $query) use ($search) {
        //         return $query
        //                     // ->where('firstname_uz_latin', 'like', "%".$search."%")
        //                     // ->orWhere('firstname_uz_cyril', 'like', "%".$search."%")
        //                     // ->orWhere('middlename_uz_latin', 'like', "%".$search."%")
        //                     // ->orWhere('middlename_uz_cyril', 'like', "%".$search."%")
        //                     // ->orWhere('lastname_uz_latin', 'like', "%".$search."%")
        //                     // ->orWhere('lastname_uz_cyril', 'like', "%".$search."%")
        //                     // ->orWhere('gender', 'like', "%".$search."%")
        //                     // ->orWhere('nationality_id', $search."%")
        //                     // ->orWhere('INPS', 'like', '%'.$search.'%')
        //                     // ->orWhere('INN', 'like', '%'.$search.'%')
        //                     // ->orWhere('born_date', 'like', '%'.$search."%")
        //                     // ->orWhere('tabel', 'like', '%'.$search.'%');
        //     });
        // } else {
        //     if (isset($filter['username'])) {
        //         $employees->where(function (Builder $query) use ($filter) {
        //             return $query->where('username', 'like', "%".$filter['username']."%");
        //         });
        //     }
            // if (isset($filter['middlename'])) {
            //     $employees->where(function (Builder $query) use ($filter) {
            //         return $query->where('middlename_uz_latin', 'like', "%".addslashes($filter['middlename'])."%")
            //                                     ->orWhere('middlename_uz_cyril', 'like', "%".$filter['middlename']."%");
            //     });
            // }
            // if (isset($filter['lastname'])) {
            //     $employees->where(function (Builder $query) use ($filter) {
            //         return $query->where('lastname_uz_latin', 'like', "%".$filter['lastname']."%")
            //                                     ->orWhere('lastname_uz_cyril', 'like', "%".$filter['lastname']."%");
            //     });
            // }

            // if (isset($filter['gender'])) {
            //     $employees->where(function (Builder $query) use ($filter) {
            //         return $query->where('gender', 'like', "%".$filter['gender']."%");
            //     });
            // }
            // if (isset($filter['nationality_id'])) {
            //     $employees->where('nationality_id', $filter['nationality_id']);
            // }
            // if (isset($filter['tabel'])) {
            //     $employees->where('tabel', 'like', '%'.$filter['tabel'].'%');
            // }
            // if (isset($filter['INPS'])) {
            //     $employees->where('INPS', 'like', '%'.$filter['INPS'].'%');
            // }
            // if (isset($filter['INN'])) {
            //     $employees->where('INN', 'like', '%'.$filter['INN'].'%');
            // }
            // if (isset($filter['born_date_from'])) {
            //     $employees->where('born_date', '>=', $filter['born_date_from']);
            // }
            // if (isset($filter['born_date_to'])) {
            //     $employees->where('born_date', '<=', $filter['born_date_to']);
            // }
        // }

        // return json_encode([
        //     'employees' => $employees->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),

        // ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
    }

    public function getOnline()
    {
        $onlineUser = User::where('online_at', '>', date("Y-m-d H:i:s", time() - 60))
            ->with('employee')
            ->orderBy('online_at', 'desc');
        return $onlineUser->get();
        // return [];
    }
    public function getUserReport(Request $request)
    {
        return [
            'all_count' => ['all_count'=>0],
            'doc_eri' => ['doc_eri'=>0],
            'doc_ad' => ['doc_ad' => 0],
            'user_all_count' => ['user_all_count'=>0],
            'user_eri' => ['user_eri'=>0],
            'user_ad' => ['user_ad'=>0]
        ];

        $user_all_count = 0;
        $user_eri = 0;
        $user_ad = 0;

        $user_all_count = User::count();
        // // $user_all_count = DB::select("SELECT count('*') as user_all_count FROM `users`");

        $user_eri = User::whereNotNull('eimzo_username')->count();
        // // $user_eri = DB::select("SELECT COUNT('*') as user_eri FROM `users` WHERE eimzo_username is not null");

        $user_ad = $user_all_count - $user_eri;

        // return ['user_all_count' => $user_all_count,'user_eri' => $user_eri,'user_ad' => $user_ad];

        $all_count = 0;
        $doc_eri = 0;
        $doc_ad = 0;

        $all_count = DocumentSigner::where('action_type_id', '!=', 6)->whereIn('status', [1,2])->count();
        // // $all_count = DB::select("SELECT count('*') as all_count FROM document_signers WHERE document_signers.action_type_id != 6  AND document_signers.status in(1,2)");

        $doc_ad = DocumentSigner::where('action_type_id', '!=', 6)->whereIn('status', [1,2])->where('sign_type', 0)->count();
        // // $doc_ad = DB::select("SELECT count(id) as doc_ad FROM document_signers WHERE document_signers.action_type_id != 6  AND document_signers.status in(1,2) and document_signers.sign_type=0");

        $doc_eri = $all_count - $doc_ad;


        return [
            'all_count' => ['all_count'=>$all_count],
            'doc_eri' => ['doc_eri'=>$doc_eri],
            'doc_ad' => ['doc_ad' => $doc_ad],
            'user_all_count' => ['user_all_count'=>$user_all_count],
            'user_eri' => ['user_eri'=>$user_eri],
            'user_ad' => ['user_ad'=>$user_ad]
        ];
    }

    // for Mobile EDO
    public function showMobile(Request $request )
    {   
        $locale = $request->input('locale');
        return User::select('id', 'username', 'email','employee_id')
        ->with(['employee' => function ($query) use ($locale) {
            $query->select('id', 'nationality_id', 'tabel', 'firstname_'.$locale.' as firstname', 'lastname_'.$locale.' as lastname', 'middlename_'.$locale.' as middlename', 'inn', 'inps', 'born_date')
            ->with('nationality:id,name_'.$locale.' as n_name')
            ->with(['mainStaff' => function ($query) use ($locale) {
                $query->select('position_id')
                ->with('position:id,name_'.$locale.' as p_name');
            }]);
        }]) 
        
        ->where('id', Auth::id())->first();

    }
}
