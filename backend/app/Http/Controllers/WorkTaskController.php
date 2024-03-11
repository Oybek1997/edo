<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\WorkTask;
use App\Http\Models\WorkTaskAssignment;
use App\Http\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\WorkTaskFile;

class WorkTaskController extends Controller
{
    public function taskCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $task = WorkTask::find($id);
            if (!$task) {
                $task = new WorkTask();
            }
            if ($request->input('status') == null) {
                $task->status = 0;
            } else {
                $task->status = $request->input('status');
            }

            $task->priority = $request->input('priority');
            $task->title = $request->input('title');
            $task->content = $request->input('content');
            $task->category_id = $request->input('taskCategory');
            $admin = Auth::user()->employee_id;
            if ($task->save()) {
                $doers_ids = collect($request->input('user'))->pluck('employee_id')->toArray();
                WorkTaskAssignment::where('task_id', $task->id)->whereNotIn('employee_id', $doers_ids)->delete();
                $task_creator = WorkTaskAssignment::where('task_id', $task->id)->where('employee_id', $admin)->first();
                if (!$task_creator) {
                    $task_creator = new WorkTaskAssignment();
                    $task_creator->task_id = $task->id;
                    $task_creator->assignment_type = 1;
                    $task_creator->employee_id = Auth::user()->employee_id;
                    $task_creator->save();
                }
                foreach ($request->input('user') as $key => $value) {
                    $emp = $value['employee_id'];
                    $task_doer = WorkTaskAssignment::where('task_id', $task->id)->where('employee_id', $emp)->first();
                    if (!$task_doer) {

                        $task_doer = new WorkTaskAssignment();
                        $task_doer->task_id = $task->id;
                        $task_doer->status = 0;
                        $task_doer->employee_id = $value['employee_id'];
                        $task_doer->assignment_type = 0;
                    }
                    $task_doer->due_datetime = $value['due_datetime'];
                    $task_doer->save();
                }
            }
            DB::commit();
            // $this->updateFile($request, $task_doer->id);
            return $task_doer->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function updateTaskStatus(Request $request)
    {
        $task_id = $request->input('task_id');
        $action = $request->input('action');
        DB::beginTransaction();
        try {
            $task = WorkTask::find($task_id);
            if ($action == 1) {
                $task->status = 2;
            } else if ($action == 0) {
                $task->status = 3;
            } else if ($action == 3) {
                $task->status = 1;
            }

            $task->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function showTaskInfo($id)
    {
        $task = WorkTask::with(['user' => function ($query) {
            $query->whereNull('deleted_at');
        }])
            ->with('user.doer')
            ->with('user.taskFiles')
            ->where('id', '=', $id)

            ->get();
        // dd($task);
        return $task;
    }
    public function taskItem(Request $request)
    {
        // return $request;
        $document = WorkTask::where('id', $request['id'])
            ->with('user')
            ->with('user.doer')
            ->get();
        return $document;
    }
    public function getAll(Request $request)
    {
        $filter = $request->input('filter');
        // return $filter;
        $task = WorkTask::whereHas('user', function ($query) {
            $query->whereNull('deleted_at')->where('employee_id', '=', Auth::user()->employee_id);
        })
            ->with('user.doer')
            ->with('category');
        if (isset($filter['title'])) {
            $task->where('title', 'like', "%" . $filter['title'] . "%");
        }
        if (isset($filter['date'])) {
            $task->where('created_at', '>=', $filter['date'][0]);
            if (isset($filter['date'][1])) {
                $task->where('created_at', '<=', $filter['date'][1]);
            }
        }
        if (isset($filter['content'])) {
            $task->where('content', 'like', "%" . $filter['content'] . "%");
        }
        if (isset($filter['category'])) {
            $task->where('category_id', $filter['category']);
        }
        if (isset($filter['status'])) {
            $task->where('status', $filter['status']);
        }
        return $task->get();
    }

    public function updateFile(Request $request, $id)
    {
        DB::beginTransaction();
        $files = $request->file('files');

        if ($files) {
            try {
                // $object_type_id = 1;
                // $object_id = $id;
                // $description = $request->input('description');

                foreach ($files as $key => $value) {
                    $filename = time() . rand();
                    Storage::putFileAs(
                        'tasks',
                        $value,
                        $filename
                    );
                    $file = new WorkTaskFile();
                    $file->object_type_id = 1;
                    $file->file_name = $value->getClientOriginalName();
                    $file->physical_name = $filename;
                    $file->object_id = $id;
                    // $file->description = $description;
                    $file->created_by = Auth::id();
                    $file->save();
                }
                DB::commit();
                return ['message' => 'Successfully saved!', 'document_id' => $id];
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }
    public function destroy($id)
    {
        WorkTask::find($id)->delete();
        WorkTaskAssignment::where('task_id', $id)->delete();
        // dd($user);
        // $user->delete();
    }
    public function fileDownload($id)
    {
        $file = WorkTaskFile::where('id', $id)->first();

        return response()->download(storage_path('app/tasks//' . $file->physical_name), $file->file_name);
    }
    public function deleteFile($id)
    {
        $document = WorkTaskFile::find($id)->delete();
    }
}
