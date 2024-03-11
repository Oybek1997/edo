<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeStaff;
use App\Http\Models\Employee;
use App\Http\Models\Staff;
use App\Http\Models\StaffLeaving;
use App\Http\Models\minRequirements;
use App\Http\Models\stafMinRequirements;
use App\Http\Models\LeavingReason;
use App\Http\Models\Coefficient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function stafMinRec(Request $request) #Вакантлар учун кўйиладиган минамал талабларни бириктириш учун
    {
        $modeldelet = stafMinRequirements::where('staff_id', $request->input('stafId'));
        if ($modeldelet) {
            stafMinRequirements::where('staff_id', $request->input('stafId'))->delete();
        }
        foreach ($request->input('minRec') as $key => $value) {
            $model = new stafMinRequirements();
            $model->staff_id = $request->input('stafId');
            $model->min_req_id = $value ? $value['id'] : $value['id'];
            $model->updated_by = Auth::id();
            $model->updated_at = date('Y-m-d h:i:s');
            $model->save();
        }
        return 1;
    }
    public function employeeStaffVacanciesCritical(Request $request) #Вакантлар бўйича маълумотларни олиш
    {

        $language = $request->input('language');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $perPage = $itemsPerPage == '-1' ? 1000000 : $itemsPerPage;
        $pageName = 'page';
        // return
        $currentPage = $page;
        $offset = ($currentPage - 1) * $perPage;

        $filter = $request->input('filter');

        $employeeStaff = Staff::select('*')
            ->with([
                'department' => function ($qu) {
                    $qu->with('branch');
                    $qu->with('functionalDepartment');
                    $qu->where('is_active', 1);
                }
            ])
            ->withCount([
                'employeeStaff as employeeCount' => function ($q) use ($filter) {
                    $q->with([
                        'employee' => function ($q) {
                            $q->where('is_active', 1);
                        }
                    ]);
                    $q->where('is_main_staff', 1);
                }
            ])
            // ->where('id','3842')
            ->with('position')
            ->with('stafMinReq')
            ->where('is_active', 1)
            ->whereHas('department')
            ->whereHas('position')
            ->whereDoesntHave('department', function ($query) {
                $query->whereIn('department_type_id', [12, 13, 14]);
            });
        //FILTER START
        if (isset($filter['branchName'])) {
            $value = $filter['branchName'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->whereHas('branch', function ($q) use ($value) {
                    $q->where('name', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['function_code'])) {
            $value = $filter['function_code'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->whereHas('functionalDepartment', function ($q) use ($value) {
                    $q->where('functional_department_code', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['department_code'])) {
            $value = $filter['department_code'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->where('department_code', 'like', $value . '%');
            });
        }
        if (isset($filter['department_name'])) {
            $value = $filter['department_name'];
            $employeeStaff->whereHas('department', function ($q) use ($value, $language) {
                $q->where('name_' . $language, 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['position_name'])) {
            $value = $filter['position_name'];
            $employeeStaff->whereHas('position', function ($q) use ($value, $language) {
                $q->where('name_' . $language, 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $value = $filter['position_id'];
            $employeeStaff->whereHas('position', function ($q) use ($value) {
                $q->where('code', 'like', $value . '%');
            });
        }
        //FILTER END
        $counter = 0;
        $model1 = collect([]);
        foreach ($employeeStaff->get() as $key => $staff) {
            $vacantCount = (int) ($staff['rate_count_bp'] - $staff['employeeCount']);
            if ($vacantCount > 0) {
                $model = [];
                $model = $staff;
                $model['vacant'] = $vacantCount;
                $employee = [];
                $sending = (int) $staff['rate_count_sv'];
                $band = (int) $staff['rate_count_reserved'];
                $critical = (int) $staff['rate_count_critical'];
                $block = (int) $staff['rate_count_blocked'];
                for ($i = 0; $i < $vacantCount; $i++) {
                    $employee[$i]['name'] = 'vacancy';
                    $employee[$i]['sending'] = ($sending > 0 && $sending - $i > 0) ? ($sending - $i > 0 ? true : null) : null;
                    if ($sending - $i <= 0 && $band >= 0 && $band - 1 >= 0) {
                        $employee[$i]['band'] = true;
                        $band = $band - 1;
                    } else {
                        $employee[$i]['band'] = null;
                    }
                    $employee[$i]['block'] = ($block > 0 && $block - $i > 0) ? ($block - $i > 0 ? true : null) : null;

                    if ($sending - $i <= 0 && $employee[$i]['band'] != true && $critical >= 0 && $critical - 1 >= 0) {
                        $employee[$i]['kritik'] = true;
                        $critical = $critical - 1;
                    } else {
                        $employee[$i]['kritik'] = null;
                    }

                }

                $model['employee'] = $employee;
                $model1[] = $model;
                $counter++;
            }
        }
        $offset = ($page - 1) * $itemsPerPage;
        $employeeStaffData = [
            'data' => array_values($model1->slice($offset, $itemsPerPage)->all()),
            'total' => $model1->count(),
            'from' => (($page - 1) * $itemsPerPage) + 1,
        ];

        return [
            'employeeStaff' => $employeeStaffData,
            'minRec' => minRequirements::get(),
        ];
    }
    public function employeeStaffKritikReject(Request $request)
    {
        $id = $request->input('reject')['id'];
        $comment = $request->input('reject')['comment'];
        $model = Staff::where('id', $id)->first();
        $model->rate_count_critical = 0;
        $model->comment = $comment;
        $model->save();
        return 1;
    }
    public function employeeStaffKritik(Request $request)
    {
        $id = $request->input('data')['id'];
        $emp = $request->input('data')['employee'];
        $kritikCount = 0;
        $blockCount = 0;
        $aa = [];
        foreach ($emp as $item) {
            if ($item['block'] != true) {
                if ($item['kritik'] === true) {
                    $kritikCount++;
                }
            }
            if ($item['block'] === true) {
                $blockCount++;
            }
        }
        $model = Staff::where('id', $id)->first();
        $model->rate_count_critical = (int) $kritikCount;
        $model->rate_count_blocked = (int) $blockCount;
        $model->comment = null;
        $model->save();
        return 1;
    }
    public function employeeStaffVacancies(Request $request) #Вакантлар бўйича маълумотларни олиш
    {
        $language = $request->input('language');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $employeeStaff = Staff::select('*')
            ->with([
                'department' => function ($qu) {
                    $qu->with('branch');
                    $qu->with('functionalDepartment');
                    $qu->where('is_active', 1);
                }
            ])
            ->withCount([
                'employeeStaff as employeeCount' => function ($q) use ($filter) {
                    $q->with([
                        'employee' => function ($q) {
                            $q->where('is_active', 1);
                        }
                    ]);
                    $q->where('is_main_staff', 1);
                }
            ])
            ->with('position')
            ->with('stafMinReq')
            ->with('uzautoJobsStatus')
            ->where('rate_count_critical','!=' ,0)
            ->orWhere('rate_count_sv','!=' ,0)
            ->where('is_active', 1)
            ->whereHas('department')
            ->whereHas('position')
            ->whereDoesntHave('department', function ($query) {
                $query->whereIn('department_type_id', [12, 13, 14]);
            })
        ;
        // ->leftJoin('staf_min_requirements','staf_min_requirements.staff_id','=','staff.id')
        if (isset($filter['branchName'])) {
            $value = $filter['branchName'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->whereHas('branch', function ($q) use ($value) {
                    $q->where('name', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['function_code'])) {
            $value = $filter['function_code'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->whereHas('functionalDepartment', function ($q) use ($value) {
                    $q->where('functional_department_code', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['department_code'])) {
            $value = $filter['department_code'];
            $employeeStaff->whereHas('department', function ($q) use ($value) {
                $q->where('department_code', 'like', $value . '%');
            });
        }
        if (isset($filter['department_name'])) {
            $value = $filter['department_name'];
            $employeeStaff->whereHas('department', function ($q) use ($value, $language) {
                $q->where('name_' . $language, 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['position_name'])) {
            $value = $filter['position_name'];
            $employeeStaff->whereHas('position', function ($q) use ($value, $language) {
                $q->where('name_' . $language, 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['position_id'])) {
            $value = $filter['position_id'];
            $employeeStaff->whereHas('position', function ($q) use ($value) {
                $q->where('code', 'like', $value . '%');
            });
        }
        return [
            'employeeStaff' => $employeeStaff->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'minRec' => minRequirements::get(),
        ];
    }
    public function employeeStaffFull(Request $request) #Штатное расписания 
    {
        $language = $request->input('language');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $dirInDir = (strtoupper(isset($filter['dirInDir']) ? $filter['dirInDir'] : null));
        if ($dirInDir) {
            if ($dirInDir === 'DIR') {
                $filter['expence_type'] = 1;
                $filter['personal_type'] = 5;
            }
            if ($dirInDir === 'KAS') {
                $filter['expence_type'] = 9;
                $filter['personal_type'] = 6;
            }
        }

        $employeeStaffFull = Staff::with([
            'employeeStaff' => function ($q) use ($filter) {
                $q->orderBy('created_at', 'asc');
                $q->whereHas('employee');
                // $q->where('is_active',1);                
                $q->with('shift');
                $q->with([
                    'employee' => function ($q) {
                        $q->where('is_active', 1);
                        $q->when(Auth::check() && Auth::user()->hasPermission('show_employee_coefficients1'), function ($q) {
                            $q->with('tariffScale');
                            $q->with('employeeCoefficients.coefficient');
                        });

                    }
                ]);
                if (isset($filter['shift'])) {
                    $value = $filter['shift'];
                    $q->whereHas('shift', function ($qw) use ($value) {
                        $qw->where('name', $value);
                    });
                }
                if (isset($filter['buyruq_number'])) {
                    $value = $filter['buyruq_number'];
                    $q->whereHas('employee', function ($qw) use ($value) {
                        $qw->where('enter_order_number', $value);
                    });
                }
                if (isset($filter['staj'])) {
                    $value = $filter['staj'];
                    $q->whereHas('employee', function ($qw) use ($value) {
                        $qw->where('experience', 'like', $value . '%');
                    });
                }
                if (isset($filter['buyruq_date'])) {
                    $value = $filter['buyruq_date'];
                    $q->whereHas('employee', function ($qw) use ($value) {
                        $qw->where('enter_order_date', 'like', $value . '%');
                    });
                }
                if (isset($filter['gender'])) {
                    $value = $filter['gender'];
                    $q->whereHas('employee', function ($qw) use ($value) {
                        $qw->where('gender', 'like', $value . '%');
                    });
                }
                if (isset($filter['kat_number'])) {
                    $value = $filter['kat_number'];
                    $q->whereHas('employee', function ($eq) use ($value) {
                        $eq->when(Auth::check() && Auth::user()->hasPermission('show_employee_coefficients'), function ($qe) use ($value) {
                            $qe->whereHas('tariffScale', function ($e) use ($value) {
                                $e->where('category', 'like', $value . '%');
                            });
                        });
                    });
                }

            }
        ])
            // ->whereHas('employeeStaff')
            ->with([
                'department' => function ($qu) {
                    $qu->with('branch');
                    $qu->with('functionalDepartment');
                    $qu->where('is_active', 1);
                }
            ])
            // ->whereHas('employeeStaff.employee')
            ->with('position')
            ->with('expenceType')
            ->with('personalType')
            ->with('staffCoefficients')
            ->with('range')
            ->whereHas('department')
            ->whereHas('position')
            ->whereDoesntHave('department', function ($query) {
                $query->whereIn('department_type_id', [12, 13, 14]);
            })
            // ->where('is_active', 1)
            // ->where('id', 559)
            // ->orderBy('employeeStaff.created_at', 'desc')
            // ->orderBy('employeeStaff.created_at', 'asc')
            // ->where('id', 383)
            // ->where('id', 571)
        ;
        if (isset($filter['tabel'])) {
            $value = $filter['tabel'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->where('tabel', $value);
                });
            });
        }
        if (isset($filter['buyruq_number'])) {
            $value = $filter['buyruq_number'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->where('enter_order_number', $value);
                });
            });
        }
        if (isset($filter['buyruq_date'])) {
            $value = $filter['buyruq_date'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->where('enter_order_date', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['staj'])) {
            $value = $filter['staj'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->where('experience', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['gender'])) {
            $value = $filter['gender'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->where('gender', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['kat_number'])) {
            $value = $filter['kat_number'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('employee', function ($q) use ($value) {
                    $q->when(Auth::check() && Auth::user()->hasPermission('show_employee_coefficients1'), function ($qe) use ($value) {
                        $qe->whereHas('tariffScale', function ($e) use ($value) {
                            $e->where('category', 'like', $value . '%');
                        });
                    });
                });
            });
        }
        if (isset($filter['shift'])) {
            $value = $filter['shift'];
            $employeeStaffFull->whereHas('employeeStaff', function ($w) use ($value) {
                $w->whereHas('shift', function ($q) use ($value) {
                    $q->where('name', $value);
                });
            });
        }

        if (isset($filter['function_code'])) {
            $value = $filter['function_code'];
            $employeeStaffFull->whereHas('department', function ($q) use ($value) {
                $q->whereHas('functionalDepartment', function ($q) use ($value) {
                    $q->where('functional_department_code', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['branchName'])) {
            $value = $filter['branchName'];
            $employeeStaffFull->whereHas('department', function ($q) use ($value) {
                $q->whereHas('branch', function ($q) use ($value) {
                    $q->where('name', 'like', $value . '%');
                });
            });
        }
        if (isset($filter['position_id'])) {
            $value = $filter['position_id'];
            $employeeStaffFull->whereHas('position', function ($q) use ($value) {
                $q->where('code', 'like', $value . '%');
            });
        }
        if (isset($filter['diapazon'])) {
            $value = $filter['diapazon'];
            $employeeStaffFull->whereHas('range', function ($q) use ($value) {
                $q->where('code', $value);
            });
        }
        if (isset($filter['position_name'])) {
            $value = $filter['position_name'];
            $employeeStaffFull->whereHas('position', function ($q) use ($value, $language) {
                $q->where('name_' . $language, 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['department_code'])) {
            $value = $filter['department_code'];
            $employeeStaffFull->whereHas('department', function ($q) use ($value) {
                $q->where('department_code', 'like', $value . '%');
            });
        }
        if (isset($filter['expence_type'])) {
            $value = $filter['expence_type'];
            $employeeStaffFull->whereHas('expenceType', function ($q) use ($value) {
                $q->where('name_ru', 'like', '%' . $value . '%');
            });
        }
        if (isset($filter['personal_type'])) {
            $value = $filter['personal_type'];
            $employeeStaffFull->whereHas('personalType', function ($q) use ($value) {
                $q->where('name_ru', 'like', '%' . $value . '%');
            });

        }
        if (isset($dirInDir) && $dirInDir === 'INDIR') {
            $employeeStaffFull
                ->whereHas('personalType', function ($q) {
                    $q->where(function ($query) {
                        $query->whereNot('name_ru', 'like', '%5%')
                            ->whereNot('name_ru', 'like', '%6%');
                    });
                })
                ->whereHas('expenceType', function ($q) {
                    $q->where(function ($query) {
                        $query->whereNot('name_ru', 'like', '%1%')
                            ->whereNot('name_ru', 'like', '%9%');
                    });
                });
        }

        if (isset($filter['bw'])) {
            $value = strtoupper($filter['bw']) === 'W';
            $employeeStaffFull->whereHas('range', function ($q) use ($value) {
                $operator = $value ? 'like' : 'not like';
                $q->where('code', $operator, '%E%');
            });
        }
        if (isset($filter['koef_code'])) {
            $value = strtoupper($filter['koef_code']);
            $employeeStaffFull->whereHas('employeeStaff.employee', function ($eq) use ($value) {
                $eq->when(Auth::check() && Auth::user()->hasPermission('show_employee_coefficients'), function ($qe) use ($value) {
                    $qe->whereHas('employeeCoefficients.coefficient', function ($e) use ($value) {
                        $e->where('code', 'like', $value . '%');
                    });
                });
            });
        }
        if (($filter['excells']) === 1) {
            $value = $filter['excells'];
            $excel = [];
            $perPage = $request->input('pagination.itemsPerPage');
            $products = $employeeStaffFull->paginate($perPage, ['*'], 'page name', $page);
            foreach ($products as $key => $value) {
                $coeff_code = implode(',', $value->staffCoefficients->map(function ($valueCoef) {
                    return $valueCoef->coefficient->code . $valueCoef->coefficient->protsent . '%';
                })->toArray());

                $perconFactCount = $value->employeeStaff ? $value->employeeStaff->where('is_main_staff', 1)->count() : 0;
                $countt = max($value->rate_count - $perconFactCount, 0);

                $newEmployee = $countt > 0 ? array_fill(0, $countt, [
                    'id' => 999999999,
                    'is_main_staff' => 99,
                    'shift_id' => null,
                    'employee' => [
                        'tariffScale' => null,
                        'firstname_ru' => "Вакант",
                        'lastname_ru' => "Вакант",
                        'middlename_ru' => "Вакант",
                        'firstname_uz_cyril' => "Вакант",
                        'lastname_uz_cyril' => "Вакант",
                        'middlename_uz_cyril' => "Vakant",
                        'lastname_uz_latin' => "Vakant",
                        'middlename_uz_latin' => "Vakant",
                        'firstname_uz_latin' => "Vakant",
                        'tabel' => "Vakant",
                        'enter_order_date' => "",
                        'enter_order_number' => "",
                        'born_date' => "",
                        'experience' => "",
                        'first_work_date' => "",
                        'tariff_scale' => "",

                    ],
                ]) : [];
                $arrayMerj = $value->employeeStaff ? array_merge($value->employeeStaff->toArray(), $newEmployee) : $newEmployee;
                $count = count($arrayMerj);
                foreach ($arrayMerj as $ke => $valueStaff) {
                    if (is_object($valueStaff) && property_exists($valueStaff, 'is_main_staff') && $valueStaff->is_main_staff == 1) {
                        $count++;
                    }
                    $personalType = $value->personalType ? $value->personalType['name_' . $language] : '';
                    $expenceType = $value->expenceType ? $value->expenceType['name_' . $language] : '';
                    $a = explode("-", $personalType)[0];
                    $b = explode("-", $expenceType)[0];
                    $excel[] = (object) [
                        "№" => $key + 1 + $page * $perPage - $perPage,
                        // "ID" => $value->id,
                        "FunctionalName" => $value->department ? ($value->department->functionalDepartment ? $value->department->functionalDepartment['name_' . $language] : '') : '',
                        "FunctionalCode" => $value->department ? ($value->department->functionalDepartment ? $value->department->functionalDepartment->functional_department_code : '') : '',
                        "Branch" => $value->department ? $value->department->branch ? $value->department->branch->name : '' : '',
                        "DepartmentName" => $value->department ? $value->department['name_' . $language] : '',
                        "DepartmentCode" => $value->department ? $value->department->department_code : '',
                        "PositionName" => $value->position ? $value->position['name_' . $language] : '',
                        "PositionCode" => $value->position ? $value->position->code : '',
                        "Status_main" => $valueStaff['is_main_staff'],
                        "Status" => $valueStaff['is_main_staff'] == 99 ? 'Vakant' : ($valueStaff['is_main_staff'] == 1 ? 'Active' : ($valueStaff['is_main_staff'] == 0 ? 'VVB' : '-')),
                        "RangeName" => $value->range ? $value->range['name_' . $language] : '',
                        "RangeCode" => $value->range ? $value->range->code : '',
                        "Shift" => $valueStaff['shift_id'] ? $valueStaff['shift_id'] : '',
                        "firstname" => $valueStaff['employee']['firstname_' . $language],
                        "lastname" => $valueStaff['employee']['firstname_' . $language],
                        "middlename" => $valueStaff['employee']['middlename_' . $language],
                        "tabel" => $valueStaff['employee']['tabel'],
                        "enterOrderDate" => $valueStaff['employee']['enter_order_date'],
                        "enterOrderNumber" => $valueStaff['employee']['enter_order_number'],
                        "bornDate" => $valueStaff['employee']['born_date'],
                        "experience" => $valueStaff['employee']['experience'],
                        "firstWorkDate" => $valueStaff['employee']['first_work_date'],
                        "Category" =>'' ,//$valueStaff['employee'] && $valueStaff['employee']['tariff_scale'] ? $valueStaff['employee']['tariff_scale']['category'] : '',
                        "personalType" => $personalType,
                        "expenceType" => $expenceType,
                        "BP" => $value->rate_count_bp,
                        "TS" => $value->rate_count,
                        "XS" => $count,
                        "AS" => count($value->employeeStaff),
                        "BPV" => (int) $value->rate_count_bp - (int) $count,
                        "TSV" => (int) ($value->rate_count) - (int) ($count),
                        "Coeff" => $coeff_code,
                        "WB" => $value->range ? (strtoupper(substr($value->range->code, 0, 1)) === "E" ? "W" : "B") : '',
                        "DirInDir" => $a == 1 && $b == 5 ? 'Dir' : $a == 9 && $b == 6 ? 'Kasana' : 'InDir',
                    ];
                }
            }

            return $excel;
        } else {
            return [
                'employee' => $employeeStaffFull->select('staff.*')->distinct()
                    ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
                'coefficient' => Coefficient::select('id', 'code', 'description_ru', 'description_uz_cyril', 'description_uz_latin')->get(),
            ];
            // return 5;   $employeeStaffFull;
        }

    }
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\EmployeeStaff  $EmployeeStaff
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeStaff $EmployeeStaff, Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeStaff  $EmployeeStaff
     * @return \Illuminate\Http\Response
     */
    public function editStaffHistory(Request $request)
    {
        $form = $request->input('form');
        $model = EmployeeStaff::where('id', $form['id'])->first();
        $model->enter_order_date = $form['enter_order_date'];
        $model->leave_order_date = $form['leave_order_date'];
        $model->save();
    }
    public function leavingtest()
    {
        // return
        $a = EmployeeStaff::whereNotNull('leaving_reason_id')
            ->with('leavingReason')
            ->whereHas('leavingReason')
            // ->limit(10)
            ->get()
            // ->count('id')
        ;
        foreach ($a as $key => $value) {
            if (isset($value->leavingReason)) {
                $staffLeaving = StaffLeaving::where('employee_staff_id', $value->id)->where('leaving_reasons_id', $value->leavingReason->id)->first();
                if (!$staffLeaving) {
                    $staffLeaving = new StaffLeaving();
                    $staffLeaving->employee_staff_id = $value->id;
                    $staffLeaving->leaving_reasons_id = $value->leavingReason->id;
                    $staffLeaving->name_uz_latin = $value->leavingReason->name_uz_latin;
                    $staffLeaving->name_uz_cyril = $value->leavingReason->name_uz_cyril;
                    $staffLeaving->name_ru = $value->leavingReason->name_ru;
                    $staffLeaving->save();
                }
            }
        }
    }

    public function update(Request $request)
    {
        $leavingReason = LeavingReason::where('id', $request->input('leaving_reason_id'))->first();
        $model = EmployeeStaff::where('employee_id', $request->input('employee_id'))
            ->where('is_active', 1)
            ->get();
        foreach ($model as $key => $value) {
            $value->leave_order_number = $request->input('leave_order_number');
            $value->leave_order_date = $request->input('leave_order_date');
            $value->leave_date = $request->input('leave_date');
            $value->leaving_reason_id = $request->input('leaving_reason_id');
            $value->is_active = 0;
            $value->save();

            $staffLeaving = new StaffLeaving();
            $staffLeaving->employee_staff_id = $value->id;
            $staffLeaving->name_uz_latin = $leavingReason->name_uz_latin;
            $staffLeaving->name_uz_cyril = $leavingReason->name_uz_cyril;
            $staffLeaving->name_ru = $leavingReason->name_ru;
            $staffLeaving->save();

            // return $request->input('leaving_reson_id');
        }
        $employee = Employee::find($request->input('employee_id'));
        $employee->is_active = 0;
        $employee->save();

        return 'Successfull saved!';
    }

    public function destroy(EmployeeStaff $EmployeeStaff, $id)
    {
        // $model = EmployeeStaff::find($id);
        // $model->delete();
        $model = EmployeeStaff::find($id);
        $model->is_active = 0;
        $model->save();
    }

    public function getHistory(Request $request)
    {
        $employee_id = $request->input('employee_id');

        return EmployeeStaff::where('employee_id', $employee_id)
            ->with('staff.department')
            ->with('staff.position')
            ->with('department')
            ->with('tariffScale')->get();
    }

    public function changeMainStaff(Request $request)
    {
        $employeeStaffs = EmployeeStaff::where('employee_id', $request['employee_id'])->where('is_active', 1)->get();
        foreach ($employeeStaffs as $key => $employeeStaff) {
            if ($employeeStaff->staff_id == $request['staff_id']) {
                $employeeStaff->is_main_staff = 1;
            } else {
                $employeeStaff->is_main_staff = 0;
            }
            $employeeStaff->save();
        }

        return $employeeStaffs;
    }
    public function employeeStaff(Request $request)
    {
        $language = $request->input('language');
        $lang = $request->input('language') == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
        $search = $request->input('search');
        // $employeeStaffs = EmployeeStaff::where('is_active',1)->query();

        $employeeStaffs = EmployeeStaff::select('id', 'employee_id', 'staff_id', 'is_active');

        $employeeStaffs->with([
            'employee' => function ($q) use ($lang, $search) {
                $q->select([
                    'id',
                    'tabel',
                    'firstname_' . $lang . ' as firstname',
                    'lastname_' . $lang . ' as lastname',
                    'middlename_' . $lang . ' as middlename'
                ])
                    ->where('is_active', 1);
            }
        ])
            ->with([
                'staff' => function ($qu) use ($language) {
                    $qu->select('id', 'department_id', 'position_id')
                        ->with([
                            'department' => function ($que) use ($language) {
                                $que->select('id', 'department_code', 'name_' . $language);
                            }
                        ])
                        ->with([
                            'position' => function ($que) use ($language) {
                                $que->select('id', 'name_' . $language);
                            }
                        ])
                        ->where('is_active', 1);
                }
            ])
            ->where('is_active', 1);
        // ->with('employee');

        if (isset($search) && $search):
            $employeeStaffs->whereHas('employee', function ($q) use ($search) {
                $q->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%")
                    ->orWhere('tabel', 'like', '%' . $search . '%');
            })
                ->where('is_active', 1)
                ->orWhereHas('staff', function ($q) use ($search) {
                    $q->whereHas('department', function ($qu) use ($search) {
                        $qu->where('department_code', 'like', '%' . $search . '%')
                            ->orWhere('name_ru', 'like', '%' . $search . '%')
                            ->orWhere('name_uz_latin', 'like', '%' . $search . '%')
                            ->orWhere('name_uz_cyril', 'like', '%' . $search . '%');
                    })
                        ->where('is_active', 1)
                        ->orWhereHas('position', function ($que) use ($search) {
                            $que->where('name_ru', 'like', '%' . $search . '%')
                                ->orWhere('name_uz_latin', 'like', '%' . $search . '%')
                                ->orWhere('name_uz_cyril', 'like', '%' . $search . '%');
                        })
                        ->where('is_active', 1);
                })
                ->where('is_active', 1);
        endif;

        return $employeeStaffs->paginate(20);
    }
}
