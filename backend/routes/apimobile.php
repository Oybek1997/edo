<?php

use Illuminate\Support\Facades\Route;


use App\Http\Models\Document;
use App\Http\Models\Department;
use App\Http\Models\FunctionalDepartment;
use App\Http\Models\KpiObject;
use App\Http\Models\DocumentStaff;
use App\Http\Controllers\DocumentSigner;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/errorstaff', function (Request $request) {
    return
    $document = Document::whereIn('status', [1,2])
    ->whereHas('documentSigners', function($q){
        $q->whereHas('staffs', function($q){
            // $q->
        });
        // $q->whereHas('employeeStaffs');
    })
    ->pluck('id')->toArray();
    // ->get();
});

Route::get('/array-functional-department', function (Request $request) {

    $array = [];

    try {
        // $array = 5 ;
        foreach ($array as $key => $ar) {
            $dep = Department::where('department_code', $ar[0])->first();
            $fdep = FunctionalDepartment::where('functional_department_code', $ar[1])->first();
            if ($dep) {
                $dep->functional_department_id  = $fdep ? $fdep->id : null;
                $dep->save();
            }
        }
        return 'ok';
        //code...
    } catch (\Throwable $th) {
        //throw $th;
        return $th;
    }
});

Route::get('/departmentS', function () {
    return
        $dep = Department::whereHas('departmentType', function ($q) {
            $q->where('sequence', '<', 6);
        })->pluck('manager_staff_id')->reject(function ($value) {
            return is_null($value);
        })->toArray();
    $dep = Department::whereHas('departmentType', function ($q) {
        $q->where('sequence', '<', 6);
    })->pluck('manager_staff_id')->toArray();
    // $dep = Department::where('parent_id', 1421)->pluck('manager_staff_id')->toArray();
    // return gettype($dep);
});
Route::get('/krill', 'KpiController@krill');
Route::get('/kpisardor', 'KpiController@testKpiSardor');
// Route::get('/kpiobjektdocid', 'KpiController@kpiDocumetnId');

Route::get('/getDepID/{dep_id}/{y}/{q}', 'KpiController@getDepID');
Route::get('/files_sardor', 'FileController@sardor');
Route::get('/stampuploadfile/{id}', 'FileController@stampuploadfile');

Route::get('/signernull', function () {
    $signers = DocumentSigner::where('action_type_id', 6)->whereNull('signer_employee_id')->get();
    foreach ($signers  as $key => $value) {
        $document = Document::find($value->document_id);
        if ($document) {
            $value->signer_employee_id = $document->created_employee_id;
            $value->save();
        }
    }
});

Route::get('/documentstaff', function () {
    // return 'sasa';
    $document = Document::select('id', 'document_number')->where('document_type_id', 57)
        ->whereIn('status', [3, 4, 5])
        // ->whereNotNull('staff_id')
        ->whereNull('staff_id')
        ->whereDoesntHave('documentStaff')
        ->whereHas('documentTemplate', function ($q) {
            $q->where('select_staff', 1);
        })
        // ->pluck('id')
        ->get();
    $x = 0;
    // foreach($document as $key=>$value){

    //     $DocumentStaff = DocumentStaff::where('document_id', $value->id)->where('staff_id',$value->staff_id)->first();
    //     if(!$DocumentStaff){
    //         $DocumentStaff = new DocumentStaff();
    //         $DocumentStaff->document_id = $value->id;
    //         $DocumentStaff->staff_id = $value->staff_id;
    //         $DocumentStaff->save();
    //         $x= $x+1;
    //     }

    // }
    // echo '<pre>';
    // print_r($document);
    // echo '<pre>';
    return
        // $x; 
        ($document);
    // count($document);
});


Route::get('/test-kpi', function () {
    // return 0;
    $kpi_objects = KpiObject::where('years', 2023)->where('quarter', 3)->get();
    $arr = [];
    foreach ($kpi_objects as $key => $value) {
        $dep_id = $value->department_id;
        $doc = Document::where('document_template_id', 431)
            ->whereHas('documentSigners', function ($q) use ($dep_id) {
                $q->whereHas('staff', function ($q1) use ($dep_id) {
                    $q1->where('department_id', $dep_id);
                })
                    ->where('action_type_id', 6);
            })
            ->whereIn('status', [3, 4, 5])
            ->whereHas('documentDetails', function ($q) {
                $q->whereHas('documentDetailContents', function ($s) {
                    $s->where('value',  2023)
                        ->where('d_d_attribute_id', 1936);
                });
            })
            ->first();
        if ($doc) {
            $arr[] = $doc->department2_id;
            $value->dep2_id = $doc->department2_id;
            $value->dep_id = $doc->department2_id;
            $value->save();
        }
    }
    return ($arr);
    return $kpi_objects;
});


// mobile uchun apilar

Route::post('login', 'MobileController@login');
Route::post('refresh-token', 'MobileController@refreshToken');

Route::post('test', 'MobileController@test');


Route::group(['middleware' => ['auth:api', 'user.log']], function () {
    Route::post('get-notifications', 'MobileController@getNotification');
    Route::post('get-documentcounts', 'MobileController@getDocumentCount');
    Route::post('get-dashboard', 'MobileController@getDashboard');
    Route::post('get-documents', 'MobileController@getDocuments');
    Route::post('get-profile', 'MobileController@profile');
    Route::post('get-templates', 'MobileController@getTemplates');
    Route::post('get-action-types', 'MobileController@actionType');
    // Route::post('resolution-employ', 'DocumentController@getResolutionEmployees');
    // Route::post('resolution-employees', 'MobileController@getResolutionEmployeesMobile');
    // ################
    // Route::post('resolution-employees', 'DocumentController@getResolutionEmployeesMobile');
    // Route::post('add-signers', 'DocumentSignerController@addDocumentSigners');
    Route::post('reaction', 'MobileController@reaction');
    Route::post('comment', 'MobileController@comment');
    Route::post('comment-files', 'MobileController@commentFile');
    // ################
    // Route::get('/profile-image/{tabel}', 'MobileController@getProfileImage');
    // Route::post('documents/set-base64', 'MobileController@setBase64');
    // Route::post('get-employee', 'MobileController@getEmployee');
    Route::post('get-employee', 'MobileController@getResolutionEmployeesMobile');
    Route::post('document-show', 'MobileController@documentShow');
    Route::post('set-base', 'MobileController@setBase64');
    Route::post('resolution', 'MobileController@resolution');
});
Route::get('storage/{tabel}', function ($tabel) {
    $path = storage_path('app/avatars/' . $tabel . '.jpg');
    $path1 = storage_path('app/avatars/' . $tabel . '.JPG');

    if (!File::exists($path) && !File::exists($path1)) {
        abort(404);
    }
    if (!File::exists($path)) {
        $path = $path1;
    }

    // return $path;
    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
