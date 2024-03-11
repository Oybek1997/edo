<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeOfficialDocument;
use App\Http\Models\Employee;
use App\Http\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeOfficialDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeOfficialDocument::with('employee.staff.department')
                                        ->with('employee.staff.position')
                                        ->with('files')
                                       ->with('officialDocumentType')->get();


        // ->leftJoin('employees', 'employees.id','=', 'employee_official_documents.employee_id')
        // ->leftJoin('official_document_types','official_document_types.id', '=', 'employee_official_documents.official_document_type_id');
    }

    public function getFile($id)
    {
        $file = File::where('id', $id)->first();
        return response()->download(storage_path('app\documents\\' . $file->physical_name), $file->file_name);
    }

    public function updateFile(Request $request, $id)
    {
        $files = $request->file('files');
        $object_type_id = 4;
        $object_id = $id;
        $description = $request->input('description');

        foreach ($files as $key => $value) {
            $filename = time() . rand();
            Storage::putFileAs(
                'documents',
                $value,
                $filename
            );
            $file = new File();
            $file->object_type_id = $object_type_id;
            $file->file_name = $value->getClientOriginalName();
            $file->physical_name = $filename;
            $file->object_id = $object_id;
            $file->description = $description;
            $file->created_by = Auth::id();
            $file->save();
        }
        return EmployeeOfficialDocument::with('files')->where('id', $id)->first();
    }

    public function deleteFile($id)
    {
        $file = File::find($id);
        Storage::delete('documents/' . $file->physical_name);
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
     * @param  \App\Http\Models\EmployeeOfficialDocument  $employeeOfficialDocument
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeOfficialDocument $employeeOfficialDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeOfficialDocument  $employeeOfficialDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeOfficialDocument $employeeOfficialDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeOfficialDocument  $employeeOfficialDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeOfficialDocument $employeeOfficialDocument)
    {
        $model = EmployeeOfficialDocument::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeOfficialDocument();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->official_document_type_id = $request['official_document_type_id'];
        $model->title = $request['title'];
        $model->series = $request['series'];
        $model->number = $request['number'];
        $model->given_organization = $request['given_organization'];
        $model->given_date = $request['given_date'];
        $model->due_date = $request['due_date'];
        $model->is_active = $request['is_active'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeOfficialDocument  $employeeOfficialDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeOfficialDocument $employeeOfficialDocument, $id)
    {
        //
        $model = EmployeeOfficialDocument::find($id);
        $model->delete();
        return 'Successfully deleted!';
    }

    public function updateAvatar(Request $request, $tabel)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // return $file->getClientOriginalName();
            $object_type_id = 8;
            $object_id = $tabel;

            if($image->extension() != 'jpg')
                return '';
            $filename = $object_id . '.' . $image->extension();
            if (Storage::exists('avatars/' . $filename)) {
                Storage::delete('avatars/' . $filename);
            }
            Storage::putFileAs(
                'avatars',
                $image,
                $filename
            );
            $file = File::where('object_type_id', $object_type_id)->where('object_id', $object_id)->first();
            if (!$file) {
                $file = new File();
            }
            $file->object_type_id = $object_type_id;
            $file->file_name = $image->getClientOriginalName();
            $file->physical_name = $filename;
            $file->object_id = $object_id;
            $file->created_by = Auth::id();
            $file->save();
        }
        if (Storage::exists('avatars/'."$object_id.jpg")) {
            $avatar = Storage::get('avatars/'."$object_id.jpg");
            $base64 = base64_encode($avatar);
            return $base64;
        } else {
            return null;
        }
    }
}
