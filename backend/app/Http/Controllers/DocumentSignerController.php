<?php

namespace App\Http\Controllers;

use App\AS400\Z08ptpf;
use App\AS400\Z50ptpf;
use App\AS400\Z54ptpf;
use App\Http\Models\ActionType;
use App\Http\Models\Department;
use App\Http\Models\DocumentSigner;
use App\Http\Models\SignerGroup;
use App\Http\Models\EmployeeCoefficient;
use App\Http\Models\Staff;
use App\Http\Models\KpiObject;
use App\Http\Models\File;
use App\Http\Models\TariffScale;
use App\Http\Models\DocumentSignerTemplate;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\Document;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\DocumentType;
use App\Http\Models\Employee;
use App\Http\Models\DocumentRelation;
use Illuminate\Http\Request;
use App\User;
use App\MailQueue;
use App\Services\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Signer;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class DocumentSignerController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function show(DocumentSigner $documentSigner)
    {
        //
    }

    public function edit(DocumentSigner $documentSigner)
    {
        //
    }

    public function update(Request $request)
    {
        if ($id = $request->input('id')) {
            $model = DocumentSigner::find($id);
        } else {
            $model = new DocumentSigner();
        }

        if ($model->status != 1 && $request->input('status') == 1) {
            return 'Ushbu hujjatni bu yerda tasdiqlay olmaysiz.';
        }
        if ($model) {
            $model->action_type_id = $request->input('action_type_id');
            $model->parent_employee_id = $request->input('parent_employee_id');
            $model->signer_employee_id = $request->input('signer_employee_id');
            $model->sequence = $request->input('sequence');
            $model->document_id = $request->input('document_id');
            $model->sequence = $request->input('sequence');
            $model->status = $request->input('status');
            $model->staff_id = $request->input('staff_id');
            $model->due_date = $request->input('due_date');
            $model->is_done = $request->input('is_done');
            $model->taken_datetime = $request->input('taken_datetime');
            $model->save();
        }
        $document = Document::find($request->input('document_id'));
        if ($document && Auth::user()->username == 'qg9592') {
            $document->status = $request->input('document_status');
            $document->save();
        }
        return 200;
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('edit_signers')) {
            return "O'chirishga sizda ruhsat yo'q.";
        }
        $document_signer = DocumentSigner::find($id);
        $document_signer->delete();
        return "SuccessFully deleted!!!";
    }

    public function deleteDocumentSigner($id)
    {
        // if(!Auth::user()->hasPermission('edit_signers')) {
        //     return "O'chirishga sizda ruhsat yo'q.";
        // }
        $signer = DocumentSigner::find($id);
        $parent = DocumentSigner::where('document_id', $signer->document_id)
            ->where('signer_employee_id', $signer->parent_employee_id)
            ->where('is_done', 2)
            ->first();
        $neighbor = DocumentSigner::where('document_id', $signer->document_id)
            ->where('parent_employee_id', $signer->parent_employee_id)
            ->where('id', '<>', $signer->id)
            ->first();
        if (!$neighbor) {
            $parent->is_done = 0;
            $parent->status = 0;
            $parent->save();
        }
        DocumentSignerEvent::where('document_signer_id', $signer->id)->delete();
        $signer->delete();
    }

    public function notification(Request $request)
    {
        $doc_id = $request->input('document_id');
        $signerNotification = DocumentSigner::all()->where('signer_user_id', '=', 'null')->where('document_id', $doc_id);
        return $signerNotification;
    }

    public function addDocumentSigners(Request $request)
    {
        $employee_ids = $request->input('employees');
        $punkts = $request->input('punkts') ? $request->input('punkts') : [];
        $documentId = $request->input('document_id');
        $assignment = $request->input('assignment');
        $dueDate = ($request->input('due_date')) ? $request->input('due_date') : date('Y-m-d', strtotime($request->input('due_date') . ' +1 day'));
        $sequence = $request->input('sequence');
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

        if ($employee_ids && count($employee_ids) > 0) {
            foreach ($employee_ids as $key => $employee_id) {
                # code...
                DB::beginTransaction();
                try {

                    $addDoc = DocumentSigner::whereIn('staff_id', $userStaffIds)
                        ->where('document_id', $documentId)
                        ->whereNotNull('taken_datetime')
                        ->where(function ($q) {
                            $q->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where(function ($q) {
                            $q->whereIn('status', [0, 3, 4])
                                ->orWhere('action_type_id', 5);
                        })
                        ->first();
                    if (!$addDoc) {
                        // if(Auth::user()->){
                        //     $documentSigner = new DocumentSigner();
                        //     $documentSigner->staff_id = Auth::user()->employee->mainStaff[0]->id;
                        //     $documentSigner->taken_datetime = date('Y-m-d H:i:s');
                        //     $documentSigner->due_date = date('Y-m-d H:i:s');
                        //     $documentSigner->document_id = $document->id;
                        //     $documentSigner->action_type_id = 11;
                        //     $documentSigner->sequence = 0;
                        //     $documentSigner->status = 0;
                        //     $documentSigner->signer_group_id = 0;
                        //     $documentSigner->is_registry = 0;
                        //     $documentSigner->fio = Auth::user()->employee->getShortName($document->locale);
                        //     $documentSigner->save();
                        // }
                        return 0;
                    }
                    if ($addDoc->status != 3) {
                        $documentSignerEvent = new DocumentSignerEvent;
                        $documentSignerEvent->document_signer_id = $addDoc->id;
                        $documentSignerEvent->action_type_id = $addDoc->action_type_id;
                        $documentSignerEvent->comment = $assignment;
                        $documentSignerEvent->status = 3;
                        $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
                        $documentSignerEvent->fio = $addDoc->fio;
                        $documentSignerEvent->save();
                    }
                    $addDoc->signer_employee_id = Auth::user()->employee_id;
                    $employee = Employee::find(Auth::user()->employee_id);
                    $document = Document::find($documentId);
                    $count = $document->locale == 'uz_latin' ? 1 : 2;
                    $addDoc->fio = $employee->getShortname($document->locale);
                    $addDoc->status = $addDoc->action_type_id != 5 ? 3 : $addDoc->status;
                    $count = DocumentSigner::where('document_id', $documentId)
                        ->where('parent_employee_id', Auth::user()->employee_id)
                        ->whereIn('status', [0, 3])->count();
                    if ($count == 0 && $request->input('action_type') == 5) {
                        $addDoc->is_done = 1;
                    } else {
                        $addDoc->is_done = 2;
                    }
                    $addDoc->save();
                    $signerStaffId = EmployeeStaff::where('employee_id', $employee_id)->where('is_active', 1)->first();
                    $empty = DocumentSigner::whereIn('staff_id', $userStaffIds)
                        ->whereNotNull('parent_employee_id')
                        ->where('signer_employee_id', $employee_id)
                        ->where('document_id', $documentId)
                        ->where('status', '<>', 1)
                        ->first();
                    if (!$empty) {
                        $documentSigner = new DocumentSigner;
                        $documentSigner->document_id = $documentId;
                        $documentSigner->staff_id = $signerStaffId->staff_id;
                        $documentSigner->taken_datetime = date("Y-m-d H:i:s", time());
                        $documentSigner->parent_employee_id = Auth::user()->employee_id;
                        $documentSigner->action_type_id = $request->input('action_type');
                        // $documentSigner->action_type_id = $request->input('action_type') ? ($addDoc->action_type_id == 12 ? 12 : $request->input('action_type')) : 3;
                        $documentSigner->assignment = $assignment;
                        // $documentSigner->due_date = date("Y-m-d H:i:s", time() + 3600 * $dueDate);
                        $documentSigner->due_date = date("Y-m-d 18:00:00", strtotime($dueDate));
                        $documentSigner->sequence = $sequence;
                        $documentSigner->signer_employee_id = $employee_id;
                        $employee = Employee::find($employee_id);
                        $document = Document::find($documentId);
                        $count = $document->locale == 'uz_latin' ? 1 : 2;
                        $documentSigner->department = $employee->staff[0]->department['name_' . $document->locale];
                        $documentSigner->position = $employee->staff[0]->position['name_' . $document->locale];
                        $documentSigner->fio = $employee->getShortname($document->locale);
                        if ($request->input('action_type') == 5) {
                            $documentSigner->status = 1;
                        }
                        $documentSigner->sign_type = 1;
                        $documentSigner->save();
                    } else {
                        return 'This employee already has signer list!!!';
                    }
                    $documentSignerEvent = new DocumentSignerEvent;
                    $documentSignerEvent->document_signer_id = $addDoc->id;
                    $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                    $documentSignerEvent->status = 6;
                    $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
                    $documentSignerEvent->fio = $addDoc->fio;
                    if($documentSigner->action_type_id == 4){
                        $documentSignerEvent->comment = Employee::find($employee_id)->getShortname('uz_cyril') . ': -' . $assignment;
                    }
                    else if($documentSigner->action_type_id == 11){
                        $documentSignerEvent->comment = Employee::find($employee_id)->getShortname('uz_cyril') . 'га назорат учун юборди';
                    }
                    else{
                        $documentSignerEvent->comment = Employee::find($employee_id)->getShortname('uz_cyril') . 'га маълумот учун юборди';
                    }
                    // $documentSignerEvent->comment = $documentSigner->action_type_id == 4 ? Employee::find($employee_id)->getShortname('uz_cyril') . ': -' . $assignment : Employee::find($employee_id)->getShortname('uz_cyril') . 'га маълумот учун юборди';
                    $documentSignerEvent->save();
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return $th;
                }
            }
            return "Successfully saved!";
        } else {
            DB::beginTransaction();
            try {
                $employee_id = $request->input('employee');

                // $punkts = $request->input('punkts') ? $request->input('punkts') : [];
                // $documentId = $request->input('document_id');
                // $assignment = $request->input('assignment');
                // $dueDate = $request->input('due_date');
                // $sequence = $request->input('sequence');
                // $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

                $addDoc = DocumentSigner::whereIn('staff_id', $userStaffIds)
                    ->where('document_id', $documentId)
                    ->where(function ($q) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereIn('status', [0, 3, 4])
                            ->orWhere('action_type_id', 5);
                    })
                    ->first();
                if (!$addDoc) {
                    return 0;
                }
                if ($addDoc->status != 3) {
                    $documentSignerEvent = new DocumentSignerEvent;
                    $documentSignerEvent->document_signer_id = $addDoc->id;
                    $documentSignerEvent->action_type_id = $addDoc->action_type_id;
                    $documentSignerEvent->comment = $assignment;
                    $documentSignerEvent->status = 3;
                    $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
                    $documentSignerEvent->fio = $addDoc->fio;
                    $documentSignerEvent->save();
                }
                $addDoc->signer_employee_id = Auth::user()->employee_id;
                $employee = Employee::find(Auth::user()->employee_id);
                $document = Document::find($documentId);
                $count = $document->locale == 'uz_latin' ? 1 : 2;
                $addDoc->fio = $employee->getShortname($document->locale);
                $addDoc->status = $addDoc->action_type_id != 5 ? 3 : $addDoc->status;
                $count = DocumentSigner::where('document_id', $documentId)
                    ->where('parent_employee_id', Auth::user()->employee_id)
                    ->whereIn('status', [0, 3])->count();
                if ($count == 0 && $request->input('action_type') == 5) {
                    $addDoc->is_done = 1;
                } else {
                    $addDoc->is_done = 2;
                }
                $addDoc->save();


                $signerStaffId = EmployeeStaff::where('employee_id', $employee_id)->where('is_active', 1)->first();
                $empty = DocumentSigner::whereIn('staff_id', $userStaffIds)
                    ->whereNotNull('parent_employee_id')
                    ->where('signer_employee_id', $employee_id)
                    ->where('document_id', $documentId)
                    // ->where(function ($query) use ($punkts) {
                    //     foreach ($punkts as $key => $value) {
                    //         $query->orWhere('document_detail_id', $value);
                    //     }
                    // })
                    ->first();
                if (!$empty) {
                    // if (count($punkts)) {
                    //     foreach ($punkts as $key_punkts => $value_punkts) {
                    //         $documentSigner = new DocumentSigner;
                    //         $documentSigner->document_id = $documentId;
                    //         $documentSigner->document_detail_id = $value_punkts;
                    //         $documentSigner->staff_id = $signerStaffId->staff_id;
                    //         $documentSigner->taken_datetime = date("Y-m-d H:i:s", time() + 86400);
                    //         $documentSigner->parent_employee_id = $parentEmployeeId->employee_id;
                    //         $documentSigner->action_type_id = $request->input('action_type') ? $request->input('action_type') : 3;
                    //         $documentSigner->assignment = $assignment;
                    //         $documentSigner->due_date = date("Y-m-d H:i:s", time() + 3600 * $dueDate);
                    //         $documentSigner->sequence = $sequence;
                    //         $documentSigner->signer_employee_id = $value;
                    //         if ($request->input('action_type') == 5) {
                    //             $documentSigner->status = 1;
                    //         }
                    //         $documentSigner->save();
                    //     }
                    // } else {
                    $documentSigner = new DocumentSigner;
                    $documentSigner->document_id = $documentId;
                    $documentSigner->staff_id = $signerStaffId->staff_id;
                    $documentSigner->taken_datetime = date("Y-m-d H:i:s", time());
                    $documentSigner->parent_employee_id = Auth::user()->employee_id;
                    $documentSigner->action_type_id = $request->input('action_type') ? ($addDoc->action_type_id == 12 ? 12 : $request->input('action_type')) : 3;
                    $documentSigner->assignment = $assignment;
                    // $documentSigner->due_date = date("Y-m-d H:i:s", time() + 3600 * $dueDate);
                    $documentSigner->due_date = date("Y-m-d 18:00:00", strtotime($dueDate));
                    $documentSigner->sequence = $sequence;
                    $documentSigner->signer_employee_id = $employee_id;
                    $employee = Employee::find($employee_id);
                    $document = Document::find($documentId);
                    $count = $document->locale == 'uz_latin' ? 1 : 2;
                    $documentSigner->department = $employee->staff[0]->department['name_' . $document->locale];
                    $documentSigner->position = $employee->staff[0]->position['name_' . $document->locale];
                    $documentSigner->fio = $employee->getShortname($document->locale);
                    if ($request->input('action_type') == 5) {
                        $documentSigner->status = 1;
                    }
                    $documentSigner->sign_type = 1;
                    $documentSigner->save();

                    // mail yuborish uchun
                    // $user =  User::where('employee_id', $employee_id)->first();
                    // if ($user && $user->email) {
                    //     $reaction_type = "Ko'rib chiqish";
                    //     SendEmail::addToQueue($user->email, $documentId, $reaction_type);
                    // }
                } else {
                    return 'This employee already has signer list!!!';
                }
                $documentSignerEvent = new DocumentSignerEvent;
                $documentSignerEvent->document_signer_id = $addDoc->id;
                $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                $documentSignerEvent->status = 6;
                $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
                $documentSignerEvent->fio = $addDoc->fio;
                $documentSignerEvent->comment = $documentSigner->action_type_id == 4 ? Employee::find($employee_id)->getShortname('uz_cyril') . ': -' . $assignment : Employee::find($employee_id)->getShortname('uz_cyril') . 'га маълумот учун юборди';
                $documentSignerEvent->save();
                DB::commit();
                return "Successfully saved!";
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }
    public function addRahbarSigners(Request $request){
        // return $request;
        $documentId = $request->input('document_id');
        $resolution = $request->input('resolution');
        $assignment = $resolution['assignment'];
        $dueDate = $resolution['due_date'];
        $parent_employee = Auth::user()->employee_id;
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

        $signerGroup = SignerGroup::with(['signerGroupDetails' => function ($q){
            $q->with('staff.employeeStaff');
        }])->find(805);
        // $emp = [];
        // // return $signerGroup->signerGroupDetails[2]->staff->employeeStaff;
        // foreach ($signerGroup->signerGroupDetails[0]->staff->employeeStaff as $key => $value) {
        //     if($value->is_main_staff == 1 && $value->is_active ==1){
        //         $emp = $value;
        //         break;
        //     }
        //     else if ($value->is_main_staff == 1 && $value->is_active ==1){
        //         $emp = $value;
        //         break;
        //     }
        // }
        // return $emp['employee_id'];
        $model = Document::find($documentId);

        $addDoc = DocumentSigner::whereIn('staff_id', $userStaffIds)
                        ->where('document_id', $documentId)
                        ->where(function ($q) {
                            $q->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where(function ($q) {
                            $q->whereIn('status', [0, 3, 4])
                                ->orWhere('action_type_id', 5);
                        })
                        ->first();
        if (!$addDoc) {
            return 0;
        }
        if ($addDoc->status != 3) {
            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $addDoc->id;
            $documentSignerEvent->action_type_id = $addDoc->action_type_id;
            $documentSignerEvent->comment = $assignment;
            $documentSignerEvent->status = 3;
            $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
            $documentSignerEvent->fio = $addDoc->fio;
            $documentSignerEvent->save();
        }
        $addDoc->signer_employee_id = Auth::user()->employee_id;
        $employee = Employee::find(Auth::user()->employee_id);
        $document = Document::find($documentId);
        $count = $document->locale == 'uz_latin' ? 1 : 2;
        $addDoc->fio = $employee->getShortname($document->locale);
        $addDoc->status = $addDoc->action_type_id != 5 ? 3 : $addDoc->status;
        $count = DocumentSigner::where('document_id', $documentId)
            ->where('parent_employee_id', Auth::user()->employee_id)
            ->whereIn('status', [0, 3])->count();
        if ($count == 0 && $request->input('action_type') == 5) {
            $addDoc->is_done = 1;
        } else {
            $addDoc->is_done = 2;
        }
        $addDoc->save();

        foreach ($signerGroup->signerGroupDetails as $key => $value) {
            $document_signer = new DocumentSigner();
            $document_signer->document_id = $documentId;
            $document_signer->staff_id = $value->staff_id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s', time());
            $document_signer->parent_employee_id = $parent_employee;
            $document_signer->action_type_id = $value->action_type_id;
            $document_signer->assignment = $assignment;
            $document_signer->due_date = $dueDate;
            $document_signer->sequence = $value->sequence;
            // $emp = [];
            // return $signerGroup->signerGroupDetails[2]->staff->employeeStaff;
            
            foreach ($value->staff->employeeStaff as $key => $val) {
                if($value->is_main_staff == 1 && $val->is_active ==1){
                    // $emp = $val;
                    $document_signer->signer_employee_id = $val['employee_id'];
                    break;
                }
                else if ($val->is_main_staff == 1 && $val->is_active ==1){
                    $document_signer->signer_employee_id = $val['employee_id'];
                    break;
                }
            }
            $document_signer->status = 0;
            $staff = Staff::with('department')->with('position')->find($value->staff_id);
            $document_signer->department = $staff->department['name_' . $model->locale];
            $document_signer->position = $staff->position['name_' . $model->locale];
            // $emp = Employee::find($value['managerStaff']['employees'][0]['id']);
            // dd($value['managerStaff']['employees'][0]['id']);
            // $document_signer->fio = $emp->getShortName($model->locale);
            $document_signer->save();
        }
    }
    public function reaction(Request $request)
    {
        $docId = $request['document_id'];
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $status = $request->input('status');
        $compliance_etiroz = $request->input('compliance_etiroz') ? $request->input('compliance_etiroz') : false;
        $sign_type = $request->input('sign_type');
        // if($docId == 2957511){
        //     dd(123);
        // }

        $signer_serial_number = $request->input('signer_serial_number');
        $description = $request->input('description');
        $document = Document::where('id', $docId)->with('documentTemplate')->with('documentType')->first();

        DB::beginTransaction();
        try {

            if ($status != 2) {
                $addDoc = DocumentSigner::where('document_id', $docId)
                ->whereIn('staff_id', $userStaffIds)
                ->whereIn('status', [0, 3, 4])
                ->whereNotNull('taken_datetime')
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->first();
            } else { // Hujjatni uni yaratuvchisi bekor qilish funksiyasi
                $addDoc = DocumentSigner::where('document_id', $docId)
                    ->whereIn('staff_id', $userStaffIds)
                    ->whereIn('status', [0, 3, 4])
                    ->whereNotNull('taken_datetime')
                    ->where(function ($q) {
                        $q->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->orWhere('document_id', $docId)
                    ->whereIn('staff_id', $userStaffIds)
                    ->whereIn('status', [1])
                    ->where('action_type_id', 6)
                    ->whereHas('documents', function ($q) {
                        $q->whereIn('status', [1, 2]);
                    })
                    ->first();
            }
            $addDoc->signer_employee_id = Auth::user()->employee_id;
            $employee = Auth::user()->employee;
            $count = $document->locale == 'uz_latin' ? 1 : 2;
            $addDoc->fio = $employee->getShortname($document->locale);
            $addDoc->signed_date = time();
            $addDoc->sign_at = date('Y-m-d H:i:s');
            $addDoc->description = $description;
            $addDoc->status = $status;
            $addDoc->sign_type = $sign_type;
            $addDoc->signer_serial_number = $signer_serial_number;
            $addDoc->save();
            
            // HUjjat yaratuvchisi hujjatni rad etganda kutilayotgan harakatdagi odamni taken_Date_time ni null qilish
            if($status==2 && $addDoc->signer_employee_id == Auth::user()->employee_id && $addDoc->action_type_id == 6){
                $proccessSigners = DocumentSigner::where('document_id', $docId)
                ->whereIn('status', [0, 3])
                ->whereNotNull('taken_datetime')->get();

                foreach ($proccessSigners as $key => $proSigner) {
                    $proSigner->taken_datetime = null;
                    $proSigner->due_date = null;
                    $proSigner->save();
                }
            }

            if ($addDoc->parent_employee_id) {
                $count = DocumentSigner::where('document_id', $docId)
                    ->where('parent_employee_id', $addDoc->parent_employee_id)
                    ->whereIn('status', [0, 3, 4])->count();
                if ($count == 0) {
                    $parent_signer = DocumentSigner::where('document_id', $docId)->where('signer_employee_id', $addDoc->parent_employee_id)->where('status', 3)->first();
                    if ($parent_signer) {
                        $parent_signer->is_done = 1;
                        $parent_signer->save();
                    }
                }
            }

            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $addDoc->id;
            $documentSignerEvent->action_type_id = $addDoc->action_type_id;
            $documentSignerEvent->comment = $description;
            if($compliance_etiroz){
                // dd(5);
                $documentSignerEvent->status = 16;                
            }else{
                $documentSignerEvent->status = $sign_type ? $status . $sign_type : $status;
            }
            $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
            $documentSignerEvent->fio = $addDoc->fio;
            $documentSignerEvent->save();

            if ($status == 2 && $addDoc->parent_employee_id == null && ($addDoc->action_type_id != 4 || $document->document_template_id == 13 && $addDoc->sequence == 6 || $document->document_type_id == 7 && !$addDoc->parent_employee_id && $addDoc->status != 0) && $addDoc->action_type_id != 8 && $addDoc->action_type_id != 17) {
                // $document = Document::where('id', $docId)->first();
                if ($document->document_type_id == 12 && $addDoc->action_type_id == 12 && $addDoc->sequence == 98) {
                    DocumentSigner::where('document_id', $document->id)->where('action_type_id', '<>', 6)->update(['status' => 0, 'taken_datetime' => null]);
                    $document->status = 0;
                    $document->save();
                } else {
                    $document->status = 6;
                    $document->save();
                }

                // Email jo'natish ---------------------
                $user = User::where('employee_id', $document->created_employee_id)->first();
                if ($user && $user->email && $document->status == 6) {
                    $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                    // if ($document->id == 211767) 
                    {
                        $employee = Employee::find(Auth::user()->employee_id);
                        $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                        $details = [
                            $fio =
                                'title' => "Ushbu hujjat " . $employee->getShortname('uz_latin') . " tomonidan bekor qilindi.",
                            'content' => json_encode([
                                'Link' => $link,
                                'Izoh' => $description,
                            ]),
                            'footer' => ''
                        ];
                        Mail::to($user->email)->send(new SendMail($details));
                    }
                }
                //------------------------
                $documentSigners = DocumentSigner::where('document_id', $document->id)->where('status', 1)->where('action_type_id', '!=', 6)->get();
                foreach ($documentSigners as $key => $documentSigner) {
                    // mail yuborish uchun
                    // $user = User::where('employee_id', $documentSigner->signer_employee_id)->first();
                    // if ($user && $user->email) {
                    //     $reaction_type = 'Siz imzolagan hujjat bekor qilindi!';
                    //     SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                    // }
                }
                if ($document->document_template_id == 114) {
                    foreach ($document->documentRelation as $key => $value) {
                        $value->status = 3;
                        $value->save();

                        $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
                        if ($ds) {
                            try {
                                $history = new DocumentSignerEvent();
                                $history->comment = 'Buyruq bekor qilindi.';
                                $history->status = 2;
                                $history->created_at = date('Y-m-d H:i:s');
                                $history->action_type_id = 5;
                                $history->document_signer_id = $ds->id;
                                $history->signer_employee_id = $ds->signer_employee_id;
                                $history->fio = $ds->fio;
                                $history->save();
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                } else if ($document->document_template_id == 399) {
                    foreach ($document->documentChildren as $key => $value) {
                        $value->status = 3;
                        $value->save();

                        $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
                        if ($ds) {
                            try {
                                $history = new DocumentSignerEvent();
                                $history->comment = 'Buyruq bekor qilindi.';
                                $history->status = 2;
                                $history->created_at = date('Y-m-d H:i:s');
                                $history->action_type_id = 5;
                                $history->document_signer_id = $ds->id;
                                $history->signer_employee_id = $ds->signer_employee_id;
                                $history->fio = $ds->fio;
                                $history->save();
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                }
            }

            $count = DocumentSigner::where('document_id', $docId)
                ->where('sequence', $addDoc->sequence)
                // ->where('action_type_id', "!=", 12)
                ->whereNull('parent_employee_id')
                ->whereIn('status', [0, 3, 4])->count();
            if ($count == 0 && $addDoc->sequence > 0) {
                // if ($count == 0 && $addDoc->sequence > 0 && $addDoc->action_type_id != 12) {
                // return $docId;
                $sequence = DocumentSigner::where('document_id', $docId)
                    ->where('status', 0)
                    // ->where('action_type_id', "!=", 12)
                    ->orderByDesc('sequence')->first();
                if ($sequence && $document->status != 6) {
                    $taken = DocumentSigner::where('document_id', $docId)
                        ->where('status', 0)
                        ->whereNull('taken_datetime')
                        ->where('sequence', $sequence->sequence);
                    foreach ($taken->get() as $key => $value) {
                        $due_day = DocumentSigner::select('document_signer_templates.due_day_count')
                            ->join('documents', 'documents.id', '=', 'document_signers.document_id')
                            ->join('document_templates', 'document_templates.id', '=', 'documents.document_template_id')
                            ->join('document_signer_templates', 'document_signer_templates.document_template_id', '=', 'document_templates.id')
                            ->where('document_signers.id', $value->id)->first();
                        $value->taken_datetime = date("Y-m-d H:i:s");
                        if ($sequence->sequence != 100 && $document->status != 2) {
                            $document->status = 2;
                            $document->save();
                        }
                        $value->due_date = isset($due_day->due_day_count) ?
                            date("Y-m-d H:i:s", time() + 3600 * $due_day->due_day_count) : ($value->due_date ? $value->due_date : date("Y-m-d H:i:s", time() + 86400));
                        if ($value->action_type_id == 5) {
                            $value->status = 1;
                        }
                        if ($value->sign_type == 0) {
                            $value->sign_type = 1;
                        }
                        $value->save();

                        $employeeStaffs = EmployeeStaff::where('staff_id', $value->staff_id)->where('is_active', 1)->get();
                        if ($addDoc->action_type_id == 1) {
                            $actionType = "Rozilik";
                        } elseif ($addDoc->action_type_id == 2) {
                            $actionType = "Tasdiqlash";
                        } elseif ($addDoc->action_type_id == 3) {
                            $actionType = "Bo'lim ichida rozilik";
                        } else {
                            $actionType = "Ko'rib chiqish";
                        }
                        foreach ($employeeStaffs as $key => $employeeStaff) {

                            // mail yuborish uchun
                            // $user = User::where('employee_id', $employeeStaff->employee_id)->first();
                            // if ($user && $user->email) {
                            //     SendEmail::addToQueue($user->email, $document->id, $actionType);
                            // }
                        }
                    }
                }
            }

            $completed = DocumentSigner::where('document_id', $document->id)
                ->where('sequence', '>', 0)
                ->where('status', '!=', 1)
                ->whereNotIn('action_type_id', [4, 8, 12, 17])
                ->whereNull('parent_employee_id');
            // DocumentSigner::where('document_id', $docId)->where('status', '!=', 1)->whereNotIn('action_type_id', [4, 5, 6, 8, 11, 12, 14])->whereNull('parent_employee_id');
            $signing = false;
            $outSigner = DocumentSigner::where('document_id', $docId)->whereIn('action_type_id', [2])->first();
            if ($completed->count() == 0 && $document->status < 3 && $document->document_type_id != 55) {
                $document->status = 3;
                $document->save();
                if($document->document_template_id == 582){ //O'quv ta'tili buyrug'i imzolanganda Aloqador hujjatlarni statusini 5 qilish
                    foreach ($document->documentRelation as $key => $value) {
                        $value->status = 5;
                        $value->save();
                    }
                }
                if ($document->document_template_id == 574) { //Tengebank reyestr tadiqlanganda Status 4
                    $this->sendStatusToTengeBank($document->id, 4);
                }
                if ($document->document_template_id == 564) { // Tengebank ariza tasdiqlanganidan keyin status 3
                    $this->sendStatusToTengeBank($document->id, 3);
                }
                $signing = true;
            } else if ($document->document_type_id == 55) {
                $completed = DocumentSigner::where('document_id', $document->id)
                    ->where('sequence', '>', 0)
                    ->where('status', '!=', 1)
                    ->whereNotIn('action_type_id', [4, 8, 17])
                    ->whereNull('parent_employee_id');
                if ($completed->count() == 0 && $document->status < 3) {
                    $document->status = 3;
                    $document->save();
                }
            }

            $notCompleted = DocumentSigner::where('document_id', $docId)->whereNotIn('status', [1, 2])->whereNull('parent_employee_id');
            if ($notCompleted->count() == 0 && $outSigner) {
                $cancelled = DocumentSigner::where('document_id', $docId)
                    ->where('status', 2)
                    ->whereIn('action_type_id', [1, 2, 9])
                    ->whereNull('parent_employee_id')
                    ->count();
                if ($cancelled == 0) {
                    $document->status = 5;
                    $document->save();
                }
            }
            $finished = DocumentSigner::where('document_id', $document->id)
                // ->where('sequence', '>', 0)
                ->where('status', '!=', 1)
                // ->whereNotIn('action_type_id', [4, 8, 12, 17])
                ->whereNull('parent_employee_id');
            if($finished->count()==0 && $document->document_template_id != 564){
                $document->status = 5;
                $document->save();
            }

            if ($document->document_template_id == 574) { //Tengebank ijrochilar ham tasdiqlaganda status 5

                $tengeAllSignersSigned = DocumentSigner::where('document_id', $document->id)
                    ->where('status', '!=', 1)
                    ->whereNull('parent_employee_id');
                if ($tengeAllSignersSigned->count() == 0) {
                    $this->sendStatusToTengeBank($document->id, 5);
                }
            }
            if ($document->document_template_id == 564) { //Tengebank Ariza bekor qilinganda status 0

                $tengeCancelled = DocumentSigner::where('document_id', $document->id)
                    ->where('status', 2)
                    ->whereNull('parent_employee_id');
                if ($tengeCancelled->count() > 0) {
                    $this->sendStatusToTengeBank($document->id, 0);
                }
            }
            if ($document->document_template_id == 636) { //Kategoriya komisssiyasidan 2 tasi bekor qilganda

                $komissiya = DocumentSigner::where('document_id', $document->id)
                    ->where('status', 2)
                    ->where('action_type_id', 8)
                    ->whereNull('parent_employee_id');
                // 2 ta komissiy a'zosi otkaz bossa dokumentni otkaz qilish
                if ($komissiya->count() > 1) {
                    // dokument otkaz boganda jarayondagi imzolovchilarni ochirish
                    $komissiyaJarayon = DocumentSigner::where('document_id', $document->id)
                    ->where('status', 0)
                    // ->where('action_type_id', 8)
                    ->whereNotNull('taken_datetime')->get();
                    foreach ($komissiyaJarayon as $key => $kom) {
                        $kom->taken_datetime = null;
                        $kom->due_date = null;
                        $kom->save();
                    }
                    $document->status = 6;
                    $document->save();
                }
                
            }
            // if ($document->document_template_id == 114 && $document->status == 3) {
            //     $this->saveAttributesToAs400($document->id);
            // }
            // if ($document->document_template_id == 287 && in_array($document->status, [3, 4, 5])) {
            //     $this->uvolnitelniyCreate($document->id);
            // }
            if ($document->document_template_id == 224 && in_array($document->status, [3, 4, 5])) {
                $this->transportRequestCreate($document->id);
            }
            DB::commit();

            // if ($document->document_template_id == 218) 
            {
                // Bo'lim ichidan chiqqanda nomer olish tartibi
                $ds = DocumentSigner::where('document_id', $document->id)
                    ->where('sequence', '>', 98)
                    ->where('status', '!=', 1)
                    ->first();
                if (!$ds && $document->documentTemplate->numbering_order == 2 && $addDoc->sequence > 98) {
                    Document::generateDocumentNumberNew2022($document->id);
                } else {
                    // Rahbar imzolaganda nomer olish tartibi
                    $ds = DocumentSigner::where('document_id', $document->id)
                        ->where('sequence', '>', 0)
                        ->where('status', '!=', 1)
                        ->whereNotIn('action_type_id', [4, 8, 12, 17]) // komissiya azolari, kuzatuvchi
                        ->first();
                    if (!$ds && $document->documentTemplate->numbering_order == 3 && $addDoc->sequence > 0 && $document->document_type_id != 55) {
                        Document::generateDocumentNumberNew2022($document->id);
                    } else if ($document->document_type_id == 55) {
                        $completed = DocumentSigner::where('document_id', $document->id)
                            ->where('sequence', '>', 0)
                            ->where('status', '!=', 1)
                            ->whereNotIn('action_type_id', [4, 8, 17])
                            ->whereNull('parent_employee_id');
                        if ($completed->count() == 0) {
                            Document::generateDocumentNumberNew2022($document->id);
                        }
                    }
                }
            }

            Document::savePdf($document->id);

            if ($document->document_template_id == 287 && in_array($document->status, [3, 4, 5])) {
                $this->uvolnitelniyCreate($document->id);
            }

            // Perevod va yangi hodi olish shablonlari uchun
            // dd($document->document_template_id, $sign_type, $document->status);
            if (in_array($document->document_template_id, [415]) && $addDoc->action_type_id == 2 && in_array($document->status, [3, 4, 5])) {
                $this->changeStaffAndSaveAs400($document->id);
            }
            if(in_array($document->document_template_id, [114]) && $signing) {
                $this->saveAttributesToAs400($document->id);

                //buyruq tasdiqlanganda aloqador hujjatlarni o'zgartirish
                foreach ($document->documentRelation as $key => $value) {
                    $value->status = 5;
                    $value->save();

                    $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->where('action_type_id', 4)->whereNull('parent_employee_id')->first();
                    if ($ds) {
                        try {
                            $ds->status = 1;
                            $ds->updated_at = date('Y-m-d H:i:s');
                            $ds->save();

                            $history = new DocumentSignerEvent();
                            $history->comment = 'Buyruq tasdiqlandi';
                            $history->status = 1;
                            $history->created_at = date('Y-m-d H:i:s');
                            $history->action_type_id = 4;
                            $history->document_signer_id = $ds->id;
                            $history->signer_employee_id = $ds->signer_employee_id;
                            $history->fio = $ds->fio;
                            $history->save();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            } elseif(in_array($document->document_template_id, [582]) && $signing) {
                $this->saveUquvTatilToAs400($document->id);
            } elseif ($document->document_template_id == 226 && $signing) {
                $this->businessTripToAs400($document->id);
            } elseif ($document->document_template_id == 597 && $signing) {
                $this->otgulToAs400($document->id);
            } elseif ($document->document_template_id == 431 && $signing) {
                // KpiObject::kpiCreatedObjekt($document->id);
                // $this->otgulToAs400($document->id);
            }

            return ['message' => 'Successfully saved!', 'document_status' => $document->status, 'signer_event_id' => $documentSignerEvent->id, 'action_type_id' => $addDoc->action_type_id];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function businessTripRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(226);
            $model = new Document();
            $model->document_template_id = 226;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = $request->input('locale');
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();
            foreach ($documents as $key => $value) {
                $dr = new DocumentRelation();
                $dr->document_id = $model->id;
                $dr->parent_document_id = $value->id;
                $dr->save();
                foreach ($value->documentDetails as $dk => $dd) {
                    foreach ($dd->documentDetailEmployees as $ddek => $dde) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;

                        // return $document_template->documentDetailTemplates[0]['content_'.$model->locale];
                        if ($key == 0) {
                            $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                        } else {
                            $document_detail->content = '';
                        }
                        $document_detail->save();

                        $emp = Employee::find($dde->employee_id);
                        $department = $dde->employee->staff[0]->department;
                        $position = $dde->employee->staff[0]->position;
                        $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                        $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'Xizmat safari.';
                        $dde_new->employee_fio = $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                        $dde_new->employee_department = $department ? $department['name_' . $model->locale] : '';
                        $dde_new->employee_position = $position ? $position['name_' . $model->locale] : '';
                        $dde_new->save();

                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        $from = '';
                        $to = '';
                        foreach ($ddas as $ddak => $dda) {
                            if ($value->document_template_id == 9) {
                                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                                $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                                if ($dda->id == 778) { // fio
                                    $document_detail_attribute_value->attribute_value =
                                        $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                                } elseif ($dda->id == 779) { // Tab.№
                                    $document_detail_attribute_value->attribute_value = $emp->tabel;
                                } elseif ($dda->id == 780) { // Ish joyi
                                    $document_detail_attribute_value->attribute_value = $department ? $department['name_' . $model->locale] : '';
                                } elseif ($dda->id == 781) { // Lavozimi
                                    $document_detail_attribute_value->attribute_value = $position ? $position['name_' . $model->locale] : '';
                                } elseif ($dda->id == 782) { // Qachondan
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 10);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $from = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 783) { // Qachongacha
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 13);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 784) { // Kunga
                                    $origin = strtotime($from);
                                    $target = strtotime($to);
                                    $datediff = $target - $origin;
                                    $fromto = round($datediff / (60 * 60 * 24)) + 1;
                                    $document_detail_attribute_value->attribute_value = $from && $to ? $fromto : '';
                                } elseif ($dda->id == 785) { // Manzil
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 9);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 786) { // Maqsad
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 8);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 787) { // Asos
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 187);
                                    $document_detail_attribute_value->attribute_value = $value->document_number;
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 2077) { // Otpravleniya
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 2066);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                }
                            } else {
                                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                                $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                                if ($dda->id == 778) { // fio
                                    $document_detail_attribute_value->attribute_value =
                                        $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                                } elseif ($dda->id == 779) { // Tab.№
                                    $document_detail_attribute_value->attribute_value = $emp->tabel;
                                } elseif ($dda->id == 780) { // Ish joyi
                                    $document_detail_attribute_value->attribute_value = $department ? $department['name_' . $model->locale] : '';
                                } elseif ($dda->id == 781) { // Lavozimi
                                    $document_detail_attribute_value->attribute_value = $position ? $position['name_' . $model->locale] : '';
                                } elseif ($dda->id == 782) { // Qachondan
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1306);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $from = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 783) { // Qachongacha
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1307);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 784) { // Kunga
                                    $origin = strtotime($from);
                                    $target = strtotime($to);
                                    $datediff = $target - $origin;
                                    $fromto = round($datediff / (60 * 60 * 24)) + 1;
                                    $document_detail_attribute_value->attribute_value = $from && $to ? $fromto : '';
                                } elseif ($dda->id == 785) { // Manzil
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1305);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 786) { // Maqsad
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1304);
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                    $to = $attr ? $attr->attribute_value : '';
                                } elseif ($dda->id == 787) { // Asos
                                    $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1308);
                                    $document_detail_attribute_value->attribute_value = $value->document_number;
                                    $to = $attr ? $attr->attribute_value : '';
                                }
                            }

                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            if ($dda->id == 2077) {
                                $table_list = DB::table('directories')->find($document_detail_attribute_value->attribute_value);
                                $column_name = 'name_' . $model->locale;
                                $documentDetailContent->value = $table_list ? $table_list->$column_name : '';
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            }

            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $document_signer->status = 1;
            $document_signer->department = Auth::user()->employee->mainStaff[0]->department['name_' . $model->locale];
            $document_signer->position = Auth::user()->employee->mainStaff[0]->position['name_' . $model->locale];
            $document_signer->fio = Auth::user()->employee->getShortName($model->locale);

            $document_signer->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = 6;
            $document_signer_event->comment = 'created';
            $document_signer_event->status = 0;
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            foreach ($document_template->documentSignerTemplates as $key => $value) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $value->staff_id;
                $document_signer->action_type_id = $value->action_type_id;
                $document_signer->sequence = $value->sequence;
                $document_signer->status = 0;
                $document_signer->sign_type = $value->sign_type;
                $document_signer->save();
            }

            DB::commit();
            return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
    }

    public function changeStaffAndSaveAs400($id)
    {
        $document = Document::find($id);

        DB::beginTransaction();
        try {
            foreach ($document->documentDetails as $detail_key => $detail) {
                // if($detail_key == 0) continue;
                $staff_id2 = null;
                $category_id2 = null;
                foreach ($detail->documentDetailAttributeValues as $key => $value) {
                    if ($value->documentDetailAttributes->data_type_id == 12) {
                        $staff_id2 = $value->attribute_value;
                    } else if ($value->documentDetailAttributes->table_list_id == 5) {
                        $category_id2 = $value->attribute_value;
                    }
                }

                $employee = Employee::find($detail->documentDetailEmployees[0]->employee_id);
                $staff1 = $employee->mainStaff[0];
                $staff2 = Staff::find($staff_id2);
                $employeeCoefficients1 = $employee->employeeCoefficients;
                $documentDetailcoefficients2 = $detail->documentDetailcoefficients;
                // dd($employee->employeeCoefficients);
                // change category
                $employee->tariff_scale_id = $category_id2;
                $employee->save();
                // delete old coefficients

                foreach ($employeeCoefficients1 as $key => $value) {
                    $value->delete();
                }

                // add new coefficients
                foreach ($documentDetailcoefficients2 as $key => $value) {
                    if ($value->type == 1) {
                        $coefficient = new EmployeeCoefficient();
                        $coefficient->employee_id = $employee->id;
                        $coefficient->coefficient_id = $value->tariff_scale_id;
                        $coefficient->percent = $value->value;
                        $coefficient->begin_date = $document->document_date;
                        $coefficient->order_number = $document->document_number;
                        $coefficient->order_date = $document->document_date;
                        $coefficient->save();
                    }
                }
                // change staff
                EmployeeStaff::where('employee_id', $employee->id)->where('is_main_staff', 1)->update(['is_active' => 0]);
                // add employee staff
                $employeeStaff = new EmployeeStaff();
                $employeeStaff->employee_id = $employee->id;
                $employeeStaff->tariff_scale_id = $employee->tariff_scale_id;
                $employeeStaff->enter_order_number = $document->document_number;
                $employeeStaff->enter_order_date = $document->document_date;
                $employeeStaff->staff_id = $staff_id2;
                $employeeStaff->is_main_staff = 1;
                $employeeStaff->is_active = 1;
                $employeeStaff->save();

                $data = [
                    'department_code1' => $staff1->department->department_code,
                    'department_code2' => substr($staff2->department->department_code, 0, 5),
                    'position_code2' => $staff2->position->code,
                    'tabno' => $employee->tabel,
                ];
                // dd($data);
                $response = Http::post('http://edo-db2.uzautomotors.com/api/changeStaffInAs400', $data);
                // return (json_encode($response->body()));
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }
    }

    public function saveAttributesToAs400($id)
    {
        $response = Http::get('http://edo-db2.uzautomotors.com/api/as400/saveAttributesToAs400/' . $id);
        $model = Document::with('documentDetails')
            ->with('documentDetails.documentDetailAttributeValues')
            ->with('documentDetails.documentDetailEmployees')
            ->where('id', $id)->first();

        foreach ($model->documentRelation as $key => $value) {
            $value->status = 4;
            $value->save();

            $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
            if ($ds) {
                $history = new DocumentSignerEvent();
                $history->comment = 'Buyruq tayyorlandi.';
                $history->status = 1;
                $history->created_at = date('Y-m-d H:i:s');
                $history->action_type_id = 5;
                $history->document_signer_id = $ds->id;
                $history->signer_employee_id = $ds->signer_employee_id;
                $history->fio = $ds->fio;
                try {
                    $history->save();
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
        return $response;
    }

    public function saveUquvTatilToAs400($id)
    {
        $response = Http::get('http://edo-db2.uzautomotors.com/api/as400/saveUquvTatilToAs400/' . $id);
        $model = Document::with('documentDetails')
            ->with('documentDetails.documentDetailAttributeValues')
            ->with('documentDetails.documentDetailEmployees')
            ->where('id', $id)->first();

        foreach ($model->documentRelation as $key => $value) {
            $value->status = 4;
            $value->save();

            $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
            if ($ds) {
                $history = new DocumentSignerEvent();
                $history->comment = 'Buyruq tayyorlandi.';
                $history->status = 1;
                $history->created_at = date('Y-m-d H:i:s');
                $history->action_type_id = 5;
                $history->document_signer_id = $ds->id;
                $history->signer_employee_id = $ds->signer_employee_id;
                $history->fio = $ds->fio;
                try {
                    $history->save();
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
        return $response;
    }

    public function otgulToAs400($id)
    {
        try {
            $response = Http::get('http://edo-db2.uzautomotors.com/api/as400/otgulToAs400/' . $id);
            return $response;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function businessTripToAs400($id)
    {
        try {
            $response = Http::get('http://edo-db2.uzautomotors.com/api/as400/businessTripToAs400/' . $id);
            // $model = Document::query()
            //     // ->with('documentDetails')
            //     // ->with('documentDetails.documentDetailAttributeValues')
            //     // ->with('documentDetails.documentDetailEmployees')
            //     ->find($id);
            return $response;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function transportRequestCompleted(Request $request)
    {
        $document_number = explode('/', $request->input('request_number'))[0];
        $comment = $request->input('comment');
        $document = Document::where('document_number', $document_number)
            ->where('document_template_id', 224)
            ->first();
        if ($document) {
            $ispolnitel = DocumentSigner::where('document_id', $document->id)
                ->where('sequence', 0)
                ->where('action_type_id', 4)
                ->first();
            if ($ispolnitel) {
                $event = new DocumentSignerEvent;
                $event->document_signer_id = $ispolnitel->id;
                $event->action_type_id = 4;
                $event->status = 1;
                $event->fio = "Transport user";
                $event->comment = $comment;
                $event->document_id = $document->id;
                $event->created_at = date('Y-m-d H:i:s');
                $event->save();
                $ispolnitel->status = 1;
                $staff_id = $ispolnitel->staff_id;
                $emp = Employee::whereHas('staff', function ($q) use ($staff_id) {
                    $q->where('staff.id', $staff_id);
                })->first();
                $count = $document->locale == 'uz_latin' ? 1 : 2;
                $ispolnitel->fio = substr($emp['firstname_' . $document->locale], 0, $count) . '. ' . substr($emp['middlename_' . $document->locale], 0, $count) . '. ' . $emp['lastname_' . $document->locale];
                $ispolnitel->signer_employee_id = $emp->id;
                $ispolnitel->save();
                $document->status = 5;
                $document->save();
                // return $event;
                return 'Dokument yakunlandi.';
            }
            return 'Ijro qiluvchi topilmadi.';
        }
        return 'Document topilmadi.';
    }

    public function transportRequestCreate($id)
    {
        $model = Document::with('documentDetails')
            ->with('documentDetails')
            ->with('documentDetails.documentDetailAttributeValues')
            ->with('documentDetails.documentDetailEmployees.employee')
            ->where('id', $id)->first();
        $data = [];

        $document_template = \App\Http\Models\DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(224);
        $ddc = \App\Http\Models\DocumentDetailContent::where('document_detail_id', $model->documentDetails['0']->id)->get();
        // $table_list = DB::table($ddc->table_name)->find($ddc->table_value);
        // $request_type = 

        $data = [];
        $signer = DocumentSigner::where('document_id', $id)->where('sequence', 1)->first();
        $signerEmployee = $signer->signerEmployee;
        $performer = DocumentSigner::where('document_id', $id)->where('sequence', 100)->where('action_type_id', 6)->first();
        $performerEmployee = $performer->signerEmployee;
        foreach ($model->documentDetails as $key => $value) {
            $data[$key]['requet_type_id'] = $value->documentDetailAttributeValues[0]->attribute_value;
            $data[$key]['requetnumber'] = $model->document_number . '/' . (count($model->documentDetails) > 0 ? ($key + 1) : '');
            $data[$key]['responsible_contact'] = $model->responsible_contact;
            $data[$key]['reason'] = $value->documentDetailAttributeValues[1]->attribute_value;
            $data[$key]['startdatetime'] = $value->documentDetailAttributeValues[2]->attribute_value;
            $data[$key]['enddatetime'] = $value->documentDetailAttributeValues[3]->attribute_value;
            $data[$key]['address'] = $value->documentDetailAttributeValues[4]->attribute_value;
            $data[$key]['end_address'] = $value->documentDetailAttributeValues[5]->attribute_value;
            $data[$key]['name_loaded'] = $value->documentDetailAttributeValues[6]->attribute_value;
            $data[$key]['purpose'] = $value->documentDetailAttributeValues[7]->attribute_value;
            $data[$key]['header_fio'] = $signerEmployee['lastname_' . $model->locale] . ' ' . $signerEmployee['firstname_' . $model->locale] . ' ' . $signerEmployee['middlename_' . $model->locale];
            $data[$key]['department_name'] = $model->from_department;
            $data[$key]['performer_fio'] = $performerEmployee['lastname_' . $model->locale] . ' ' . $performerEmployee['firstname_' . $model->locale] . ' ' . $performerEmployee['middlename_' . $model->locale];
            $data[$key]['header_signed_at'] = date('Y-m-d H:i:s');
            $data[$key]['performer_signed_at'] = $performer->updated_at->format('Y-m-d H:i:s');
        }

        $response = Http::post('https://b-garaj.uzautomotors.com/api/request-edo', $data);

        $description1 = 'Taqsimotga uchun ro`yxatga olindi!';
        $description2 = 'Bazaga saqlanmadi! (Murojaat uchun tel: 3051, 3057)';

        $document_signer = DocumentSigner::select('id', 'document_id', 'signer_employee_id', 'fio')
            ->where('document_id', $id)
            ->where('action_type_id', 1)
            ->where('staff_id', 717)
            ->whereNotIn('sequence', [100, 1])
            ->first();
        if ($document_signer) {
            $documentSignerEvent = new DocumentSignerEvent;

            if ($response->status() == 200) {
                $documentSignerEvent->comment = $description1;
            } else {
                $documentSignerEvent->comment = $description2;
            }
            $documentSignerEvent->document_signer_id = $document_signer->id;
            $documentSignerEvent->action_type_id = 4;
            $documentSignerEvent->status = 1;
            $documentSignerEvent->signer_employee_id = $document_signer->signer_employee_id ? $document_signer->signer_employee_id : '';
            $documentSignerEvent->fio = $document_signer->fio;
            $documentSignerEvent->save();
        }

        return $response->body();
        return ($response->json());
        // http://b-garaj.uzautomotors.com/api
        // die;
        // dd(json_decode(json_encode($document_template->documentDetailTemplates[0]->documentDetailAttributes)));die;
    }

    public function uvolnitelniyCreate($id)
    {
        $model = Document::with('documentDetails')
            ->with('documentDetails')
            ->with('documentDetails.documentDetailAttributeValues')
            ->with('documentDetails.documentDetailEmployees.employee')
            ->where('id', $id)->first();
        // dd($model->DocumentDetails[0]->documentDetailAttributeValues[0]->attribute_value);
        $data = [];

        foreach ($model->documentDetails as $key => $detail) {
            foreach ($detail->documentDetailEmployees as $k => $v) {
                // if($model->document_number == '21AAA-10537')
                //     dd([
                //         date('Y-m-d H:i:s',strtotime(str_replace('Т',' ',$detail->documentDetailAttributeValues[0]->attribute_value))),
                //         date('Y-m-d H:i:s',strtotime(str_replace('Т',' ',$detail->documentDetailAttributeValues[1]->attribute_value)))

                //     ]);
                // dd(str_replace('   ',' ',$detail->documentDetailAttributeValues[1]->attribute_value));
                $data['1' . $key . $k . '1']['document_number'] = $model->document_number;
                $data['1' . $key . $k . '1']['tabel'] = $v->employee->tabel; //Z101KEYTN
                $data['1' . $key . $k . '1']['start'] = date('Y-m-d H:i:s', strtotime(str_replace('Т', ' ', $detail->documentDetailAttributeValues[0]->attribute_value))); //Z101VAQTFR
                $data['1' . $key . $k . '1']['end'] = date('Y-m-d H:i:s', strtotime(str_replace('Т', ' ', $detail->documentDetailAttributeValues[1]->attribute_value))); //Z101VAQTTO
                // dd($detail->documentDetailAttributeValues[1]->attribute_value);
                $data['1' . $key . $k . '1']['type'] = $detail->documentDetailAttributeValues[2]->attribute_value; //50-shahsiy, 51-xizmat
                // $data[1.$key.$k]['maqsad'] = $detail->documentDetailAttributeValues[3]->attribute_value; //Z101MAQSAD
                $data['1' . $key . $k . '1']['where'] = $detail->documentDetailAttributeValues[4]->attribute_value; //Z101MANZIL
                $data['1' . $key . $k . '1']['imzo'] = $model->status == 3 || $model->status == 4 || $model->status == 5 ? 1 : 0;
            }
        }
        // if($model->document_number == '21AAA-12806'){
        //     echo '<pre>';
        //     print_r($data);
        //     return $data;
        // }

        $response = Http::post('http://edo-db2.uzautomotors.com/api/uvolnitelniy-create', $data);
        $response = $response->body();
        return $response;
    }

    public function confirmation(Request $request)
    {
        $docId = $request['document_id'];
        $signerEmployeeId = User::with('employee:id')->where('id', Auth::id())->first();
        $signerStaffId = EmployeeStaff::where('employee_id', $signerEmployeeId->employee_id)->where('is_active', 1)->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $description = $request->input('description');
        $status = $request->input('status');
        DB::beginTransaction();
        try {
            $addDoc = DocumentSigner::whereIn('staff_id', $userStaffIds)
                ->where('document_id', $docId)
                ->where(function ($q) use ($signerEmployeeId) {
                    return $q->whereNotNull('parent_employee_id')
                        ->where('signer_employee_id', $signerEmployeeId->employee_id)
                        ->orWhereNull('parent_employee_id');
                })->first();
            if ($addDoc) {
                $documentSignerEvent = new DocumentSignerEvent;
                $documentSignerEvent->document_signer_id = $addDoc->id;
                $documentSignerEvent->action_type_id = 9;
                $documentSignerEvent->comment = $description;
                $documentSignerEvent->status = $status == 1 ? 10 : 12;
                $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
                $documentSignerEvent->fio = $addDoc->fio;
                $documentSignerEvent->save();
            }

            if ($status == 1) {
                $document = Document::where('id', $docId)->first();
                $document->status = 5;
                $document->save();
            } else {
                $document = Document::where('id', $docId)->first();
                $document->status = 3;
                $document->save();

                $signer_doers = DocumentSigner::where('document_id', $docId)
                    ->where('sequence', 0)->whereNull('parent_employee_id')->get();
                foreach ($signer_doers as $key => $signer_doer) {
                    $signer_doer->due_date = date("Y-m-d H:i:s", time() + 86400);
                    $signer_doer->status = 3;
                    $signer_doer->save();

                    // mail yuborish uchun //
                    // $user =  User::where('employee_id', $signer_doer->signer_employee_id)->first();
                    // if ($user && $user->email) {
                    //     $reaction_type = 'Qayta ko\'rib chiqish! Yaratuvchi ijrodan norozi';
                    //     SendEmail::addToQueue($user->email, $docId, $reaction_type);
                    // }
                }
            }
            DB::commit();
            return "Successfully saved!";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function toReturn(Request $request)
    {
        $docId = $request->input('document_id');
        $description = $request->input('description');
        $signer_id = $request->input('signer_id');
        $signerEmployeeId = User::find(Auth::id());

        DB::beginTransaction();
        try {
            $signer = DocumentSigner::where('document_id', $docId)
                // ->where('signer_employee_id', $signerEmployeeId->employee_id)
                ->whereIn('status', [0, 3])
                ->first();
            $signer->is_done = 2;
            $signer->save();
            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $signer->id;
            $documentSignerEvent->action_type_id = 9;
            $documentSignerEvent->comment = $description;
            $documentSignerEvent->status = 12;
            $documentSignerEvent->signer_employee_id = $signer->signer_employee_id;
            $documentSignerEvent->fio = $signer->fio;
            $documentSignerEvent->save();

            $signer_doer = DocumentSigner::find($signer_id);
            $signer_doer->due_date = date("Y-m-d H:i:s", time() + 86400);
            $signer_doer->assignment = $description;
            $signer_doer->status = 3;
            $signer_doer->save();

            // mail yuborish uchun //
            // $user =  User::where('employee_id', $signer_doer->signer_employee_id)->first();
            // if ($user && $user->email) {
            //     $reaction_type = 'Qayta ko\'rib chiqish';
            //     SendEmail::addToQueue($user->email, $docId, $reaction_type);
            // }
            if ($docId == 1) {

                $document = Document::find($docId);
                $user =  User::where('employee_id', $signer_doer->signer_employee_id)->first();
                if ($user && $user->email) {
                    $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                    $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                    $details = [
                        'title' => "Qayta ko'rib chiqish",
                        'content' => json_encode([
                            'Link' => $link,
                            'Izoh' => $description,
                            // 'Hujjat muallifi' => Auth::user()->employee->getShortname('uz_latin')
                        ]),
                        'footer' => ''
                    ];
                    Mail::to($user->email)->send(new SendMail($details));
                }
            }
            DB::commit();
            return "Successfully saved!";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function processing(Request $request)
    {
        $docId = $request['document_id'];
        $assignment = $request['comment'];
        $signerEmployeeId = User::with('employee:id')->where('id', Auth::id())->first();
        $signerStaffId = EmployeeStaff::where('employee_id', $signerEmployeeId->employee_id)->where('is_active', 1)->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        // DB::beginTransaction();
        // try {
        $addDoc = DocumentSigner::whereIn('staff_id', $userStaffIds)
            ->where('document_id', $docId)
            ->whereNotNull('taken_datetime')
            ->whereIn('status', [0, 3])
            ->where(function ($q) use ($signerEmployeeId) {
                return $q->where('signer_employee_id', $signerEmployeeId->employee_id)
                    ->orWhereNull('parent_employee_id');
            })->first();
        if (!$addDoc) {
            return 0;
        }
        $addDoc->signer_employee_id = $signerEmployeeId->employee_id;
        $addDoc->status = 3;
        $employee = Auth::user()->employee;
        $document = Document::find($docId);
        $addDoc->fio = $employee->getShortname($document->locale);
        $addDoc->save();
        $documentSignerEvent = new DocumentSignerEvent;
        $documentSignerEvent->document_signer_id = $addDoc->id;
        $documentSignerEvent->action_type_id = $addDoc->action_type_id;
        $documentSignerEvent->comment = $assignment;
        $documentSignerEvent->status = 3;
        $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
        $documentSignerEvent->fio = $addDoc->fio;
        $documentSignerEvent->save();
        // DB::commit();
        return "Successfully saved!";
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     return $th;
        // }
    }

    public function comment(Request $request)
    {
        $docId = $request['document_id'];
        $assignment = $request['comment'];
        $signer_id = $request['signer_id'];
        $substantiate = $request['substantiate'];
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $addDoc = DocumentSigner::where('document_id', $docId)
            ->whereIn('staff_id', $userStaffIds)
            ->where(function ($q) {
                return $q->where('signer_employee_id', Auth::user()->employee_id)
                    ->orWhereNull('signer_employee_id');
            })
            ->whereNotNull('taken_datetime')
            // ->whereIn('status',[0,3,1])
            ->first();
        if (!$addDoc) {
            return 0;
        } else {
            $addDoc->signer_employee_id = Auth::user()->employee_id;
            $employee = Auth::user()->employee;
            $document = Document::find($docId);
            if ($substantiate) {
                $addDoc->status = 4;
            }
            $addDoc->fio = $employee->getShortname($document->locale);
            $addDoc->save();
        }
        $documentSignerEvent = new DocumentSignerEvent;
        $documentSignerEvent->document_signer_id = $addDoc->id;
        $documentSignerEvent->action_type_id = $addDoc->action_type_id;

        $documentSignerEvent->comment = $assignment;
        $documentSignerEvent->status = $substantiate ? 4 : 5;
        $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
        $documentSignerEvent->fio = $addDoc->fio;
        $documentSignerEvent->save();

        // mail yuborish uchun //
        $document = Document::where('id', $docId)->with('documentTemplate')->with('documentType')->first();
        if ($signer_id) {
            $comment_signer = DocumentSigner::find($signer_id);
            if ($comment_signer->signer_employee_id) {
                $employee = Employee::find($comment_signer->signer_employee_id);
                $documentSignerEvent->comment = substr($employee->firstname_uz_cyril, 0, 2) . '.' . substr($employee->middlename_uz_cyril, 0, 2) . '. ' . $employee->lastname_uz_cyril . ': -' . $assignment;
                $documentSignerEvent->save();
                $user = User::where('employee_id', $comment_signer->signer_employee_id)->first();
            } else {
                $comment_employee = EmployeeStaff::where('staff_id', $comment_signer->staff_id)->where('is_active', 1)->first();
                $employee = Employee::find($comment_employee->employee_id);
                $documentSignerEvent->comment = substr($employee->firstname_uz_cyril, 0, 2) . '.' . substr($employee->middlename_uz_cyril, 0, 2) . '. ' . $employee->lastname_uz_cyril . ': -' . $assignment;
                $documentSignerEvent->save();
                $user = User::where('employee_id', $comment_employee->employee_id)->first();
            }
            $comment_signer->assignment = $assignment;
            $comment_signer->save();
        } elseif ($addDoc->parent_employee_id) {
            $user = User::where('employee_id', $addDoc->parent_employee_id)->first();
        } else {
            $user = User::where('employee_id', $document->created_employee_id)->first();
        }

        // Obosnovat bosilganda hujjat avtoriga email jo'natish
        $user = User::where('employee_id', $document->created_employee_id)->first();
        if ($user && $user->email) {
            // $reaction_type = 'Ushbu hujjatga izox yozildi. Ko\'rib chiqing.';
            if ($substantiate) {
                $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                // SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                // if ($document->id == 164861) 
                {
                    $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                    $details = [
                        'title' => "Ushbu hujjat shaxsan asoslab berilsin.",
                        'content' => json_encode([
                            'Link' => $link,
                        ]),
                        'footer' => ''
                    ];
                    Mail::to($user->email)->send(new SendMail($details));
                }
            }
        }

        // Avtor comment yozganda Obosnavat bosgan odamga email jo'natish
        if (!$substantiate && $document->created_employee_id == Auth::user()->employee_id) {
            $signers = DocumentSigner::where('status', 4)->where('document_id', $docId)->whereNotNull('signer_employee_id')->get();
            foreach ($signers as $key => $value) {
                $user = User::where('employee_id', $value->signer_employee_id)->first();
                if ($user && $user->email) {
                    $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                    $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                    $details = [
                        'title' => "Sizning so'rovnomangizga javob berildi.",
                        'content' => json_encode([
                            'Link' => $link,
                            'Izoh' => $assignment,
                            'Hujjat muallifi' => Auth::user()->employee->getShortname('uz_latin')
                        ]),
                        'footer' => ''
                    ];
                    Mail::to($user->email)->send(new SendMail($details));
                }
            }
        }
        return $documentSignerEvent;
    }

    public function preAgreement(Request $request)
    {
        $docId = $request['document_id'];
        $assignment = $request['comment'];
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $docSigners = DocumentSigner::where('document_id', $docId)
            ->whereIn('staff_id', $userStaffIds)
            ->get();
        if (!count($docSigners)) {
            return 0;
        } else {
            $employee = Auth::user()->employee;
            $document = Document::find($docId);
            foreach ($docSigners as $key => $docSigner) {
                $docSigner->status = 6;
                $docSigner->signer_employee_id = $employee->id;
                $docSigner->fio = $employee->getShortname($document->locale);
                $docSigner->save();
            }
            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $docSigners[0]->id;
            $documentSignerEvent->action_type_id = $docSigners[0]->action_type_id;

            $documentSignerEvent->comment = $assignment;
            $documentSignerEvent->status = 5;
            $documentSignerEvent->signer_employee_id = $docSigners[0]->signer_employee_id;
            $documentSignerEvent->fio = $docSigners[0]->fio;
            $documentSignerEvent->save();
        }
        return "SuccessFully saved!!!";
    }
    public function sendStatusToTengeBank($id, $status)
    {
        $docs = [];
        if ($status == 4 || $status == 5) {
            $relationDocuments = DocumentRelation::select('document_id')->where('parent_document_id', $id)->get();
            foreach ($relationDocuments as $key => $value) {
                $document = Document::find($value->document_id);
                $document->status = 5;
                $document->save();
                array_push($docs, ['document_id' => $value->document_id, 'document_status' => $status]);
            }
        } else {
            // $document = Document::find($id);
            // $document->status = 5;
            // $document->save();
            array_push($docs, ['document_id' => $id, 'document_status' => $status]);
        }
        $response = Http::post('https://tenge.uzautomotors.com/api/signed-documents', $docs);
        return $response->body();
    }
}
