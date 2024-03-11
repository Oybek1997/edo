<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Models\Employee;
use App\Http\Models\File;
use App\Http\Models\DocumentDetail;
use App\Http\Models\KpiObject;
use App\Http\Models\KpiPlanComission;
use App\Http\Models\Document;
use App\Http\Models\KpiResolutionComission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Http\Models\Department;
use App\Http\Models\KpiobjektUser;
use App\Http\Models\kpiSettingDate;
use App\Http\Models\DocumentDetailFakt;
use App\Http\Models\KpiFactComission;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\KpiComment;

class KpiController extends Controller
{

    // DocumentDetailAttributeController dan kochirildi

    public function fileDownload($id)
    {
        $file = File::where('id', $id)->first();
        if (substr($file->file_name, -3) == 'mp4') {
            return response()->download(storage_path('app/kpi/video//' . $file->physical_name), $file->file_name);
        } else {
            return response()->download(storage_path('app/kpi//' . $file->physical_name), $file->file_name);
        }
    }

    public function kpiComments(Request $request)
    {
        // if (Auth::user()->hasPermission('kpi-owner')) {
        //     $user_dep = Auth::user()->employee->mainStaff[0]->department->id;
        // } else if (Auth::user()->hasPermission('kpi-comission')) {
        //     $user_dep = $request['filter']['department_id'];
        // }

        if ($request['comment'] == '') {
            return 0;
        }

        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $d_d_id = $request['filter']['d_d_id'];

        if (Auth::user()->hasPermission('kpi-comission')) {
            $user_dep = Department::find($request['filter']['department_id'])->department_code;
            // if (Auth::user()->username == 'qg9592') {
            //     return $user_dep;
            // }
            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];

            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->whereIn('dep2_id', $dep_ids)
                ->first();
            $comment = new KpiComment();
            $comment->kpi_object_id = $kpi_object->id;
            $comment->d_d_id = $d_d_id;
            $comment->comment = $request['comment'];
            $comment->employee_id = Auth::user()->employee->id;
            $comment->save();

            return 'succes';
        } else {
            $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;

            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

            $kpi_object = KpiObject::where('years', $years)->where('quarter', $quarter)->whereIn('department_id', $dep_ids)->first();

            $comment = new KpiComment();
            $comment->kpi_object_id = $kpi_object->id;
            $comment->d_d_id = $d_d_id;
            $comment->comment = $request['comment'];
            $comment->employee_id = Auth::user()->employee->id;
            $comment->save();
        }

