<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeTraningTask;
use App\Http\Models\EmployeeTraningTaskComment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeTraningTaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $taskComment = EmployeeTraningTaskComment::where('employee_traning_task_id', $id)->get();
        return $taskComment;
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
     * @param  \App\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeTraningTask $EmployeeTraningTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeTraningTask $EmployeeTraningTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = EmployeeTraningTaskComment::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeTraningTaskComment();
            $model->created_by = Auth::id();
            $model->description = $request->input('description');
            $model->employee_traning_task_id = $request->input('employee_traning_task_id');
            $model->save();
        }

        if ($request->file('file')) {
            $this->saveFile($request, $model->id);
        }

        return $model;
    }

    public function saveFile(Request $request, $obj_id)
    {
        $task_comment = EmployeeTraningTaskComment::find($obj_id);
        $model = $request->file('file');
        $object_type_id = 10;
        $object_id = $obj_id;
        if (!$task_comment->file) {
            $filename = time() . rand();
            Storage::putFileAs(
                'documents',
                $model,
                $filename
            );
            $file = new File();
            $file->object_type_id = $object_type_id;
            $file->file_name = $model->getClientOriginalName();
            $file->physical_name = $filename;
            $file->object_id = $object_id;
            $file->created_by = Auth::id();
            $file->save();
        } else {
            $filename = $task_comment->file->physical_name;

            $file = File::find($cp->file->id);
            $file->file_name = $model->getClientOriginalName();
            $file->save();

            // dd($filename);
            if (Storage::exists('documents/' . $filename)) {
                Storage::delete('documents/' . $filename);
            }
            Storage::putFileAs(
                'documents',
                $model,
                $filename
            );
            $file = File::find($task_comment->id);
            $file->object_type_id = $object_type_id;
            $file->file_name = $model->getClientOriginalName();
            $file->updated_by = Auth::id();
            $file->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = EmployeeTraningTaskComment::find($id);
        $model->delete();
    }
}
