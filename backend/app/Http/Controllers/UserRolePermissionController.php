<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Models\Employee;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;

class UserRolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
                ->with('roles')->with('permissions');

        if (isset($filter['username'])) {
            $users->where('username', 'like', '%'.$filter['username'].'%');
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
        if (isset($filter['permission'])) {
            $users->whereHas('permissions', function ($q) use ($filter) {
                $q->where('name', 'like', "%" . $filter['permission'] . "%");
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
        return ['users' => $users->select('users.*')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['users.id'], 'page name', $page)];
    
    }

    
}
