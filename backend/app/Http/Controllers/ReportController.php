<?php

namespace App\Http\Controllers;

use Adldap\Query\Builder;
use Illuminate\Http\Request;
use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\CompanyRequisite;
use App\Http\Models\Staff;
use App\Http\Models\Employee;
use App\Http\Models\ReportTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\SelectedTemplates;
use App\Http\Models\Mailing;
use App\User;
use Carbon\Traits\Timestamp;
use DocumentTypes;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function As400ToExcel(Request $request)
    {
        $data = ['z101ptpf'];
        $response = Http::post('http://edo-db2.uzautomotors.com/api/as400-to-excel', $data);
        $response = $response->body();
        return $response;
    }

    public function index(Request $request)
    {
        $departments = Department::with('managerStaff')
            ->with('staff')
            ->with('staff.employees')
            ->with('staff.position');

        return $departments->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function getDepartment(Request $request)
    {
        $parent_id = $request->input('parent_id') ? $request->input('parent_id') : null;
        $departments = Department::select('id', 'name_ru', 'name_uz_latin', 'name_uz_cyril')->where('parent_id', $parent_id)->get();

        // $arr = $this->getDepIds($departments[1]->id);
        // $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();

        foreach ($departments as $key => $department) {
            $arr = $this->getDepIds($department->id);
            $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();

            $document = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            });

            $departments[$key]->create_documents = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', '>', 0)->count();

            $departments[$key]->completed_on_time = 0;
            $departments[$key]->failed_in_time = 0;
            $departments[$key]->published = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 1)->count();

            $departments[$key]->processing = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 2)->count();

            $departments[$key]->signed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 3)->count();

            $departments[$key]->ready = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 4)->count();

            $departments[$key]->completed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 5)->count();

            $departments[$key]->cancelled = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 6)->count();

            $departments[$key]->expired = $document->whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '<', date("Y-m-d H:i:s"))
                    ->whereIn('status', [0, 3]);
            })->count();

            $departments[$key]->waiting = $document->whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->where('status', 0);
            })->count();

            $departments[$key]->prosesing = $document->whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->where('status', 3);
            })->count();
        }

        $arr = $this->getDepIds($parent_id);
        $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();
        $all = [
            'name_uz_latin' => 'Jami:',
            'name_uz_cyril' => 'Жами:',
            'name_ru' => 'Итого:',
            'create_documents' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', '>', 0)->count(),
            'published' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 1)->count(),
            'processing' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 2)->count(),
            'signed' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 3)->count(),
            'ready' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 4)->count(),
            'completed' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 5)->count(),
            'cancelled' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 6)->count(),
        ];

        $staff_ids = collect(Staff::select('id')->where('department_id', $parent_id)->get())->pluck('id')->toArray();
        if ($parent_id) {
            $department = Department::select('id', 'name_ru', 'name_uz_latin', 'name_uz_cyril')->where('id', $parent_id)->first();
            $department->create_documents = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', '>', 0)->count();
            $department->published = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 1)->count();

            $department->processing = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 2)->count();

            $department->signed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 3)->count();

            $department->ready = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 4)->count();

            $department->completed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 5)->count();

            $department->cancelled = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 6)->count();
        } else {
            $department = 0;
        }

        return [
            'department' => $department,
            'departments' => $departments,
            'all' => $all,
        ];
    }

    public function getDepartmentOkd(Request $request)
    {
        if ($request->input('parent_id')) {
            $parent_id = $request->input('parent_id');
        } elseif (Auth::user()->hasPermission('okd_kanselyariya')) {
            $parent_id = 1;
        } else {
            $parent_id = Auth::user()->employee->mainStaff->first()->department_id;
        }
        // $parent_id = $request->input('parent_id') ? $request->input('parent_id') : Auth::user()->employee->mainStaff->first()->department_id;
        // return Auth::user()->employee->mainStaff->first()->department_id;
        $departments = Department::select('id', 'name_ru', 'name_uz_latin', 'name_uz_cyril')->where('parent_id', $parent_id)->get();

        $arr = $this->getDepIds($departments[1]->id);
        // $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();

        foreach ($departments as $key => $department) {
            $arr = $this->getDepIds($department->id);
            $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();

            // $document = Document::leftJoin('document_signers', 'document_signers.document_id', '=', 'documents.id');
            // ->select(
            //     'documents.id as doc_id',
            //     'documents.status as doc_status',
            //     'document_signers.staff_id as staff_id',
            //     'document_signers.status as doc_signer_status',
            //     'document_signers.action_type_id as action_type_id',
            //     'document_signers.due_date as due_date',
            //     'document_signers.taken_datetime as taken_datetime',
            //     'document_signers.updated_at as updated_at'
            // );
            // ->where(DB::raw("timestamp(`document_signers`.`due_date`)"), '>=', DB::raw("timestamp(`document_signers`.`updated_at`)"));

            $document = Document::leftJoin('document_signers', 'document_signers.document_id', '=', 'documents.id')
                ->select('documents.id')
                ->whereIn('document_signers.staff_id', $staff_ids)
                ->whereNotIn('document_signers.action_type_id', [3, 6])
                ->where('documents.status', '>', 0)
                ->groupBy('documents.id')->get();

            $department->inbox = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6]);
            })
                ->where('status', '>', 0)->count();

            $department->done = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [1, 2]);
            })
                ->where('status', '>', 0)->count();

            $department->on_time = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [1, 2])
                    ->where(DB::raw("timestamp(`document_signers`.`due_date`)"), '>=', DB::raw("timestamp(`document_signers`.`updated_at`)"));
            })
                ->where('status', '>', 0)->count();

            $department->out_of_date = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [1, 2])
                    ->where(DB::raw("timestamp(`document_signers`.`due_date`)"), '<', DB::raw("timestamp(`document_signers`.`updated_at`)"));
            })
                ->where('status', '>', 0)->count();

            $department->ok = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->where('status', 1);
            })
                ->where('status', '>', 0)->count();

            $department->cancel = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->where('status', 2);
            })
                ->where('status', '>', 0)->count();

            $department->on_performance = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [0, 3])
                    ->where('due_date', '<', date("Y-m-d h:i:s"));
            })
                ->where('status', '>', 0)->count();

            $department->up_to_1_day = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [0, 3])
                    ->where('due_date', '<', date("Y-m-d h:i:s"))
                    ->where('due_date', '>=', date("Y-m-d h:i:s", time() - 86400));
            })
                ->where('status', '>', 0)->count();

            $department->up_to_2_3_days = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [0, 3])
                    ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400))
                    ->where('due_date', '>=', date("Y-m-d h:i:s", time() - 86400 * 3));
            })
                ->where('status', '>', 0)->count();

            $department->more_than_3_days = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [0, 3])
                    ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400 * 3));
            })
                ->where('status', '>', 0)->count();

            $department->overdue = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereNotNull('taken_datetime')
                    ->whereNotIn('action_type_id', [3, 6])
                    ->whereIn('status', [0, 3])
                    ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400 * 3));
            })
                ->where('status', '>', 0)->count();
        }

        $arr = $this->getDepIds($parent_id);
        $staff_ids = collect(Staff::select('id')->whereIn('department_id', $arr)->get())->pluck('id')->toArray();
        $all = [
            'name_uz_latin' => 'Jami:',
            'name_uz_cyril' => 'Жами:',
            'name_ru' => 'Итого:',
            'create_documents' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', '>', 0)->count(),
            'published' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 1)->count(),
            'processing' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 2)->count(),
            'signed' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 3)->count(),
            'ready' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 4)->count(),
            'completed' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 5)->count(),
            'cancelled' => Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 6)->count(),
        ];

        $staff_ids = collect(Staff::select('id')->where('department_id', $parent_id)->get())->pluck('id')->toArray();
        if ($parent_id) {
            $employees = Employee::whereHas('employeeStaff', function ($q) use ($parent_id) {
                $q->whereIn('staff_id', collect(Staff::where('department_id', Department::find($parent_id)->id)->get())->pluck('id'))
                    ->where('is_active', 1);
            })->get();
            foreach ($employees as $key => $employee) {
                $emp_staff_ids = collect($employee->staff)->pluck('id');
                $employee->inbox = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6]);
                })
                    ->where('status', '>', 0)->count();

                $employee->done = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [1, 2]);
                })
                    ->where('status', '>', 0)->count();

                $employee->on_time = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [1, 2])
                        ->where(DB::raw("timestamp(`document_signers`.`due_date`)"), '>=', DB::raw("timestamp(`document_signers`.`updated_at`)"));
                })
                    ->where('status', '>', 0)->count();

                $employee->out_of_date = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [1, 2])
                        ->where(DB::raw("timestamp(`document_signers`.`due_date`)"), '<', DB::raw("timestamp(`document_signers`.`updated_at`)"));
                })
                    ->where('status', '>', 0)->count();

                $employee->ok = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->where('status', 1);
                })
                    ->where('status', '>', 0)->count();

                $employee->cancel = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->where('status', 2);
                })
                    ->where('status', '>', 0)->count();

                $employee->on_performance = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [0, 3])
                        ->where('due_date', '<', date("Y-m-d h:i:s"));
                })
                    ->where('status', '>', 0)->count();

                $employee->up_to_1_day = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [0, 3])
                        ->where('due_date', '<', date("Y-m-d h:i:s"))
                        ->where('due_date', '>=', date("Y-m-d h:i:s", time() - 86400));
                })
                    ->where('status', '>', 0)->count();

                $employee->up_to_2_3_days = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [0, 3])
                        ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400))
                        ->where('due_date', '>=', date("Y-m-d h:i:s", time() - 86400 * 3));
                })
                    ->where('status', '>', 0)->count();

                $employee->more_than_3_days = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [0, 3])
                        ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400 * 3));
                })
                    ->where('status', '>', 0)->count();

                $employee->overdue = Document::whereHas('documentSigners', function ($q) use ($employee, $emp_staff_ids) {
                    $q->whereIn('staff_id', $emp_staff_ids)
                        ->where(function ($q) use ($employee) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $employee->id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime')
                        ->whereNotIn('action_type_id', [3, 6])
                        ->whereIn('status', [0, 3])
                        ->where('due_date', '<', date("Y-m-d h:i:s", time() - 86400 * 3));
                })
                    ->where('status', '>', 0)->count();
            }
            $department = Department::select('id', 'name_ru', 'name_uz_latin', 'name_uz_cyril')->where('id', $parent_id)->first();
            $department->create_documents = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', '>', 0)->count();
            $department->published = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 1)->count();

            $department->processing = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 2)->count();

            $department->signed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 3)->count();

            $department->ready = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 4)->count();

            $department->completed = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 5)->count();

            $department->cancelled = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('action_type_id', 6);
            })->where('status', 6)->count();
        } else {
            $department = 0;
        }

        return [
            'department' => $department,
            'departments' => $departments,
            'employees' => $employees,
            'all' => $all,
        ];
    }

    public function getDepIds($dep_id)
    {
        $ids = [$dep_id];
        $deps = Department::select('id')->where('parent_id', $dep_id)->get();
        foreach ($deps as $key => $value) {
            $ids = array_merge($ids, $this->getDepIds($value->id));
        }
        return $ids;
    }

    public function getUserDocument($id)
    {
        // $user = Auth::user()->employee_id;
        $documents = Document::with('DocumentType')->with('documentTemplate')->where('created_employee_id', $id)->orderBy('created_at', 'desc')->get();
        return $documents;
    }

    public function getZnzReport(Request $request)
    {
        $filter = $request->input('filter');
        // return $filter;
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $statuses = [];
        if ($filter['document_status'][0]) {
            $statuses[] = 1;
        }
        if ($filter['document_status'][1]) {
            $statuses[] = 2;
        }
        if ($filter['document_status'][2]) {
            $statuses[] = 6;
        }

        $documents = Document::select(
            'id',
            'department_id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_number',
            'status',
            'document_id',
            'created_employee_id',
            'base64',
            'pdf_file_name'
        )->where('documents.document_template_id', '=', 41)
            ->whereIn('documents.status', $statuses)
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['department' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'manager_staff_id',
                    'name_' . $locale
                )
                    ->with('managerStaff.employees')
                    ->with('managerStaff.position');
            }])
            ->with(['documentTemplate' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_' . $lang,
                    'middlename_' . $lang,
                    'firstname_' . $lang,
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_' . $locale
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
                // ->parentDepartments('document.created_employee_id');
            }])
            ->with(['documentDetails' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'document_id',
                    'content'
                )
                    ->with(['documentDetailEmployees' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'id',
                            'document_detail_id',
                            'employee_id'
                        )
                            ->with(['employee' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                );
                            }]);
                    }]);
            }])
            ->with(['documentSigners' => function ($q) use ($filter, $user) {
                $q->select('id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                    ->whereNotNull('taken_datetime')
                    // ->whereIn('status', [0, 3])
                    ->with('signerEmployee')
                    ->with(['employeeStaffs' => function ($empStaffquery) {
                        $empStaffquery->with('employee')
                            ->where('is_active', '=', 1);
                    }]);
            }]);

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if ($filter['document_start_date']) {
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        // if (isset($filter['document_type_id']) && $filter['document_type_id']) {
        //     $documents->where('document_type_id', $filter['document_type_id']);
        // }

        // if (isset($filter['document_template_id']) && $filter['document_template_id']) {
        //     $documents->where('document_template_id', $filter['document_template_id']);
        // }

        if ($filter['document_number']) {
            $documents->where('document_number', 'like', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            $documents->whereDoesntHave('documentSigners', function ($q) {
                $q->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3]);
            });
        }

        return ['documents' => $documents
            ->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page)];
    }

    public function getLspReport(Request $request)
    {
        $filter = $request->input('filter');
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $statuses = [];
        if ($filter['document_statuses'][0]) {
            $statuses[] = 1;
        }
        if ($filter['document_statuses'][1]) {
            $statuses[] = 2;
        }
        if ($filter['document_statuses'][2]) {
            $statuses[] = 3;
        }
        if ($filter['document_statuses'][3]) {
            $statuses[] = 4;
        }
        if ($filter['document_statuses'][4]) {
            $statuses[] = 5;
        }
        if ($filter['document_statuses'][5]) {
            $statuses[] = 6;
        }

        $documents = Document::select(
            'id',
            'department_id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_number',
            'status',
            'created_employee_id',
            'base64',
            'pdf_file_name'
        )->where('documents.document_type_id', '=', 7)->whereIn('documents.status', $statuses)
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['department' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'manager_staff_id',
                    'name_' . $locale
                )
                    ->with('managerStaff.employees')
                    ->with('managerStaff.position');
            }])
            ->with(['documentTemplate' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_' . $lang,
                    'middlename_' . $lang,
                    'firstname_' . $lang,
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_' . $locale
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
                // ->parentDepartments('document.created_employee_id');
            }])
            ->with(['documentDetails' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'document_id',
                    'content'
                )
                    ->with(['documentDetailEmployees' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'id',
                            'document_detail_id',
                            'employee_id'
                        )
                            ->with(['employee' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                );
                            }]);
                    }])->with(['documentDetailAttributeValues' => function ($query) use ($lang, $locale) {
                        $query->select('*')->whereIn('d_d_attribute_id', [DB::raw(("SELECT id  FROM document_detail_attributes WHERE attribute_name_ru LIKE 'Настоящая (С)'"))]);
                    }]);
            }])
            ->with(['documentSigners' => function ($q) use ($filter, $user) {
                $q->select('id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                    ->whereNotNull('taken_datetime')
                    // ->whereIn('status', [0, 3])
                    ->with('signerEmployee')
                    ->with(['employeeStaffs' => function ($empStaffquery) {
                        $empStaffquery->with('employee')
                            ->where('is_active', '=', 1);
                    }]);
            }]);

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if (isset($filter['document_start_date'])) {
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        // if (isset($filter['document_statuses']) && $filter['document_statuses']) {
        //     $documents->whereIn('status', $filter['document_statuses']);
        // }

        if (isset($filter['document_template_id']) && $filter['document_template_id']) {
            $documents->where('document_template_id', $filter['document_template_id']);
        }

        if ($filter['document_number']) {
            $documents->where('document_number', 'like', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            $documents->whereDoesntHave('documentSigners', function ($q) {
                $q->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3]);
            });
        }

        return ['documents' => $documents
            ->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page)];
    }

    public function getLspExcel(Request $request)
    {
        $filter = $request->input('filter');
        $locale = $request->input('locale');
        $lang = $request->input('locale') == 'ru' ? 'uz_cyril' : $request->input('locale');
        $user = User::with('roles')->find(Auth::id());
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $page = $request->input('page');
        $perPage = $request->input('perPage');
        $statuses = [];
        if ($filter['document_statuses'][0]) {
            $statuses[] = 1;
        }
        if ($filter['document_statuses'][1]) {
            $statuses[] = 2;
        }
        if ($filter['document_statuses'][2]) {
            $statuses[] = 3;
        }
        if ($filter['document_statuses'][3]) {
            $statuses[] = 4;
        }
        if ($filter['document_statuses'][4]) {
            $statuses[] = 5;
        }
        if ($filter['document_statuses'][5]) {
            $statuses[] = 6;
        }
        $documents = Document::select(
            'id',
            'department_id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_number',
            'status',
            'created_employee_id',
            'base64',
            'pdf_file_name'
        )->where('documents.document_type_id', '=', 7)
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['department' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'manager_staff_id',
                    'name_' . $locale
                )
                    ->with('managerStaff.employees')
                    ->with('managerStaff.position');
            }])
            ->with(['documentTemplate' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_' . $lang,
                    'middlename_' . $lang,
                    'firstname_' . $lang,
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_' . $locale
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
            }])
            ->with(['documentDetails' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'document_id',
                    'content'
                )
                    ->with(['documentDetailEmployees' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'id',
                            'document_detail_id',
                            'employee_id'
                        )
                            ->with(['employee' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                );
                            }]);
                    }])->with(['documentDetailAttributeValues']);
            }])
            ->with(['documentSigners' => function ($q) use ($filter, $user) {
                $q->select('id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                    ->whereNotNull('taken_datetime')
                    ->with('signerEmployee')
                    ->with(['employeeStaffs' => function ($empStaffquery) {
                        $empStaffquery->with('employee')
                            ->where('is_active', '=', 1);
                    }]);
            }]);

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if (isset($filter['document_start_date'])) {
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        if (isset($filter['document_statuses']) && $filter['document_statuses']) {
            $documents->whereIn('status', $statuses);
        }

        if (isset($filter['document_template_id']) && $filter['document_template_id']) {
            $documents->where('document_template_id', $filter['document_template_id']);
        }

        if ($filter['document_number']) {
            $documents->where('document_number', 'like', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            $documents->whereDoesntHave('documentSigners', function ($q) {
                $q->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3]);
            });
        }

        $excel = [];
        $document_number = '';
        $document_date = '';
        $total_sum = '';
        $template = '';
        $writer = '';
        $department = '';
        $receiver = '';
        $status = '';
        foreach ($documents->orderByDesc('documents.document_date')->get() as $index => $value) {
            $document_number = $value->document_number ? $value->document_number : '';
            $document_date = $value->document_date ? $value->document_date : '';
            $total_sum = $value->documentDetails &&
                $value->documentDetails[0]->documentDetailAttributeValues->count() > 0 ?
                $value->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : '';
            $template = $value->documentTemplate ? $value->documentTemplate->name_ru : '';
            $writer = $value->employee ? $value->employee->lastname_uz_cyril . " " . $value->employee->firstname_uz_cyril . " " .
                $value->employee->middlename_uz_cyril : '';
            $department = $value->employee->employeeStaff->count() > 0 ? $value->employee->employeeStaff[0]->staff->department->name_ru : '';
            $receiver = $value->department && $value->department->managerStaff && $value->department->managerStaff->employees->count() > 0 ?
                $value->department->managerStaff->employees[0]->lastname_uz_cyril . " " . $value->department->managerStaff->employees[0]->firstname_uz_cyril .
                " " . $value->department->managerStaff->employees[0]->middlename_uz_cyril : '';
            if ($value->documentSigners) {
                $pending_action = '';
                foreach ($value->documentSigners as $key => $document_signer) {
                    if (
                        $value->status != 6 &&
                        ($document_signer->status == 0 || $document_signer->status == 3)
                    ) {
                        if ($document_signer->signerEmployee) {
                            $pending_action = $pending_action . '' . $document_signer->signerEmployee->lastname_uz_cyril . " " .
                                $document_signer->signerEmployee->firstname_uz_cyril . " " .
                                $document_signer->signerEmployee->middlename_uz_cyril;
                        } else {
                            $pending_action = $document_signer->employeeStaffs ?
                                $pending_action . ', ' . $document_signer->employeeStaffs->employee->lastname_uz_cyril . " " .
                                $document_signer->employeeStaffs->employee->firstname_uz_cyril . " " .
                                $document_signer->employeeStaffs->employee->middlename_uz_cyril : "";
                        }
                    }
                }
            }
            $status = $value->status == 0 ? 'Новый' : ($value->status == 1 ? 'Опубликованный' : ($value->status == 2 ? 'Обработка' : ($value->status = 3 ? 'Подписано' : ($value->status == 4 ? 'Выполнено' : ($value->status == 5 ? 'Завершено' : 'Отменен')))));
            array_push($excel, (object)[
                "№" => $index + 1 + $page * $perPage - $perPage,
                "Номер документа" => $document_number,
                "Настоящая сумма" => $total_sum,
                "Дата документа" => $document_date,
                "Тип документа" => $template,
                "Ответственный" => $writer,
                "Отправитель" => $department,
                "Получатель" => $receiver,
                "Ожидающее действие" => $pending_action,
                "Статус" => $status,
            ]);
        }
        return $excel;
    }

    public function unvReportDocument(Request $request, $id)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('locale');

        $reportTemplate = ReportTemplate::find($id);
        $select = ['documents.id as document_id', 'documents.pdf_file_name as pdf_file_name', 'documents.pdf_file_name as pdf_file_name', 'document_details.id'];
        foreach ($reportTemplate->reportColumns as $key => $reportColumn) {
            if ($reportColumn->report_column_table == 1) {
                $select[] = 'documents.' . $reportColumn->report_column_name;
            }
            if ($reportColumn->report_column_table == 2) {
                if ($reportColumn->report_column_name == 'employee') {
                    $select[] = 'employees.' . $reportColumn->table_list_column_name;
                } else {
                    $select[] = 'document_detail_employees.' . $reportColumn->report_column_name;
                }
            }
        }

        $report = DocumentDetail::leftJoin('documents', 'documents.id', 'document_details.document_id')
            ->leftJoin('document_detail_employees', 'document_detail_employees.document_detail_id', 'document_details.id')
            ->leftJoin('employees', 'document_detail_employees.employee_id', 'employees.id')
            ->select($select)
            ->with(['documentDetailContents' => function ($q) {
                $q->select('id', 'document_detail_id', 'd_d_attribute_id', 'attribute_name', 'value');
            }])
            ->with(['documentSigners' => function ($q) use ($language) {
                $q->select('id', 'document_id', 'action_type_id', 'fio', 'status', 'staff_id', 'signer_employee_id')
                    // ->with(['signerEmployee' => function ($q2) use ($language) {
                    //     $q2->select(
                    //         'id',
                    //         'lastname_' . $language,
                    //         'middlename_' . $language,
                    //         'firstname_' . $language,
                    //         'tabel'
                    //     );
                    // }])
                    ->with(['employeeStaffs' => function ($empStaffquery) use ($language) {
                        $empStaffquery->with(['employee' => function ($q2)  use ($language) {
                        }])
                            ->select(
                                'employee_id',
                                'staff_id'
                            )
                            ->where('is_active', '=', 1);
                    }]);;
            }])
            ->where('documents.status', '>', 0)
            ->orderBy('documents.id', 'desc');

        if ($reportTemplate->document_type_id) {
            $report->where('documents.document_type_id', $reportTemplate->document_type_id);
        }
        if ($reportTemplate->document_template_id) {
            $report->where('documents.document_template_id', $reportTemplate->document_template_id);
        }
        return [
            'report' => $report->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'report_template' => $reportTemplate
        ];
    }

    public function downloadExcel(Request $request, $id)
    {
        # code...
    }

    // OKD uchun xisobot Yangisi
    public function reportFull(Request $request)
    {
        // return 0;
        $from_date = ($request['search']['from_date']) ? $request['search']['from_date'] : '';
        $to_date = ($request['search']['to_date']) ? $request['search']['to_date'] : '';
        $jurnal = ($request['search']['incoming_journal_id']) ? $request['search']['incoming_journal_id'] : '';
        // $from_date = '2023-06-01';
        // $to_date = date('Y-m-d H:i:s');
        $data_range = (($from_date) && ($to_date)) ?  "and d.document_date BETWEEN  '$from_date'  and  '$to_date' " : "";

        // $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        // return
        // $parents = implode(',', $parent);
        $parents = Mailing::select('employee_id')->pluck('employee_id')->implode(',');
        // return $data_range;

        $documentQuery = "
        select MIN(doc_id), s.id staff_id, MIN(CONCAT(dep.name_uz_latin, ' - ',p.name_uz_latin )) as shtati, SUM(CAST(all_doc AS INTEGER)) as all_doc, SUM(CAST(all_ok AS INTEGER)) as all_ok, SUM(CAST(all_ok_in AS INTEGER)) as all_ok_in, SUM(CAST(all_ok_out AS INTEGER)) as all_ok_out, SUM(CAST(all_execution AS INTEGER)) as all_execution,
        SUM(CAST(execution_one AS INTEGER)) as execution_one, SUM(CAST(execution_two AS INTEGER)) as execution_two, SUM(CAST(execution_three AS INTEGER)) as execution_three, SUM(CAST(execution_out AS INTEGER)) as execution_out, SUM(CAST(cancel AS INTEGER)) as cancel

        from (SELECT d.id doc_id, ds.staff_id staff_id, '1' all_doc,  
        case WHEN ds.status=1 THEN 1 else 0 end all_ok,    
        case WHEN ds.status=1 and (ds.due_date >= ds.sign_at or ds.due_date is null) THEN 1 else 0 end all_ok_in,
        case WHEN ds.status=1 and ds.due_date < ds.sign_at THEN 1 else 0 end all_ok_out,
        case WHEN ds.status in (0,3,4) THEN 1 else 0 end all_execution,
        case WHEN ds.status in (0,3,4) and NOW() BETWEEN ds.due_date - INTERVAL '1 DAY' AND ds.due_date
            THEN 1 else 0 end execution_one,
        case WHEN ds.status in (0,3,4) and NOW() BETWEEN ds.due_date - INTERVAL '2 DAY' AND ds.due_date - INTERVAL '1 DAY'
            THEN 1 else 0 end execution_two,
        case WHEN ds.status in (0,3,4) and  NOW() <= ds.due_date - INTERVAL '3 DAY'
            THEN 1 else 0 end execution_three,
        case WHEN ds.status in (0,3,4) and ds.due_date < NOW() THEN 1 else 0 end execution_out, 
        case WHEN ds.status = 2 THEN 1 else 0 end cancel   

        FROM documents d

        inner join document_signers ds on ds.document_id=d.id

        where ds.action_type_id = 4 and d.deleted_at is null and d.status in (3,4,5) and ds.parent_employee_id is null
            and ds.deleted_at is null and ds.taken_datetime is not null and ";
        if($jurnal){
            $documentQuery .= "(";
            foreach ($jurnal as $key => $code) {
                if($key==0){
                    if($code==30){
                        $documentQuery .= "d.document_number ilike '%$code-%' and d.document_template_id=70";
                    }
                    else{
                        $documentQuery .= "d.document_number ilike '%-$code-%'";
                    }
                }
                else{
                    if($code==30){
                        $documentQuery .= " or d.document_number ilike '%$code-%' and d.document_template_id=70";
                    }
                    else{
                        $documentQuery .= " or d.document_number ilike '%-$code-%'";
                    }
                }
            }
            $documentQuery .= ") and";
        }
        $documentQuery .= " ds.due_date is not null $data_range and EXISTS (select ds1.id from document_signers ds1 where ds1.document_id=d.id and ds1.action_type_id=11 and staff_id=3360)) t

        inner join staff s on s.id=t.staff_id and s.is_active=true
        inner join positions p on p.id=s.position_id
        inner join departments dep on dep.id=s.department_id
        group by s.id              
        ORDER BY all_doc  DESC";
        // return $documentQuery;
        // (109, 111, 969, 976, 978, 7312, 8871, 11876)
        // and (d.document_date between (NOW()-INTERVAL 10 DAY) and (NOW()))
        $report = DB::select($documentQuery);
        return $report;
    }
    

    public function itemFull(Request $request){

        // return $request;
        
        $reportType = $request->input('route_array')[0];
        $staff_id = $request->input('route_array')[1];
        $from_date = $request->input('route_array')[2];
        $to_date = $request->input('route_array')[3];
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parents = Mailing::select('employee_id')->get()->pluck('employee_id');
        return
        $document = Document::select('id')
        ->whereNotIn('status', [0,6])
        ->whereBetween('created_at', [$from_date, $to_date])
        ->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
            $q->where('staff_id',$staff_id)
            ->whereIn('parent_employee_id',$parents)
            ->whereIn('action_type_id', [4]);
        })
        ->whereHas('DocumentSigners', function ($q){
            $q->where('action_type_id', 11);
        })->get();

    }

    // OKD uchun xisobot Yangisi
    public function reportFull2(Request $request)
    {
        
        $from_date = ($request['search']['from_date']) ? $request['search']['from_date'] : '';
        $to_date = ($request['search']['to_date']) ? $request['search']['to_date'] : '';
        // $from_date = '2023-06-01';
        // $to_date = date('Y-m-d H:i:s');
        $data_range = (($from_date) && ($to_date)) ?  "and d.document_date BETWEEN  '" . $from_date . "'  and  '" . $to_date . "' " : "";

        // $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        // return
        // $parents = implode(',', $parent);
        $parents = Mailing::select('employee_id')->pluck('employee_id')->implode(',');
        // return $data_range;
        
        $documentQuery = "
            select doc_id, s.id staff_id, CONCAT(dep.name_uz_latin, ' - ',p.name_uz_latin ) as shtati, SUM(all_doc) as all_doc, SUM(all_ok) as all_ok, SUM(all_ok_in) as all_ok_in, SUM(all_ok_out) as all_ok_out, SUM(all_execution) as all_execution,
            SUM(execution_one) as execution_one, SUM(execution_two)as execution_two, SUM(execution_three) as execution_three, SUM(execution_out) as execution_out, SUM(cancel) as cancel

            from (SELECT d.id doc_id, ds.staff_id staff_id, '1' all_doc,  
            case WHEN ds.status=1 THEN 1 else 0 end all_ok,    
            case WHEN ds.status=1 and (ds.due_date >= ds.sign_at or ds.due_date is null) THEN 1 else 0 end all_ok_in,
            case WHEN ds.status=1 and ds.due_date < ds.sign_at THEN 1 else 0 end all_ok_out,
            case WHEN ds.status in (0,3,4) THEN 1 else 0 end all_execution,
            case WHEN ds.status in (0,3,4) and NOW() BETWEEN ds.due_date - INTERVAL 1 DAY AND ds.due_date
                THEN 1 else 0 end execution_one,
            case WHEN ds.status in (0,3,4) and NOW() BETWEEN ds.due_date - INTERVAL 2 DAY AND ds.due_date - INTERVAL 1 DAY
                THEN 1 else 0 end execution_two,
            case WHEN ds.status in (0,3,4) and  NOW() <= ds.due_date - INTERVAL 3 DAY
                THEN 1 else 0 end execution_three,
            case WHEN ds.status in (0,3,4) and ds.due_date < NOW() THEN 1 else 0 end execution_out, 
            case WHEN ds.status = 2 THEN 1 else 0 end cancel   

            FROM documents d

            inner join document_signers ds on ds.document_id=d.id

            where ds.action_type_id = 4 and d.deleted_at is null and d.status in (3,4,5) and ds.parent_employee_id in (" . $parents . ")
                and ds.deleted_at is null and ds.taken_datetime is not null and ds.due_date is not null " .$data_range. "  

            and EXISTS (select ds1.id from document_signers ds1 where ds1.document_id=d.id and ds1.action_type_id=11 and staff_id=3360)) t

            inner join staff s on s.id=t.staff_id and s.is_active=1
            inner join positions p on p.id=s.position_id
            inner join departments dep on dep.id=s.department_id
            group by s.id              
            ORDER BY all_doc  DESC
        ";
        // (109, 111, 969, 976, 978, 7312, 8871, 11876)
        // and (d.document_date between (NOW()-INTERVAL 10 DAY) and (NOW()))
        $report = DB::select($documentQuery);
        return $report;
    }
    public function itemFull2(Request $request){

        // return $request;
        
        $reportType = $request->input('route_array')[0];
        $staff_id = $request->input('route_array')[1];
        $from_date = $request->input('route_array')[2];
        $to_date = $request->input('route_array')[3];
        $journal1 = $request->input('route_array')[4];
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parents = Mailing::select('employee_id')->get()->pluck('employee_id');
        $today = date("Y-m-d H:i:s");
        $one_day = date('Y-m-d', strtotime('+1 days'));
        $two_day = date('Y-m-d', strtotime('+2 days'));
        $three_day = date('Y-m-d', strtotime('+3 days'));
        // return $reportType;
        // return
        $journals = [];
        if($journal1){
            $journals = explode(",", $journal1);
        }
        // return $journals;
        $document = Document::whereNotIn('status', [0,6])
        ->whereBetween('document_date', [$from_date, $to_date])
        ->with('DocumentSigners')
        ->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
            $q->where('staff_id',$staff_id)
            ->whereNull('parent_employee_id')
            ->whereIn('action_type_id', [4]);
        })
        ->whereHas('DocumentSigners', function ($q){
            $q->where('action_type_id', 11)
            ->where('staff_id', 3360);
        })
        ->where(function ($query) use ($journals) {
            foreach ($journals as $number) {
                if($number==30){
                    $query->orWhere('document_number', 'ILIKE', '%' . $number . '-%')
                          ->where('document_template_id', 70);
                }
                else{
                    $query->orWhere('document_number', 'ILIKE', '%-' . $number . '-%');
                }
            }
        });
        // foreach ($journals as $key => $jurnal) {
        //     $document->orWhere('document_number', 'ilike', '%' . $jurnal . '%');
        // }
        if($reportType == 'all_doc'){
            return $document->get();
        }
        if($reportType == 'all_ok'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->where('status', 1);
            });
        }
        if($reportType == 'all_ok_in'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->where('status', 1)
                ->whereColumn('due_date', '>=', 'sign_at');
            });
        }
        if($reportType == 'all_ok_out'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->where('status', 1)
                ->whereColumn('due_date', '<', 'sign_at');
            });
        }
        if($reportType == 'all_execution'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [0, 3, 4]);
            });
        }
        if($reportType == 'execution_one'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents, $one_day){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [0, 3, 4])
                ->whereBetween('due_date', [date('Y-m-d'), $one_day]);
            });
        }
        if($reportType == 'execution_two'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents, $one_day, $three_day){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [0, 3, 4])
                ->whereBetween('due_date', [$one_day, $three_day]);
            });
        }
        if($reportType == 'execution_three'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents, $three_day){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [0, 3, 4])
                ->where('due_date', '>=', $three_day);
            });
        }
        if($reportType == 'execution_out'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents, $today){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [0, 3, 4])
                ->where('due_date', '<', $today);
            });
        }
        if($reportType == 'cancel'){
            // dd( $document);
            $document->whereHas('DocumentSigners', function ($q) use($staff_id, $parents, $today){
                $q->where('staff_id',$staff_id)
                ->whereNull('parent_employee_id')
                ->whereIn('action_type_id', [4])
                ->whereIn('status', [2]);
            });
        }
        return $document->get();
    }
    public function okdReportFull(Request $request)
    {
        if(Auth::id() != 2571){
            return 'OKD report vaqtinchalik to\'htatildi.';
        }
        $from_date =  $request['search']['from_date'];

        $to_date =  $request['search']['to_date'];

        $incoming_journal =  $request['search']['incoming_journal_id'];

        $mainDep = Department::select('id')->where('parent_id', 1)->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')->whereIn('parent_id', $mainDep)->get()->pluck('id')->toArray();

        $state_ids = [];
        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->where('department_id', $value)->get()->pluck('id')->toArray();
        }
        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }

        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');


        $model = [];
       
        foreach ($state_ids as $key => $item) :
            $it = $state_ids[$key];
            // $it = $state_ids[3];
            $document_all = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('parent_employee_id', $parent)
                ->whereIn('action_type_id', [16, 4])
                ->where('status', '!=', 2)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })
                ->pluck('id')->toarray();

            $document_comp = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('parent_employee_id', $parent)
                ->whereIn('action_type_id', [16, 4])
                ->where('status', 1)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_comp_in_due_date = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('parent_employee_id', $parent)
                ->whereIn('action_type_id', [16, 4])
                ->where('status', 1)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereColumn('due_date', '>=', 'sign_at')
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_without_due_date = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->where('status', 1)
                ->whereIn('parent_employee_id', $parent)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereColumn('due_date', '<', 'sign_at')
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_signer = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereIn('parent_employee_id', $parent)
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                    ->whereHas('DocumentSigners', function ($query) {
                        $query
                        ->where('staff_id', 3360)
                        ->where('action_type_id', 11);
                    });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();



            
            $one_date = date('Y-m-d', strtotime('+1 days'));
            $two_date = date('Y-m-d', strtotime('+2 days'));
            $three_date = date('Y-m-d', strtotime('+3 days'));

            $document_do_one = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereIn('parent_employee_id', $parent)
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_do_three = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereIn('parent_employee_id', $parent)
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereBetween('due_date', [$one_date, $three_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out_three = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->whereIn('status', [0, 4, 3])
                ->where('due_date', '>=', $three_date)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereIn('parent_employee_id', $parent)
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out = DocumentSigner::select('id')
                ->whereIn('staff_id', $it)
                ->whereIn('action_type_id', [16, 4])
                ->whereIn('status', [0, 4, 3])
                ->where('due_date', '<', date('Y-m-d'))
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereIn('parent_employee_id', $parent)
                ->whereHas('Documents', function ($q) use ($template_id) {
                    $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                        $query->whereIn('id',  $template_id);
                    })
                        ->whereHas('DocumentSigners', function ($query) {
                            $query->where('staff_id', 3360)->where('action_type_id', 11);
                        });
                })
                ->whereHas('documents', function ($querys) use ($incoming_journal) {
                    $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                        if ($incoming_journal)
                            $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                                $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                    $q->whereIn('attribute_value',  $incoming_journal)
                                        ->where('table_list_id', 19)
                                        ->where('data_type_id', 6);
                                });
                            });
                    });
                })
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();


            $count = [
                count($document_all),
                count($document_comp),
                count($document_comp_in_due_date),
                count($document_without_due_date),
                count($document_signer),
                count($document_do_one),
                count($document_do_three),
                count($document_out_three),
                count($document_out)
            ];
            if (!empty($document_all)) :
                $dep = Department::where('id', $key)->get();
                $model[] = [$dep, $count];
            endif;

        // $dep = Department::where('id', $key)->get();
        // $model[] = [$dep, $count];


        endforeach;

        return $model;
    }

    public function OkdReportItemFull(Request $request)
    {
        $filter = $request->input('filter');

        $from_date =  $request['route_array'][2];

        $to_date =  $request['route_array'][3];

        $incoming_journal =  $request['route_array'][4];

        $incoming_journal = ($incoming_journal) ? explode(",", $incoming_journal) : '';

        $dep_id = intval($request['route_array'][0]);
        // $dep_id = 181;

        $row = intval($request['route_array'][1]);
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');

        return $doc = $this->templateFull($row, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $parent, $template_id, $incoming_journal);
    }

    public function templateFull($request, $dep_id, $lang, $from_date, $to_date, $filter,  $locale, $parent, $template_id, $incoming_journal)
    {
        $one_date = date('Y-m-d', strtotime('+1 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));

        // $mainDep = Department::select('id')->where('parent_id', 1)->get()->pluck('id');
        $mainDep = Department::select('id')->where('parent_id', 1)->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')->whereIn('parent_id', $mainDep)->get()->pluck('id');

        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->whereIn('department_id', [$value])->get()->pluck('id')->toArray();
        }

        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }
        $template[] = Department::select('id', 'name_uz_cyril', 'name_uz_latin', 'name_ru')->where('id', $dep_id)->get();
        $dep_id = $state_ids[$dep_id];
        // $dep_id = $state_ids[3];

        $documents = DocumentSigner::whereIn('staff_id', $dep_id)
            ->whereIn('action_type_id', [4])
            ->whereIn('parent_employee_id', $parent)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereHas('Documents', function ($q) use ($template_id) {
                $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                    ->whereHas('DocumentSigners', function ($query) {
                        $query->where('staff_id', 3360)->where('action_type_id', 11);
                    });
            })
            ->whereHas('documents', function ($querys) use ($incoming_journal) {
                $querys->whereHas('documentDetails', function ($query) use ($incoming_journal) {
                    if ($incoming_journal)
                        $query->whereHas('documentDetailAttributeValues', function ($quer) use ($incoming_journal) {
                            $quer->whereHas('documentDetailAttributes', function ($q) use ($incoming_journal) {
                                $q->whereIn('attribute_value',  $incoming_journal)
                                    ->where('table_list_id', 19)
                                    ->where('data_type_id', 6);
                            });
                        });
                });
            })
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->with(['signerEmployee' => function ($q2) use ($lang) {
                $q2->select(
                    'id',
                    'lastname_' . $lang,
                    'middlename_' . $lang,
                    'firstname_' . $lang,
                    'tabel'
                );
            }])
            ->with(['Documents' => function ($q) use ($locale, $lang) {
                $q->with(['documentType' => function ($q) use ($locale) {
                    $q->select(
                        'id',
                        'name_' . $locale
                    );
                }])
                    ->with(['documentDetails' => function ($query) {
                        $query->select(
                            'id',
                            'document_id',
                            'content'
                        );
                    }])
                    ->with(['employee' => function ($query) use ($lang) {
                        $query->select(
                            'id',
                            'lastname_' . $lang,
                            'middlename_' . $lang,
                            'firstname_' . $lang,
                            'tabel'
                        )
                            ->with(['employeeStaff' => function ($query) use ($lang) {
                                $query->select(
                                    'employee_id',
                                    'staff_id'
                                )
                                    ->with(['staff' => function ($query) use ($lang) {
                                        $query->select(
                                            'id',
                                            'position_id',
                                            'department_id'
                                        );
                                    }])
                                    ->where('is_active', 1);
                            }]);
                    }]);
            }]);



        switch ($request) {
            case 0:
                $documents->where('status', '!=', 2);

                break;
            case 1:
                $documents->where('status', 1);
                break;
            case 2:
                $documents->where('status', 1)->whereColumn('due_date', '>=', 'sign_at');
                break;
            case 3:
                $documents->where('status', 1)->whereColumn('due_date', '<', 'sign_at');
                break;
            case 4:
                $documents->whereIn('status', [0, 4, 3]);
                break;
            case 5:
                $documents->whereIn('status', [0, 4, 3])->whereBetween('due_date', [date('Y-m-d'), $one_date]);
                break;
            case 6:
                $documents->whereIn('status', [0, 4, 3])->whereBetween('due_date', [$one_date, $three_date]);
                break;
            case 7:
                $documents->whereIn('status', [0, 4, 3])->where('due_date', '>=', $three_date);
                break;
            case 8:
                $documents->whereIn('status', [0, 4, 3])->where('due_date', '<', date('Y-m-d'));
                break;
        }

        if (isset($filter['document_type_id']) && ($filter['document_type_id'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('document_type_id', $filter['document_type_id']);
            });
        }

        if (isset($filter['document_number']) && ($filter['document_number'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('document_number', 'like', '%' . $filter['document_number'] . '%');
            });
        }
        if (isset($filter['status_signer']) && ($filter['status_signer'])) {
            $documents->where('status', 'like', '%' . $filter['status_signer'] . '%');
        }

        if (isset($filter['id']) && ($filter['id'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('id', $filter['id']);
            });
        }

        $template[] = $documents->get();
        $template[] = $request;
        return $template;
    }


    public function getStaffIds($deps)
    {
        return Staff::select('id')->whereIn('department_id', $deps)->get()->pluck('id')->toArray();
    }

    public function getChilds($ids)
    {
        $deps = Department::select('id')->whereIn('parent_id', $ids)->get()->pluck('id')->toArray();

        if (count($deps) > 0) {
            $childs = $this->getChilds($deps);
            $deps = array_merge($deps, $childs);
        }

        return $deps;
    }



    // OKD detalizatsiya

    public function okdReportTabFull(Request $request)
    {
        $from_date =  $request['route_array'][1];

        $to_date =  $request['route_array'][2];

        // $mainDep = Department::select('id')->where('parent_id', 1)->get()->pluck('id');
        $mainDep = Department::select('id')->where('parent_id', 1)->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')->whereIn('parent_id', $mainDep)->get()->pluck('id');

        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->whereIn('department_id', [$value])->get()->pluck('id')->toArray();
        }

        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }

        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');

        $row = $request['route_array'][0];
        $model = [];


        foreach ($state_ids as $key => $item) :
            $it = $state_ids[$key];

            $dep = Department::where('id', $key)->get();

            switch ($row) {
                case 2:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->whereIn('parent_employee_id', $parent)
                        ->where('status', 1)
                        ->whereColumn('due_date', '>=', 'sign_at')
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
                                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                                            $q2->select(
                                                'id',
                                                'lastname_' . $lang,
                                                'middlename_' . $lang,
                                                'firstname_' . $lang,
                                                'tabel'
                                            );
                                        }])
                                        ->with(['staff' => function ($staff)  use ($locale) {
                                            $staff
                                                ->select('id', 'department_id')
                                                ->with(['department' => function ($dep) use ($locale) {
                                                    $dep->select('name_' . $locale, 'id');
                                                }]);
                                        }])
                                        ->with(['employeeStaffs' => function ($empStaffquery) use ($lang) {
                                            $empStaffquery->with(['employee' => function ($q2)  use ($lang) {
                                                $q2->select(
                                                    'id',
                                                    'lastname_ru',
                                                    'middlename_ru',
                                                    'firstname_ru',
                                                    'lastname_uz_cyril',
                                                    'middlename_uz_cyril',
                                                    'firstname_uz_cyril',
                                                    'lastname_uz_latin',
                                                    'middlename_uz_latin',
                                                    'firstname_uz_latin',
                                                    'tabel'
                                                );
                                            }])
                                                ->select(
                                                    'employee_id',
                                                    'staff_id'
                                                )
                                                ->where('is_active', '=', 1);
                                        }]);
                                }]);
                        }]);

                    $count = $document->get();
                    break;
                case 3:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->where('status', 1)
                        ->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '<', 'sign_at')
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
                                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                                            $q2->select(
                                                'id',
                                                'lastname_' . $lang,
                                                'middlename_' . $lang,
                                                'firstname_' . $lang,
                                                'tabel'
                                            );
                                        }])
                                        ->with(['staff' => function ($staff)  use ($locale) {
                                            $staff
                                                ->select('id', 'department_id')
                                                ->with(['department' => function ($dep) use ($locale) {
                                                    $dep->select('name_' . $locale, 'id');
                                                }]);
                                        }])
                                        ->with(['employeeStaffs' => function ($empStaffquery) use ($lang) {
                                            $empStaffquery->with(['employee' => function ($q2)  use ($lang) {
                                                $q2->select(
                                                    'id',
                                                    'lastname_ru',
                                                    'middlename_ru',
                                                    'firstname_ru',
                                                    'lastname_uz_cyril',
                                                    'middlename_uz_cyril',
                                                    'firstname_uz_cyril',
                                                    'lastname_uz_latin',
                                                    'middlename_uz_latin',
                                                    'firstname_uz_latin',
                                                    'tabel'
                                                );
                                            }])
                                                ->select(
                                                    'employee_id',
                                                    'staff_id'
                                                )
                                                ->where('is_active', '=', 1);
                                        }]);
                                }]);
                        }]);

                    // $count = (!empty($document_without_due_date))? $document_without_due_date->get(): null;
                    $count =  $document->get();
                    break;
                case 8:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->whereIn('status', [0, 4, 3])
                        ->whereIn('parent_employee_id', $parent)
                        ->where('due_date', '<', date('Y-m-d'))
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
                                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                                            $q2->select(
                                                'id',
                                                'lastname_' . $lang,
                                                'middlename_' . $lang,
                                                'firstname_' . $lang,
                                                'tabel'
                                            );
                                        }])
                                        ->with(['staff' => function ($staff)  use ($locale) {
                                            $staff
                                                ->select('id', 'department_id')
                                                ->with(['department' => function ($dep) use ($locale) {
                                                    $dep->select('name_' . $locale, 'id');
                                                }]);
                                        }])
                                        ->with(['employeeStaffs' => function ($empStaffquery) use ($lang) {
                                            $empStaffquery->with(['employee' => function ($q2)  use ($lang) {
                                                $q2->select(
                                                    'id',
                                                    'lastname_ru',
                                                    'middlename_ru',
                                                    'firstname_ru',
                                                    'lastname_uz_cyril',
                                                    'middlename_uz_cyril',
                                                    'firstname_uz_cyril',
                                                    'lastname_uz_latin',
                                                    'middlename_uz_latin',
                                                    'firstname_uz_latin',
                                                    'tabel'
                                                );
                                            }])
                                                ->select(
                                                    'employee_id',
                                                    'staff_id'
                                                )
                                                ->where('is_active', '=', 1);
                                        }]);
                                }]);
                        }]);

                    $count =  $document->get();
                    break;
            }

            // return (!empty($count))?1:0;
            // return count($count);

            // $model[] = [$dep, $count];

            if (count($count) != 0) :
                $model[] = [$dep, $count];
            endif;

        endforeach;

        return ($model);
        // return ('sasas');
    }

    // Boshqalar uchun xisobot


    public function documentReportEmployee(Request $request)
    {
        if(Auth::id() == 33){
            return 'OKD report vaqtinchalik to\'htatildi.';
        }
        $from_date = !empty($request['search']['from_date']) ?
            $request['search']['from_date'] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['search']['to_date']) ?
            $request['search']['to_date'] :
            date('Y-m-d');


        $user_employee_id = ($request['status_report'] == 0) ?
            Auth::user()->employee_id :  Auth::user()->employee->dr_employee_id;
        // $user_employee_id = 916;
        // return $user_employee_id;
        $model = [];

        $r_employe = DocumentSigner::select('signer_employee_id')
            ->where('parent_employee_id', $user_employee_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->groupBy('signer_employee_id')
            ->get()->pluck('signer_employee_id');
        foreach ($r_employe as $key => $it) {

            $document_all = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', '!=', 2)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })
                ->pluck('id')->toarray();


            $document_comp = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_comp_in_due_date = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereColumn('due_date', '>=', 'sign_at')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();



            $document_without_due_date = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereColumn('due_date', '<', 'sign_at')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();


            $document_signer = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();


            $one_date = date('Y-m-d', strtotime('+1 days'));
            $two_date = date('Y-m-d', strtotime('+2 days'));
            $three_date = date('Y-m-d', strtotime('+3 days'));

            $document_do_one = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_do_three = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereBetween('due_date', [$one_date, $three_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out_three = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->where('due_date', '>=', $three_date)
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->where('due_date', '<', date('Y-m-d'))
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $eployee = Employee::where('id', $it)->get();


            $count = [
                count($document_all),
                count($document_comp),
                count($document_comp_in_due_date),
                count($document_without_due_date),
                count($document_signer),
                count($document_do_one),
                count($document_do_three),
                count($document_out_three),
                count($document_out)
            ];


            if (!empty($document_all)) :
                $model[] = [$eployee, $count];
            endif;
        }
        return $model;
    }

    public function documentReportEmployeeItem(Request $request)
    {

        $filter = $request->input('filter');
        $from_date = !empty($request['route_array'][2]) ?
            $request['route_array'][2] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['route_array'][3]) ?
            $request['route_array'][3] :
            date('Y-m-d');

        $dep_id = intval($request['route_array'][0]);

        $row = intval($request['route_array'][1]);
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');

        $dr_employee_id = $request['route_array'][4];

        return $doc = $this->Employeetemplate($row, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $dr_employee_id);
    }


    public function Employeetemplate($request, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $dr_employee_id)
    {

        $user_employee_id = ($dr_employee_id == 1) ?
            Auth::user()->employee->dr_employee_id : Auth::user()->employee_id;

        // $user_employee_id = 916;
        // return $user_employee_id;

        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));

        $template[] = Employee::select('id', 'firstname_' . $lang, 'lastname_' . $lang, 'middlename_' . $lang)
            ->where('id', $dep_id)->get();

        switch ($request) {
            case 0:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', '!=', 2)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 1:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 2:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '>=', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 3:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '<', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 4:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 5:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 6:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [$one_date, $three_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 7:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->where('due_date', '>=', $three_date)
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 8:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->where('due_date', '<', date('Y-m-d'))
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
        }
        if (isset($filter['document_type_id']) && $filter['document_type_id']) {
            $documents->where('document_type_id', $filter['document_type_id']);
        }

        if (isset($filter['document_number']) && $filter['document_number']) {
            $documents->where('document_number', 'like', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['id'])) :
            $documents->where('id', $filter['id']);
        endif;
        $template[] = $documents->get();
        $template[] = $request;
        return $template;
    }
    public function documentReportMy(Request $request)
    {
        $from_date = !empty($request['search']['from_date']) ?
            $request['search']['from_date'] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['search']['to_date']) ?
            $request['search']['to_date'] :
            date('Y-m-d');
        // $myDoc = [0 => 'Chiquvchi barcha xujjatlar', 1 => 'Kiruvchi barcha xujjatlar'];
        $myDoc = [0];
        $user_employee_id = ($request['status_report'] == false) ?
            Auth::user()->employee_id :  Auth::user()->employee->dr_employee_id;

        $model = [];
        // foreach ($myDoc as $key => $t) {

        $document_all =  DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', '!=', 2)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })
            ->pluck('id')->toarray();

        $document_comp = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_comp_in_due_date = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereColumn('due_date', '>=', 'sign_at')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();



        $document_without_due_date = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereColumn('due_date', '<', 'sign_at')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();


        $document_signer = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();


        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));

        $document_do_one = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereBetween('due_date', [date('Y-m-d'), $one_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_do_three = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereBetween('due_date', [$one_date, $three_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_out_three = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->where('due_date', '>=', $three_date)
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_out = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->where('due_date', '<', date('Y-m-d'))
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $count = [
            count($document_all),
            count($document_comp),
            count($document_comp_in_due_date),
            count($document_without_due_date),
            count($document_signer),
            count($document_do_one),
            count($document_do_three),
            count($document_out_three),
            count($document_out)
        ];
        $model[] = [$myDoc[0], $count];
        // $model = [$myDoc, [0, 1, 2, 3, 4, 5, 6, 7, 8]];
        // $moyDoc = 
        // }
        return $model;
    }

    public function documentReportMyItem(Request $request)
    {
        $filter = $request->input('filter');
        $from_date = !empty($request['route_array'][2]) ?
            $request['route_array'][2] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['route_array'][3]) ?
            $request['route_array'][3] :
            date('Y-m-d');
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');

        $row = intval($request['route_array'][1]);

        $dr_employee_id = $request['route_array'][4];
        $user_employee_id = ($dr_employee_id == 1) ? Auth::user()->employee->dr_employee_id : Auth::user()->employee_id;
        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));
        switch ($row) {
            case 0:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', '!=', 2)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 1:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 2:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '>=', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 3:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '<', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 4:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 5:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 6:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [$one_date, $three_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 7:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->where('due_date', '>=', $three_date)
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 8:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->where('due_date', '<', date('Y-m-d'))
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
        }

        $template[] = $documents->get();
        $template[] = $row;
        return $template;
    }
    public function executorReport(Request $request){
        // return 'salom';
        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');

        $result = DB::select("SELECT e.tabel,ds.fio,SUBSTRING(ds.sign_at,1,7) month,dt.id,dt.name_uz_latin,count(ds.id) cnt,
        COUNT(CASE WHEN ds.action_type_id = 1 THEN 1 END) as solg,
        COUNT(CASE WHEN ds.action_type_id = 2 THEN 1 END) as utv,
        COUNT(CASE WHEN ds.action_type_id = 4 THEN 1  END) as isp,
        COUNT(CASE WHEN ds.action_type_id = 6 THEN 1  END) as soz,
        COUNT(CASE WHEN ds.action_type_id = 11 THEN 1  END) as kon,
        COUNT(CASE WHEN ds.action_type_id = 13 THEN 1  END) as red,
        COUNT(CASE WHEN ds.action_type_id = 14 THEN 1  END) as ras,
        COUNT(CASE WHEN ds.action_type_id = 16 THEN 1  END) as isp2
        FROM document_signers ds
        INNER join documents d on d.id = ds.document_id
        INNER join document_templates dt on dt.id=d.document_template_id
        INNER join employees e on e.id=ds.signer_employee_id
        where dt.id in (55,117,118,173,209)
        and e.tabel in ('9112','K638','9790','M336','B795','D884','6447','9331','4991')
        and ds.status=1
        and ds.sign_at BETWEEN '$startDate' and '$endDate'
        group by e.tabel, month
            ");
            return $result;
    }
    public function controlPunktReport(Request $request){
        // return 'salom';
        // $startDate = $request->input('startdate');
        // $endDate = $request->input('enddate');

        $result = DB::select("SELECT d.id, d.document_number, d.pdf_file_name,
        CONCAT(SUBSTRING(e.firstname_uz_latin,1,1),'. ',SUBSTRING(e.middlename_uz_latin,1,1),'. ',e.lastname_uz_latin) as fio, 
        dp.name_uz_latin Bulim, p.name_uz_latin Lavozim, 
        ds.due_date, datediff('2023-04-25 12:00', ds.due_date) kun, ds.status FROM documents d
        inner join document_signers ds on ds.document_id=d.id
        inner join staff s on s.id = ds.staff_id
        inner join employee_staff es on s.id = es.staff_id  and es.is_active=1
        inner join employees e on e.id = es.employee_id
        INNER join departments dp on dp.id = s.department_id
        INNER join positions p on p.id = s.position_id
        where d.created_at > '2023-01-01 00:00' and d.status in (1,2)
        and ds.action_type_id=1 and ds.status in (0,3) and ds.taken_datetime is NOT null
        and datediff('2023-04-25 12:00', ds.due_date) > 3  
        ORDER BY kun  ASC
            ");
        
        return $result;
    }
    public function signerReport(Request $request){
        // return 'salom';
        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');

        $result = DB::select("SELECT 
            d.id ID1,
            dtype.name_uz_latin 'document_type', 
            dt.name_uz_latin 'document_template', 
            d.created_at 'created_at', 
            FROM_UNIXTIME(signer.signed_date) 'signed_date', 
            signer.status 'signer_status', signer.status 'document_status', 
            concat(e2.firstname_uz_latin,' ',e2.lastname_uz_latin,' ',e2.middlename_uz_latin) 'creator_emp',
            d.from_department 'creator_dep', 
            concat(e1.firstname_uz_latin,' ',e1.lastname_uz_latin,' ',e1.middlename_uz_latin) 'signer_emp',
            d.to_department 'signer_dep'
            FROM documents d 
            inner join document_signers signer on signer.document_id=d.id and signer.action_type_id in (1,2)
            inner join employees e1 on e1.id=signer.signer_employee_id
            inner join document_signers creator on creator.document_id=d.id and creator.action_type_id=6
            inner join employees e2 on e2.id=creator.signer_employee_id
            inner join document_types dtype on dtype.id=d.document_type_id 
            inner join document_templates dt on dt.id=d.document_template_id 
            where d.status not in (0,6) and d.deleted_at is null and d.document_template_id=225 and d.document_date BETWEEN '$startDate' and '$endDate'
            group by d.id
            ");
            return $result;
    }
    
    public function reportMyDocs(Request $request)
    {
        $from_date = ($request['search']['from_date']) ? $request['search']['from_date'] : '';
        $to_date = ($request['search']['to_date']) ? $request['search']['to_date'] : '';
        $data_range = (($from_date) && ($to_date)) ?  "and d.document_date BETWEEN  '" . $from_date . "'  and  '" . $to_date . "' " : "";
        $employee_id = Auth::user()->employee->id;


        $documentInboxQuery = "select min(d.id) docId, min(d.document_number), dt.id as type, min(dt.name_uz_latin) name_uz_latin, count(d.id) as cnt from documents d 
        inner join document_signers ds on ds.document_id = d.id
        inner join document_templates dt on dt.id = d.document_template_id
        where ds.signer_employee_id = " . $employee_id . " and ds.sequence != 100 " . $data_range . " and d.status not in (0,6) ";

        $allInboxCount = DB::select(
            $documentInboxQuery . "group by type"
        );
        $allInboxDone = DB::select(
            $documentInboxQuery . "and ds.status = 1 group by type"
        );
        
        $allInboxPending = DB::select(
            $documentInboxQuery . "and ds.status in (0,3) and ds.sign_at is null group by type"
        );
        
        $allInboxPendingOutTime = DB::select(
            $documentInboxQuery . "and ds.status in (0,3)
            and ds.due_date <= NOW() group by type"
        );
        $allInboxDoc = [
            'id' => 1,
            'name' => "Kiruvchi",
            'all' => $allInboxCount ? $allInboxCount : 0,
            'done' => $allInboxDone ? $allInboxDone : 0,
            'pending' => $allInboxPending ? $allInboxPending : 0,
            'pending_out_time' => $allInboxPendingOutTime ? $allInboxPendingOutTime : 0,
        ];


        $documentOutboxQuery = "select min(d.id) docId, min(d.document_number), dt.id as type, min(dt.name_uz_latin) name_uz_latin, count(d.id) as cnt from documents d 
        inner join document_signers ds on ds.document_id = d.id
        inner join document_templates dt on dt.id = d.document_template_id
        where ds.signer_employee_id = " . $employee_id . " and ds.sequence = 100 " . $data_range . " and d.status not in (0,6) ";

        $allOutboxCount = DB::select(
            $documentOutboxQuery . "group by type"
        );
        $allOutboxDone = DB::select(
            $documentOutboxQuery . "and ds.status = 1 group by type"
        );
        
        $allOutboxPending = DB::select(
            $documentOutboxQuery . "and ds.status in (0,3) and ds.sign_at is null group by type"
        );
        
        $allOutPendingOutTime = DB::select(
            $documentOutboxQuery . "and ds.status in (0,3)
            and ds.due_date <= NOW() group by type"
        );
        $allOutboxDoc = [
            'id' => 2,
            'name' => "Chiquvchi",
            'all' => $allOutboxCount ? $allOutboxCount : 0,
            'done' => $allOutboxDone ? $allOutboxDone : 0,
            'pending' => $allOutboxPending ? $allOutboxPending : 0,
            'pending_out_time' => $allOutPendingOutTime ? $allOutPendingOutTime : 0,
        ];
        // return $allCount;
        $model = [];
        array_push($model, $allInboxDoc, $allOutboxDoc);
        return $model;
    }
    
    public function reportMyDocsItem(Request $request)
    {
        // return $request;
        $from_date = ($request['route_array'][3]) ? $request['route_array'][3] : '';
        $to_date = ($request['route_array'][4]) ? $request['route_array'][4] : '';
        $data_range = (($from_date) && ($to_date)) ?  "and d.document_date BETWEEN  '" . $from_date . "'  and  '" . $to_date . "' " : "";
        $employee_id = Auth::user()->employee->id;
        $document_type = $request['route_array'][0];
        $document_template = $request['route_array'][1];
        $report_type = $request['route_array'][2];

        if($document_type==1){

            $documentInboxQuery = "select d.id, d.document_number, d.document_date, d.pdf_file_name, d.title, d.status, dt.id as type, dt.name_uz_latin from documents d 
            inner join document_signers ds on ds.document_id = d.id
            inner join document_templates dt on dt.id = d.document_template_id
            where ds.signer_employee_id = " . $employee_id . " and ds.sequence != 100 " . $data_range . " and d.document_template_id = " . $document_template . " and d.status not in (0,6) ";
            if ($report_type==1){
                $documents = DB::select($documentInboxQuery ."order by d.id desc");
            }
            if ($report_type==2){
                $documents = DB::select($documentInboxQuery . "and ds.status = 1 order by d.id desc");
            }
            if ($report_type==3){
                $documents = DB::select($documentInboxQuery . "and ds.status in (0,3) and ds.sign_at is null order by d.id desc");
            }
            if ($report_type==4){
                $documents = DB::select($documentInboxQuery . "and ds.status in (0,3)
                and ds.due_date <= DATE_ADD(CURDATE(), INTERVAL 0 DAY) order by d.id desc");
            }
            return $documents;
        }
        if($document_type==2){

            $documentInboxQuery = "select d.id, d.document_number, d.document_date, d.pdf_file_name, d.title, d.status, dt.id as type, dt.name_uz_latin from documents d 
            inner join document_signers ds on ds.document_id = d.id
            inner join document_templates dt on dt.id = d.document_template_id
            where ds.signer_employee_id = " . $employee_id . " and ds.sequence = 100 " . $data_range . " and d.document_template_id = " . $document_template . " and d.status not in (0,6) ";
            if ($report_type==1){
                $documents = DB::select($documentInboxQuery ."order by d.id desc");
            }
            if ($report_type==2){
                $documents = DB::select($documentInboxQuery . "and ds.status = 1 order by d.id desc");
            }
            if ($report_type==3){
                $documents = DB::select($documentInboxQuery . "and ds.status in (0,3) and ds.sign_at is null order by d.id desc");
            }
            if ($report_type==4){
                $documents = DB::select($documentInboxQuery . "and ds.status in (0,3)
                and ds.due_date <= DATE_ADD(CURDATE(), INTERVAL 0 DAY) order by d.id desc");
            }
            return $documents;
        }
        else{
            return '';
        }
    }

    public function documentSheetsReport(Request $request)
    {
        $result = DocumentTemplate::with('documentType')
            ->with('department')
            ->get();
        
        $modifiedData = [];
    
        foreach ($result as $item) {
            $modifiedItem = [
                'id' => $item->id,
                'nomi' => $item->name_uz_latin,
                'document_turi' => $item->documentType ? $item->documentType->name_uz_latin : null,
                'department_nomi' => $item->department ? $item->department->name_uz_latin : null,
            ];
    
            $modifiedData[] = $modifiedItem;
        }
    
        return response()->json($modifiedData);
    }
    public function RequisitesReport(Request $request){
        $result = CompanyRequisite::get();
        
        $modifiedData = [];

        foreach ($result as $item) {
            $modifiedItem = [
                'id' => $item->id,
                'nomi' => $item->name_uz_latin,
                'adress' => $item->address_uz_latin,
                'inn' => $item->inn,
                'akkount' => $item->account,
                'SWIFT code' => $item->swift,
                'OKHX code' => $item->oknh,
                'MFO' => $item->mfo,
            ];

            $modifiedData[] = $modifiedItem;
        }

        return response()->json($modifiedData);
        }
}