        return 'succes';
    }

    public function kpiAsistentComments(Request $request)
    {
        $comment = $request['comment'];
        $res_id = $request['filter']['res_id'];
        $resolution = KpiResolutionComission::find($res_id);
        if ($resolution) {
            $resolution->comments = $comment;
            $resolution->save();
            return 'succes';
        }
    }

    public function kpiGetComments(Request $request)
    {
        if (Auth::user()->hasPermission('kpi-comission')) {
            $user_dep = Department::find($request['filter']['department_id'])->department_code;
            // if (Auth::user()->username == 'qg9592') {
            //     return $user_dep;
            // }
            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];
            $d_d_id = $request['filter']['d_d_id'];
            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->whereIn('dep2_id', $dep_ids)
                ->first();

            $comment = KpiComment::where('kpi_object_id', $kpi_object->id)
                ->where('d_d_id', $d_d_id)
                ->with(['employee' => function ($q) {
                    $q->select('id')->with(['user' => function ($qu) {
                        $qu->select('id', 'username', 'employee_id');
                    }]);
                }])->get();
            return $comment;
        } else {
            $dep = Auth::user()->employee->mainStaff[0]->department;
        }

        $user_dep = $dep->department_code;

        $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

        // return
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $d_d_id = $request['filter']['d_d_id'];

        $kpi_object = KpiObject::where('years', $years)->where('quarter', $quarter)->whereIn('department_id', $dep_ids)->first();

        $comment = KpiComment::where('kpi_object_id', $kpi_object->id)
            ->where('d_d_id', $d_d_id)
            ->with(['employee' => function ($q) {
                $q->select('id')->with(['user' => function ($qu) {
                    $qu->select('id', 'username', 'employee_id');
                }]);
            }])->get();
        return $comment;
    }

    public function kpiGetFilesResolution(Request $request)
    {
        $res_id = $request['filter']['res_id'];
        if ($res_id) :
            $model = KpiResolutionComission::where('id', $res_id)->first();
            $kpi_object = KpiObject::where('id', $model->kpi_object_id)->first();
            $files = File::where('object_id', $kpi_object->id)->where('object_type_id', 13)->get();
            return $files;
        endif;
        return 'no objekt';
    }
    public function kpiGetFiles(Request $request)
    {
        if (Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager')) {
            $user_dep = Department::find($request['filter']['department_id'])->department_code;

            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();
            // $dep_ids2 = Department::whereIn('parent_id', $dep_ids)->pluck('id')->toArray();

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];

            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->whereIn('dep2_id', $dep_ids)
                // ->whereIn('department_id', $dep_ids2)
                ->first();
            // if (Auth::user()->username == 'qg9592') {
            //     return [$dep_ids];
            // }
            if (!$kpi_object) return [];



            $files = File::where('object_id', $kpi_object->id)->where('object_type_id', 13)->get();
            return $files;
        } else {
            $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;

            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];

            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->whereIn('department_id', $dep_ids)

                ->first();
            if (!$kpi_object) return [];


            // if (Auth::user()->username == 'nm076') {
            //     return [$dep_ids];
            // }

            $files = File::where('object_id', $kpi_object->id)->where('object_type_id', 13)->get();
            return $files;
        }
    }

    public function getAttributesFakts(Request $request)
    {
        $selected_doc_id = isset($request['filter']['doc_id']) ? $request['filter']['doc_id'] : null;

        $quarter = $request['filter']['quarter'];
        $year = $request['filter']['year'];
        $dep_id = $request['filter']['department_id'];
        $dep_ids = [];
        //965
        $user = Auth::user();
        // if($user->id == 965){
        //     return $user->employee_id;
        // }

        if (!$quarter) {
            if ((date('m-d H:i:s') > '04-01 00:00:00' && date('m-d H:i:s') < '04-30 00:00:00')) {
                $quarter = 1;
            } else if (date('m-d H:i:s') > '07-01 00:00:00' && date('m-d H:i:s') < '07-30 00:00:00') {
                $quarter = 2;
            } else if (date('m-d H:i:s') > '10-01 00:00:00' && date('m-d H:i:s') < '10-30 00:00:00') {
                $quarter = 3;
            } else if (date('m-d H:i:s') > '01-01 00:00:00' && date('m-d H:i:s') < '02-10 00:00:00') {
                $quarter = 4;
            }
        }

        if (Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager')) {
            $dep_id = Department::find($dep_id);

            if ($dep_id) $dep_ids = [$dep_id->id];
        } else {
            // $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;
            // $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();
        }

        if ($selected_doc_id) {
            // return $selected_doc_id;
            $doc_id = Document::where('id', $selected_doc_id);
        } else {

            $doc_id = Document::select('id')
                ->whereIn('status', [3, 4, 5])
                ->where('document_template_id', 431);
            if (!Auth::user()->hasPermission('kpi-comission') && !Auth::user()->hasPermission('kpi-manager')) {

                $doc_id->whereHas('documentSigners', function ($q) use ($user) {
                    $q->where('signer_employee_id', $user->employee_id)
                        ->where('action_type_id', 6);
                });
                // return $doc_id->get();
            } else {
                $doc_id->whereIn('department2_id', $dep_ids);
            }
        }

        $doc_id->whereHas('documentDetails', function ($q) use ($year) {
            $q->whereHas('documentDetailContents', function ($s) use ($year) {
                $s
                    ->where('value',  $year)
                    ->where('d_d_attribute_id', 1936);
            });
        });

        // if (Auth::user()->username == 'Ssa709') {
        //     return $doc_id->get();
        // }

        $doc_id = $doc_id->get()->pluck('id');

        $doc_count =  count($doc_id);
        $doc = $doc_count > 1 ? Document::whereIn('id', $doc_id)->get() : $doc_id;


        if ($year == 2022 && count($doc_id) == 0) {
            $doc_id = Document::select('id')
                // ->whereIn('department2_id', $dep_ids)
                ->whereIn('status', [3, 4, 5])
                ->where('document_template_id', 431)
                ->whereHas('documentDetails', function ($q) {
                    $q->whereDoesntHave('documentDetailContents', function ($s) {
                        $s->where('d_d_attribute_id', 1936);
                    });
                });
            if (!Auth::user()->hasPermission('kpi-comission') && !Auth::user()->hasPermission('kpi-manager')) {
                $doc_id->whereHas('documentSigners', function ($q) use ($user) {
                    $q->where('signer_employee_id', $user->employee_id)
                        ->where('action_type_id', 6);
                });
            } else {
                $doc_id->whereIn('department2_id', $dep_ids);
            }
            $doc_id = $doc_id->pluck('id')->toArray();
        }

        // $doc_id = Document::where('document_template_id', 431)
        // ->whereIn('department2_id', $dep_ids)->whereIn('status', [3, 4, 5])
        // ->where('document_date', 'like', $year . '%')
        // ->pluck('id')->toArray();
        // }

        $comission = Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager');
        $documentDetails = DocumentDetail::query();
        $documentDetails->with('documentDetailAttributeValues.documentDetailAttributes.tableList');
        $documentDetails->with('documentDetailContents');
        $documentDetails->with('document');
        $documentDetails->with('kpiComments.employee');
        $documentDetails->with('kpiComments.kpiObjekt');
        $documentDetails->with(['DocumentDetailFakt' => function ($q) use ($quarter, $comission) {
            $q->with('comissions.employee:id,firstname_ru,firstname_uz_cyril,firstname_uz_latin,lastname_ru,lastname_uz_cyril,lastname_uz_latin,middlename_ru,middlename_uz_cyril,middlename_uz_latin,tabel');
            $q->where('quarter', $quarter);
            if ($comission) {
                $q->whereNotNull('fakt');
            }
        }]);
        // if ($comission) {
        //     $documentDetails->whereDoesntHave('DocumentDetailFakt', function ($q) {
        //         $q->whereNull('fakt');
        //     });
        // }
        $documentDetails->whereIn('document_id', $doc_id);
        $document_details = $documentDetails->get();
        // return 

        // return $document_details;

        $fakt = DocumentDetailFakt::WhereHas('documentDetail', function ($q) use ($doc_id) {
            $q->where('document_id', $doc_id);
        })
            ->where('status', 1)
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->first();

        $change_data = false;
        $change_reaction = false;
        if (
            ($quarter == 1 && date('Y-m-d H:i:s') > $year . '-04-01 00:00:00' && date('Y-m-d H:i:s') < $year . '-04-20 00:00:00') ||
            ($quarter == 2 && date('Y-m-d H:i:s') > $year . '-07-01 00:00:00' && date('Y-m-d H:i:s') < $year . '-12-20 00:00:00') ||
            ($quarter == 3 && date('Y-m-d H:i:s') > $year . '-09-01 00:00:00' && date('Y-m-d H:i:s') < $year . '-12-20 00:00:00') ||
            ($quarter == 4 && date('Y-m-d H:i:s') > ($year + 1) . '-01-01 00:00:00' && date('Y-m-d H:i:s') < ($year + 1) . '-01-20 10:30:00')
            // || $fakt
        ) {
            $change_data = true;
        }
        if (
            ($quarter == 1 && date('Y-m-d H:i:s') > $year . '-04-11 00:00:00' && date('Y-m-d H:i:s') < $year . '-04-30 00:00:00') ||
            ($quarter == 2 && date('Y-m-d H:i:s') > $year . '-07-11 00:00:00' && date('Y-m-d H:i:s') < $year . '-12-28 00:00:00') ||
            ($quarter == 3 && date('Y-m-d H:i:s') > $year . '-07-11 00:00:00' && date('Y-m-d H:i:s') < $year . '-12-28 00:00:00') ||
            ($quarter == 4 && date('Y-m-d H:i:s') > ($year + 1) . '-01-11 00:00:00' && date('Y-m-d H:i:s') < ($year + 1) . '-02-10 00:00:00')
        ) {
            $change_reaction = true;
        }

        return [
            'document_details' => $document_details,
            'change_data' => $change_data,
            'change_reaction' => $change_reaction,
            'quarter' => $quarter,
            'doc_count' => $doc_count,
            'doc' => $doc
        ];
    }

    public function saveFacts(Request $request)
    {
        // kpi_fakt
        $document_details = $request['document_details'];
        $doc = Document::find($document_details[0]['document_id']);
        foreach ($document_details as $key => $value) {
            $ddf = DocumentDetailFakt::find($value['document_detail_fakt']['id']);
            if (!$ddf) {
                $ddf = new DocumentDetailFakt();
            }
            $is_changed = false;
            if (
                $ddf->fakt != $value['document_detail_fakt']['fakt'] ||
                $ddf->achieving_goal != $value['document_detail_fakt']['achieving_goal'] ||
                $ddf->reward_amount != $value['document_detail_fakt']['reward_amount']
            ) {
                if (Auth::id() != 6) {

                    foreach ($ddf->comissions as $comission) {
                        $comission->status = null;
                        $comission->save();
                    }
                }
                $is_changed = true;
            }
            $ddf->d_d_id = $value['document_detail_fakt']['d_d_id'];
            $ddf->fakt = $value['document_detail_fakt']['fakt'];
            $ddf->achieving_goal = $value['document_detail_fakt']['achieving_goal'];
            $ddf->reward_amount = $value['document_detail_fakt']['reward_amount'];
            $ddf->year = $value['document_detail_fakt']['year'];
            $ddf->quarter = $value['document_detail_fakt']['quarter'];
            if ($is_changed && Auth::id() != 6) {
                $ddf->status = null;
            }
            // $ddf->status = 2;
            $ddf->save();
        }

        // kpi_objekt
        $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;
        $dep_id = Auth::user()->employee->mainStaff[0]->department->id;
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];

        // $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

        // $kpi_object = KpiObject::where('years', $years)->where('quarter', $quarter)->whereIn('department_id', $dep_ids)->first();

        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            // ->where('department_id', $dep_id)
            ->where('dep_id', $doc->department2_id)
            // ->whereIn('department_id', $dep_ids)
            ->first();

        if (!$kpi_object) {
            $kpi_object = new KpiObject();
            $kpi_object->department_id = $dep_id;
            $kpi_object->quarter = $quarter;
            $kpi_object->years = $years;
            $kpi_object->doc_id = $doc->id;
            $kpi_object->dep2_id = $doc->department2_id;
            $kpi_object->dep_id = $doc->department2_id;
            $kpi_object->save();

            $kpiObjectuser = new KpiobjektUser();
            $kpiObjectuser->user_id = Auth::user()->id;
            $kpiObjectuser->kpi_objects_id = $kpi_object->id;
            $kpiObjectuser->save();
        } else {
            $kpi_object->doc_id = $doc->id;
            $kpi_object->dep2_id = $doc->department2_id;
            $kpi_object->dep_id = $doc->department2_id;
            $kpi_object->save();
        }

        $object_id = $kpi_object->id;

        return $object_id;
        // return 'success!';
    }

    public function commentFiles(Request $request, $id)
    {
        // return $id;
        DB::beginTransaction();
        $files = $request->file('files');
        if ($files) {
            try {
                $object_type_id = 16;
                $object_id = $id;

                foreach ($files as $key => $value) {
                    $filename = time() . rand();
                    Storage::putFileAs(
                        'kpi',
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
                }
                DB::commit();
                return ['message' => 'Successfully saved!', 'document_id' => $object_id];
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }
    public function updateFiles(Request $request, $id)
    {
        // return $id;
        DB::beginTransaction();
        $files = $request->file('files');
        if ($files) {
            try {
                $object_type_id = 13;
                $object_id = $id;

                foreach ($files as $key => $value) {
                    $filename = time() . rand();
                    // dd(substr($value->getClientOriginalName(), -3));
                    if (substr($value->getClientOriginalName(), -3) == 'mp4') {
                        Storage::putFileAs(
                            'kpi/video',
                            $value,
                            $filename
                        );
                    } else {
                        Storage::putFileAs(
                            'kpi',
                            $value,
                            $filename
                        );
                    }
                    $file = new File();
                    $file->object_type_id = $object_type_id;
                    $file->file_name = $value->getClientOriginalName();
                    $file->physical_name = $filename;
                    $file->object_id = $object_id;
                    // $file->description = $description;
                    $file->created_by = Auth::id();
                    $file->save();
                }
                DB::commit();
                return ['message' => 'Successfully saved!', 'document_id' => $object_id];
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }

    public function saveReactionsPlan(Request $request)
    {
        $document_details = $request['document_details'];

        foreach ($document_details as $key => $value) {
            $kpi_plan_comissions = $value['kpi_plan_comissions'];
            $document = Document::find($document_details[0]['document_id']);
            $accepted_comissions_length = 0;
            $not_accepted_comissions_length = 0;
            foreach ($kpi_plan_comissions as $v) {
                // return
                $comission = KpiPlanComission::find($v['id']);

                if (!$comission) {
                    $comission = new KpiPlanComission();
                    $comission->employee_id = Auth::user()->employee_id;
                    $comission->d_d_id = $v['d_d_id'];
                    $comission->quarter = $v['quarter'];
                }
                if ($comission->employee_id == Auth::user()->employee_id) {
                    $comission->status = $v['status'];
                    $comission->save();
                }
                if ($comission->status == true) {
                    $accepted_comissions_length++;
                }
                if ($comission->status === false) {
                    $not_accepted_comissions_length++;
                }

                if ($not_accepted_comissions_length > 5) {
                    // $document = Document::find($document_details[0]['document_id']);
                    $document->status = 0;
                    $document->save();

                    $com = KpiPlanComission::whereHas('documentDetail', function ($q) use ($document) {
                        $q->where('document_id', $document->id);
                    })->update(['status' => null]);

                    return ['message' => 'Xujjat komissiyalar tomonidan bekor qilindi !', 'status' => 300];
                } else if ($accepted_comissions_length > 5 && $accepted_comissions_length + $not_accepted_comissions_length == 11) {
                    $document->status = 9;
                    $document->save();
                    return ['message' => 'Xujjat komissiyalar tomonidan tasdiqlandi !', 'status' => 200];
                }
            }
        }

        return $request;
    }
    public function saveReactions(Request $request)
    {
        $document_details = $request['document_details'];
        foreach ($document_details as $key => $value) {
            $ddf = DocumentDetailFakt::find($value['document_detail_fakt']['id']);
            $comissions = $value['document_detail_fakt']['comissions'];
            $accepted_comissions_length = 0;
            $not_accepted_comissions_length = 0;
            foreach ($comissions as $c) {
                $comission = KpiFactComission::find($c['id']);
                if (!$comission) {
                    $comission = new KpiFactComission();
                    $comission->employee_id = Auth::user()->employee_id;
                    $comission->d_d_fakt_id = $value['document_detail_fakt']['id'];
                }
                if ($comission->employee_id == Auth::user()->employee_id) {
                    $comission->status = $c['status'];
                    $comission->save();
                }
                if ($comission->status == true) {
                    $accepted_comissions_length++;
                    // $accepted_comissions_length = $accepted_comissions_length+$accepted_comissions_length;                    

                }
                if ($comission->status == false) {
                    $not_accepted_comissions_length++;
                    // $not_accepted_comissions_length = $not_accepted_comissions_length+$not_accepted_comissions_length;
                }
            }
            // $comission = KpiFactComission::find(21093);
            // if ($comission->status == false){
            //     return 10;
            // }else{
            //     return 5;
            // }

            // return [$accepted_comissions_length, $not_accepted_comissions_length ];
            // if ($accepted_comissions_length < 6 && $accepted_comissions_length + $not_accepted_comissions_length == 11) {
            if ($not_accepted_comissions_length > 5) {
                $ddf->status = 1;
                $ddf->save();
            } else if ($accepted_comissions_length > 5 && $accepted_comissions_length + $not_accepted_comissions_length == 11) {
                $ddf->status = 2;
                $ddf->save();
            }
        }
        return 'success!';
    }

    public function CloseAllDetails(Request $request)
    {
        foreach ($request->input('document_details') as $key => $value) {
            DocumentDetailFakt::where('id', $value['document_detail_fakt']['id'])->update(['status' => 1]);
        }
    }
    public function validateDocumentPlan(){
        $employee_id = Auth::user()->employee->id;
        $document = Document::select('id')
        ->where('created_employee_id', $employee_id)
        ->where('document_template_id', 431)
        ->where('status', 0)
        ->first();
        $validate_create_doc = 1;

        if(!$document){
            $validate_create_doc = 0;
        }

        return $validate_create_doc;
    }

    public function kpiDepartmentsPlan(Request $request)
    {
        // return
        //  5;  and doc.created_at > '".$year."-01-01'
        $year = $request['years'];

        $query = "SELECT min(dep.id) id, dep.department_code, min(dep.name_uz_latin) name_uz_latin
       
        FROM departments dep
                inner JOIN documents doc on doc.department2_id = dep.id               
                WHERE doc.document_template_id=431
                -- and doc.created_at > '2024-01-01'
                and doc.deleted_at is null
                and doc.status = 7
                and dep.deleted_at is null
                group by dep.department_code
                order by dep.department_code";

        $deps = DB::select($query);

        return ['departments' => $deps];
    }
    public function kpiDepartments(Request $request)
    {
        // return $request['type']; 
        if (isset($request['type']) && $request['type'] == 0) {
            return Department::whereHas('kpiObjektdep')
                ->get();
        }
        $chorak = 0;
        // if ((date('m-d H:i:s') > '04-01 00:00:00' && date('m-d H:i:s') < '04-30 00:00:00')) {
        //     $chorak = 1;
        // } else if (date('m-d H:i:s') > '07-01 00:00:00' && date('m-d H:i:s') < '07-30 00:00:00') {
        //     $chorak = 2;
        // } else if (date('m-d H:i:s') > '10-01 00:00:00' && date('m-d H:i:s') < '10-30 00:00:00') {
        //     $chorak = 3;
        // } else if (date('m-d H:i:s') > '01-01 00:00:00' && date('m-d H:i:s') < '01-30 00:00:00') {
        //     $chorak = 4;
        // }


        $kpiSettingDate = kpiSettingDate::get();
        $date = date('Y-m-d');
        $y = date('Y');
        $quarter_real = null;
        if ($y . '-' . $kpiSettingDate[0]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[1]['from_kpi_facts']) {
            // return 1;
            $quarter_real = $kpiSettingDate[0]->quarter;
        } else if ($y . '-' . $kpiSettingDate[1]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[2]['from_kpi_facts']) {
            // return 2;
            $quarter_real = $kpiSettingDate[1]->quarter;
        } else if ($y . '-' . $kpiSettingDate[2]['from_kpi_facts'] < $date && $date < ($y + 1) . '-' . $kpiSettingDate[3]['from_kpi_facts']) {
            // return 3;
            $quarter_real = $kpiSettingDate[2]->quarter;
        } else if ($y . '-' . $kpiSettingDate[3]['from_kpi_facts'] < $date && $date < ($y - 1) . '-' . $kpiSettingDate[0]['from_kpi_facts']) {
            // return 4;
            $quarter_real = $kpiSettingDate[3]->quarter;
        }

        $chorak = $quarter_real;



        $year = $request['years'];

        $quarter = ($request['quarter'] == 0) ? $chorak : $request['quarter'];
        // $departments = $departments->orderBy('department_code')->get();

        $employee_id = Auth::user()->employee_id;

        // return $quarter;
        // return $year;

        // left join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id  and kfc.employee_id=" . $employee_id . "

        $query = "SELECT min(dep.id) id, dep.department_code, min(dep.name_uz_latin) name_uz_latin, 
        sum(CASE WHEN kfc.status = true THEN 1 ELSE 0 END) ok,
        sum(CASE WHEN kfc.status = false THEN 1 ELSE 0 END) cancel,
        sum(CASE WHEN kfc.status IS NULL THEN 1 ELSE 0 END) notok
        FROM departments dep
                inner JOIN documents doc on doc.department2_id = dep.id
                inner join document_details dd on dd.document_id=doc.id
                INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
                left join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id  and kfc.employee_id= '" . $employee_id . "'
                WHERE doc.document_template_id=431 and doc.status in (3,4,5) 
                and ddf.year= '" . $year . "'  and ddf.quarter='" . $quarter . "' and dep.deleted_at is null
                group by dep.department_code
                order by dep.department_code";

        //     $query = "SELECT dep.id, dep.department_code, dep.name_uz_latin, 
        //     COUNT(*) FILTER (WHERE kfc.status = TRUE) AS ok, 
        //     COUNT(*) FILTER (WHERE kfc.status = FALSE) AS cancel, 
        //     COUNT(*) FILTER (WHERE kfc.status IS NULL) AS notok
        //   FROM departments dep
        //   INNER JOIN documents doc ON doc.department2_id = dep.id
        //   INNER JOIN document_details dd ON dd.document_id = doc.id
        //   INNER JOIN document_detail_fakts ddf ON ddf.d_d_id = dd.id
        //   LEFT JOIN kpi_fact_comissions kfc ON kfc.d_d_fakt_id = ddf.id AND kfc.employee_id = 916
        //   WHERE doc.document_template_id = 431 
        //     AND doc.status IN (3, 4, 5) 
        //     AND ddf.year = 2023 
        //     AND ddf.quarter = 4 
        //     AND dep.deleted_at IS NULL
        //   GROUP BY dep.id, dep.department_code, dep.name_uz_latin
        //   ORDER BY dep.department_code";


        // $query = "SELECT dep.id, dep.department_code, dep.name_uz_latin, sum(kfc.status=1) ok, sum(kfc.status=0) cancel, sum(kfc.status is null) notok FROM `departments` dep
        // inner JOIN documents doc on doc.department2_id = dep.id
        // inner join document_details dd on dd.document_id=doc.id
        // INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
        // left join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id  and kfc.employee_id=" . $employee_id . "
        // WHERE doc.document_template_id=431 and doc.status in (3,4,5) 
        // and ddf.year= " . $year . " and ddf.quarter=" . $quarter . " and dep.deleted_at is null
        // group by department_code
        // order by dep.department_code";

        $deps = DB::select($query);

        return ['departments' => $deps, 'chorak' => $chorak];
    }

    public function resolutionKpiEmployeeId(Request $request)
    {
        return $request;
    }

    // DocumentDetailAttributeController dan kochirildi


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
    public function store(Request $request)
    {
        //
    }

    public function validateCreateDoc(Request $request)
    {
        // return $request;
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        // return
        $dep_id = Auth::user()->employee->mainStaff[0]->department_id;

        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            ->where('department_id', $dep_id)
            ->first();
        if ($kpi_object && $kpi_object->report_doc_id) {
            $document = Document::select('id', 'status')->where('id', $kpi_object->report_doc_id)->whereIn('status', [1, 2, 3, 4, 5])->first();
            // return ($document);
            return ($document) ? $document->id : 'no report_doc_id!';
        }
        // if(!$kpi_object->report_doc_id){
        //     return $kpi_object;
        // }
        return 'no report_doc_id!';
    }
    public function sendResolutionEmployee(Request $request)
    {
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $dep_id = $request['filter']['department_id'];
        $resolution_id = $request['resolution_filter']['resolution_comission_id'];
        $document_id = $request['resolution_filter']['document_id'];
        // return
        $kpi_object = KpiObject::where('years', $years)->where('quarter', $quarter)->where('dep_id', $dep_id)->first();

        $model = KpiResolutionComission::where('resolution_id', $resolution_id)
            ->where('comission_id', Auth::user()->employee_id)
            ->where('kpi_object_id', $kpi_object->id)
            ->where('document_id', $document_id)
            ->first();

        if (!$model) {
            $model = new KpiResolutionComission();
            $model->resolution_id = $resolution_id;
            $model->kpi_object_id = $kpi_object->id;
            $model->document_id = $document_id;
            $model->comission_id = Auth::user()->employee_id;
            $model->save();
            return 'success';
        }
        return 'double';
    }

    public function asistantComment(Request $request)
    {
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $dep_id = $request['filter']['department_id'];
        $language = $request->input('language');
        // return
        $kpi_object = KpiObject::where('years', $years)->where('quarter', $quarter)->where('dep2_id', $dep_id)->first();
        if ($kpi_object) :
            return
                $model = KpiResolutionComission::where('comission_id', Auth::user()->employee_id)
                ->with(['resolutionEmployee' => function ($q) use ($language) {
                    $q->select([
                        'id', 'tabel', 'tariff_scale_id', 'firstname_' . $language . ' as firstname',
                        'lastname_' . $language . ' as lastname', 'middlename_' . $language . ' as middlename'
                    ]);
                }])
                ->where('kpi_object_id', $kpi_object->id)
                ->get();

        endif;

        // return 'no objekt';
    }
    public function resolutionEmployee(Request $request)
    {
        $language = $request->input('language');
        $employee = Employee::select([
            'id', 'tabel', 'tariff_scale_id', 'firstname_' . $language . ' as firstname',
            'lastname_' . $language . ' as lastname', 'middlename_' . $language . ' as middlename'
        ])
            ->where('is_active', 1)
            ->get();
        return $employee;
    }
    public function resolutionDepartments(Request $request)
    {

        $kpiSettingDate = kpiSettingDate::get();
        $date = date('Y-m-d');
        $y = date('Y');
        $quarter_real = null;
        $year_real = null;

        if ($y . '-' . $kpiSettingDate[0]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[1]['from_kpi_facts']) {
            // return 1;
            $quarter_real = $kpiSettingDate[0]->quarter;
            $year_real = $y;
        } else if ($y . '-' . $kpiSettingDate[1]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[2]['from_kpi_facts']) {
            // return 2;
            $quarter_real = $kpiSettingDate[1]->quarter;
            $year_real = $y;
        } else if ($y . '-' . $kpiSettingDate[2]['from_kpi_facts'] < $date && $date < ($y + 1) . '-' . $kpiSettingDate[3]['from_kpi_facts']) {
            // return 3;
            $quarter_real = $kpiSettingDate[2]->quarter;
            $year_real = $y;
        } else if (($y - 1) . '-' . $kpiSettingDate[3]['from_kpi_facts'] < $date && $date < ($y) . '-' . $kpiSettingDate[0]['from_kpi_facts']) {
            // return 4;
            $quarter_real = $kpiSettingDate[3]->quarter;
            $year_real = $y - 1;
        }

        // if ((date('m-d H:i:s') > '04-01 00:00:00' && date('m-d H:i:s') < '04-30 00:00:00')) {
        //     $quarter = 1;
        // } else if (date('m-d H:i:s') > '07-01 00:00:00' && date('m-d H:i:s') < '07-30 00:00:00') {
        //     $quarter = 2;
        // } else if (date('m-d H:i:s') > '10-01 00:00:00' && date('m-d H:i:s') < '10-30 00:00:00') {
        //     $quarter = 3;
        // } else if (date('m-d H:i:s') > '01-01 00:00:00' && date('m-d H:i:s') < '02-10 00:00:00') {
        //     $quarter = 4;
        // }
        // return 
        // $year_real;
        $quarter = $quarter_real;

        return
            $model = KpiResolutionComission::with(['kpiObjektresolution' => function ($q) use ($year_real) {
                $q->with('departmentResolution');
            }])
            ->where('resolution_id', Auth::user()->employee_id)
            ->whereHas('kpiObjektresolution', function ($q) use ($quarter, $year_real) {
                $q
                    // ->where('years', $year_real)
                    ->where('years', 'ilike', "%" . $year_real . "%")
                    ->where('quarter', $quarter);
            })
            // ->whereNull('comments')
            ->get();
    }
    public function resolutionAttributesFakts(Request $request)
    {
        // return
        $res_id = $request['filter']['res_id'];
        if ($res_id) :
            $model = KpiResolutionComission::where('id', $res_id)->first();
            $kpi_object = KpiObject::where('id', $model->kpi_object_id)->first();
            $doc_id = $model->document_id;
            $comment = $model->comments;
            $quarter = $kpi_object->quarter;

            $documentDetails = DocumentDetail::query();
            $documentDetails->with('documentDetailAttributeValues.documentDetailAttributes.tableList');
            $documentDetails->with('documentDetailContents');
            $documentDetails->with('document');
            $documentDetails->with(['DocumentDetailFakt' => function ($q) use ($quarter) {
                $q->with('comissions.employee:id,firstname_ru,firstname_uz_cyril,firstname_uz_latin,lastname_ru,lastname_uz_cyril,lastname_uz_latin,middlename_ru,middlename_uz_cyril,middlename_uz_latin,tabel');
                $q->where('quarter', $quarter);
            }]);

            $documentDetails->where('document_id', $doc_id);
            $document_details = $documentDetails->get();

            return ['document_details' => $document_details, 'quarter' => $quarter, 'comment' => $comment];
        endif;
        return 'no objekt';
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
    public function kpiOtchet(Request $request)
    {
        // return 
        $filter = $request['filter'];
        $quarter = $filter['quarter'];
        $year = $filter['year'];
        $year = (string)$year;
        // return 
        // gettype( $year);

        $otchet = DB::select('SELECT dep.department_code, min(dep.name_uz_latin) name_uz_latin, min(doc.document_number) document_number, 
            min(ddv.attribute_value) attribute_value, min(ddf.achieving_goal) achieving_goal, min(ddf.reward_amount) mukofot, 
            sum(CASE WHEN kfc.status = true THEN 1 ELSE 0 END) tasdiq,  
            sum(CASE WHEN kfc.status = false THEN 1 ELSE 0 END) otkaz, 
            sum(CASE WHEN kfc.status IS NULL THEN 1 ELSE 0 END) not_signed  
            FROM departments dep
            inner JOIN documents doc on doc.department2_id = dep.id
            inner join document_details dd on dd.document_id=doc.id
            inner join document_detail_attribute_values ddv on ddv.document_detail_id=dd.id
            INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
            inner join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id
            WHERE doc.document_template_id= 431 and doc.status in (3,4,5) and ddf.year= \'' . $year . '\' and
            ddf.quarter=  ' . $quarter . '   and ddv.d_d_attribute_id = 1321
            group by department_code, dd.id
        ');

        // $otchet = DB::select("SELECT dep.department_code, dep.name_uz_latin, doc.document_number, ddf.fakt, ddf.reward_amount 'mukofot', sum(kfc.status=1) tasdiq,  sum(kfc.status=0) otkaz,  sum(kfc.status is null) not_signed  FROM departments dep
        // inner JOIN documents doc on doc.department2_id = dep.id
        // inner join document_details dd on dd.document_id=doc.id
        // INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
        // inner join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id
        // WHERE doc.document_template_id=431 and doc.status in (3,4,5) and ddf.year=2022 and ddf.quarter=4
        // group by department_code, dd.id");

        return $otchet;
    }
    public function kpiOtchet2()
    {
        $otchet = DB::select('SELECT min(dep.id) id, dep.department_code, min(dep.name_uz_latin) name_uz_latin, min(doc.document_number) document_number , sum(ddf.reward_amount) mukofot
        FROM departments dep
        inner JOIN documents doc on doc.department2_id = dep.id
        inner join document_details dd on dd.document_id=doc.id
        INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
        WHERE doc.document_template_id= 431 and doc.status in (3,4,5) and ddf.year= \'2023\' and ddf.quarter=4
        group by dep.department_code');

        return $otchet;
    }
    public function kpiOtchet3()
    {


        // $otchet = DB::select("select tmp.*, count(dd1.id) max_detail from (SELECT dep.department_code,  doc.id doc_id,
        // sum(CASE WHEN kfc.employee_id = 33 && kfc.status is not null THEN 1 ELSE 0 END) as '33',
        // sum(CASE WHEN kfc.employee_id = 46 && kfc.status is not null THEN 1 ELSE 0 END) as '46',
        // sum(CASE WHEN kfc.employee_id = 94 && kfc.status is not null THEN 1 ELSE 0 END) as '94',
        // sum(CASE WHEN kfc.employee_id = 100 && kfc.status is not null THEN 1 ELSE 0 END) as '100',
        // sum(CASE WHEN kfc.employee_id = 3347 && kfc.status is not null THEN 1 ELSE 0 END) as '3347',
        // sum(CASE WHEN kfc.employee_id = 10786 && kfc.status is not null THEN 1 ELSE 0 END) as '10786',
        // sum(CASE WHEN kfc.employee_id = 916 && kfc.status is not null THEN 1 ELSE 0 END) as '916',        
        // sum(CASE WHEN kfc.employee_id = 11869 && kfc.status is not null THEN 1 ELSE 0 END) as '11869',
        // sum(CASE WHEN kfc.employee_id = 8688 && kfc.status is not null THEN 1 ELSE 0 END) as '8688',
        // sum(CASE WHEN kfc.employee_id = 8721 && kfc.status is not null THEN 1 ELSE 0 END) as '8721',
        // sum(CASE WHEN kfc.employee_id = 68 && kfc.status is not null THEN 1 ELSE 0 END) as '68'
        // FROM departments dep
        // inner JOIN documents doc on doc.department2_id = dep.id
        // inner join document_details dd on dd.document_id=doc.id
        // INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
        // inner join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id
        // WHERE doc.document_template_id=431 and doc.status in (3,4,5) and ddf.year=2023 and ddf.quarter=4 
        // group by department_code) tmp
        // inner join document_details dd1 on dd1.document_id=tmp.doc_id and exists (select * from document_detail_fakts ddf where ddf.d_d_id=dd1.id and ddf.fakt is not null and ddf.year=2023 and ddf.quarter=4 )
        // where dd1.deleted_at is null
        // group by tmp.department_code");


        $otchet = DB::select('SELECT tmp.department_code,  min(tmp.a33) a33,  min(tmp.a46) a46,  min(tmp.a94) a94,  min(tmp.a100) a100,  min(tmp.a3347) a3347,  min(tmp.a10786) a10786,
         min(tmp.a916) a916,  min(tmp.a12272) a12272,  min(tmp.a8688) a8688,  min(tmp.a8721) a8721,  min(tmp.a68) a68,  count(dd1.id) max_detail from 
        (
            SELECT dep.department_code,  min(doc.id) doc_id,
                sum(CASE WHEN kfc.employee_id = 33 and kfc.status is not null THEN 1 ELSE 0 END) as "a33",
                sum(CASE WHEN kfc.employee_id = 46 and kfc.status is not null THEN 1 ELSE 0 END) as "a46",
                sum(CASE WHEN kfc.employee_id = 94 and kfc.status is not null THEN 1 ELSE 0 END) as "a94",
                sum(CASE WHEN kfc.employee_id = 100 and kfc.status is not null THEN 1 ELSE 0 END) as "a100",
                sum(CASE WHEN kfc.employee_id = 3347 and kfc.status is not null THEN 1 ELSE 0 END) as "a3347",
                sum(CASE WHEN kfc.employee_id = 10786 and kfc.status is not null THEN 1 ELSE 0 END) as "a10786",
                sum(CASE WHEN kfc.employee_id = 916 and kfc.status is not null THEN 1 ELSE 0 END) as "a916",        
                sum(CASE WHEN kfc.employee_id = 12272 and kfc.status is not null THEN 1 ELSE 0 END) as "a12272",
                sum(CASE WHEN kfc.employee_id = 8688 and kfc.status is not null THEN 1 ELSE 0 END) as "a8688",
                sum(CASE WHEN kfc.employee_id = 8721 and kfc.status is not null THEN 1 ELSE 0 END) as "a8721",
                sum(CASE WHEN kfc.employee_id = 68 and kfc.status is not null THEN 1 ELSE 0 END) as "a68"
                FROM departments dep
                inner JOIN documents doc on doc.department2_id = dep.id
                inner join document_details dd on dd.document_id=doc.id
                INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
                inner join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id
                WHERE doc.document_template_id = 431 and doc.status in (3,4,5) and ddf.year= \'2023\' and ddf.quarter=4 
                group by department_code) tmp
                inner join document_details dd1 on dd1.document_id=tmp.doc_id and exists (select * from document_detail_fakts ddf where ddf.d_d_id=dd1.id 
                and ddf.fakt is not null and ddf.year= \'2023\' and  ddf.quarter=4 )
                where dd1.deleted_at is null
                group by tmp.department_code
        ');

        //         select tmp.*, count(dd1.id) max_detail from 
        // (
        // 	SELECT dep.department_code,  min(doc.id) doc_id,
        //         sum(CASE WHEN kfc.employee_id = 33 and kfc.status is not null THEN 1 ELSE 0 END) as "33",
        //         sum(CASE WHEN kfc.employee_id = 46 and kfc.status is not null THEN 1 ELSE 0 END) as "46",
        //         sum(CASE WHEN kfc.employee_id = 94 and kfc.status is not null THEN 1 ELSE 0 END) as "94",
        //         sum(CASE WHEN kfc.employee_id = 100 and kfc.status is not null THEN 1 ELSE 0 END) as "100",
        //         sum(CASE WHEN kfc.employee_id = 3347 and kfc.status is not null THEN 1 ELSE 0 END) as "3347",
        //         sum(CASE WHEN kfc.employee_id = 10786 and kfc.status is not null THEN 1 ELSE 0 END) as "10786",
        //         sum(CASE WHEN kfc.employee_id = 916 and kfc.status is not null THEN 1 ELSE 0 END) as "916",        
        //         sum(CASE WHEN kfc.employee_id = 11869 and kfc.status is not null THEN 1 ELSE 0 END) as "11869",
        //         sum(CASE WHEN kfc.employee_id = 8688 and kfc.status is not null THEN 1 ELSE 0 END) as "8688",
        //         sum(CASE WHEN kfc.employee_id = 8721 and kfc.status is not null THEN 1 ELSE 0 END) as "8721",
        //         sum(CASE WHEN kfc.employee_id = 68 and kfc.status is not null THEN 1 ELSE 0 END) as "68"
        //         FROM departments dep
        //         inner JOIN documents doc on doc.department2_id = dep.id
        //         inner join document_details dd on dd.document_id=doc.id
        //         INNER JOIN document_detail_fakts ddf on ddf.d_d_id=dd.id
        //         inner join kpi_fact_comissions kfc on kfc.d_d_fakt_id=ddf.id
        //         WHERE doc.document_template_id=431 and doc.status in (3,4,5) and ddf.year='2023' and ddf.quarter=4 
        //         group by department_code
        // ) tmp
        //         inner join document_details dd1 on dd1.document_id=tmp.doc_id and exists (select * from document_detail_fakts ddf where ddf.d_d_id=dd1.id 
        // 		and ddf.fakt is not null and ddf.year='2023' and ddf.quarter=4 )
        //         where dd1.deleted_at is null
        //         group by tmp.department_code

        return $otchet;
    }

    public function kpiOtchet4()
    {
        // $otchet = DB::select('SELECT dep.department_code, dep.name_uz_latin,d.document_number, substring(d.document_date,1,10) document_date, d.id, d.status, d.pdf_file_name,
        $otchet = DB::select('SELECT min(dep.department_code) department_code, min(dep.name_uz_latin) name_uz_latin, min(d.document_number) document_number,
        substring(CAST(d.document_date AS VARCHAR), 1, 10) as document_date, d.id, sum(d.status) as status, min(d.pdf_file_name) pdf_file_name,
        sum(CASE WHEN ds.status = 1 and ds.action_type_id=1 THEN 1 ELSE 0 END) as quantity,
        sum(CASE WHEN ds.staff_id = 1 and ds.status = 1 THEN 1 WHEN ds.staff_id = 1 and ds.status = 2 THEN 2 ELSE 0 END) as "1",
        sum(CASE WHEN ds.staff_id = 39 and ds.status = 1 THEN 1 WHEN ds.staff_id = 39 and ds.status = 2 THEN 2 ELSE 0 END) as "39",
        sum(CASE WHEN ds.staff_id = 5687 and ds.status = 1 THEN 1 WHEN ds.staff_id = 5687 and ds.status = 2 THEN 2 ELSE 0 END) as "5687",
        sum(CASE WHEN ds.staff_id = 14 and ds.status = 1 THEN 1 WHEN ds.staff_id = 14 and ds.status = 2 THEN 2 ELSE 0 END) as "14",
        sum(CASE WHEN ds.staff_id = 40 and ds.status = 1 THEN 1 WHEN ds.staff_id = 40 and ds.status = 2 THEN 2 ELSE 0 END) as "40",
        sum(CASE WHEN ds.staff_id = 934 and ds.status = 1 THEN 1 WHEN ds.staff_id = 934 and ds.status = 2 THEN 2 ELSE 0 END) as "934",
        sum(CASE WHEN ds.staff_id = 4312 and ds.status = 1 THEN 1 WHEN ds.staff_id = 4312 and ds.status = 2 THEN 2 ELSE 0 END) as "4312",
        sum(CASE WHEN ds.staff_id = 2307 and ds.status = 1 THEN 1 WHEN ds.staff_id = 2307 and ds.status = 2 THEN 2 ELSE 0 END) as "2307",
        sum(CASE WHEN ds.staff_id = 2260 and ds.status = 1 THEN 1 WHEN ds.staff_id = 2260 and ds.status = 2 THEN 2 ELSE 0 END) as "2260",
        sum(CASE WHEN ds.staff_id = 65 and ds.status = 1 THEN 1 WHEN ds.staff_id = 65 and ds.status = 2 THEN 2 ELSE 0 END) as "65",
        sum(CASE WHEN ds.staff_id = 93 and ds.status = 1 THEN 1 WHEN ds.staff_id = 93 and ds.status = 2 THEN 2 ELSE 0 END) as "93",
        sum(CASE WHEN ds.staff_id = 4559 and ds.status = 1 THEN 1 WHEN ds.staff_id = 4559 and ds.status = 2 THEN 2 ELSE 0 END) as "4559"
        FROM documents d
        INNER join document_signers ds on ds.document_id=d.id
        inner join departments dep on dep.id = d.department2_id
        where d.document_template_id=431 and d.status not in (0,6) and EXISTS (select id from document_signers ds1 where ds1.document_id=d.id 
        and ds1.staff_id in (1,39,3799,14,40,934,4312,2321,2260,65,93,4559))
        and EXISTS (select id from document_details dd where dd.document_id=d.id 
        and EXISTS (select id from document_detail_contents ddc where ddc.document_detail_id=dd.id and ddc.d_d_attribute_id = 1936 and ddc.value = \'2023 \'))
        GROUP BY d.id
        order by department_code');

        return $otchet;
    }
    public function kpiOtchet5()
    {

        $otchet = DB::select('SELECT  min(dep.department_code) department_code, min(dep.name_uz_latin) name_uz_latin,min(d.document_number) document_number, 
        substring(CAST(d.document_date AS VARCHAR), 1, 10) as document_date, d.id, min(d.status) status, min(d.pdf_file_name) pdf_file_name,
        sum(CASE WHEN ds.status = 1 and ds.action_type_id=8 THEN 1 ELSE 0 END) as quantity,
        sum(CASE WHEN ds.staff_id = 1 and ds.status = 1 THEN 1 WHEN ds.staff_id = 1 and ds.status = 2 THEN 2 ELSE 0 END) as "1",
        sum(CASE WHEN ds.staff_id = 39 and ds.status = 1 THEN 1 WHEN ds.staff_id = 39 and ds.status = 2 THEN 2 ELSE 0 END) as "39",
        sum(CASE WHEN ds.staff_id = 14 and ds.status = 1 THEN 1 WHEN ds.staff_id = 14 and ds.status = 2 THEN 2 ELSE 0 END) as "14",
        sum(CASE WHEN ds.staff_id = 6747 and ds.status = 1 THEN 1 WHEN ds.staff_id = 6747 and ds.status = 2 THEN 2 ELSE 0 END) as "6747",
        sum(CASE WHEN ds.staff_id = 93 and ds.status = 1 THEN 1 WHEN ds.staff_id = 93 and ds.status = 2 THEN 2 ELSE 0 END) as "93",
        sum(CASE WHEN ds.staff_id = 65 and ds.status = 1 THEN 1 WHEN ds.staff_id = 65 and ds.status = 2 THEN 2 ELSE 0 END) as "65",
        sum(CASE WHEN ds.staff_id = 380 and ds.status = 1 THEN 1 WHEN ds.staff_id = 380 and ds.status = 2 THEN 2 ELSE 0 END) as "380",
        sum(CASE WHEN ds.staff_id = 934 and ds.status = 1 THEN 1 WHEN ds.staff_id = 934 and ds.status = 2 THEN 2 ELSE 0 END) as "934",
        sum(CASE WHEN ds.staff_id = 6500 and ds.status = 1 THEN 1 WHEN ds.staff_id = 6500 and ds.status = 2 THEN 2 ELSE 0 END) as "6500",
        sum(CASE WHEN ds.staff_id = 3794 and ds.status = 1 THEN 1 WHEN ds.staff_id = 3794 and ds.status = 2 THEN 2 ELSE 0 END) as "3794",
        sum(CASE WHEN ds.staff_id = 4312 and ds.status = 1 THEN 1 WHEN ds.staff_id = 4312 and ds.status = 2 THEN 2 ELSE 0 END) as "4312",
        sum(CASE WHEN ds.staff_id = 4559 and ds.status = 1 THEN 1 WHEN ds.staff_id = 4559 and ds.status = 2 THEN 2 ELSE 0 END) as "4559"
        FROM documents d
        INNER join document_signers ds on ds.document_id=d.id
        inner join departments dep on dep.id = d.department2_id
        where d.document_template_id=432 and d.id>=2465836 and d.status not in (0,6) and EXISTS (select id from document_signers ds1 where ds1.document_id=d.id 
        and ds1.staff_id in ( 1, 39, 14, 6747, 93, 65, 380, 934, 6500, 3794, 4312, 4559))
        GROUP BY d.id
        order by d.id desc');

        return $otchet;
    }
    // new functions


    public function kpiGetFilesNew(Request $request)
    {
        $selected_doc_id = isset($request['filter']['doc_id']) ? $request['filter']['doc_id'] : null;
        $department_id = isset($request['filter']['department_id']) ? $request['filter']['department_id'] : '';
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $user = Auth::user();
        if (Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager')) {
            if (!$department_id) {
                return 'department topilmadi!';
            }

            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->where('dep_id', $department_id)
                ->first();
            if (!$kpi_object) return [];
            $files = File::where('object_id', $kpi_object->id)->where('object_type_id', 13)->get();
            return $files;
        } else {
            $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;

            $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();


            $kpiobjektUser = KpiobjektUser::where('user_id', $user->id)
                ->whereHas('kpiobjekt', function ($q) use ($years, $quarter) {
                    $q->where('years', $years);
                    $q->where('quarter', $quarter);
                })
                ->with('kpiobjekt')
                ->first();

            // $dep_id =  $this->getDepID(Auth::user()->employee->staff[0]->department_id, $years, $quarter);

            $quarter = $request['filter']['quarter'];
            $years = $request['filter']['year'];

            $kpi_object = KpiObject::where('years', $years)
                ->where('quarter', $quarter)
                ->whereIn('dep_id', $dep_ids)
                ->first();
            // if (!$kpi_object) return [];
            if (!$kpiobjektUser) return [];



            $files = File::where('object_id', $kpiobjektUser->kpi_objects_id)->where('object_type_id', 13)->get();
            return $files;
        }
    }

    public function getDepID($dep_id, $year, $quarter, $step = 0)
    {
        // return 'sasa';
        $kpiObject = KpiObject::where('quarter', $quarter)->where('years', $year)->where('dep_id', $dep_id)->first();
        if ($kpiObject) {
            return
                $kpiObject->dep_id;
        } else {
            $dep = Department::find($dep_id);
            if ($dep && $dep->parent_id && $step < 10) {
                return $this->getDepID($dep->parent_id, $year, $quarter, $step++);
            } else {
                return 0;
            }
        }
    }

    public function getAttributesFaktsPlan(Request $request)
    {

        $filter = $request['filter'];
        $quarter = $filter['quarter'];

        $employee_id = Auth::user()->employee->id;
        // return 
        // $filter['department_id'];
        // $year = $filter['year'].'-01-01';

        if (Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager')) {

            if (isset($filter['department_id'])) {

                $document = Document::with('files')
                    ->where('department2_id', $filter['department_id'])
                    ->where('document_template_id', 431)
                    ->where('status', 7)
                    // ->where('created_at', '>', $filter['year'] . '-01-01')
                    ->first();
            }
        } else {
            $document = Document::with('files')
                ->where('created_employee_id', $employee_id)
                ->where('document_template_id', 431)
                ->whereNotIn('status', [6])
                // ->where('created_at', '>', $filter['year'] . '-01-01')
                ->first();
        }
        if (!isset($document)) {
            return 0;
        }

        $documentDetails = DocumentDetail::query();
        $documentDetails->with('documentDetailAttributeValues.documentDetailAttributes.tableList');
        $documentDetails->with('documentDetailContents');
        $documentDetails->with('document');
        $documentDetails->with(['kpiPlanComissions' => function ($q) use ($quarter) {
            $q->where('quarter', $quarter);
            $q->with('employee');
            $q->orderBy('created_at');
        }]);
        // $documentDetails->with('kpiComments.employee');
        $documentDetails->with(['kpiComments' => function ($q) use ($quarter) {
            $q->with('employee');
            $q->with('files');
            $q->where('kpi_object_id', 0);
            $q->where('quarter', $quarter);
        }]);
        // $documentDetails->with('kpiComments.files');
        $documentDetails->where('document_id', $document->id);
        $document_details = $documentDetails->get();
        return [
            'document_details' => $document_details,
            'document' => $document
        ];
    }
    public function getAttributesFaktsNew(Request $request)
    {
        $kpi_user = false;
        $selected_doc_id = isset($request['filter']['doc_id']) ? $request['filter']['doc_id'] : null;
        $quarter = $request['filter']['quarter'];
        // return
        $year = $request['filter']['year'];

        // if (Auth::id() == 1) {
        //     return $this->getDepID(Auth::user()->employee->staff[0]->department_id, $year, $quarter);
        // }
        $user = Auth::user();
        $d = Auth::user()->employee->mainStaff[0]->department_id;
        $dep_id = isset($request['filter']['department_id']) ? $request['filter']['department_id'] : '';

        $dep_ids = [];
        // if($user->id == 34){
        //     return $request;
        // }

        $quarter_real = 1;
        $kpiSettingDate = kpiSettingDate::get();
        $date = date('m-d H:i:s');
        foreach ($kpiSettingDate as $key => $value) {
            if ($value->from_kpi_facts < $date && $date < $value->to_comissions) {
                $quarter_real = $value->quarter;
            }
        }

        if (!$quarter) {
            $quarter = $quarter_real;
        }
        // return [$quarter, $quarter_real];
        if (Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager')) {
            if (!$dep_id) {
                return 'department topilmadi!';
            }
            // return
            $dep_id = Department::find($dep_id);

            if ($dep_id) $dep_ids = [$dep_id->id];
        } else {
        }
        $doc_id = Document::select('id')
            ->whereIn('status', [3, 4, 5])
            ->where('document_template_id', 431)
            ->orderBy('id', 'desc');

        if (!Auth::user()->hasPermission('kpi-comission') && !Auth::user()->hasPermission('kpi-manager')) {

            $doc_id->whereHas('documentSigners', function ($q) use ($user) {
                $q->where('signer_employee_id', $user->employee_id)
                    ->where('action_type_id', 6);
            });
            $document_validate = clone $doc_id;
            $document_validate = $document_validate->get();
            $kpi_object = null;
            if (count($document_validate) > 0) {
                // return $document_validate->pluck('id');
                $kpi_object = KpiObject::where('years', $year)
                    ->where('quarter', $quarter)
                    ->whereIn('doc_id', $document_validate->pluck('id'))
                    ->first();
            }


            if (count($document_validate) < 1 && !$kpi_object) {

                $kpiobjektUser = KpiobjektUser::where('user_id', $user->id)
                    ->whereHas('kpiobjekt', function ($q) use ($year, $quarter) {
                        $q->where('years', $year);
                        $q->where('quarter', $quarter);
                    })
                    ->with('kpiobjekt')
                    ->first();

                if ($kpiobjektUser) {
                    $doc_id = Document::select('id')
                        ->whereIn('status', [3, 4, 5])
                        ->where('document_template_id', 431)
                        ->where('department2_id', $kpiobjektUser->kpiobjekt->dep_id)
                        ->orderBy('id', 'desc');
                    $doc_id_user = clone $doc_id;
                    $doc_id_user->first();
                    $kpi_user = true;
                } else {
                    // $dep_id =  $this->getDepID(Auth::user()->employee->staff[0]->department_id, $year, $quarter);
                    // if ($dep_id) {
                    //     $doc_id = Document::select('id')
                    //         ->whereIn('status', [3, 4, 5])
                    //         ->where('document_template_id', 431)
                    //         ->orderBy('id', 'desc');

                    //     $doc_id->whereIn('department2_id', [$dep_id]);
                    // } else {

                    //     return 'ushbu foydalanuvchida ruxsat yo`q';
                    // }
                    return 'ushbu foydalanuvchida ruxsat yo`q';
                }
            }
        } else {
            $doc_id->whereIn('department2_id', $dep_ids);
        }
        $docid = clone $doc_id;
        // return $doc_id->get();
        // return $year;

        $doc_id->whereHas('documentDetails', function ($q) use ($year) {
            $q->whereHas('documentDetailContents', function ($s) use ($year) {
                $s
                    ->where('value', 'ilike', "%" . $year . "%")
                    ->where('d_d_attribute_id', 1936);
            });
        });
        // $doc_id->whereHas('documentDetails', function ($q) use ($year) {
        //     $q->whereHas('documentDetailContents', function ($s) use ($year) {
        //         $s->where('value',  $year)
        //         ->where('d_d_attribute_id', 1936);
        //     });
        // });
        // return $doc_id->get();


        $doc = Document::with('employee')->whereIn('id', $doc_id)->orderBy('id', 'desc')->get();
        if ($selected_doc_id) {
            // return $selected_doc_id;
            $doc_id = Document::where('id', $selected_doc_id);

            $doc_id->whereHas('documentDetails', function ($q) use ($year) {
                $q->whereHas('documentDetailContents', function ($s) use ($year) {
                    $s
                        ->where('value', 'ilike', "%" . $year . "%")
                        // ->where('value',  $year)
                        ->where('d_d_attribute_id', 1936);
                });
            });
            // return $doc_id->get();
        }

        $doc_count = count($docid->get()->pluck('id'));
        $doc_id = $doc_id->get()->pluck('id');

        if ($doc_count > 1) {
            // return $doc_id;
            if ($quarter_real > $quarter) {
                $doc_id = $doc_id;
            } else {
                // return $doc_id;
                $doc_id = [$doc_id[0]];
                $doc = $doc_id;
                $doc = Document::with('employee')->whereIn('id', $doc_id)->orderBy('id', 'desc')->get();
            }
        } else {
            $doc = $doc_id;
            $doc = Document::with('employee')->whereIn('id', $doc_id)->orderBy('id', 'desc')->get();
        }

        if ($year == 2022 && count($doc_id) == 0) {
            $doc_id = Document::select('id')
                // ->whereIn('department2_id', $dep_ids)
                ->whereIn('status', [3, 4, 5])
                ->where('document_template_id', 431)
                ->whereHas('documentDetails', function ($q) {
                    $q->whereDoesntHave('documentDetailContents', function ($s) {
                        $s->where('d_d_attribute_id', 1936);
                    });
                });
            if (!Auth::user()->hasPermission('kpi-comission') && !Auth::user()->hasPermission('kpi-manager')) {
                $doc_id->whereHas('documentSigners', function ($q) use ($user) {
                    $q->where('signer_employee_id', $user->employee_id)
                        ->where('action_type_id', 6);
                });
            } else {
                $doc_id->whereIn('department2_id', $dep_ids);
            }
            $doc_id = $doc_id->pluck('id')->toArray();
        }

        $comission = Auth::user()->hasPermission('kpi-comission') || Auth::user()->hasPermission('kpi-manager');
        $documentDetails = DocumentDetail::query();
        $documentDetails->with('documentDetailAttributeValues.documentDetailAttributes.tableList');
        $documentDetails->with('documentDetailContents');
        $documentDetails->with('document');
        $documentDetails->with('kpiComments.employee');
        $documentDetails->with('kpiComments.files');
        $documentDetails->with('kpiComments.kpiObjekt');
        $documentDetails->with(['DocumentDetailFakt' => function ($q) use ($quarter, $comission) {
            $q->with('comissions.employee:id,firstname_ru,firstname_uz_cyril,firstname_uz_latin,lastname_ru,lastname_uz_cyril,
            lastname_uz_latin,middlename_ru,middlename_uz_cyril,middlename_uz_latin,tabel');
            $q->with(['comissions' => function ($q) {
                $q->with(['employee' => function ($q) {
                    $q->select(
                        'id',
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
                    );
                }]);
                $q->orderBy('id');
            }]);
            $q->where('quarter', $quarter);
            if ($comission) {
                $q->whereNotNull('fakt');
            }
        }]);
        $documentDetails->whereIn('document_id', $doc_id);
        // $documentDetails->whereDoesntHave('kpiComments', function($q){
        //     $q->whereNotNull('deleted_at');    
        // });
        $document_details = $documentDetails->get();

        $change_data = false;
        $change_reaction = false;
        $fulldate = date('Y-m-d H:i:s');
        $y = date('Y');

        $change_data_validate = KpiobjektUser::where('user_id', $user->id)
            ->whereHas('kpiobjekt', function ($q) use ($year, $quarter) {
                $q->where('years', $year);
                $q->where('quarter', $quarter);
            })
            ->with('kpiobjekt')
            ->first();
        if ($change_data_validate && $change_data_validate->for_facts == 1) {
            $change_data = true;
        }
        foreach ($kpiSettingDate as $key => $value) {
            if ($quarter == $value->quarter && $date > $value->from_kpi_facts && $date < $value->to_kpi_facts) {
                $change_data = true;
            }
        }
        // return  $dep_id;
        // $change_reaction_validate = KpiobjektUser::whereHas('kpiobjekt', function ($q) use ($year, $dep_id, $quarter) {
        //     $q->where('years', $year);
        //     $q->where('quarter', $quarter);
        //     $q->where('dep_id', $dep_id->id);
        // })
        //     ->with('kpiobjekt')
        //     ->first();
        $change_reaction_validate = false;

        if ($change_reaction_validate && $change_reaction_validate->for_comission == 1) {
            $change_reaction = true;
        }
        foreach ($kpiSettingDate as $key => $value) {
            if ($quarter == $value->quarter && $date > $value->from_comissions && $date < $value->to_comissions) {
                $change_reaction = true;
            }
        }
        // if(!$change_data){

        // }
        // if (
        //     ($quarter == 1 && $fulldate > $year . '-04-01 00:00:00' && $fulldate < $year . '-04-20 00:00:00') ||
        //     ($quarter == 2 && $fulldate > $year . '-07-01 00:00:00' && $fulldate < $year . '-12-20 00:00:00') ||
        //     ($quarter == 3 && $fulldate > $year . '-09-01 00:00:00' && $fulldate < $year . '-12-20 00:00:00') ||
        //     ($quarter == 4 && $fulldate > ($year + 1) . '-01-01 00:00:00' && $fulldate < ($year + 1) . '-01-20 10:30:00')
        //     // || $fakt
        // ) {
        //     $change_data = true;
        // }
        // if (
        //     ($quarter == 1 && $fulldate > $year . '-04-11 00:00:00' && $fulldate < $year . '-04-30 00:00:00') ||
        //     ($quarter == 2 && $fulldate > $year . '-07-11 00:00:00' && $fulldate < $year . '-07-20 00:00:00') ||
        //     ($quarter == 3 && $fulldate > $year . '-10-15 00:00:00' && $fulldate < $year . '-10-25 00:00:00') ||
        //     ($quarter == 4 && $fulldate > ($year + 1) . '-01-11 00:00:00' && $fulldate < ($year + 1) . '-02-10 00:00:00')
        // ) {
        //     $change_reaction = true;
        // }

        return [
            'document_details' => $document_details,
            'change_data' => $change_data,
            'change_reaction' => $change_reaction,
            'quarter' => $quarter,
            'doc_count' => $doc_count,
            'doc' => $doc,
            'kpi_user' => $kpi_user
        ];
    }

    public function saveFactsNew(Request $request)
    {
        // kpi_fakt
        $document_details = $request['document_details'];
        $doc = Document::find($document_details[0]['document_id']);
        foreach ($document_details as $key => $value) {
            $ddf = DocumentDetailFakt::find($value['document_detail_fakt']['id']);
            if (!$ddf) {
                $ddf = new DocumentDetailFakt();
            }
            $is_changed = false;
            if (
                $ddf->fakt != $value['document_detail_fakt']['fakt'] ||
                $ddf->achieving_goal != $value['document_detail_fakt']['achieving_goal'] ||
                $ddf->reward_amount != $value['document_detail_fakt']['reward_amount']
            ) {
                if (Auth::id() != 6) {
                    foreach ($ddf->comissions as $comission) {
                        $comission->status = null;
                        $comission->save();
                    }
                }
                $is_changed = true;
            }
            $ddf->d_d_id = $value['document_detail_fakt']['d_d_id'];
            $ddf->fakt = $value['document_detail_fakt']['fakt'];
            $ddf->achieving_goal = $value['document_detail_fakt']['achieving_goal'];
            $ddf->reward_amount = $value['document_detail_fakt']['reward_amount'];
            $ddf->year = $value['document_detail_fakt']['year'];
            $ddf->quarter = $value['document_detail_fakt']['quarter'];
            if ($is_changed && Auth::id() != 6) {
                $ddf->status = null;
            }
            $ddf->status = 2;
            $ddf->save();
        }

        // kpi_objekt
        $user_dep = Auth::user()->employee->mainStaff[0]->department->department_code;
        $dep_id = Auth::user()->employee->mainStaff[0]->department->id;
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];

        // $dep_ids = Department::where('department_code', 'like', substr($user_dep, 0, 5) . '%')->pluck('id')->toArray();

        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            // ->where('department_id', $dep_id)
            ->where('dep_id', $doc->department2_id)
            // ->whereIn('department_id', $dep_ids)
            ->first();

        if (!$kpi_object) {
            $kpi_object = new KpiObject();
            $kpi_object->department_id = $dep_id;
            $kpi_object->quarter = $quarter;
            $kpi_object->years = $years;
            $kpi_object->dep2_id = $doc->department2_id;
            $kpi_object->dep_id = $doc->department2_id;
            $kpi_object->doc_id = $doc->id;
            $kpi_object->save();

            $kpiObjectuser = new KpiobjektUser();
            $kpiObjectuser->user_id = Auth::user()->id;
            $kpiObjectuser->kpi_objects_id = $kpi_object->id;
            $kpiObjectuser->save();
        }

        $object_id = $kpi_object->id;

        return $object_id;
        // return 'success!';
    }


    public function kpiCommentsPlan(Request $request)
    {
        // return $request;

        if ($request['comment'] == '') {
            return 0;
        }

        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $d_d_id = $request['filter']['d_d_id'];
        // $d_d_a_v_id = $request['filter']['d_d_a_v_id'];
        $department_id = isset($request['filter']['department_id']) ?  $request['filter']['department_id'] : '';


        // bo'lmay qolsa b rejaga o'tamiz
        $documentDetail = DocumentDetail::find($d_d_id);
        $document = Document::find($documentDetail->document_id);
        // bo'lmay qolsa b rejaga o'tamiz


        $documentDetail = DocumentDetail::find($d_d_id);
        $document = Document::find($documentDetail->document_id);
        $department_id = $document->department2_id;

        // $kpi_object = KpiObject::where('years', $years)
        //     ->where('quarter', $quarter)
        //     ->where('dep_id', $department_id)
        //     ->first();

        $comment = new KpiComment();
        $comment->kpi_object_id = 0;
        $comment->d_d_id = $d_d_id;
        $comment->quarter = $quarter;
        $comment->comment = $request['comment'];
        $comment->employee_id = Auth::user()->employee->id;
        $comment->save();

        return $comment->id;
        return 'succes';
    }
    public function kpiCommentsNew(Request $request)
    {

        if ($request['comment'] == '') {
            return 0;
        }

        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];
        $d_d_id = $request['filter']['d_d_id'];
        $department_id = isset($request['filter']['department_id']) ?  $request['filter']['department_id'] : '';


        // bo'lmay qolsa b rejaga o'tamiz
        $documentDetail = DocumentDetail::find($d_d_id);
        $document = Document::find($documentDetail->document_id);
        // bo'lmay qolsa b rejaga o'tamiz


        $documentDetail = DocumentDetail::find($d_d_id);
        $document = Document::find($documentDetail->document_id);
        $department_id = $document->department2_id;

        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            ->where('dep_id', $department_id)
            ->first();

        $comment = new KpiComment();
        $comment->kpi_object_id = $kpi_object->id;
        $comment->d_d_id = $d_d_id;
        $comment->comment = $request['comment'];
        $comment->employee_id = Auth::user()->employee->id;
        $comment->save();

        return $comment->id;
        return 'succes';
    }
    public function kpiobjektuser(Request $request)
    {
        // $request;
        $department = isset($request['filter']) && isset($request['filter']['department']) ? $request['filter']['department'] : '';
        $select = isset($request['filter']) && isset($request['filter']['select']) ? $request['filter']['select'] : false;
        $employee = isset($request['filter']) && isset($request['filter']['employees']) ? $request['filter']['employees'] : '';
        $quarter = isset($request['filter']) && isset($request['filter']['quarter']) ? $request['filter']['quarter'] : '';
        $years = isset($request['filter']) && isset($request['filter']['years']) ? $request['filter']['years'] : '';
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $kpiobjektuser = KpiobjektUser::with(['user' => function ($q) {
            $q->select('id', 'employee_id')
                ->with(['employee' => function ($q) {
                    $q->select(
                        'id',
                        'firstname_ru',
                        'firstname_uz_cyril',
                        'firstname_uz_latin',
                        'lastname_ru',
                        'lastname_uz_cyril',
                        'lastname_uz_latin',
                        'middlename_ru',
                        'middlename_uz_cyril',
                        'middlename_uz_latin'
                    );
                }]);
        }])
            ->with(['kpiobjekt' => function ($q) {
                $q->with('dep');
                $q->with('department');
            }])
            // ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)
            // ->get()
        ;
        if ($employee) {
            $kpiobjektuser->whereHas('user', function ($q) use ($employee) {
                $q->where('employee_id', $employee);
            });
        }
        if ($select) {
            $kpiobjektuser->where('for_facts', 1);
        }
        if ($quarter) {
            $kpiobjektuser->whereHas('kpiobjekt', function ($q) use ($quarter) {
                $q->where('quarter', $quarter);
            });
        }
        if ($years) {
            $kpiobjektuser->whereHas('kpiobjekt', function ($q) use ($years) {
                $q->where('years', $years);
            });
        }
        if ($department) {
            $kpiobjektuser->whereHas('kpiobjekt', function ($q) use ($department) {
                $q->where('dep_id', $department);
            });
        }
        return $kpiobjektuser->get();
    }

    public function kpiobjektuserdestroy($id)
    {
        $model = KpiobjektUser::find($id);
        $model->delete();
    }
    public function kpiobjektuserupdate(Request $request)
    {
        // return 
        $request;
        $years =  $request['year'];
        $quarter =  $request['quarter'];
        $department_id =  $request['department_id'];
        $employee_id =  $request['resolution_comission_id'];

        // return 

        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            ->where('dep_id', $department_id)
            // ->whereIn('department_id', $dep_ids2)
            ->first();
        $user = User::where('employee_id', $employee_id)->first();
        // return
        $kpiobjektUser = KpiobjektUser::whereHas('kpiobjekt', function ($q) use ($years, $quarter) {
            $q->where('years', $years)
                ->where('quarter', $quarter);
        })
            ->where('user_id', $user->id)
            ->first();
        if (($kpiobjektUser)) {
            return ['messatge' => 'Bu hodimga kpi biriktirilgan', 'status' => 400];
        }

        $kpiobjektUser = new KpiobjektUser();
        $kpiobjektUser->kpi_objects_id = $kpi_object->id;
        $kpiobjektUser->user_id = $user->id;
        $kpiobjektUser->save();
        return ['message' => 'save successfull', 'status' => 200, $kpiobjektUser->id];
    }
    public function kpisettingdate(Request $request)
    {
        $kpisettingdate = kpiSettingDate::get();
        return $kpisettingdate;
    }
    public function kpiallstatusupdate(Request $request)
    {
        // return
        $items = $request['items'];
        $type = $request['i'];
        $status = $request['status'];
        foreach ($items as $item) {
            $KpiobjektUser = KpiobjektUser::find($item['id']);
            $KpiobjektUser->$type = ($status) ? 1 : 0;
            $KpiobjektUser->save();
        }
        return $KpiobjektUser;
    }
    public function kpistatusupdate(Request $request)
    {
        // return 
        // $request;
        $type = $request['i'];
        $id = $request['id'];
        $status = $request['status'];
        $KpiobjektUser = KpiobjektUser::find($id);
        $KpiobjektUser->$type = ($status) ? 1 : 0;
        $KpiobjektUser->save();
        return $KpiobjektUser;
    }
    public function kpisettingdateupdate(Request $request)
    {
        // return  
        // $request;
        $kpisettingdate = kpiSettingDate::find($request['id']);
        $kpisettingdate->from_kpi_facts = $request['from_kpi_facts'];
        $kpisettingdate->to_kpi_facts = $request['to_kpi_facts'];
        $kpisettingdate->from_comissions = $request['from_comissions'];
        $kpisettingdate->to_comissions = $request['to_comissions'];
        $kpisettingdate->save();
        return 'save succesfull';
    }
    public function kpiDocumetnId()
    {

        $kpi_object = KpiObject::get();

        foreach ($kpi_object as $key => $value) {
            // return $value;
            $ddf = DocumentDetailFakt::where('year', $value->years)
                ->where('quarter', $value->quarter)
                ->whereHas('documentDetail', function ($q) use ($value) {
                    $q->whereHas('document', function ($q) use ($value) {
                        $q->where('department2_id', $value->dep_id);
                    });
                })->first();
            if ($ddf) {
                // return $ddf;
                $value->doc_id = $ddf->documentDetail->document_id;
                $value->save();
            }
        }
    }

    public function testKpiSardor()
    {
        // return
        $document = Document::where('document_template_id', 431)
            ->whereIn('status', [3, 4, 5])
            ->with('documentDetails')
            ->get();
        try {
            //code...
            foreach ($document as $key => $value) {
                $employee = Employee::find($value->created_employee_id);
                $user = User::select('id')->where('employee_id', $value->created_employee_id)->first();
                for ($x = 1; $x <= 4; $x++) {
                    $kpiObjekt = KpiObject::where('quarter', $x)
                        // ->where('years', date('Y', strtotime($value->document_date)))
                        ->where('dep_id', $value->department2_id)
                        ->where('doc_id', $value->id)
                        ->first();
                    if ((($kpiObjekt))) {
                        $kpiobjekt_user = KpiobjektUser::where('user_id', $user->id)->where('kpi_objects_id', $kpiObjekt->id)->first();
                        if (!($kpiobjekt_user)) {
                            $kpiobjekt_user = new KpiobjektUser();
                            $kpiobjekt_user->user_id = $user->id;
                            $kpiobjekt_user->kpi_objects_id = $kpiObjekt->id;
                            $kpiobjekt_user->save();
                        }
                    }
                }
            }
            return 200;
        } catch (\Throwable $th) {
            return
                $value->id;
            // $employee->mainStaff[0];
            //throw $th;
        }
        // return KpiObject::kpiCreatedObjekt(2427824);
    }
    public function quarter()
    {
        $kpiSettingDate = kpiSettingDate::get();
        $date = date('Y-m-d');
        $y = date('Y');
        $quarter_real = null;
        $year_real = null;
        // return $date < ($y).'-'.$kpiSettingDate[0]['from_kpi_facts'];
        // return $y.'-'.$kpiSettingDate[3]['from_kpi_facts'] < $date;
        if ($y . '-' . $kpiSettingDate[0]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[1]['from_kpi_facts']) {
            // return 1;
            $quarter_real = $kpiSettingDate[0]->quarter;
            $year_real = $y;
        } else if ($y . '-' . $kpiSettingDate[1]['from_kpi_facts'] < $date && $date < $y . '-' . $kpiSettingDate[2]['from_kpi_facts']) {
            // return 2;
            $quarter_real = $kpiSettingDate[1]->quarter;
            $year_real = $y;
        } else if ($y . '-' . $kpiSettingDate[2]['from_kpi_facts'] < $date && $date < ($y + 1) . '-' . $kpiSettingDate[3]['from_kpi_facts']) {
            // return 3;
            $quarter_real = $kpiSettingDate[2]->quarter;
            $year_real = $y;
        } else if (($y - 1) . '-' . $kpiSettingDate[3]['from_kpi_facts'] < $date && $date < ($y) . '-' . $kpiSettingDate[0]['from_kpi_facts']) {
            // return 4;
            $quarter_real = $kpiSettingDate[3]->quarter;
            $year_real = $y - 1;
        }

        return [$quarter_real, $year_real];
    }
    public function krill()
    {
        return
            // 'ds';
            $krillRecords = Employee::where('firstname_uz_latin', 'REGEXP', '^[ҚқҒғӘәҰұҮүІіӨөҢңШшЧчҚқҒғӘәҰұҮүІіӨөҢңҮү]+$')
            // ->orWhere('lastname_uz_latin', 'REGEXP', '^[ҚқҒғӘәҰұҮүІіӨөҢңШшЧчҚқҒғӘәҰұҮүІіӨөҢңҮү]+$')
            // ->orWhere('middlename_uz_latin', 'REGEXP', '^[ҚқҒғӘәҰұҮүІіӨөҢңШшЧчҚқҒғӘәҰұҮүІіӨөҢңҮү]+$')
            ->get();
    }
    public function validateKpiDocument(Request $request)
    {
        $id = $request['id'];

        $document = Document::find($id);

        $document_details = DocumentDetail::where('document_id', $id)->get();
        $change = false;

        foreach ($document_details as $key => $value) {

            for ($x = 1; $x <= 4; $x++) {
                $okey = 0;
                $notokey = 0;
                $kpi_plan_comission = KpiPlanComission::where('quarter', $x)->where('d_d_id', $value->id)->get();
                foreach ($kpi_plan_comission as $k => $val) {
                    if ($val->status == true) {
                        $okey++;
                    }
                    if ($val->status === false) {
                        $notokey++;
                    }
                }
                if ($notokey > 5) {
                    $change = true;
                }
                if ($change) {
                    return ['change' => $change];
                }

                if (($notokey + $okey) == 11) {
                    $full_change = true;
                } else {
                    $full_change = false;
                }
                // if ($full_change == false) {
                //     return ['full_change' => $full_change];
                // }
                return ['full_change' => $full_change];
            }
        }
    }
    public function sendKpiComission(Request $request)
    {

        $id = $request['id'];

        $document = Document::find($id);
        $document->status = 7;
        $document->save();
        return ['message' => 'status 8 ga o`zgartirildi', 'status' => 200];
    }
}
