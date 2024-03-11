<?php

namespace App\Http\Controllers;

use App\Http\Models\Branch;
use App\Http\Models\Shift;
use App\Http\Models\Coefficient;
use App\Http\Models\Department;
use App\Http\Models\ExpenceType;
use App\Http\Models\File;
use App\Http\Models\ObjectType;
use App\Http\Models\PersonalType;
use App\Http\Models\Position;
use App\Http\Models\Document;
use App\Http\Models\Range;
use App\Http\Models\Requirement;
use App\Http\Models\Staff;
use App\Http\Models\StaffFile;
use App\Http\Models\StaffCoefficient;
use App\Http\Models\StaffShift;
use App\Http\Models\StaffRequirement;
use App\Services\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request)
    {
        // return $request;
        $language = $request->input('language');
        $search = $request->input('search');
        $staff = Staff::leftJoin('departments', 'departments.id', 'staff.department_id')
            ->leftJoin('positions', 'positions.id', 'staff.position_id')
            ->select(
                'staff.id',
                'staff.department_id',
                'staff.position_id',
                'departments.department_code as department_code',
                'departments.name_ru as department_name_ru',
                'departments.name_uz_latin as department_name_uz_latin',
                'departments.name_uz_cyril as department_name_uz_cyril',
                'positions.name_ru as position_name_ru',
                'positions.name_uz_latin as position_name_uz_latin',
                'positions.name_uz_cyril as position_name_uz_cyril'
            )
            ->where('staff.is_active', 1);


        if (isset($request['employee']) && $request['employee']) {
            $staff->whereHas('employeeStaff', function ($q) {
                $q->where('employee_id', Auth::user()->employee_id);
            });
        }
        if (isset($search) && $search) {
            $staff->where(function ($query) use ($search) {
                return $query
                    ->orWhere('departments.department_code', 'ilike', "%" . $search . "%")
                    ->orWhere('departments.name_ru', 'ilike', "%" . $search . "%")
                    ->orWhere('departments.name_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('departments.name_uz_cyril', 'ilike', "%" . $search . "%")
                    ->orWhere('positions.name_ru', 'ilike', "%" . $search . "%")
                    ->orWhere('positions.name_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('positions.name_uz_cyril', 'ilike', "%" . $search . "%");
            });
        }
        // return $staff->orderBy('departments.department_code')->paginate(20);
        return $staff->orderBy('staff.id')->paginate(20);
    }


    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        // $branches = User::hrRoles();
        $branches = [1, 2, 3, 4, 5];
        $user = Auth::user();
        $staff = Staff::with([
            'requirements' => function ($q) {
                $q->with('requirementType');
            }
        ])
            ->with('staffCoefficients')
            ->with('staffShift')
            ->with([
                'department' => function ($q) {
                    $q->with([
                        'documents' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name', 'department2_id');
                            $q->where('document_type_id', 58);
                            $q->whereIn('status', [3, 4, 5]);
                        }
                    ]);
                    // ->where('departments.is_active', 1);
                    // $q->whereHas('documents', function ($q) {
                    //     $q->whereIn('status', [3, 4, 5]);
                    // })
                    // ;
                }
            ])
            ->with([
                'documentStaffs' => function ($q) {
                    $q->with([
                        'document' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name');
                            // ->where('document_type_id', 57);
                        }
                    ]);
                    $q->whereHas('document', function ($q) {
                        $q->whereIn('status', [3, 4, 5]);
                    });
                }
            ])
            ->with('files')
            // ->with('department')
            ->with('position')
            ->with('range')
            ->with('expenceType')
            ->with('personalType')
            ->with('coefficient')
            ->with([
                'employeeStaff' => function ($query) {
                    $query->with('employee');
                    $query->where('employee_staff.is_active', 1);
                }
            ])
            ->whereIn('departments.branch_id', $branches)
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id');
        if (isset($filter['department_name'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.name_uz_latin', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere('departments.name_uz_cyril', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere("departments.name_ru", 'ilike', "%" . $filter['department_name'] . "%");
            });
        }
        if (isset($filter['department_code'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.department_code', 'ilike', '%' . $filter['department_code'] . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.position_id', $filter['position_id']);
            });
        }
        if (isset($filter['range_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.range_id', $filter['range_id']);
            });
        }
        if (isset($filter['expence_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.expence_type_id', $filter['expence_type_id']);
            });
        }
        if (isset($filter['personal_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.personal_type_id', $filter['personal_type_id']);
            });
        }
        if (isset($filter['status'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.is_active', $filter['status']);
            });
        }
        if (!$user->hasRole('hr')) {
            $staff = $staff->where('staff.is_active', 1);
        }
        return $staff->select('staff.*')
            // ->distinct()
            ->orderBy('departments.department_code')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }



    // shtatnoya raspisaniya

    public function indexNew(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        // $branches = User::hrRoles();
        $branches = [1, 2, 3, 4, 5];
        $user = Auth::user();
        $staff = Staff::with([
            'requirements' => function ($q) {
                $q->with('requirementType');
            }
        ])
            ->with('staffCoefficients')
            ->with('staffShift')
            ->with([
                'department' => function ($q)  use ($branches) {
                    $q->whereIn('branch_id', $branches);                   
                    $q->with('functionalDepartment');
                    $q->with([
                        'documents' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name', 'department2_id');
                            $q->where('document_type_id', 58);
                            $q->whereIn('status', [3, 4, 5]);
                        }
                    ]);
                }
            ])
            ->with([
                'documentStaffs' => function ($q) {
                    $q->with([
                        'document' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name');
                            // ->where('document_type_id', 57);
                        }
                    ]);
                    $q->whereHas('document', function ($q) {
                        $q->whereIn('status', [3, 4, 5]);
                    });
                }
            ])
            ->with('files')

            ->with('position')
            ->with('range')
            ->with('branch')
            ->with('expenceType')
            ->with('personalType')
            ->with('coefficient')
            ->with([
                'employeeStaff' => function ($query) {
                    $query->with('employee');
                    $query->where('employee_staff.is_active', 1);
                }
            ])
            // ->whereIn('departments.branch_id', $branches)
            // ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
        ;
        if (isset($filter['department_name'])) {
            $staff->whereHas('department', function (Builder $query) use ($filter) {
                $query->where('name_uz_latin', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere('name_uz_cyril', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere("name_ru", 'ilike', "%" . $filter['department_name'] . "%");
            });
        }
        if (isset($filter['department_code'])) {
            $staff->whereHas('department', function (Builder $query) use ($filter) {
                $query->where('department_code', 'ilike', '%' . $filter['department_code'] . '%');
            });
        }
        if (isset($filter['staff_shift_id'])) {
            $staff->whereHas('staffShift', function ($query) use ($filter) {
                $query->whereHas('shift', function ($q) use ($filter) {
                    $q->where('id', 'ilike', '%' . $filter['staff_shift_id'] . '%');
                });
            });
        }
        if (isset($filter['staff_coefficients_id'])) {
            $staff->whereHas('staffCoefficients', function ($query) use ($filter) {
                $query->whereHas('coefficient', function ($q) use ($filter) {
                    $q->where('id',  $filter['staff_coefficients_id']);
                });
            });
        }
        if (isset($filter['functionalgroup'])) {
            $staff->whereHas('department', function (Builder $query) use ($filter) {
                $query->whereHas('functionalDepartment', function ($q) use ($filter) {
                    $q->where('functional_department_code', 'ilike', '%' . $filter['functionalgroup'] . '%')
                        ->orWhere('name_ru', 'ilike', '%' . $filter['functionalgroup'] . '%')
                        ->orWhere('name_uz_latin', 'ilike', '%' . $filter['functionalgroup'] . '%')
                        ->orWhere('name_uz_cyril', 'ilike', '%' . $filter['functionalgroup'] . '%');
                });
            });
        }
        if (isset($filter['functionalgroupcode'])) {
            $staff->whereHas('department', function (Builder $query) use ($filter) {
                $query->whereHas('functionalDepartment', function ($q) use ($filter) {
                    $q->where('functional_department_code',  $filter['functionalgroupcode']);
                });
            });
        }
        if (isset($filter['order_number'])) {
            $staff->where(function (Builder $query) use ($filter) {
                $query->where('order_number', 'ilike', '%' . $filter['order_number'] . '%');
            });
        }
        if (isset($filter['order_date'])) {
            $staff->where(function (Builder $query) use ($filter) {
                $query->where('order_date', 'ilike', '%' . $filter['order_date'] . '%');
            });
        }
        if (isset($filter['description'])) {
            $staff->where(function ($query) use ($filter) {
                $query->where('description', 'ilike', '%' . $filter['description'] . '%');
            });
        }
        if (isset($filter['end_date'])) {
            $staff->where(function (Builder $query) use ($filter) {
                $query->where('end_date', 'ilike', '%' . $filter['end_date'] . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                $query->where('position_id', $filter['position_id']);
            });
        }
        if (isset($filter['branch_id'])) {
            $staff->where('branch_id', $filter['branch_id']);
        }
        if (isset($filter['range_id'])) {
            $staff
                ->whereHas('range', function ($query) use ($filter) {
                    // return $query
                    //     ->where('staff.range_id', $filter['range_id']);
                    $query->where('code', 'ilike', '%' . $filter['range_id'] . '%');
                });
        }
        if (isset($filter['expence_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.expence_type_id', $filter['expence_type_id']);
            });
        }
        if (isset($filter['personal_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.personal_type_id', $filter['personal_type_id']);
            });
        }
        if (isset($filter['status'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.is_active', $filter['status']);
            });
        }
        if (!$user->hasRole('hr')) {
            $staff = $staff->where('staff.is_active', 1);
        }
        return $staff->select('staff.*')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')            
            // ->distinct()
            // ->groupBy('departments')
            ->orderBy('departments.department_code', 'asc')
            ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
        // $documents->orderBy(DB::raw('case when document_date_reg is not null then document_date_reg else document_date end'), 'desc');
    }
    // shtatnoya raspisaniya
    public function updateCoefficient(Request $request)
    {
        if ($request->input('form')['id']) {
            if ($request->input('form')['coefficient_id']) {
                StaffCoefficient::where('staff_id', $request->input('form')['id'])->delete();
                foreach ($request->input('form')['coefficient_id'] as $value) {
                    $model = new StaffCoefficient();
                    $model->staff_id = $request->input('form')['id'];
                    $model->coefficient_id = $value;
                    $model->save();
                }
                return 1;
            } else {
                StaffCoefficient::where('staff_id', $request->input('form')['id'])->delete();
            }
        }
        return 0;
    }
    public function updateShift(Request $request)
    {
        if ($request->input('form')['id']) {
            if ($request->input('form')['shift_id']) {
                StaffShift::where('staff_id', $request->input('form')['id'])->delete();
                foreach ($request->input('form')['shift_id'] as $value) {
                    $model = new StaffShift();
                    $model->staff_id = $request->input('form')['id'];
                    $model->shift_id = $value;
                    $model->save();
                }
                return 1;
            } else {
                StaffShift::where('staff_id', $request->input('form')['id'])->delete();
            }
        }
        return 0;
    }
    public function index2(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        // $branches = User::hrRoles();
        $branches = [1, 2, 3, 4, 5];
        $user = Auth::user();
        $staff = Staff::with([
            'requirements' => function ($q) {
                $q->with('requirementType');
            }
        ])
            ->with('staffCoefficients')
            ->with('staffShift')
            ->with([
                'department' => function ($q) {
                    $q->with([
                        'documents' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name', 'department2_id');
                            $q->where('document_type_id', 58);
                            $q->whereIn('status', [3, 4, 5]);
                        }
                    ]);
                    // ->where('departments.is_active', 1);
                    // $q->whereHas('documents', function ($q) {
                    //     $q->whereIn('status', [3, 4, 5]);
                    // })
                    // ;
                }
            ])
            ->with([
                'documentStaffs' => function ($q) {
                    $q->with([
                        'document' => function ($q) {
                            $q->select('id', 'status', 'document_number', 'pdf_file_name');
                            // ->where('document_type_id', 57);
                        }
                    ]);
                    $q->whereHas('document', function ($q) {
                        $q->whereIn('status', [3, 4, 5]);
                    });
                }
            ])
            ->with('files')
            // ->with('department')
            ->with('position')
            ->with('range')
            ->with('expenceType')
            ->with('personalType')
            ->with('coefficient')
            ->with([
                'employeeStaff' => function ($query) {
                    $query->with('employee');
                    $query->where('employee_staff.is_active', 1);
                }
            ])
            ->whereIn('departments.branch_id', $branches)
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id');
        if (isset($filter['department_name'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.name_uz_latin', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere('departments.name_uz_cyril', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere("departments.name_ru", 'ilike', "%" . $filter['department_name'] . "%");
            });
        }
        if (isset($filter['department_code'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.department_code', 'ilike', '%' . $filter['department_code'] . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.position_id', $filter['position_id']);
            });
        }
        if (isset($filter['range_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.range_id', $filter['range_id']);
            });
        }
        if (isset($filter['expence_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.expence_type_id', $filter['expence_type_id']);
            });
        }
        if (isset($filter['personal_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.personal_type_id', $filter['personal_type_id']);
            });
        }
        if (isset($filter['status'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.is_active', $filter['status']);
            });
        }
        if (!$user->hasRole('hr')) {
            $staff = $staff->where('staff.is_active', 1);
        }
        return $staff->select('staff.*')->distinct()->orderBy('departments.department_code')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef(Request $request)
    {
        $language = $request->input('language');
        return [
            'departments' => Department::get(),
            'coefficients' => Coefficient::select('id', 'code', 'protsent', 'description', 'description_ru', 'description_uz_cyril', 'description_uz_latin')
                ->where('type', '1')->get(),
            'shift' => Shift::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name_' . $language, 'code')
                ->withCount('staff')
                ->get(),
            'ranges' => Range::select('id', 'code')->get(),
            'personal_types' => PersonalType::select('id', 'name_' . $language)->get(),
            'expence_types' => ExpenceType::select('id', 'name_' . $language)->get(),
            'branches' => Branch::select('id', 'name', 'code')->get(),
            'object_types' => ObjectType::select('id', 'name_' . $language)->where('controller', 'staff')->get(),
            'requirements' => Requirement::with('requirementType')->select('id', 'name_' . $language, 'requirement_type_id')->get(),
        ];
    }

    public function updateRequirements(Request $request)
    {
        $staff = $request->input('staff');
        StaffRequirement::where('staff_id', $staff['id'])->delete();
        foreach ($staff['requirements'] as $key => $value) {
            $staffRequirement = new StaffRequirement();
            $staffRequirement->staff_id = $staff['id'];
            $staffRequirement->requirement_id = $value['id'];
            $staffRequirement->save();
        }
        return 8;
    }

    public function fileDownload($id)
    {
        $file = File::where('id', $id)->first();
        // return $file->created_at;
        if ($file->created_at > '2023-09-01 00:00:00') {
            return response()->download(storage_path('app/documents_new/' . $file->physical_name), $file->file_name);
        } else {
            return response()->download(storage_path('app/documents/' . $file->physical_name), $file->file_name);
        }
    }

    public function getFile($id)
    {
        $file = File::where('id', $id)->first();
        if ($file->created_at > '2023-09-01 00:00:00') {
            $path = Storage::path('documents_new/' . $file->physical_name);
        } else {
            $path = Storage::path('documents/' . $file->physical_name);
        }
        return response()->file($path, ['Content-Type' => 'application/pdf']);

        // $file = File::where('id', $id)->first();
        // $path = Storage::path('documents\\' . $file->physical_name);
        // return response()->file($path);
    }

    public function getFileByName($name)
    {
        $file = File::where('physical_name', $name)->first();
        $path = Storage::path('documents_new//' . $file->physical_name);
        return response()->file($path, ['Content-Type' => 'application/pdf']);

        // $file = File::where('id', $id)->first();
        // $path = Storage::path('documents\\' . $file->physical_name);
        // return response()->file($path);
    }

    public function updateFiles(Request $request, $id)
    {
        $files = $request->file('files');
        $object_type_id = $request->input('object_type_id');
        $object_id = $request->input('object_id');
        $description = $request->input('description');

        foreach ($files as $key => $value) {
            $filename = time() . rand();
            Storage::putFileAs(
                'documents_new',
                $value,
                $filename
            );
            $file = new File();
            $file->object_type_id = $object_type_id;
            $file->file_name = $value->getClientOriginalName();
            $file->physical_name = $filename;
            $file->object_id = $object_id;
            // $file->description = $description;
            $file->created_by = Auth::id();
            $file->save();

            $staffFile = new StaffFile();
            $staffFile->staff_id = $id;
            $staffFile->object_type_id = $object_type_id;
            $staffFile->file_id = $file->id;
            $staffFile->save();
        }
        return Staff::with('files')->where('id', $id)->first();
    }

    public function deleteFile($id)
    {
        StaffFile::where('file_id', $id)->delete();
        $file = File::find($id);
        Storage::delete('documents_new/' . $file->physical_name);
        $file->delete();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        $model = Staff::find($request->input('id'));
        if (!$model) {
            $model = new Staff();
            $model->created_by = Auth::id();
            $model->is_active = 1;
        } else {
            $model->updated_by = Auth::id();
        }
        $model->department_id = $request['department_id'];
        $model->position_id = $request['position_id'];
        $model->range_id = $request['range_id'];
        $model->personal_type_id = $request['personal_type_id'];
        $model->expence_type_id = $request['expence_type_id'];
        $model->rate_count_bp = $request['rate_count_bp'];
        $model->rate_count = $request['rate_count'];
        $model->order_date = $request['order_date'];
        $model->order_number = $request['order_number'];
        $model->begin_date = $request['begin_date'];
        $model->end_date = $request['end_date'];
        $model->branch_id = $request['branch_id'];
        $model->shift = $request['shift'];
        $model->coefficient_id = $request['coefficient_id'];
        // $model->requirements = $request['requirements'];
        $model->instruction_file_path = $request['instruction_file_path'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        //
        $staff = Staff::find($id);
        // return $staff;
        if (count($staff->employeeStaff) > 0) {
            return ['error' => 'Connected Employee Exist to this Staff', 'status' => 215];
        }
        //return 11111;
        if ($staff) {
            if ($staff->is_active) {
                $staff->is_active = 0;
            } else {
                $staff->is_active = 1;
            }
            $staff->save();
        }
        return $staff->is_active;
    }

    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $filter = $request->input('filter');
        $perPage = $request->input('perPage');
        $language = $request->input('locale');
        $staff = Staff::with('requirements')
            ->with('requirements.requirementType')
            ->with('files')
            ->with([
                'employeeStaff' => function ($query) {
                    $query->select('id', 'staff_id', 'employee_id');
                    $query->with('employee');
                    // $query->where('is_main_staff',1);
                    $query->whereNull('leave_order_date');
                }
            ])
            ->select([
                'staff.*',
                'departments.name_' . $language . ' as department_name',
                'departments.department_code',
                'positions.name_' . $language . ' as position_name',
                'expence_types.id as expence_type',
                'expence_types.name_' . $language . ' as expence_type_name',
                'personal_types.id as personal_type',
                'personal_types.name_' . $language . ' as personal_type_name',
                'ranges.code'
            ])
            ->leftJoin('positions', 'staff.position_id', '=', 'positions.id')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
            ->leftJoin('expence_types', 'staff.expence_type_id', '=', 'expence_types.id')
            ->leftJoin('personal_types', 'staff.personal_type_id', '=', 'personal_types.id')
            ->leftJoin('ranges', 'staff.range_id', '=', 'ranges.id')
            ->orderBy('departments.department_code');
        if (isset($filter['department_name'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.name_uz_latin', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere('departments.name_uz_cyril', 'ilike', '%' . $filter['department_name'] . '%')
                    ->orWhere("departments.name_ru", 'ilike', "%" . $filter['department_name'] . "%");
            });
        }
        if (isset($filter['department_code'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('departments.department_code', 'ilike', '%' . $filter['department_code'] . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.position_id', $filter['position_id']);
            });
        }
        if (isset($filter['range_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.range_id', $filter['range_id']);
            });
        }
        if (isset($filter['expence_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.expence_type_id', $filter['expence_type_id']);
            });
        }
        if (isset($filter['personal_type_id'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.personal_type_id', $filter['personal_type_id']);
            });
        }
        if (isset($filter['status'])) {
            $staff->where(function (Builder $query) use ($filter) {
                return $query
                    ->where('staff.is_active', $filter['status']);
            });
        }
        if (!Auth::user()->hasRole('hr')) {
            $staff = $staff->where('is_active', 1);
        }
        $staff = $staff->paginate($perPage, ['*'], 'page name', $page);
        $excel = [];
        $department_code = '';
        $code = '';
        $department = '';
        $position = '';
        $expence_type = '';
        $rate_count = '';
        $rate_count_bp = '';
        $personal_type = '';
        foreach ($staff as $key => $value_codes) {
            $department_code = $value_codes ? $value_codes->department_code : '';
            $department = $value_codes->department_name;
            $code = $value_codes ? $value_codes->code : '';
            $position = $value_codes->position_name;
            $expence_type = $value_codes->expence_type;
            $rate_count = $value_codes->rate_count;
            $rate_count_bp = $value_codes->rate_count_bp;
            $personal_type = $value_codes->personal_type ? $value_codes->personal_type : '';

            array_push($excel, (object) [
                "№" => $key + 1 + $page * $perPage - $perPage,
                "Код подразделения" => $department_code,
                "Подразделения" => $department,
                "Должность" => $position,
                "Разряд" => $code,
                "Тип расход" => $expence_type,
                "Тип персонала" => $personal_type,
                "Количество ставок(БП)" => $rate_count_bp,
                "Количество ставок" => $rate_count,
                "Кол-во сотр." => $value_codes->employeeStaff->count(),
            ]);
        }
        return $excel;
    }

    public function getAttSigner(Request $request)
    {
        $staffs = $request->input('staffs');
        $locale = $request->input('locale');

        $attStaffs = Staff::with('department')
            ->with('position')
            ->where(function ($q) use ($staffs) {
                foreach ($staffs as $key => $value) {
                    $q->orWhere('id', $value['staff_id']);
                }
            })->get();
        return $attStaffs;
    }
    public function itemDescription(Request $request)
    {
        // return 
        $item = $request['item'];
        $staff = Staff::find($item['id']);
        $staff->description = isset($item['description']) ? $item['description'] : '';
        // $staff->description = '1207';
        $staff->save();
        if ($staff->save()) {
            return 'success';
        } else {
            return 'error';
        }
    }
    public function documents(Request $request)
    {
        $documents = Document::whereIn('document_template_id', [312, 313, 314, 315])
        ->with('employee.employeeStaff.staff.position')
        ->with('employee.employeeStaff.staff.department')
        ->with('documentTemplate')
        ->with('documentSigners.signerEmployee')
        ->whereNotIn('status', [0, 1, 2, 6])->get();

        return $documents;
    }
}
