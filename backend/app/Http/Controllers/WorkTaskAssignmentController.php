<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\WorkTask;
use App\Http\Models\WorkTaskcomment;
use App\Http\Models\Employee;
use App\Http\Models\WorkTaskAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkTaskAssignmentController extends Controller
{
    public function getTaskInfo($id)
    {
        $task = WorkTaskAssignment::with('getTask')
            ->with('doer')
            ->where('task_id', '=', $id)
            ->whereNotNull('description')
            // ->where('employee_id', Auth::user()->employee_id)
            ->get();
        // dd($task);
        return $task;
    }
    public function getChildTaskInfo($id)
    {
        $task = WorkTaskAssignment::with('doer')
            ->with('comments')
            ->with('comments.commentedBy')
            ->where('id', '=', $id)
            ->get();
        // dd($task);
        return $task;
    }
    public function getExpired()
    {
        $task = WorkTaskAssignment::whereNull('deleted_at')
            ->whereNotNull('due_datetime')
            // ->where('due_datetime', '<', date('Y-m-d H:i:s', time()))
            ->where('assignment_type', '3')
            ->with('doer');
        return $task->get();
    }
    public function addComment(Request $request)
    {
        $comment = new WorkTaskComment();
        $comment->task_assignment_id = $request->input('id');
        $comment->comment = $request->input('comment');
        $comment->created_by = Auth::user()->employee_id;
        $comment->save();
    }
    public function empinfo()
    {
        // $task = WorkTask::whereHas('user', function ($query) {
        //     $query->whereNull('deleted_at')->where('employee_id', '=', Auth::user()->employee_id);
        // })
        //     ->with('user.doer')
        //     // ->with('user.taskFiles')
        //     ->get();
        // return Auth::user()->employee_id;
        $emp = Employee::whereHas('worktasks', function ($query) {
            $query->where('assignment_type', '!=', '1');
        })
            ->with('worktasks')
            ->get();
        return $emp;
    }
    public function childTaskCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $task = new WorkTaskAssignment();

            $task->task_id = $request->input('task_id');
            $task->employee_id = Auth::user()->employee_id;
            $task->description = $request->input('content');
            $task->priority = $request->input('priority');
            $task->due_datetime = $request->input('due_datetime');
            $task->assignment_type = 3;
            $task->status = 2;
            $task->save();

            DB::commit();
            return $task->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function childTaskDone($id)
    {
        DB::beginTransaction();
        try {
            $task = WorkTaskAssignment::find($id);
            $task->status = 7;
            $task->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function destroy($id)
    {
        WorkTaskAssignment::find($id)->delete();
        // dd($user);
        // $user->delete();
    }
    public function employeeTasks()
    {
        // $task = Employee::whereNull('deleted_at')->with('worktasks')->get();

        $task = WorkTaskAssignment::whereNull('deleted_at')
            ->select(DB::raw('count(DISTINCT(task_id)) as task_count, employee_id'))
            ->groupBy('employee_id')

            ->with('doer')
            ->get();
        // $filtered = $task->filter(function ($value) {
        //     return array_count_values($value['employee_id']);
        // });
        // dd($task);
        return $task;
    }
}
