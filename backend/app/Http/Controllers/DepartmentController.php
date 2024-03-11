<?php

namespace App\Http\Controllers;

use App\DocumentAttachments;
use App\Http\Models\Branch;
use App\Http\Models\Company;
use App\Http\Models\AccessDepartment;
use App\Http\Models\Department;
use App\Http\Models\DepartmentType;
use App\Http\Models\Document;
use App\Http\Models\DocumentType;
use App\Http\Models\Employee;
use App\Http\Models\EmployeePhone;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\Staff;
use App\Services\User;
use Illuminate\Support\Facades\Http;
use App\User as AppUser;
use Facades\App\Repository\Deps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\Deprecated;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::with(['managerStaff' => function ($q) {
            $q->select('department_id', 'position_id', 'personal_type_id', 'id')
                ->with('employeeMainStaff.employee')
                ->with(['employees' => function ($q1) {
                    $q1->select(
                        'avatar_path',
                        'firstname_uz_latin',
                        'firstname_uz_cyril',
                        'firstname_ru',
                        'lastname_uz_latin',
                        'lastname_uz_cyril',
                        'lastname_ru',
                        'middlename_uz_latin',
                        'middlename_uz_cyril',
                        'middlename_ru'
                    );
                }]);
        }])->with(['employeeStaff' => function ($q2) {
            $q2->where('is_main_staff', 1)
                ->where('is_active', 1);
        }])
            ->select('id', 'manager_staff_id', 'department_code', 'name_uz_latin', 'name_uz_cyril', 'name_ru', 'parent_id');

        $avatars = Storage::allFiles('/avatars');

        return [
            'deps' => $departments->get(),
            'avatars' => $avatars
        ];
    }

    public function indexSelect(Request $request)
    {
        $locale = $request->input('locale');
        $search = $request->input('search');
        $departments = Department::select('id as value', 'name_ru', 'name_uz_latin', 'name_uz_cyril');

        if (isset($search)) {
            // return $search;
            $departments->where(function ($query) use ($search) {
                return $query
                    ->where('name_ru', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%");
            });
        }
        return $departments->paginate(20);
    }


    public function indexView(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $branches = User::hrRoles();
        $departments = Department::with('company')
            ->with('functionalDepartment')
            ->with('parent')
            ->with('functionalParent')
            ->with('branch')
            ->with('departmentType')
            ->with('managerStaff')
            ->with('managerStaff.employeeStaff')
            ->with('managerStaff.employeeStaff.employee')
            ->with('staff')
            ->with('staff.position')
            ->with('staff.range')
            ->with('staff.personalType')
            ->with('staff.expenceType')
            ->with('staff.employeeStaff.employee')
            ->leftJoin('departments as parent', 'parent.id', '=', 'departments.parent_id')
            ->whereIn('departments.branch_id', $branches)
            ->orderBy('departments.department_code', 'asc');
        if (isset($filter['department_name'])) {
            $departments->where(function (Builder $query) use ($filter) {
                return $query
                    ->where("departments.name_uz_latin", 'like', "%" . $filter['department_name'] . "%")
                    ->orWhere("departments.name_uz_cyril", 'like', "%" . $filter['department_name'] . "%")
                    ->orWhere("departments.name_ru", 'like', "%" . $filter['department_name'] . "%");
            });
        }

        if (isset($filter['parent_department_code'])) {
            $departments->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('parent.department_code', 'like', $filter['parent_department_code'] . '%')
                    ->orWhere("parent.name_ru", 'like', "%" . $filter['parent_department_code'] . "%")
                    ->orWhere(DB::raw("concat(parent.department_code,' ',parent.name_ru)"), 'like', "%" . $filter['parent_department_code'] . "%")
                    ->orWhere(DB::raw("concat(parent.department_code,' ',parent.name_uz_latin)"), 'like', "%" . $filter['parent_department_code'] . "%")
                    ->orWhere(DB::raw("concat(parent.department_code,' ',parent.name_uz_cyril)"), 'like', "%" . $filter['parent_department_code'] . "%");
            });
        }

        if (isset($filter['department_type_id'])) {
            $departments->where('departments.department_type_id', $filter['department_type_id']);
        }
        if (isset($filter['functional_department_code'])) {
            $departments->whereHas('functionalDepartment', function($q) use($filter){
                $q->where('functional_department_code', $filter['functional_department_code']);
            });
        }
        if (isset($filter['functional_department_name'])) {
            if($filter['functional_department_name'] == 0){
                $departments->whereNull('departments.functional_department_id');
            }else{
                $departments->whereHas('functionalDepartment', function($q) use($filter){
                    // $q->where('functional_department_code', $filter['functional_department_name']);
                    $q->where('name_uz_latin', 'like', "%" . $filter['functional_department_name'] . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $filter['functional_department_name'] . "%")
                    ->orWhere('name_ru', 'like', "%" . $filter['functional_department_name'] . "%");
                });
            }
        }
        if (isset($filter['department_code'])) {
            $departments->where('departments.department_code', 'like', '%' . $filter['department_code'] . '%');
        }
        if (isset($filter['branch_id'])) {
            $departments->where('departments.branch_id', $filter['branch_id']);
        }
        return [
            'departments' => $departments->select('departments.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'filter' => $filter,
        ];
    }

    public function tree()
    {
        return [
            'departments' => Department::tree(null),
            'departmentType' => DepartmentType::get(),
            'staff' => Staff::get(),
        ];
    }

    public function orgChartNew3(Request $request)
    {
        $id = $request->input('id');
        $parent_id = $request->input('parent_id');
        // if (Auth::user()->employee->tabel == 9592) 
        {
            $tree = Department::orgChartNew3($id, $parent_id, 1);
            if (!$id) {
                $tree[0]['root'] = true;
            }
            return $tree;
        }
        return [];
    }

    public function getChilds10($dep_id)
    {
        $childs = Department::select('id', 'parent_id', 'name_uz_latin', 'manager_staff_id', 'head_count', 'rate_count', 'rate_count_bp','department_code')->where('parent_id', $dep_id)->get();
        $arr = [];
        foreach ($childs as $key => $value) {
            $arr[] = $value;
            if($value->children){
                $arr = array_merge($arr,$this->getChilds10($value->id));
            }
        }
        return $arr;
    }


    public function orgCharttest10(Request $request)
    {
        $id = $request->input('id');
        $data = [];

        $parent_id = $request->input('parent_id');
        if($id==1){
            $departments = Department::select('id', 'parent_id', 'name_uz_latin', 'manager_staff_id', 'head_count', 'rate_count', 'rate_count_bp')
                                        ->where('is_active', true)
                                        ->whereNotIn('parent_id', [19,36,38, 43,49, 58,822,841, 1129,1131,1156,1671])
                                        ->whereNotIn('department_type_id', [13,14])
                                        ->whereNotIn('id', [837,1125,1490])
                                        ->orWhereNull('parent_id')
                                        ->whereNotIn('id', [837,1125,1490])
                                        ->whereNotIn('department_type_id', [13,14])->get();
        }
        else{
            $departments = Department::select('id', 'parent_id', 'name_uz_latin', 'manager_staff_id', 'head_count', 'rate_count', 'rate_count_bp')->find($id);
            $departments = array_merge([$departments],$this->getChilds10($id));
            // return $departments;
        }
        // $departments = $departments->get();
        // return $departments;

        foreach ($departments as $key => $value) {
            $data[$key]['id'] = $value->id;
            $data[$key]['name'] = $value->name_uz_latin;
            $data[$key]['parentId'] = $value->parent_id;
            if($id==$value->id){
                $data[$key]['parentId'] = null;
            }
            $data[$key]['head_count'] = $value->head_count;
            $data[$key]['rate_bp'] = $value->rate_count_bp;
            $data[$key]['rate_count'] = $value->rate_count;
            // $data[$key]['root'] = false;
            // $data[$key]['expand'] = false;

            // if ($step == 1) {
            //     $data[$key]['children'] = Department::orgChartNew($value->id, 2);
            // } else {
            //     $data[$key]['children'] = [];
            // }

            if ($staff = $value->managerStaff) {
                $data[$key]['position'] = $staff->position->name_uz_latin ? $staff->position->name_uz_latin: '-';
                $data[$key]['range'] = $staff->range ? $staff->range->code: '-';
                $data[$key]['tabel'] = 'avatar';
                $data[$key]['tariff'] = '-';
                try {
                    $employee = $staff->managerEmployee[0];
                    $data[$key]['manager'] = $employee ? $employee->fi() : '';
                    $data[$key]['tariff'] = $employee ? $employee->tariffScale->category : '';
                    if ($employee && (file_exists(storage_path('app/avatars/' . $employee->tabel . '.JPG')) || file_exists(storage_path('app/avatars/' . $employee->tabel . '.jpg')))) {
                        $data[$key]['tabel'] = $employee->tabel;
                    } else {
                        $data[$key]['tabel'] = 'avatar';
                    }
                } catch (\Throwable $th) {
                    $data[$key]['manager'] = '';
                    $data[$key]['tabel'] = 'avatar';
                }
            }
        }
        // if ($id == null) {
        //     $departments->where('id', 1);
        // } else {
        //     $departments->where('parent_id', $id);
        // }

        // $departments = $departments->get();
        return $data;
    }

    public function orgChartNew2(Request $request)
    {
        $id = $request->input('id');
        $parent_id = $request->input('parent_id');
        // if (Auth::user()->employee->tabel == 9592) 
        {
            $tree = Department::orgChartNew2($id, $parent_id, 1);
            if (!$id) {
                $tree[0]['root'] = true;
            }
            return $tree;
        }
        return [];
    }

    public function orgChartNew(Request $request)
    {
        $id = $request->id;
        // if (Auth::user()->employee->tabel == 9592) 
        {
            return Department::orgChartNew($id, 1);
        }
        return [];
    }

    public function orgChart(Request $request)
    {
        $id = $request->id;
        // if (Auth::user()->employee->tabel == 9592) 
        {
            return Department::orgChart($id, 1);
        }
        return [];
    }

    public function getDocuments(Request $request)
    {
        $id = $request->id;

        $documents = Document::select('document_number', 'id', 'document_date', 'pdf_file_name')
            ->where('department2_id', $id)
            ->get();
        $staff_documents = Document::select('document_number', 'id', 'document_date', 'pdf_file_name')
            ->whereHas('staff', function ($q) use ($id) {
                $q->where('department_id', $id);
            })
            ->get();
        return [$documents, $staff_documents];
    }

    public function tree1()
    {
        return Department::tree1(null);
    }

    public function getRef()
    {
        $departments = Department::get();
        return [
            'staff' =>  Staff::get(),
            'departmentType' => DepartmentType::get(),
            'deplists' => $departments,
            'branches' => Branch::get()
        ];
    }

    public function getParentDepartment($tabel)
    {
        return Employee::parentDepartments($tabel);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentAttachments  $documentAttachments
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request)
    // {
    //     $model = Department::find($request->input('id'));
    //     if (!$model) {
    //         $model = new Department();
    //         $model->created_by = Auth::id();
    //     } else {
    //         $model->updated_by = Auth::id();
    //     }

    //     $departmentId = $model->id;
    //     $departmentModel = Department::where('id', '=', $departmentId)->first();


    //     $model->company_id = Auth::user()->employee->company_id;
    //     $model->parent_id = $request->input('parent_id');
    //     $model->functional_parent_id = $request->input('functional_parent_id');
    //     $model->branch_id = $request->input('branch_id');
    //     $model->department_type_id = $request->input('department_type_id');
    //     $model->manager_staff_id = $request->input('manager_staff_id');
    //     $model->department_code = $request->input('department_code');
    //     $model->name_uz_latin = $request->input('name_uz_latin');
    //     $model->name_uz_cyril = $request->input('name_uz_cyril');
    //     $model->name_ru = $request->input('name_ru');
    //     $model->save();

    //     $data = [
    //         'dep_code' => $model->department_code,
    //         'dep_name' => preg_replace('/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu', '', $model->name_uz_latin),
    //     ];
    //     $response = Http::post('http://edo-db2.uzautomotors.com/api/new-department', $data);
    //     dd($response->body());


    //     // return  'Connected Staff Exist to this Department';
    //     return ['error' => 'Connected Staff Exist to this Department', 'status' => 215];
    // }

    public function update(Request $request)
    {
        $model = Department::find($request->input('id'));
        if (!$model) {
            $model = new Department();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        $model->company_id = Auth::user()->employee->company_id;
        $model->parent_id = $request->input('parent_id');
        $model->functional_parent_id = $request->input('functional_parent_id');
        $model->branch_id = $request->input('branch_id');
        $model->department_type_id = $request->input('department_type_id');
        $model->functional_department_id = $request->input('functional_department_id');
        $model->manager_staff_id = $request->input('manager_staff_id');
        $model->department_code = $request->input('department_code');
        $model->name_uz_latin = $request->input('name_uz_latin');
        $model->name_uz_cyril = $request->input('name_uz_cyril');
        $model->name_ru = $request->input('name_ru');
        // $model->is_active = $request->input('is_active') ? 1 : 0;
        $model->save();

        $data = [
            'dep_code' => $model->department_code,
            'dep_name' => preg_replace('/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu', '', $model->name_uz_latin),
        ];
        try {
            $response = Http::post('http://edo-db2.uzautomotors.com/api/new-department', $data);
            if ($response->status() == 500) {
                // dd($response->getBody()->getContents());
                // return ['error' => 'Can`t Insert to Host, Duplicate Entry', 'status' => 215];
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy($id)
    {
        // return $id;
        $departmentModel = Department::find($id);

        if (count($departmentModel->staff) == 0) {
            $department = Department::find($id);
            $department->delete();
        } else {
            return ['error' => 'Connected Staff Exist to this Department', 'status' => 215];
        }
    }

    public function getList(Request $request)
    {
        $search = $request->input('search');
        $locale = $request->input('locale');
        $department = Department::with('managerStaff.position')
            ->with('managerStaff.employees')
            ->where('department_code', '!=', "10000")
            // ->where('department_code', '!=', "20000000")
            ->join('staff', 'staff.id', '=', 'departments.manager_staff_id')
            ->join('positions', 'staff.position_id', '=', 'positions.id')
            ->join('employee_staff', 'staff.id', '=', 'employee_staff.staff_id')
            ->join('employees', 'employees.id', '=', 'employee_staff.employee_id')
            ->where('employee_staff.is_active', 1);

        if (isset($search)) {
            $department->where(function ($query) use ($search) {
                return $query
                    ->where('departments.department_code', 'ilike', $search . "%")
                    ->orWhere('departments.name_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('departments.name_uz_cyril', 'ilike', "%" . $search . "%")
                    ->orWhere('departments.name_ru', 'ilike', "%" . $search . "%")

                    ->orWhere(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril)"), 'ilike', "%" . $search . "%")

                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril)"), 'ilike', "%" . $search . "%")

                    ->orWhere('employees.lastname_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('employees.lastname_uz_cyril', 'ilike', "%" . $search . "%")

                    ->orWhere('positions.name_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('positions.name_uz_cyril', 'ilike', "%" . $search . "%")
                    ->orWhere('positions.name_ru', 'ilike', "%" . $search . "%");
            });
        }

        return $department->select([
            'departments.*',
            'departments.manager_staff_id as manager_staff_id',
            'departments.department_code as code',
            'departments.name_' . $locale . ' as department_name',
            'positions.name_' . $locale . ' as position_name',
            'employees.firstname_' . ($locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril') . ' as first_name',
            'employees.middlename_' . ($locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril') . ' as middle_name',
            'employees.lastname_' . ($locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril') . ' as last_name'
        ])->orderBy('department_code')->paginate(20);
    }

    public function changeBranch($dep_id)
    {
        $dep = Department::find($dep_id);
    }

    public function getJointVenture(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $jointVenture = Staff::whereHas('department', function ($q) {
            $q->whereHas('departmentType', function ($q1) {
                $q1->whereNotNull('type');
            });
        })
            ->select('id', 'department_id', 'position_id')
            ->with(['department' => function ($q) {
                $q->select('id', 'name_uz_latin', 'name_uz_cyril', 'name_ru', 'department_type_id', 'department_code')
                    ->with(['jointCompany' => function ($q1) {
                        $q1->select('id', 'dep_id', 'country_id', 'region_id');
                    }]);
            }])
            ->with(['employees' => function ($q) {
                $q->select(
                    'employees.id',
                    'firstname_ru',
                    'firstname_uz_cyril',
                    'firstname_uz_latin',
                    'lastname_ru',
                    'lastname_uz_cyril',
                    'lastname_uz_latin',
                    'middlename_ru',
                    'middlename_uz_cyril',
                    'middlename_uz_latin',
                    'tabel'
                )
                    ->with(['employeePhones' => function ($q1) {
                        $q1->select('id', 'employee_id', 'phone_number')
                            ->where('phone_type', 'Mobile');
                    }])
                    ->with(['user' => function ($q1) {
                        $q1->select('id', 'employee_id', 'username', 'email');
                    }]);
            }]);
        return $jointVenture->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function jointVentureUpdate(Request $request)
    {
        $department = $request->input('department');
        $employee = $request->input('employee');
        DB::beginTransaction();
        try {
            $departmentModel = Department::find($department['id']);
            if (!$departmentModel) {
                $departmentModel = new Department();
                $staff = new Staff();
                $departmentModel->created_by = Auth::user()->id;
            } else {
                $departmentModel->updated_by = Auth::user()->id;
            }
            $departmentModel->name_uz_latin = $department['name_uz_latin'];
            $departmentModel->name_uz_cyril = $department['name_uz_cyril'];
            $departmentModel->name_ru = $department['name_ru'];
            $departmentModel->company_id = 1;
            $departmentModel->department_type_id = $department['department_type_id'];
            $departmentModel->department_code = $department['department_code'];
            $departmentModel->save();

            $company = Company::where('dep_id', $departmentModel->id)->first();
            if (!$company) {
                $company = new Company();
                $company->created_by = Auth::user()->id;
                $company->dep_id = $departmentModel->id;
            } else {
                $company->updated_by = Auth::user()->id;
            }
            $company->name = $department['name_uz_latin'];
            $company->name_uz_cyril = $department['name_uz_cyril'];
            $company->name_ru = $department['name_ru'];
            $company->country_id = $department['country_id'];
            $company->region_id = $department['region_id'];
            $company->save();

            $empModel = Employee::find($employee['id']);
            if (!$empModel) {
                $empModel = new Employee();
                $empModel->created_by = Auth::user()->id;
            } else {
                $empModel->updated_by = Auth::user()->id;
            }
            $empModel->firstname_uz_cyril = $employee['firstname_uz_cyril'];
            $empModel->lastname_uz_cyril = $employee['lastname_uz_cyril'];
            $empModel->middlename_uz_cyril = isset($employee['middlename_uz_cyril']) ? $employee['middlename_uz_cyril'] : null;
            $empModel->firstname_uz_latin = $employee['firstname_uz_latin'];
            $empModel->lastname_uz_latin = $employee['lastname_uz_latin'];
            $empModel->middlename_uz_latin = isset($employee['middlename_uz_latin']) ? $employee['middlename_uz_latin'] : null;
            $empModel->firstname_ru = $employee['firstname_uz_cyril'];
            $empModel->lastname_ru = $employee['lastname_uz_cyril'];
            $empModel->middlename_ru = isset($employee['middlename_uz_cyril']) ? $employee['middlename_uz_cyril'] : null;
            $empModel->company_id = 1;
            $empModel->tabel = $employee['tabel'];
            $empModel->nationality_id = 1;
            $empModel->born_date = '1970-01-01';
            $empModel->INN = 123123123;
            $empModel->INPS = 123123123123;
            $empModel->is_active = 1;
            $empModel->save();

            $empPhone = EmployeePhone::where('employee_id', $empModel->id)->first();
            if (!$empPhone) {
                $empPhone = new EmployeePhone();
                $empPhone->created_by = Auth::user()->id;
            }
            $empPhone->phone_number = $employee['phone_number'];
            $empPhone->employee_id = $empModel->id;
            $empPhone->phone_type = 'Mobile';
            $empPhone->save();

            $user = $employee['user'];
            $userModel = AppUser::find($user['id']);
            if (!$userModel) {
                $userModel = new AppUser();
                $userModel->password = Hash::make('123456');
            }
            $userModel->username = $empModel->firstname_uz_latin . "." . $empModel->lastname_uz_latin;
            $userModel->employee_id = $empModel->id;
            $userModel->email = $user['email'];
            $userModel->type = DepartmentType::find($departmentModel->department_type_id)->type;
            $userModel->save();

            /* staff create */
            if (isset($staff)) {
                $staff->department_id = $departmentModel->id;
                $staff->position_id = 182;
                $staff->is_active = 1;
                $staff->created_by = Auth::user()->id;
                $staff->save();

                $departmentModel->manager_staff_id = $staff->id;
                $departmentModel->save();

                $empStaff = new EmployeeStaff();
                $empStaff->employee_id = $empModel->id;
                $empStaff->staff_id = $staff->id;
                $empStaff->save();
            }
            DB::commit();
            return "Successfully saved!";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function getChildDepartment(Request $request)
    {
        $parent_id = $request->input('parent_id');
        if ($parent_id)
            $departments = Department::select('id', 'department_code', 'name_uz_latin')->where('parent_id', $parent_id)->whereNotIn('department_type_id', [12, 13])->get()->toArray();
        else {
            $departments = Department::select('id', 'department_code', 'name_uz_latin')->where('id', 1)->whereNotIn('department_type_id', [12, 13])->get()->toArray();
        }

        return $departments;
    }

    public function getDepartment(Request $request)
    {
        $lang = $request->input('language');
        $staff_ids = Auth::user()->employee->staff->pluck('id');
        $department_codes = Department::whereHas('staff', function ($q) use ($staff_ids) {
            $q->whereIn('id', $staff_ids);
        })
            ->get()->map(function ($value) {
                return substr($value->department_code, 0, 2);
            });

        $departments = Department::select('id', 'department_code as code', 'name_' . $lang . ' as name');
        if (!Auth::user()->hasPermission('show_all_staff_and_department')) {
            foreach ($department_codes as $code) {
                $departments->orWhere('department_code', 'LIKE', $code . '%');
            }
        }
        return $departments->get()->toArray();
    }

    public function getStaff(Request $request)
    {
        $lang = $request->input('language');
        $staff_ids = Auth::user()->employee->staff->pluck('id');
        $dep_ids = Auth::user()->employee->staff->pluck('department_id');
        $deps = Department::whereIn('id', $dep_ids)->get();
        $department_codes = Department::whereHas('staff', function ($q) use ($staff_ids) {
            $q->whereIn('id', $staff_ids);
        })
            ->get()->map(function ($value) {
                return substr($value->department_code, 0, 2);
            });

        $departments = Department::select('id', 'department_code as code', 'name_' . $lang . ' as name');
        foreach ($department_codes as $code) {
            $departments->orWhere('department_code', 'LIKE', $code . '%');
        }
        $department_ids = $departments->get()->pluck('id');


        //-------------------------------------------------
        $staff = Staff::query()
            ->wherehas('department')
            ->where('is_active', 1);
        if (!Auth::user()->hasPermission('show_all_staff_and_department') && !(count($deps) && $deps->first(function ($value, $key) {
            return substr($value->department_code, 0, 3) == '223';
        }))) {
            $staff->whereIn('department_id', $department_ids);
        }
        return $staff->get()->map(function ($data) {
            try {
                return [
                    'id' => $data->id,
                    'department_id' => $data->department_id,
                    'position_id' => $data->position_id,
                    'department_code' => $data->department->department_code,
                    'department_name_ru' => $data->department->name_uz_latin,
                    'department_name_uz_latin' => $data->department->name_uz_cyril,
                    'department_name_uz_cyril' => $data->department->name_ru,

                    'position_name_ru' => $data->position ? $data->position->name_uz_latin : '',

                    'position_name_uz_latin' => $data->position ? $data->position->name_uz_cyril : '',
                    'position_name_uz_cyril' => $data->position ? $data->position->name_ru : '',
                ];
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }
    public function getAllStaff(Request $request)
    {
        $lang = $request->input('language');
        $document_template_id = $request->input('document_template_id');
        $staff_ids = Auth::user()->employee->staff->pluck('id');
        $department_codes = Department::whereHas('staff', function ($q) use ($staff_ids) {
            $q->whereIn('id', $staff_ids);
        })
            ->get()->map(function ($value) {
                return substr($value->department_code, 0, 2);
            });

        $departments = Department::select('id', 'department_code as code', 'name_' . $lang . ' as name');
        foreach ($department_codes as $code) {
            $departments->orWhere('department_code', 'LIKE', $code . '%');
        }
        $department_ids = $departments->get()->pluck('id');


        //-------------------------------------------------
        $staff = Staff::query()
            ->wherehas('department', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('is_active', 1);
        if ($document_template_id == 420) {
            $staff->wherehas('department', function ($q) {
                $q->where('department_code', 'like', '9%');
            });
        }

        // if(!Auth::user()->hasPermission('show_all_staff_and_department')){
        //     $staff->whereIn('department_id',$department_ids);
        // }
        return $staff->get()->map(function ($data) {
            try {
                return [
                    'id' => $data->id,
                    'department_id' => $data->department_id,
                    'position_id' => $data->position_id,
                    'department_code' => $data->department->department_code,
                    'department_name_ru' => $data->department->name_uz_latin,
                    'department_name_uz_latin' => $data->department->name_uz_cyril,
                    'department_name_uz_cyril' => $data->department->name_ru,

                    'position_name_ru' => $data->position ? $data->position->name_uz_latin : '',

                    'position_name_uz_latin' => $data->position ? $data->position->name_uz_cyril : '',
                    'position_name_uz_cyril' => $data->position ? $data->position->name_ru : '',
                ];
            } catch (\Throwable $th) {
                dd($th);
            }
        });
    }
    public function dep()
    {
        return
            $departments = Department::get();
    }
    public function findDepartmentByUser(Request $request)
    {
        // return $request;
        $array = [];

        $tabel = $request->input('tabel');

        $employee = Employee::where('tabel', $tabel)
            ->with(['staff' => function ($q) {
                $q->with('position');
                $q->with(['department' => function ($q) {
                    $q->with('departmentType');
                }]);
            }])->first();
        $array[] = $employee;
        if ($employee->staff[0]->department->manager_staff_id != $employee->staff[0]->id) {
            $id = $employee->staff[0]->department->manager_staff_id;
            $employee1 = Employee::whereHas('employeeStaff', function ($q) use ($id) {
                $q->where('staff_id', $id);
                $q->where('is_active', 1);
            })
                ->with(['staff' => function ($q) {
                    $q->with('position');
                    $q->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }]);
                }])->first();
            if (isset($employee1)) {
                $array[] = $employee1;
            }
        }

        for ($x = 0; $employee->staff[0]->department->departmentType->sequence > 3; $x++) {
            $employee =  $this->empdep($employee->staff[0]->department->parent_id);
            if (!isset($employee->staff[0])) {
                break;
            } else {
                if ($employee->staff[0]->department->manager_staff_id != $employee->staff[0]->id) {
                    $id = $employee->staff[0]->department->manager_staff_id;
                    $employee1 = Employee::whereHas('employeeStaff', function ($q) use ($id) {
                        $q->where('staff_id', $id);
                        $q->where('is_active', 1);
                    })
                        // ->whereDoesntHave('staff', function ($q) {
                        ->whereHas('staff', function ($q) {
                            $q->whereHas('department', function ($q) {
                                $q->whereHas('departmentType', function ($q) {
                                    $q->where('sequence', '>', 3);
                                    // $q->where('sequence', '<', 4);
                                });
                            });
                        })
                        ->with(['staff' => function ($q) {
                            $q->with('position');
                            $q->with(['department' => function ($q) {
                                $q->with('departmentType');
                            }]);
                        }])->first();
                    if (isset($employee1)) {
                        $array[] = $employee1;
                    }
                }
            }
            $array[] = $employee;
        }
        $array = array_reverse($array);
        // $emp = Employee::find($array[0]->id);        
        return $array;
    }
    public function newProjektEmployees(Request $request)
    {
        $id = isset($request['id']) ? $request['id'] : '';
        $employee =  Employee::whereHas('employeeStaff', function ($q) use ($id) {
            $q->where('is_active', 1);
        })
            ->where('id', $id)
            ->with(['mainStaff' => function ($q) {
                $q->with('position');
                $q->with(['department' => function ($q) {
                    $q->with('departmentType');
                }]);
            }])->first();

        $manager_staff_id =  $employee->mainStaff[0]->department->manager_staff_id;
        $department_id =  $employee->mainStaff[0]->department->id;

        if ($employee->mainStaff[0]->id == $manager_staff_id) { // seksiya raxbari degani
            // return 1;
            $parent_id = $employee->mainStaff[0]->department->parent_id;

            $boss =  Employee::whereHas('employeeStaff', function ($q) use ($parent_id) {
                $q->where('is_active', 1);
                $q->whereHas('staff', function ($q) use ($parent_id) {
                    $q->whereHas('department', function ($q) use ($parent_id) {
                        $q->where('id', $parent_id);
                    });
                });
            })
                ->with(['mainStaff' => function ($q) use ($parent_id) {
                    $q->with('position');
                    $q->with(['department' => function ($q) use ($parent_id) {
                        $q->with('departmentType');
                    }]);
                }])->first();

            if (!$boss) {
                $boss =  Staff::where('is_active', 1)
                    ->where('is_active', 1)
                    ->whereHas('department', function ($q) use ($parent_id) {
                        $q->where('id', $parent_id);
                    })
                    ->with('position')
                    ->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }])
                    ->first();
            }


            $shayka1  =  Employee::whereHas('employeeStaff', function ($q) use ($department_id, $manager_staff_id) {
                $q->where('is_active', 1);
                $q->whereHas('staff', function ($q) use ($department_id, $manager_staff_id) {
                    $q->where('id', '!=', $manager_staff_id);
                    $q->whereHas('department', function ($q) use ($department_id) {
                        $q->where('id', $department_id);
                    });
                });
            })
                ->with(['mainStaff' => function ($q) use ($parent_id) {
                    $q->with('position');
                    $q->with(['department' => function ($q) use ($parent_id) {
                        $q->with('departmentType');
                    }]);
                }])->get();
            // return
            $dep_id = Department::where('parent_id', $employee->mainStaff[0]->department->id)
                // ->get()
                ->pluck('manager_staff_id')->toArray();

            $shayka2  =  Employee::whereHas('employeeStaff', function ($q) use ($dep_id) {
                $q->where('is_active', 1);
                $q->whereHas('staff', function ($q) use ($dep_id) {
                    $q->whereIn('id', $dep_id);
                });
            })
                ->with(['mainStaff' => function ($q) {
                    $q->with('position');
                    $q->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }]);
                }])->get();

            if (count($shayka1) > 0 && count($shayka2) > 0) {
                $shayka = array_merge($shayka1, $shayka2);
            } else if (count($shayka1) == 0 && count($shayka2) > 0) {
                $shayka = $shayka2;
            } else if (count($shayka2) == 0 && count($shayka1) > 0) {
                $shayka = $shayka1;
            } else {
                $shayka = [];
            }
        } else {
            // return 2;
            $boss =  Employee::whereHas('employeeStaff', function ($q) use ($manager_staff_id) {
                $q->where('is_active', 1);
                $q->whereHas('staff', function ($q) use ($manager_staff_id) {
                    $q->where('id', $manager_staff_id);
                });
            })
                ->with(['mainStaff' => function ($q) {
                    $q->with('position');
                    $q->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }]);
                }])->first();

            $shayka  =  Employee::whereHas('employeeStaff', function ($q) use ($department_id) {
                $q->where('is_active', 1);
                $q->whereHas('staff', function ($q) use ($department_id) {
                    $q->whereHas('department', function ($q) use ($department_id) {
                        $q->where('parent_id', $department_id);
                    });
                });
            })
                ->with(['mainStaff' => function ($q) {
                    $q->with('position');
                    $q->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }]);
                }])->get();
        }
        return [$employee, $boss, $shayka];
    }
    public function empdep($id)
    {
        return
            $employee = Employee::whereHas('staff', function ($q) use ($id) {
                $q->whereHas('department', function ($q) use ($id) {
                    $q->where('id', $id);
                });
            })
            // ->whereDoesntHave('staff', function ($q) {
            ->whereHas('staff', function ($q) {
                $q->whereHas('department', function ($q) {
                    $q->whereHas('departmentType', function ($q) {
                        $q->where('sequence', '>', 3);
                        // $q->where('sequence', '<', 4);
                    });
                });
            })
            ->with(['staff' => function ($q) {
                $q->with('position');
                $q->with(['department' => function ($q) {
                    $q->with('departmentType');
                }]);
            }])->first();
    }
    public function getChildrenEmployees($id){ //hozirda ishlayotgan hodimlar sonini iolib keluvchi funksiya
        $departments = Department::where('id', $id)->first();
        $staff=$departments->staff;
        $cnt = 0;
        foreach ($staff as $key => $value) {
            $cnt += count($value->employees);
        }

        $childrens = Department::where('parent_id', $id)->where('is_active', 1)->get();
        // return $childrens;
        if(count($childrens) > 0){
            foreach ($childrens as $key => $children) {
                $cnt += $this->getChildrenEmployees($children->id);
            }
        }
        return $cnt;
    }
    public function headCounter($type){
        $departments = Department::where('is_active', 1)->get();
        foreach ($departments as $key => $value) {
            $department = Department::find($value->id);
            if($type==2){
                $bp = $this->getRateCountEmployees($value->id)[0];
                $rc = $this->getRateCountEmployees($value->id)[1];
                $department->rate_count_bp = $bp;
                $department->rate_count = $rc;
            }
            else{
                $cnt = $this->getChildrenEmployees($value->id);
                $department->head_count = $cnt;
            }
            $department->save();
        }
        return 'done';
    }
    public function getRateCountEmployees($id){ //Bieznes plan va tasdiqlangan hodimlar sonini olib kelish funksiyasi
        $departments = Department::where('id', $id)->first();
        $staff=$departments->staff;
        $bp = 0;
        $rc = 0;
        foreach ($staff as $key => $value) {
            $bp += $value->rate_count_bp;
            $rc += $value->rate_count;
        }

        $childrens = Department::where('parent_id', $id)->where('is_active', 1)->get();
        // return $childrens;
        if(count($childrens) > 0){
            foreach ($childrens as $key => $children) {
                $bp += $this->getRateCountEmployees($children->id)[0];
                $rc += $this->getRateCountEmployees($children->id)[1];
            }
        }
        return [$bp, $rc];
    }
    public function headCounter2(){ //Bieznes plan va tasdiqlangan hodimlar sonini Department tablitsaga yozish
        $departments = Department::where('is_active', 1)->get();
        foreach ($departments as $key => $value) {
            $department = Department::find($value->id);
            $emps = $this->getRateCountEmployees($value->id);
            $bp = $emps[0];
            $rc = $emps[1];
            $department->rate_count_bp = $bp;
            $department->rate_count = $rc;
            $department->save();
        }
        return 'done';
    }
}
