<?php

namespace App\Http\Controllers;

use Adldap\Query\Builder;
use Illuminate\Http\Request;
use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Staff;
use App\Http\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\User;
use DocumentTypes;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments = Department::with('managerStaff')
            ->with('staff')
            ->with('staff.employees')
            ->with('staff.position');

        return $departments->get();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

            $departments[$key]->inbox = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->whereIn('action_type_id', [3, 6]);
            })->where('status', '>', 0)->count();

            $departments[$key]->completed_on_time = 0;
            $departments[$key]->failed_in_time = 0;
            $departments[$key]->done = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('status', 1)
                    ->where('action_type_id', "!=", 6);
            })->count();

            $departments[$key]->on_time = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                    ->where('status', 1)
                    ->where('document_signers.due_date', '>=', 'document_signers.updated_at')
                    ->where('action_type_id', "!=", 6);
            })->count();

            $departments[$key]->out_of_date = Document::whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->whereIn('staff_id', $staff_ids)
                ->where('status', 1)
                ->where('document_signers.due_date', '<', '`document_signers`.`updated_at`')
                ->where('action_type_id', "!=", 6);
            })->count();

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

    public function getDepIds($dep_id)
    {
        $ids = [$dep_id];
        $deps = Department::select('id')->where('parent_id', $dep_id)->get();
        foreach ($deps as $key => $value) {
            $ids = array_merge($ids, $this->getDepIds($value->id));
        }
        return $ids;
    }

    public function getUserDocument()
    {   $user = Auth::user()->employee_id;
        $documents = Document::with('DocumentType')->where('created_employee_id', $user)->get();
        return $documents;
    }
}
