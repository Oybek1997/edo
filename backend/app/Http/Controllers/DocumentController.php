<?php

namespace App\Http\Controllers;

use App\AS400\Z01ptpf;
use App\AS400\Z500ptpf;
use App\AS400\Z502ptpf;
use App\Document as AppDocument;
use App\Http\Models\AccessDepartment;
use App\Http\Models\EmployeeDepartmentPosition;
use Illuminate\Support\Carbon;
use App\Http\Models\Graphic;
use App\Http\Models\DocumentStaff;
use App\Http\Models\UnblockedUser;
use App\Http\Models\DocumentDetailFakt;
use App\Http\Models\ActionType;
use App\Http\Models\CancelledDocument;
use App\Http\Models\Department;
use App\Http\Models\OtgulDate;
use App\Http\Models\ComplaensAnswer;
use App\Http\Models\ComplaensRelative;
use App\Http\Models\Directory;
use App\Http\Models\DocumentBookmark;
use App\Http\Models\DocumentBlankTemplate;
use App\Http\Models\DocumentControlPunkt;
use App\Http\Models\DocumentType;
use App\Http\Models\TariffScale;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailAttribute;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailCoefficient;
use App\Http\Models\KpiObject;
use App\Http\Models\DocumentRelation;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\DocumentDetailEmployee;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\Employee;
use App\Http\Models\Staff;
use App\Http\Models\File;
use App\Http\Models\UserTemplate;
use App\Http\Models\Notification;
use App\Http\Models\TableList;
use App\Http\Models\ActDate;
// use App\Http\Models\MaterialResponsiblePeople;
use App\User;
use App\MailQueue;
use App\Services\SendEmail;
use App\WorkCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use League\CommonMark\Block\Element\Document as ElementDocument;
use GuzzleHttp\Client as GuzzleClient;
use \DateTime;
use App\Http\Models\KpiResolutionComission;
use App\Http\Models\MaterialResponsiblePeople;

class DocumentController extends Controller
{
    public function changeRegData(Request $request)
    {
        $document_id = $request->input('document_id');
        $reg_number = $request->input('reg_number');
        $reg_date = $request->input('reg_date');
        $document = Document::find($document_id);
        if ($document) {
            $document->document_number_reg = $reg_number;
            $document->document_date_reg = $reg_date;
            $document->save();
            Document::savePdf($document_id);
        }
        if ($document->document_template_id == 587) {
            $sana = null;
            $modda = null;
            $tabel = Employee::find($document->documentDetails[0]->documentDetailEmployees[0]->employee_id)->tabel;
            foreach ($document->documentDetails[0]->documentDetailContents as $key => $value) {
                if ($value->d_d_attribute_id == 2515) {
                    $sana = $value->value;
                } else if ($value->d_d_attribute_id == 2516) {
                    $modda = $value->value;
                }
            }

            $response = Http::withoutVerifying()
                ->post('https://edo-db2.uzautomotors.com/api/as400/employee-leaving', [
                    'tabel' => $tabel,
                    'sana' => $sana,
                    'modda' => $modda,
                    'document_number' => $document->document_number_reg,
                    'document_date' => $document->document_date_reg,
                ]);
            if (!$response[0])
                return $response;
        }
        return $request->all();
    }

    public function returnDocument(Request $request)
    {
        $document_id = $request->input('document_id');
        $message = $request->input('message');
        $document = Document::find($document_id);
        if ($document && ($document->status == 1 || $document->status == 2) && in_array($document->document_template_id, [25, 214, 357, 71, 305, 333, 218])) {
            $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
            $signer = DocumentSigner::where('document_id', $document_id)
                ->whereIn('staff_id', $userStaffIds)
                ->whereIn('status', [0, 3, 4])
                ->whereNotNull('taken_datetime')
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->first();


            if ($document_id > 0) {
                DocumentSigner::where('document_id', $document_id)->where('action_type_id', '<>', 6)->limit(10)->update(['status' => 0, 'taken_datetime' => null, 'due_date' => null, 'signed_date' => null]);

                $documentSignerEvent = new DocumentSignerEvent;
                $documentSignerEvent->document_signer_id = $signer->id;
                $documentSignerEvent->action_type_id = $signer->action_type_id;

                $documentSignerEvent->comment = "Ushbu xujjat menga taluqli emas. " . $message;
                $documentSignerEvent->status = 4;
                $documentSignerEvent->signer_employee_id = Auth::user()->employee_id;
                $documentSignerEvent->fio = $signer->fio;
                $documentSignerEvent->save();
                return $documentSignerEvent;
            }

            $document->status = 0;
            $document->save();
            Document::savePdf($document_id);
        }
        return $request->all();
    }

    public function meningHujjatimEmas(Request $request)
    {
        $document_id = $request->input('document_id');
        $message = $request->input('message');
        $document = Document::find($document_id);
        if ($document && ($document->status == 1 || $document->status == 2) && in_array($document->document_template_id, [1, 25, 214, 357, 71, 305, 333, 218])) {
            $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
            $signer = DocumentSigner::where('document_id', $document_id)
                ->whereIn('staff_id', $userStaffIds)
                ->whereIn('status', [0, 3, 4])
                ->whereNotNull('taken_datetime')
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->first();

            if ($document_id > 0) {
                $signer->signer_employee_id = Auth::user()->employee_id;
                $signer->fio = Auth::user()->employee->getShortname($document->locale);
                $signer->save();
                DocumentSigner::where('document_id', $document_id)->where('action_type_id', '<>', 6)->limit(10)->update(['status' => 0, 'taken_datetime' => null, 'due_date' => null, 'signed_date' => null]);

                $documentSignerEvent = new DocumentSignerEvent;
                $documentSignerEvent->document_signer_id = $signer->id;
                $documentSignerEvent->action_type_id = $signer->action_type_id;

                $documentSignerEvent->comment = "Ushbu xujjat menga taluqli emas. " . $message;
                $documentSignerEvent->status = 4;
                $documentSignerEvent->signer_employee_id = Auth::user()->employee_id;
                $documentSignerEvent->fio = $signer->fio;
                $documentSignerEvent->save();
            }
            $document->status = 0;
            $document->save();
            Document::savePdf($document_id);
            return 'Successfully saved.';
        }
        return $request->all();
    }

    public function addSigner(Request $request)
    {
        $document_id = $request->input('document_id');
        $department_id = $request->input('department_id');
        $action_type_id = $request->input('action_type_id');
        $due_date = $request->input('due_date');
        $user = User::find(Auth::id());
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $department = Department::where('id', $department_id)->with('managerStaff.employeeMainStaff')->first();
        $document_signer = DocumentSigner::where(function ($q) {
            $q->whereNotNull('signer_employee_id')
                ->where('signer_employee_id', Auth::user()->employee_id)
                ->orWhereNull('signer_employee_id');
        })
            ->where('sequence', 0)
            ->whereIn('staff_id', $userStaffIds)
            ->where('document_id', $document_id)->first();

        DB::beginTransaction();
        try {
            if (!$document_signer) {
                $document_signer = new DocumentSigner();
                $new_signer = EmployeeStaff::where('employee_id', $user->employee_id)->where('is_main_staff', 1)->first();
                $document_signer->document_id = $document_id;
                $document_signer->staff_id = $new_signer->staff_id;
                $document_signer->taken_datetime = date('Y-m-d H:i:s');
                $document_signer->action_type_id = 4;
                $document_signer->due_date = date('Y-m-d H:i:s', time() + 86400);
                $document_signer->sequence = 0;
                $document_signer->sign_type = 1;
            }
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $employee = Auth::user()->employee;
            $document = Document::find($document_id);
            $document_signer->department = $employee->staff[0]->department['name_' . $document->locale];
            $document_signer->position = $employee->staff[0]->position['name_' . $document->locale];
            $document_signer->fio = $employee->getShortname($document->locale);
            $document_signer->is_done = 2;
            $document_signer->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = $document_signer->action_type_id;
            $document_signer_event->status = 6;
            $document_signer_event->comment = $action_type_id == 4 ? $department->name_uz_cyril . 'га ижро учун юборди' : $department->name_uz_cyril . 'га маълумот учун юборди';
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            // $document_signer_event = DocumentSignerEvent::where('document_signer_id', $document_signer->id)->where('action_type_id', 8)->first();
            // if (!$document_signer_event && $action_type_id == 4) {
            //     $document_signer_event = new DocumentSignerEvent();
            //     $document_signer_event->document_signer_id = $document_signer->id;
            //     $document_signer_event->action_type_id = $document_signer->action_type_id;
            //     $document_signer_event->comment = 'processing';
            //     $document_signer_event->status = 3;
            //     $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            //     $document_signer_event->fio = $document_signer->fio;
            //     $document_signer_event->save();
            // }

            $new_document_signer = DocumentSigner::where('staff_id', $department->manager_staff_id)
                ->where('sequence', 0)
                ->where('document_id', $document_id)->first();
            if (!$new_document_signer) {
                $new_document_signer = new DocumentSigner();
                $new_document_signer->document_id = $document_id;
                $new_document_signer->staff_id = $department->manager_staff_id;
                $new_document_signer->taken_datetime = date('Y-m-d H:i:s');
                $new_document_signer->action_type_id = $action_type_id;
                $new_document_signer->due_date = date('Y-m-d H:i:s', time() + $due_date * 3600);
                $new_document_signer->sequence = 0;
                $new_document_signer->status = 0;
                if ($action_type_id == 5) {
                    $new_document_signer->status = 1;
                }

                if ($action_type_id == 4) {
                    $new_document_signer->parent_employee_id = $document_signer->signer_employee_id;
                    $new_document_signer->assignment = 'ижро учун юборди';
                }
                $new_document_signer->signer_employee_id = $department->managerStaff->employeeMainStaff->employee_id;
                $employee = $department->managerStaff->employeeMainStaff->employee;
                $document = Document::find($document_id);
                $count = $document->locale == 'uz_latin' ? 1 : 2;
                $new_document_signer->department = $department['name_' . $document->locale];
                $new_document_signer->position = $department->managerStaff->position['name_' . $document->locale];
                $new_document_signer->fio = $employee->getShortname($document->locale);
                $new_document_signer->sign_type = 1;
                $new_document_signer->save();

                // if ($action_type_id == 4) {
                //     $documentSignerEvent = new DocumentSignerEvent;
                //     $documentSignerEvent->document_signer_id = $new_document_signer->id;
                //     $documentSignerEvent->action_type_id = $new_document_signer->action_type_id;
                //     $documentSignerEvent->status = 7;
                //     $documentSignerEvent->comment = 'ижро учун юборди';
                //     $documentSignerEvent->save();
                // }
                $employeeStaffs = EmployeeStaff::where('staff_id', $new_document_signer->staff_id)->where('is_active', 1)->get();
                $actionType = "Ko'rib chiqish";

                foreach ($employeeStaffs as $key => $employeeStaff) {
                    $send_user = User::where('employee_id', $employeeStaff->employee_id)->first();
                    $document = Document::where('id', $document_id)->with('documentTemplate')->with('documentType')->first();
                    // if ($send_user && $send_user->email) {
                    //     $reaction_type = $actionType;
                    //     SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                    // }
                }
            } else {
                return 'there is a database';
            }
            // return $user->employee_id;
            if (DocumentSigner::where('parent_employee_id', $user->employee_id)->where('document_id', $document_id)->whereIn('status', [0, 3])->count() || $action_type_id == 4 || $action_type_id == 11) {
                $document_signer->status = 3;
            } else {
                $document_signer->status = 1;
            }
            $document_signer->save();

            DB::commit();
            return 'Successfully saved!';
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        // return Auth::user();
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $documents = Document::with('employee')
            ->with('documentTemplate')
            ->where('status', '>=', 3)
            ->where('status', '<', 6);
        if (isset($search)) {
            $documents->where(function ($query) use ($search) {
                return $query
                    ->where('document_number', 'ilike', "%" . $search . "%")
                    ->orWhere('document_number_reg', 'ilike', "%" . $search . "%");
            });
        }
        return $documents->orderBy('documents.document_date', 'desc')->paginate(20);
    }

    public function indexFilterMobile(Request $request)
    {
        $filter = $request->input('filter');
        $filter['reaction_status'] = array_merge($filter['reaction_status'], [5, 6]);
        $created_by = isset($filter['created_by']) ? $filter['created_by'] : 0;
        $content = isset($filter['content']) ? $filter['content'] : 0;
        $summary = isset($filter['summary']) ? $filter['summary'] : 0;
        $korrespondent = isset($filter['korrespondent']) ? $filter['korrespondent'] : 0;
        $registration = isset($filter['registration']) ? $filter['registration'] : 0;
        $type = isset($filter['type']) ? $filter['type'] : 0;
        $send_by = isset($filter['send_by']) ? $filter['send_by'] : 0;
        $receive_by = isset($filter['receive_by']) ? $filter['receive_by'] : 0;
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());

        $documents = Document::select(
            'documents.id',
            'documents.department_id'
        )->where('documents.id', '=', 1);
        return $documents;
    }

    public function indexFilter(Request $request)
    {
        $filter = $request->input('filter');
        $filter['reaction_status'] = array_merge($filter['reaction_status'], [5, 6]);
        $created_by = isset($filter['created_by']) ? $filter['created_by'] : 0;
        $content = isset($filter['content']) ? $filter['content'] : null;
        $barn = isset($filter['barn']) ? $filter['barn'] : null;
        $yearmonth = isset($filter['yearmonth']) ? $filter['yearmonth'] : null;
        $content_table = isset($filter['content_table']) ? $filter['content_table'] : null;
        $from_department = isset($filter['from_department']) ? $filter['from_department'] : 0;
        $summary = isset($filter['summary']) ? $filter['summary'] : 0;
        $branchs = isset($filter['branchs']) ? $filter['branchs'] : 0;
        $korrespondent = isset($filter['korrespondent']) ? $filter['korrespondent'] : 0;
        $organization = isset($filter['organization']) ? $filter['organization'] : 0;
        $expence_name = isset($filter['expence_name']) ? $filter['expence_name'] : 0;
        $contract_number = isset($filter['contract_number']) ? $filter['contract_number'] : 0;
        $approval_sheet = isset($filter['approval_sheet']) ? $filter['approval_sheet'] : 0;
        $registration = isset($filter['registration']) ? $filter['registration'] : 0;
        $registration_uz_as = isset($filter['registration_uz_as']) ? $filter['registration_uz_as'] : 0;
        $fio = isset($filter['fio']) ? $filter['fio'] : 0;
        $appeal = isset($filter['appeal']) ? $filter['appeal'] : 0;
        $partners_name = isset($filter['partners_name']) ? $filter['partners_name'] : 0;
        $currency = isset($filter['currency']) ? $filter['currency'] : 0;
        $model = isset($filter['model']) ? $filter['model'] : 0;
        $region = isset($filter['region']) ? $filter['region'] : 0;
        $type = isset($filter['type']) ? $filter['type'] : 0;
        $document_type = isset($filter['document_type']) ? $filter['document_type'] : 0;
        $company_name = isset($filter['company_name']) ? $filter['company_name'] : 0;
        $send_by = isset($filter['send_by']) ? $filter['send_by'] : 0;
        $receive_by = isset($filter['receive_by']) ? $filter['receive_by'] : 0;
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        if (isset($filter['staff_id']) && $filter['staff_id']) {
            $userStaffIds = [$filter['staff_id']];
        } else {
            $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        }
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $documents = Document::select(
            'id',
            'department_id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_date_reg',
            'document_number',
            'document_number_reg',
            'status',
            'created_employee_id',
            'base64',
            'pdf_file_name',
            'from_department',
            'from_manager',
            'to_department',
            'to_manager',
            'title',
            'restore'
        );

        $documents->with(['documentType' => function ($q) use ($locale) {
            $q->select(
                'id',
                'name_' . $locale
            );
        }])
            ->with('documentTemplate:id,name_' . $locale)
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
                                    }])
                                    ->with(['position' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_' . $locale
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
            }])
            ->with('documentDetails:id,document_id')
            ->with('actDate')
            ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id', 'is_done')
                    ->whereNotNull('taken_datetime')
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

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if ($created_by) {
            $fioSearch = explode(" ", $created_by, 2);
            $lastname = $fioSearch[0];
            $firstname = '';
            $middlename = '';
            if (isset($fioSearch[1])) {
                $firstname = $fioSearch[1];
            }
            if (isset($fioSearch[2])) {
                $middlename = $fioSearch[2];
            }
            $documents->whereHas('employee', function (Builder $query) use ($firstname, $lastname, $middlename) {
                $query->select('id')->where('firstname_uz_latin', 'ilike', '%' . $firstname . '%')
                    ->where('lastname_uz_latin', 'ilike', '%' . $lastname . '%')
                    ->where('middlename_uz_latin', 'ilike', '%' . $middlename . '%')
                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $firstname . '%')
                    ->where('lastname_uz_cyril', 'ilike', '%' . $lastname . '%')
                    ->where('middlename_uz_cyril', 'ilike', '%' . $middlename . '%')
                    ->orWhere('firstname_ru', 'ilike', '%' . $firstname . '%')
                    ->where('lastname_ru', 'ilike', '%' . $lastname . '%')
                    ->where('middlename_ru', 'ilike', '%' . $middlename . '%');
            });
        }

        if ($send_by) {
            $documents->where(function ($q) use ($send_by) {
                $q->where('from_department', 'ilike', '%' . $send_by . '%')
                    ->orWhere('from_manager', 'ilike', '%' . $send_by . '%');
            });
        }

        if ($receive_by) {
            $documents->where(function ($q) use ($receive_by) {
                $q->where('to_department', 'ilike', '%' . $receive_by . '%')
                    ->orWhere('to_manager', 'ilike', '%' . $receive_by . '%');
            });
        }

        // $documents->where('document_date', '>','2023-01-01 00:00:00');

        if ($content) {
            // dd($content);
            $documents->whereHas('documentDetails', function ($q) use ($content) {
                //$q->whereRaw("MATCH (content) AGAINST ('+*" . $content . "*' IN BOOLEAN MODE)");
                $q->where("content", "ilike", "%" . $content . "%");
            });
        }

        if ($content_table) {
            $ddav = DocumentDetailAttributeValue::select('document_detail_id')
                //->whereRaw("MATCH (attribute_value) AGAINST ('+*" . $content_table . "*' IN BOOLEAN MODE)")
                ->where('attribute_value', 'ilike', '%' . $content_table . '%')
                // ->limit(100)
                ->get()
                ->pluck('document_detail_id');
            $documents->whereHas('documentDetails', function ($q) use ($content, $ddav) {
                $q->whereIn('id', $ddav);
            });
        }

        if ($filter['document_start_date']) {
            $ids = collect(Auth::user()->employee->staff)->pluck('id');
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        if (isset($filter['tabel']) && strlen($filter['tabel']) == 4) {
            $emp = Employee::select('id')
                //$emp = Employee::select('id')->whereRaw("MATCH (tabel) AGAINST ('+*" . $filter['tabel'] . "*' IN BOOLEAN MODE)")->first();

                ->where("tabel", 'ilike', '%' . $filter['tabel'] . '%')
                ->first();
            if ($emp) {
                $documents->whereHas('documentDetails', function ($q) use ($emp) {
                    $q->whereHas('documentDetailEmployees', function ($q1) use ($emp) {
                        $q1->where('employee_id', $emp->id * 1);
                    });
                });
            }
        }

        if (
            isset($filter['document_type_id']) &&
            $filter['document_type_id'] &&
            $filter['document_type_id'] != 'uzauto' &&
            $filter['document_type_id'] != 'employee' &&
            $filter['document_type_id'] != 'staff'
        ) {
            $documents->where('document_type_id', $filter['document_type_id']);
        }

        if (isset($filter['document_template_id']) && $filter['document_template_id']) {
            $documents->where('document_template_id', $filter['document_template_id']);
        }

        if (isset($filter['id']) && $filter['id']) {
            $documents->where('id', $filter['id']);
        }

        if (isset($filter['title']) && $filter['title']) {
            $documents->where('title', 'ilike', '%' . $filter['title'] . '%');
        }
        if (isset($from_department) && $from_department) {
            $documents->where('from_department', 'ilike', '%' . $from_department . '%');
        }

        $act_date = isset($filter['act_date']) ? $filter['act_date'] : null;
        if (isset($act_date) && $act_date) {
            $documents->whereHas('actDate', function ($q) use ($act_date) {
                $q->where('act_date', $act_date);
            });
        }

        if ($filter['document_number']) {
            $documents->where('document_number_reg', 'ilike', '%' . $filter['document_number'] . '%');
            $documents->orWhere(function ($q) use ($filter) {
                $q->whereNull('document_number_reg');
                $q->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
            });
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            if ($filter['pending_action'] == 'empty') {
                $documents->whereDoesntHave('documentSigners', function ($q) {
                    $q->select('id')->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3]);
                });
            } else {
                $search = $filter['pending_action'];
                $documents->whereHas('documentSigners', function ($q) use ($search) {
                    $q->select('id')->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3])
                        ->whereHas('employeeStaffs', function ($q2) use ($search) {
                            $q2->select('id')->whereHas('employee', function ($q3) use ($search) {
                                $q3->select('id')->where('firstname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_cyril', 'ilike', '%' . $search . '%');
                            });
                        });
                });
            }
        }
        // if(Auth::user()->username == 'qg9592'){}
        //  else 
        if ($filter['menu_item'] == 'all') {
            // dd(Auth::user()->hasPermission('all-document-show'));
            if (!Auth::user()->hasPermission('all-document-show')) {
                $user_id = Auth::id();
                $documents->whereHas('DocumentTemplate', function ($q) use ($user_id) {
                    $q->whereHas('UserTemplates', function ($q1) use ($user_id) {
                        $q1->where('user_id', $user_id);
                    });
                });
            }
            $documents->whereNotIn('status', [0, 6, 7]);
        } elseif ($filter['menu_item'] == 'inbox') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', '!=', 3)
                    ->where('action_type_id', '!=', 6)
                    ->where(function ($q) use ($user) {
                        return $q->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->whereIn('status', $filter['reaction_status'])
                    ->where(function ($q) use ($filter) {
                        $q->whereIn('status', [5, 6])
                            ->orWhereNotNull('taken_datetime');
                    });
            })
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }])
                ->wherenotIn('status', [0, 6]);
        } elseif ($filter['menu_item'] == 'outbox') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->where('action_type_id', 3)
                            ->orWhere('action_type_id', 6);
                    })
                    ->where(function ($q) use ($user) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->whereIn('status', $filter['reaction_status'])
                    ->where(function ($q) use ($filter) {
                        $q->whereIn('status', [5, 6])
                            ->orWhereNotNull('taken_datetime');
                    });
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'nazorat') {
            $documents
                ->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->whereIn('staff_id', $userStaffIds)
                        ->where('action_type_id', 11)
                        ->whereIn('status', [0, 3, 4])
                        ->orWhereHas('parentNazorat')
                        ->whereIn('staff_id', $userStaffIds)
                        ->where('action_type_id', 4)
                        ->whereIn('status', [0, 3, 4]);
                })
                ->whereNotIn('status', [0, 4, 5, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'in_out') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    // ->whereIn('status', $filter['reaction_status'])
                    ->where(function ($q) use ($filter) {
                        $q->whereIn('status', [5, 6])
                            ->orWhereNotNull('taken_datetime');
                    });
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'draft') {
            $documents->where('created_employee_id', $user->employee_id)->where('status', 0);
        } elseif ($filter['menu_item'] == 'kpi_plan_doc') {
            $documents->where('created_employee_id', $user->employee_id)
                ->whereIn('document_template_id', [431])
                ->whereIn('status', [0, 7, 9]);
        } elseif ($filter['menu_item'] == 'cancel') {
            if ($filter['document_type_id'] == 'uzauto' && Auth::user()->hasPermission('okd_kanselyariya')) {
                $documents->whereHas('documentSigners', function ($q) {
                    $q->select('id')->where('staff_id', 1)
                        ->where('status', 2);
                })->where('status', 9);
            } else {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->select('id')
                        // ->whereIn('staff_id', $userStaffIds)
                        ->where('signer_employee_id', $user->employee_id);
                })
                    ->whereHas('documentSigners', function ($query) {
                        $query->where('status', 2)
                            ->whereNull('parent_employee_id')
                            // ->where('updated_at', '>', date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month')))
                        ;
                    })
                    ->where('status', '=', 6);
            }
        } elseif ($filter['menu_item'] == 'notification') {
            $documents->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotIn('action_type_id', [4, 11])
                    ->whereNotNull('taken_datetime')
                    // ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->where('status', 0);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'znz') {
            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentSigners', function ($q) {
                    $q->where('staff_id', 1753)
                        ->where('action_type_id', 4);
                })
                ->whereIn('document_template_id', [173, 174]);
        } elseif ($filter['menu_item'] == 'akt') {
            // return count($request->input('pagination')['sortBy']);
            if (count($request->input('pagination')['sortBy']) > 0) {
                $documents
                    ->whereNotIn('status', [0])
                    // ->whereHas('documentSigners', function ($q) {
                    //     $q->where('staff_id', 1753)
                    //         ->where('action_type_id', 4);
                    // })
                    ->where('document_type_id', 12)
                    ->where('document_template_id', '>', 487)
                    ->orderBy('id', 'desc');
            } else {
                $documents
                    ->whereNotIn('status', [0])
                    // ->whereHas('documentSigners', function ($q) {
                    //     $q->where('staff_id', 1753)
                    //         ->where('action_type_id', 4);
                    // })
                    ->where('document_type_id', 12)
                    ->where('document_template_id', '>', 487)
                    ->orderBy('updated_at', 'desc');
            }
        } elseif ($filter['menu_item'] == 'akt-cancel') {
            $documents
                ->where('status', 0)
                ->whereHas('documentSigners', function ($q) {
                    $q->where('sequence', 98)
                        ->where('action_type_id', 12)
                        ->whereNull('taken_datetime')
                        ->whereNotNull('due_date');
                })
                ->where('document_type_id', 12)
                ->where('document_template_id', '>', 487)
                ->orderBy('id', 'desc');
        } elseif ($filter['menu_item'] == 'lavozim-y') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 57);
                });
            // sardor
        } elseif ($filter['menu_item'] == 'lavozim-y-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 57);
                });
            // sardor
        } elseif ($filter['menu_item'] == 'payment_sheet') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [263, 397]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'compliance_incoming') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    // $q->whereIn('id', [357]);
                    $q->whereIn('id', [71, 305, 357, 214]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'compliance_report') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    // $q->whereIn('id', [357]);
                    $q->whereIn('id', [71, 305, 357, 214]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'compliance_restr_one') {
            $documents
                ->with(['compleansAnswer' => function ($q) {
                    $q->select('document_id', 'employee_id', 'question_id', 'answer');
                    $q->whereIn('question_id', [9, 30]);
                }])
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [615, 622]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'compliance_restr_two') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [619]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'compliance_restr_three') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [615, 622]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'lsp') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [57, 66, 83, 86, 90, 95, 97, 98, 102, 117, 118, 121, 133, 140, 150, 159, 171, 189, 209, 230, 240, 252]);
                })
                // -with(['department' => function ($q) {
                //     $q ->select('id', 'name_uz_latin')
                //     ->where('name_uz_latin', 'Komplayens xizmati');
                // }])                 
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'guest') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [558]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }])
                        ->with(['documentDetailSignerAttributes' => function ($q1) {
                            $q1->select(
                                'id',
                                'd_d_attribute_id',
                                'document_detail_id',
                                'staff_id',
                                'emloyee_id',
                                'value'
                            )->with(['documentDetailAttributes' => function ($q2) {
                                $q2->select(
                                    'id',
                                    'attribute_name_ru',
                                    'attribute_name_uz_cyril',
                                    'attribute_name_uz_latin'
                                );
                            }]);
                        }]);
                }]);
            $documents->whereHas('documentDetails', function ($dd) use ($filter) {
                if (isset($filter['fio']) && $filter['fio']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2377)
                            ->where('value', 'ilike', '%' . $filter['fio'] . '%');
                    });
                }
                if (isset($filter['company']) && $filter['company']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2378)
                            ->where('value', 'ilike', '%' . $filter['company'] . '%');
                    });
                }
                if (isset($filter['position']) && $filter['position']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2379)
                            ->where('value', 'ilike', '%' . $filter['position'] . '%');
                    });
                }
                if (isset($filter['purpose']) && $filter['purpose']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2380)
                            ->where('value', 'ilike', '%' . $filter['purpose'] . '%');
                    });
                }
                if (isset($filter['from']) && $filter['from']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2381)
                            ->where('value', 'ilike', '%' . $filter['from'] . '%');
                    });
                }
                if (isset($filter['to']) && $filter['to']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2382)
                            ->where('value', 'ilike', '%' . $filter['to'] . '%');
                    });
                }
                if (isset($filter['document']) && $filter['document']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2383)
                            ->where('value', 'ilike', '%' . $filter['document'] . '%');
                    });
                }
                if (isset($filter['passport_series']) && $filter['passport_series']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2384)
                            ->where('value', 'ilike', '%' . $filter['passport_series'] . '%');
                    });
                }
                if (isset($filter['passport_number']) && $filter['passport_number']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2385)
                            ->where('value', 'ilike', '%' . $filter['passport_number'] . '%');
                    });
                }
                if (isset($filter['passport_given_by']) && $filter['passport_given_by']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2386)
                            ->where('value', 'ilike', '%' . $filter['passport_given_by'] . '%');
                    });
                }
                if (isset($filter['passport_created_at']) && $filter['passport_created_at']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2387)
                            ->where('value', 'ilike', '%' . $filter['passport_created_at'] . '%');
                    });
                }
                if (isset($filter['responsible']) && $filter['responsible']) {
                    $dd->whereHas('documentDetailContents', function ($ddc) use ($filter) {
                        $ddc->where('d_d_attribute_id', 2388)
                            ->where('value', 'ilike', '%' . $filter['responsible'] . '%');
                    });
                }
            });
            if (isset($filter['status']) && $filter['status']) {
                $documents->where('status', $filter['status']);
            }
        } elseif ($filter['menu_item'] == 'murojaat') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [214, 357]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'murojaat_nazorat') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [214, 357]);
                })
                ->whereHas('documentSigners', function ($q) {
                    $q->where('action_type_id', 11);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'kiruvchi') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [71, 305]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'kiruvchi_nazorat') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [71, 305]);
                })->whereHas('documentSigners', function ($q) {
                    $q->where('action_type_id', 11);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'report_material') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [625]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'buyruq') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [70, 190]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
                        }]);
                }]);
        } elseif ($filter['menu_item'] == 'chiquvchi') {
            $documents
                ->whereIn('status', [1, 2, 3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('id', [157, 264]);
                })
                ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                        ->whereNotNull('taken_datetime')
                        ->with(['signerEmployee' => function ($q2) use ($lang) {
                            $q2->select(
                                'id',
                                'lastname_' . $lang,
                                'middlename_' . $lang,
                                'firstname_' . $lang,
                                'tabel'
                            );
                        }])
                        ->with(['comments' => function ($comment)  use ($locale) {
                            $comment
                                ->select('id', 'document_signer_id', 'comment');
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
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
            // sardor
        } elseif ($filter['menu_item'] == 'kasbiy-y') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 59);
                });
        } elseif ($filter['menu_item'] == 'kasbiy-y-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 59);
                });
        } elseif ($filter['menu_item'] == 'standard') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [244, 247, 248, 394]);
                });
        } elseif ($filter['menu_item'] == 'standard-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [244, 247, 248, 394]);
                });
        } elseif ($filter['menu_item'] == 'karta-p') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [390, 391]);
                });
        } elseif ($filter['menu_item'] == 'karta-p-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [390, 391]);
                });
        } elseif ($filter['menu_item'] == 'risk') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [388, 389]);
                });
        } elseif ($filter['menu_item'] == 'risk-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->whereIn('document_template_id', [388, 389]);
                });
        } elseif ($filter['menu_item'] == 'annulirovan') {

            $documents
                ->whereIn('status', [8])
                // ->whereHas('documentTemplate', function ($q) {
                //     $q->where('document_type_id', 59);
                // })
            ;
        } elseif ($filter['menu_item'] == 'tarkibiy-t') {

            $documents
                ->whereIn('status', [3, 4, 5])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 58);
                });
        } elseif ($filter['menu_item'] == 'tarkibiy-t-cancel') {

            $documents
                ->whereIn('status', [6])
                ->whereHas('documentTemplate', function ($q) {
                    $q->where('document_type_id', 58);
                });
        } elseif ($filter['menu_item'] == 'processing') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->select('id')->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 0);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'substantiate') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->select('id')->where('signer_employee_id', $user->employee_id)
                    ->where('status', 4)
                    ->whereIn('is_done', [0, 1]);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'resolutions') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->select('id')->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 2);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'resolution_results') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->select('id')->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 1);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'expired') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '<', date("Y-m-d H:i:s"))
                    ->where('is_done', '<>', 2)
                    ->whereIn('status', [0, 3]);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'mehmon') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotNull('taken_datetime')
                    ->where('action_type_id', 4)
                    ->where('sequence', 0)
                    ->whereIn('status', [0, 3]);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'executor') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where('action_type_id', 4)
                    ->where('is_done', 0)
                    ->whereNotNull('taken_datetime')
                    ->where('status', 0);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'watcher') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->where('action_type_id', 11)
                    ->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3, 4]);
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'allhr') {
            $documents->where('status', '!=', 0)
                ->where('department_id', 14);
        } elseif ($filter['menu_item'] == 'alllsp') {
            $documents->where('status', '!=', 0)
                ->where('document_type_id', 7);
        } elseif ($filter['menu_item'] == 'allznz') {
            $documents->where('status', '!=', 0)
                ->where('document_template_id', 41);
        } elseif ($filter['menu_item'] == 'archive') {
            if ($filter['document_type_id'] == 'staff') {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds);
                })->where('status', '!=', 0);
            } elseif ($filter['document_type_id'] == 'employee') {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->select('id')->where('signer_employee_id', $user->employee_id);
                })->where('status', '!=', 0);
                if (isset($filter['staff_id']) && $filter['staff_id']) {
                    $documents->whereHas('documentSigners', function ($q) use ($filter) {
                        $q->where('staff_id', $filter['staff_id']);
                    });
                }
            }
        } elseif ($filter['menu_item'] == 'information') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds)
                        ->where('action_type_id', 5);
                })
                    ->whereHas('documentSigners', function ($q) {
                        $q->select('id')->whereIn('status', [0, 3, 4]);
                    });
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'document_out_three') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')
                        // ->whereIn('signer_group_id', [21, 22, 23, 24, 143, 161, 241, 274, 275, 278])
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [date('Y-m-d', strtotime('+3 days')), date('Y-m-d', strtotime('+4 days'))])
                        ->whereIn('status', [0, 3]);
                });
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'document_out_two') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')
                        // ->whereIn('signer_group_id', [21, 22, 23, 24, 143, 161, 241, 274, 275, 278])
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [date('Y-m-d', strtotime('+2 days')), date('Y-m-d', strtotime('+3 days'))])
                        ->whereIn('status', [0, 3]);
                });
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'document_out_one') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')
                        // ->whereIn('signer_group_id', [21, 22, 23, 24, 143, 161, 241, 274, 275, 278])
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [date('Y-m-d'), date('Y-m-d', strtotime('+2 days'))])
                        ->whereIn('status', [0, 3]);
                });
            })
                ->whereNotIn('status', [0, 6])
                ->where('created_at', '>', '2023-01-01 00:00:01');
        } elseif ($filter['menu_item'] == 'canceled') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds);
                })
                    ->whereHas('documentSigners', function (Builder $query) {
                        $query->where('status', 2)
                            ->whereNull('parent_employee_id')
                            ->where('updated_at', '>', date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month')));
                    })
                    ->whereDoesntHave('CancelledDocument', function ($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->whereHas('documentSigners', function ($q) {
                        $q->select('id')
                            ->where('signer_employee_id', Auth::user()->employee_id);
                    });
            })
                ->where('status', 6)
                ->where('created_at', '>', '2024-01-01 00:00:01')
                ->orderBy('document_date', 'desc');
        } elseif ($filter['menu_item'] == 'agreement') {
            $documents->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds)
                        ->where('status', 5);
                })->orWhere(function ($q1) {
                    $q1->whereDoesntHave('documentSigners', function ($q2) {
                        $q2->where('status', 5);
                    })->where('created_employee_id', Auth::user()->employee_id);
                });
            })
                ->where('status', 7);
        } elseif ($filter['menu_item'] == 'star') {
            $ids = collect(DocumentBookmark::where('user_id', Auth::id())->get())->pluck('document_id');
            $documents->whereIn('documents.id', $ids);
        } else {
            return [];
        }

        // if(count($filter['reaction_status']) < 7){
        //     $documents->whereHas('documentSigners', function ($q) use ($filter) {
        //         $q->whereIn('status', $filter['reaction_status']);
        //     });
        // }

        if ($korrespondent) {
            $documents->where(function ($q) use ($korrespondent) {
                $q->whereHas('documentDetails', function ($q1) use ($korrespondent) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($korrespondent) {
                        $q2->where('value', 'ilike', '%' . $korrespondent . '%');
                    });
                });
            });
        }
        if ($branchs) {
            $documents->whereHas('documentTemplate', function ($q) use ($branchs) {
                $q->where('id', $branchs);
            });
        }
        if ($organization) {
            $documents->where(function ($q) use ($organization) {
                $q->whereHas('documentDetails', function ($q1) use ($organization) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($organization) {
                        $q2->where('value', 'ilike', '%' . $organization . '%');
                    });
                });
            });
        }
        if ($expence_name) {
            $documents->where(function ($q) use ($expence_name) {
                $q->whereHas('documentDetails', function ($q1) use ($expence_name) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($expence_name) {
                        $q2->where('value', 'ilike', '%' . $expence_name . '%');
                    });
                });
            });
        }
        if ($contract_number) {
            $documents->where(function ($q) use ($contract_number) {
                $q->whereHas('documentDetails', function ($q1) use ($contract_number) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($contract_number) {
                        $q2->where('value', 'ilike', '%' . $contract_number . '%');
                    });
                });
            });
        }
        if ($approval_sheet) {
            $documents->where(function ($q) use ($approval_sheet) {
                $q->whereHas('documentDetails', function ($q1) use ($approval_sheet) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($approval_sheet) {
                        $q2->where('value', 'ilike', '%' . $approval_sheet . '%');
                    });
                });
            });
        }
        if ($barn) {
            $documents->where(function ($q) use ($barn) {
                $q->whereHas('documentDetails', function ($q1) use ($barn) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($barn) {
                        $q2->where('value', 'ilike', '%' . $barn . '%');
                    });
                });
            });
        }
        if ($yearmonth) {
            $documents->where(function ($q) use ($yearmonth) {
                $q->whereHas('documentDetails', function ($q1) use ($yearmonth) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($yearmonth) {
                        $q2->where('value', 'ilike', '%' . $yearmonth . '%');
                    });
                });
            });
        }
        if ($summary) {
            $documents->where(function ($q) use ($summary) {
                $q->whereHas('documentDetails', function ($q1) use ($summary) {
                    $q1->select('id')->where('content', 'ilike', '%' . $summary . '%')
                        ->orWhereHas('documentDetailAttributeValues', function ($q2) use ($summary) {
                            $q2->where('attribute_value', 'ilike', '%' . $summary . '%');
                        });
                });
            });
        }
        if ($registration) {
            $documents->where(function ($q) use ($registration) {
                $q->whereHas('documentDetails', function ($q1) use ($registration) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($registration) {
                        $q2->select('id')->where('value', 'ilike', '%' . $registration . '%');
                    });
                });
            });
        }
        if ($registration_uz_as) {
            $documents->where(function ($q) use ($registration_uz_as) {
                $q->whereHas('documentDetails', function ($q1) use ($registration_uz_as) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($registration_uz_as) {
                        $q2->select('id')->where('value', 'ilike', '%' . $registration_uz_as . '%');
                    });
                });
            });
        }
        if ($fio) {
            $documents->where(function ($q) use ($fio) {
                $q->whereHas('documentDetails', function ($q1) use ($fio) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($fio) {
                        $q2->select('id')->where('value', 'ilike', '%' . $fio . '%');
                    });
                });
            });
        }
        if ($region) {
            $documents->where(function ($q) use ($region) {
                $q->whereHas('documentDetails', function ($q1) use ($region) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($region) {
                        $q2->select('id')->where('value', 'ilike', '%' . $region . '%');
                    });
                });
            });
        }
        if (isset($filter['document_status']) && (in_array($filter['document_status'], [0, 1, 2, 3, 4, 5, 6, 8]))) {
            $documents->where('status', $filter['document_status']);
        }
        if ($appeal) {
            $documents->where(function ($q) use ($appeal) {
                $q->whereHas('documentDetails', function ($q1) use ($appeal) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($appeal) {
                        $q2->select('id')->where('value', 'ilike', '%' . $appeal . '%');
                    });
                });
            });
        }
        if ($partners_name) {
            $documents->where(function ($q) use ($partners_name) {
                $q->whereHas('documentDetails', function ($q1) use ($partners_name) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($partners_name) {
                        $q2->select('id')->where('value', 'ilike', '%' . $partners_name . '%');
                    });
                });
            });
        }
        if ($currency) {
            $documents->where(function ($q) use ($currency) {
                $q->whereHas('documentDetails', function ($q1) use ($currency) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($currency) {
                        $q2->select('id')->where('value', 'ilike', '%' . $currency . '%');
                    });
                });
            });
        }
        if ($model) {
            $documents->where(function ($q) use ($model) {
                $q->whereHas('documentDetails', function ($q1) use ($model) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($model) {
                        $q2->select('id')->where('value', 'ilike', '%' . $model . '%');
                    });
                });
            });
        }

        if ($type) {
            $documents->where(function ($q) use ($type) {
                $q->whereHas('documentDetails', function ($q1) use ($type) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($type) {
                        $q2->select('id')->where('value', 'ilike', '%' . $type . '%');
                    });
                });
            });
        }
        if ($document_type) {
            $documents->where(function ($q) use ($document_type) {
                $q->whereHas('documentDetails', function ($q1) use ($document_type) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($document_type) {
                        $q2->select('id')->where('value', 'ilike', '%' . $document_type . '%');
                    });
                });
            });
        }
        if ($company_name) {
            $documents->where(function ($q) use ($company_name) {
                $q->whereHas('documentDetails', function ($q1) use ($company_name) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($company_name) {
                        $q2->select('id')->where('value', 'ilike', '%' . $company_name . '%');
                    });
                });
            });
        }

        return ['documents' => $documents
            ->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page)];
    }

    public function signed(Request $request)
    {
        $filter = $request->input('filter');
        $created_by = isset($filter['created_by']) ? $filter['created_by'] : 0;
        $content = isset($filter['content']) ? $filter['content'] : 0;
        $summary = isset($filter['summary']) ? $filter['summary'] : 0;
        $korrespondent = isset($filter['korrespondent']) ? $filter['korrespondent'] : 0;
        $registration = isset($filter['registration']) ? $filter['registration'] : 0;
        $type = isset($filter['type']) ? $filter['type'] : 0;
        $send_by = isset($filter['send_by']) ? $filter['send_by'] : 0;
        $receive_by = isset($filter['receive_by']) ? $filter['receive_by'] : 0;
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        // if (isset($filter['staff_id']) && $filter['staff_id']) {
        //     $userStaffIds = [$filter['staff_id']];
        // } else {
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        // }

        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

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
            'pdf_file_name',
            'from_department',
            'from_manager',
            'to_department',
            'to_manager',
            'restore'
        )
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
                    }])
                    ->with(['documentDetailContents' => function ($query) {
                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                    }]);
            }])
            ->with(['documentSigners' => function ($q) use ($filter, $user, $userStaffIds) {
                $q->select('id', 'document_id', 'status', 'signer_employee_id', 'staff_id', 'taken_datetime', 'signed_date')
                    ->whereNotNull('taken_datetime')
                    ->with('signerEmployee')
                    ->whereIn('staff_id', $userStaffIds)
                    ->with(['employeeStaffs' => function ($empStaffquery) {
                        $empStaffquery->with('employee')
                            ->where('is_active', '=', 1);
                    }]);
            }])
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->wherenotNull('signed_date')
                    ->wherenotNull('taken_datetime');
            });

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if (isset($filter['document_status']) && $filter['document_status']) {
            $documents->where('status', $filter['document_status']);
        }

        if ($created_by) {
            $documents->whereHas('employee', function (Builder $query) use ($created_by) {
                $query->select('id')->where('firstname_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('lastname_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('middlename_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $created_by . '%')
                    ->orWhere('lastname_uz_cyril', 'ilike', '%' . $created_by . '%')
                    ->orWhere('middlename_uz_cyril', 'ilike', '%' . $created_by . '%');
            });
        }

        if ($send_by) {
            $documents->where(function ($q) use ($send_by) {
                $q->where('from_department', 'ilike', '%' . $send_by . '%')
                    ->orWhere('from_manager', 'ilike', '%' . $send_by . '%');
            });
        }

        if ($receive_by) {
            $documents->where(function ($q) use ($receive_by) {
                $q->where('to_department', 'ilike', '%' . $receive_by . '%')
                    ->orWhere('to_manager', 'ilike', '%' . $receive_by . '%');
            });
        }

        if ($content) {
            $documents->where(function ($q) use ($content) {
                $q->whereHas('documentDetails', function ($q1) use ($content) {
                    $q1->select('id')->where('content', 'ilike', '%' . $content . '%')
                        ->orWhereHas('documentDetailAttributeValues', function ($q2) use ($content) {
                            $q2->where('attribute_value', 'ilike', '%' . $content . '%');
                        })
                        ->orWhereHas('documentDetailEmployees', function ($q2) use ($content) {
                            $q2->where('employee_fio', 'ilike', '%' . $content . '%')
                                ->orWhere('employee_department', 'ilike', '%' . $content . '%')
                                ->orWhere('employee_position', 'ilike', '%' . $content . '%')
                                ->orWhereHas('employee', function ($q3) use ($content) {
                                    $q3->where('tabel', 'ilike', '%' . $content . '%');
                                });
                        });
                });
            });
        }

        if ($filter['document_start_date']) {
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        if (
            isset($filter['document_type_id']) &&
            $filter['document_type_id'] &&
            $filter['document_type_id'] != 'uzauto' &&
            $filter['document_type_id'] != 'employee' &&
            $filter['document_type_id'] != 'staff'
        ) {
            $documents->where('document_type_id', $filter['document_type_id']);
        }

        if (isset($filter['document_template_id']) && $filter['document_template_id']) {
            $documents->where('document_template_id', $filter['document_template_id']);
        }

        if ($filter['document_number']) {
            $documents->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            if ($filter['pending_action'] == 'empty') {
                $documents->whereDoesntHave('documentSigners', function ($q) {
                    $q->select('id')->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3]);
                });
            } else {
                $search = $filter['pending_action'];
                $documents->whereHas('documentSigners', function ($q) use ($search) {
                    $q->select('id')->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3])
                        ->whereHas('employeeStaffs', function ($q2) use ($search) {
                            $q2->select('id')->whereHas('employee', function ($q3) use ($search) {
                                $q3->select('id')->where('firstname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_cyril', 'ilike', '%' . $search . '%');
                            });
                        });
                });
            }
        }
        if ($korrespondent) {
            $documents->where(function ($q) use ($korrespondent) {
                $q->whereHas('documentDetails', function ($q1) use ($korrespondent) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($korrespondent) {
                        $q2->select('id')->where('value', 'ilike', '%' . $korrespondent . '%');
                    });
                });
            });
        }
        if ($summary) {
            $documents->where(function ($q) use ($summary) {
                $q->whereHas('documentDetails', function ($q1) use ($summary) {
                    $q1->select('id')->where('content', 'ilike', '%' . $summary . '%')
                        ->orWhereHas('documentDetailAttributeValues', function ($q2) use ($summary) {
                            $q2->select('id')->where('attribute_value', 'ilike', '%' . $summary . '%');
                        });
                });
            });
        }

        if ($registration) {
            $documents->where(function ($q) use ($registration) {
                $q->whereHas('documentDetails', function ($q1) use ($registration) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($registration) {
                        $q2->select('id')->where('value', 'ilike', '%' . $registration . '%');
                    });
                });
            });
        }

        if ($type) {
            $documents->where(function ($q) use ($type) {
                $q->whereHas('documentDetails', function ($q1) use ($type) {
                    $q1->select('id')->whereHas('documentDetailContents', function ($q2) use ($type) {
                        $q2->select('id')->where('value', 'ilike', '%' . $type . '%');
                    });
                });
            });
        }

        return ['documents' => $documents
            ->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page)];
    }

    public function indexMobile(Request $request)
    {
        $filter = $request->input('filter');
        $created_by = isset($filter['created_by']) ? $filter['created_by'] : 0;
        $content = isset($filter['content']) ? $filter['content'] : 0;
        $summary = isset($filter['summary']) ? $filter['summary'] : 0;
        $korrespondent = isset($filter['korrespondent']) ? $filter['korrespondent'] : 0;
        $registration = isset($filter['registration']) ? $filter['registration'] : 0;
        $type = isset($filter['type']) ? $filter['type'] : 0;
        $send_by = isset($filter['send_by']) ? $filter['send_by'] : 0;
        $receive_by = isset($filter['receive_by']) ? $filter['receive_by'] : 0;
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        if (isset($filter['staff_id']) && $filter['staff_id']) {
            $userStaffIds = [$filter['staff_id']];
        } else {
            $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        }

        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

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
            'pdf_file_name',
            'from_department',
            'from_manager',
            'to_department',
            'to_manager',
            'restore'
        )
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['documentTemplate' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['documentSigners' => function ($q) use ($filter, $user) {
                $q->select('id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                    ->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3, 4]);
            }]);

        if (isset($filter['document_range'])) {
            $documents->where('document_date', '>=', $filter['document_range'][0]);
            if (isset($filter['document_range'][1])) {
                $documents->where('document_date', '<=', $filter['document_range'][1]);
            }
        }

        if (isset($filter['document_status']) && $filter['document_status']) {
            $documents->where('status', $filter['document_status']);
        }

        if ($created_by) {
            $documents->whereHas('employee', function (Builder $query) use ($created_by) {
                $query->where('firstname_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('lastname_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('middlename_uz_latin', 'ilike', '%' . $created_by . '%')
                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $created_by . '%')
                    ->orWhere('lastname_uz_cyril', 'ilike', '%' . $created_by . '%')
                    ->orWhere('middlename_uz_cyril', 'ilike', '%' . $created_by . '%');
            });
        }

        if ($send_by) {
            $documents->where(function ($q) use ($send_by) {
                $q->where('from_department', 'ilike', '%' . $send_by . '%')
                    ->orWhere('from_manager', 'ilike', '%' . $send_by . '%');
            });
        }

        if ($receive_by) {
            $documents->where(function ($q) use ($receive_by) {
                $q->where('to_department', 'ilike', '%' . $receive_by . '%')
                    ->orWhere('to_manager', 'ilike', '%' . $receive_by . '%');
            });
        }

        if ($content) {
            $documents->where(function ($q) use ($content) {
                $q->whereHas('documentDetails', function ($q1) use ($content) {
                    $q1->where('content', 'ilike', '%' . $content . '%')
                        ->orWhereHas('documentDetailAttributeValues', function ($q2) use ($content) {
                            $q2->where('attribute_value', 'ilike', '%' . $content . '%');
                        })
                        ->orWhereHas('documentDetailEmployees', function ($q2) use ($content) {
                            $q2->where('employee_fio', 'ilike', '%' . $content . '%')
                                ->orWhere('employee_department', 'ilike', '%' . $content . '%')
                                ->orWhere('employee_position', 'ilike', '%' . $content . '%')
                                ->orWhereHas('employee', function ($q3) use ($content) {
                                    $q3->where('tabel', 'ilike', '%' . $content . '%');
                                });
                        });
                });
            });
        }

        if ($filter['document_start_date']) {
            $documents->where('document_date', '>=', $filter['document_start_date']);
        }

        if (
            isset($filter['document_type_id']) &&
            $filter['document_type_id'] &&
            $filter['document_type_id'] != 'uzauto' &&
            $filter['document_type_id'] != 'employee' &&
            $filter['document_type_id'] != 'staff'
        ) {
            $documents->where('document_type_id', $filter['document_type_id']);
        }

        if (isset($filter['document_template_id']) && $filter['document_template_id']) {
            $documents->where('document_template_id', $filter['document_template_id']);
        }

        if ($filter['document_number']) {
            $documents->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['pending_action']) && $filter['pending_action']) {
            if ($filter['pending_action'] == 'empty') {
                $documents->whereDoesntHave('documentSigners', function ($q) {
                    $q->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3]);
                });
            } else {
                $search = $filter['pending_action'];
                $documents->whereHas('documentSigners', function ($q) use ($search) {
                    $q->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3])
                        ->whereHas('employeeStaffs', function ($q2) use ($search) {
                            $q2->whereHas('employee', function ($q3) use ($search) {
                                $q3->where('firstname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_latin', 'ilike', '%' . $search . '%')
                                    ->orWhere('firstname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('lastname_uz_cyril', 'ilike', '%' . $search . '%')
                                    ->orWhere('middlename_uz_cyril', 'ilike', '%' . $search . '%');
                            });
                        });
                });
            }
        }
        if ($filter['menu_item'] == 'inbox') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', '!=', 3)
                    ->where('action_type_id', '!=', 6)
                    ->where(function ($q) use ($user) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($filter) {
                        foreach ($filter['reaction_status'] as $key => $value) {
                            $q->orWhere('status', $value);
                        }
                    })
                    ->whereNotNull('taken_datetime');
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
            // if ($userStaffIds[0] == 1) {
            //     $documents->whereHas('documentSigners', function ($q) use ($userStaffIds) {
            //         $q->where('staff_id', 1)
            //             ->where('is_registry', 0);
            //     });
            // }
        } elseif ($filter['menu_item'] == 'outbox') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->where('action_type_id', 3)
                            ->orWhere('action_type_id', 6);
                    })
                    ->where(function ($q) use ($user) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($filter) {
                        foreach ($filter['reaction_status'] as $key => $value) {
                            $q->orWhere('status', $value);
                        }
                    })
                    ->whereNotNull('taken_datetime');
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'draft') {
            $documents->where('created_employee_id', $user->employee_id)->where('status', 0);
        } elseif ($filter['menu_item'] == 'cancel') {
            if ($filter['document_type_id'] == 'uzauto' && Auth::user()->hasPermission('okd_kanselyariya')) {
                $documents->whereHas('documentSigners', function ($q) use ($userStaffIds, $user, $filter) {
                    $q->where('staff_id', 1)
                        ->where('status', 2);
                })->where('status', 6);
            } else {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->whereIn('staff_id', $userStaffIds)
                        ->where('signer_employee_id', $user->employee_id);
                })
                    ->where('status', '=', 6);
            }
        } elseif ($filter['menu_item'] == 'notification') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds, $filter) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        return $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where('document_signers.status', 0)
                    ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->whereNotNull('taken_datetime');
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6)
                ->orWhere(function ($q) use ($user) {
                    $q->where('created_employee_id', $user->employee_id)
                        ->where('status', '=', 4);
                });
            // if ($userStaffIds[0] == 1) {
            //     $documents->whereHas('documentSigners', function ($q) use ($userStaffIds) {
            //         $q->where('staff_id', 1)
            //             ->where('is_registry', 0);
            //     });
            // }
        } elseif ($filter['menu_item'] == 'processing') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 0);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'substantiate') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->where('signer_employee_id', $user->employee_id)
                    ->where('status', 4)
                    ->where('is_done', 0);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'resolutions') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 2);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'resolution_results') {
            $documents->whereHas('documentSigners', function ($q) use ($user) {
                $q->where('signer_employee_id', $user->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 1);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'expired') {
            $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $user->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '<', date("Y-m-d H:i:s"))
                    ->whereIn('document_signers.status', [0, 3]);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
            // if ($userStaffIds[0] == 1) {
            //     $documents->whereHas('documentSigners', function ($q) use ($userStaffIds) {
            //         $q->where('staff_id', 1)
            //             ->where('is_registry', 0);
            //     });
            // }
        } elseif ($filter['menu_item'] == 'executor') {
            $documents->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where('action_type_id', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('status', 0);
            })
                ->where('status', '!=', 0)
                ->where('status', '!=', 6);
        } elseif ($filter['menu_item'] == 'all') {
            $documents->where('status', '!=', 0);
        } elseif ($filter['menu_item'] == 'allhr') {
            $documents->where('status', '!=', 0)
                ->where('department_id', 14);
        } elseif ($filter['menu_item'] == 'alllsp') {
            $documents->where('status', '!=', 0)
                ->where('document_type_id', 7);
        } elseif ($filter['menu_item'] == 'allznz') {
            $documents->where('status', '!=', 0)
                ->where('document_template_id', 41);
        } elseif ($filter['menu_item'] == 'archive') {
            if ($filter['document_type_id'] == 'staff') {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->whereIn('staff_id', $userStaffIds);
                })->where('status', '!=', 0);
            } elseif ($filter['document_type_id'] == 'employee') {
                $documents->whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                    $q->where('signer_employee_id', $user->employee_id);
                })->where('status', '!=', 0);
                if (isset($filter['staff_id']) && $filter['staff_id']) {
                    $documents->whereHas('documentSigners', function ($q) use ($filter) {
                        $q->where('staff_id', $filter['staff_id']);
                    });
                }
            }
        } else {
            return [];
        }
        if ($korrespondent) {
            $documents->where(function ($q) use ($korrespondent) {
                $q->whereHas('documentDetails', function ($q1) use ($korrespondent) {
                    $q1->whereHas('documentDetailContents', function ($q2) use ($korrespondent) {
                        $q2->where('value', 'ilike', '%' . $korrespondent . '%');
                    });
                });
            });
        }
        if ($summary) {
            $documents->where(function ($q) use ($summary) {
                $q->whereHas('documentDetails', function ($q1) use ($summary) {
                    $q1->where('content', 'ilike', '%' . $summary . '%')
                        ->orWhereHas('documentDetailAttributeValues', function ($q2) use ($summary) {
                            $q2->where('attribute_value', 'ilike', '%' . $summary . '%');
                        });
                });
            });
        }

        if ($registration) {
            $documents->where(function ($q) use ($registration) {
                $q->whereHas('documentDetails', function ($q1) use ($registration) {
                    $q1->whereHas('documentDetailContents', function ($q2) use ($registration) {
                        $q2->where('value', 'ilike', '%' . $registration . '%');
                    });
                });
            });
        }

        if ($type) {
            $documents->where(function ($q) use ($type) {
                $q->whereHas('documentDetails', function ($q1) use ($type) {
                    $q1->whereHas('documentDetailContents', function ($q2) use ($type) {
                        $q2->where('value', 'ilike', '%' . $type . '%');
                    });
                });
            });
        }

        return ['documents' => $documents
            ->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page)];
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
            $documents->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
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
            $documents->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
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
        return [];
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
            $documents->where('document_number', 'ilike', '%' . $filter['document_number'] . '%');
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

    public function getLspCount()
    {
        $model = Document::select(DB::raw('count(status) as count'), 'status')
            ->where('document_type_id', 7)
            ->where('status', '>', 0)
            ->groupBy('status')->get();
        return $model;
    }

    public function getZnzCount()
    {
        $model = Document::select(DB::raw('count(status) as count'), 'status')
            ->where('document_template_id', 41)
            ->whereIn('status', [1, 2, 6])
            ->groupBy('status')->get();
        return $model;
    }

    public function tableList(Request $request)
    {
        $table_name = $request->input('table_name');
        $column_name = $request->input('column_name');
        $search = $request->input('search');
        $table_list = DB::table($table_name)->select('id', $column_name . ' as text')
            ->where($column_name, 'ilike', "%" . $search . "%");
        return $table_list->orderBy($column_name)
            ->paginate(20);
    }

    public function setBase64(Request $request)
    {
        $document_id = $request->input('document_id');
        $document = Document::find($document_id);
        if (!$document->pdf_table) {
            $document->pdf_table = Document::savePdf($document_id);
        }
        DB::connection('workflow_pdf')
            ->table($document->pdf_table)
            ->where('document_id', $document->id)
            ->update(['eimzoBase64' => $request->input('base64')]);
    }

    public function create($user_id)
    {
        $chiefs = User::whereHas('roles', function (Builder $query) {
            $query->where('name', 'director');
            $query->orWhere('name', 'deputy_director');
            $query->orWhere('name', 'general_manager');
        })->get();
        $departments = Department::where('is_parent_department', true)->get();
        $documentTypes = DocumentType::get();
        return ['chiefs' => $chiefs, 'departments' => $departments, 'documentTypes' => $documentTypes];
    }

    public function showDocumentSigners(Request $request)
    {
        $pdf_file_name = $request->input('pdf_file_name');
        $document = Document::where('pdf_file_name', $request->input('pdf_file_name'))
            ->with(['documentSigners' => function ($q) {
                $q->orderBy('sequence', 'desc')
                    ->orderBy('id', 'asc')
                    ->with('staff.employees')
                    ->with('staff.department')
                    ->with('staff.position')
                    ->with('signerEmployee')
                    ->with('parentEmployee')
                    ->with('actionType');
            }])
            ->first();
        return [
            'document' => $document,
            'action_types' => ActionType::get(),
        ];
    }

    public function showDocumentNew(Request $request)
    {
        $diff1 = 0;
        $diff2 = 0;

        $user = Auth::user();
        $pdf_file_name = $request->input('pdf_file_name');
        $refresh_pdf = $request->input('refresh_pdf');
        $document = Document::where('pdf_file_name', $request->input('pdf_file_name'))->first();
        $documentId = $document->id;
        $employee_id = $user->employee_id;
        $empLocale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
        if (!$documentId) {
            return 0;
        }

        $documentSigner = DocumentSigner::where('document_id', $document->id)
            ->where(function ($q) use ($user) {
                $staff_ids = EmployeeStaff::where('employee_id', $user->employee_id)->where('is_active', 1)->get()->pluck('staff_id');
                $q->whereIn('staff_id', $staff_ids)
                    ->orWhere('signer_employee_id', $user->employee_id);
            })
            ->first();

        if (!($documentSigner || $user->hasPermission('all-document-show') && $document->documentTemplate->is_confidential != 1 || in_array($document->document_template_id, [244, 247, 248, 316, 317, 318, 319, 320, 321, 388, 389, 390, 391, 394]))) {
            return $user->hasPermission('all-document-show');
        }

        $userStaffIds = collect($user->employee->staff)->pluck('id');
        $signer = DocumentSigner::where('document_id', $documentId)->whereIn('staff_id', $userStaffIds);
        $employee = DocumentSigner::where('document_id', $documentId)->where('signer_employee_id', $employee_id);
        if (!in_array($document->document_type_id, [57, 58, 59, 49, 63, 62]) && !($signer->count() ||
            $employee->count() ||
            $user->hasPermission('all-document-show') ||
            Document::find($documentId)->department_id == Staff::find($userStaffIds[0])->department_id)) {
            if (!UserTemplate::where('user_id', $user->id)->where('document_template_id', $document->document_template_id)->first())
                return $document->document_type_id;
        }

        if (!$document->pdf_table || $refresh_pdf) {
            Document::savePdf($document->id);
        }

        $document = Document::select()
            ->with(['Staff' => function ($q) {
                $q->leftJoin('departments', 'departments.id', 'staff.department_id')
                    ->leftJoin('positions', 'positions.id', 'staff.position_id')
                    ->select(
                        'staff.id',
                        'staff.department_id',
                        'staff.position_id',
                        'departments.department_code as department_code',
                        'departments.name_ru as department_name_ru',
                        'departments.name_uz_latin as department_name_uz_latin',
                        'departments.name_uz_cyril as department_name_uz_cyril',
                        'positions.name_ru as position_name_ru',
                        'positions.name_uz_latin as position_name_uz_latin',
                        'positions.name_uz_cyril as position_name_uz_cyril'
                    );
            }])
            ->with(['department2' => function ($q) {
                $q->select(
                    'id',
                    'department_code as department_code',
                    'name_ru as department_name_ru',
                    'name_uz_latin as department_name_uz_latin',
                    'name_uz_cyril as department_name_uz_cyril'
                );
            }])
            ->with(['documentDetails' => function ($q) use ($userStaffIds, $empLocale) {
                $q->select('id', 'document_id')
                    ->with(['documentDetailContents' => function ($q1) {
                        $q1->select(
                            'd_d_attribute_id',
                            'attribute_name',
                            'document_detail_id',
                            'value'
                        );
                    }])
                    ->with(['documentDetailAttributeValues' => function ($q2) use ($userStaffIds) {
                        $q2->whereHas('documentDetailAttributes', function ($qu) use ($userStaffIds) {
                            $qu->whereNotNull('signer_staff_id')
                                ->whereIn('signer_staff_id', $userStaffIds);
                        })
                            ->with('documentDetailAttributes');
                    }])
                    ->with(['documentDetailSignerAttributes' => function ($q1) {
                        $q1->with(['documentDetailAttributes' => function ($q2) {
                            // $q2->select(
                            //     'id',
                            //     'attribute_name_ru',
                            //     'attribute_name_uz_cyril',
                            //     'attribute_name_uz_latin'
                            // );
                        }])->with('attributeSignerStaff');
                    }])
                    ->with(['documentDetailEmployees' => function ($q1) use ($empLocale) {
                        $q1->select(
                            'employee_id',
                            'document_detail_id',
                            'employee_fio',
                            'employee_department',
                            'employee_position'
                        )->with(['employee' => function ($q2) use ($empLocale) {
                            $q2->select(
                                'id',
                                'tabel',
                                'firstname_' . $empLocale . ' as firsname',
                                'lastname_' . $empLocale . ' as lastname',
                                'middlename_' . $empLocale . ' as middlename',
                                'inn',
                                'inps'
                            )
                                ->with(['employeePhones' => function ($q1) {
                                    $q1->select('id', 'employee_id', 'phone_type', 'phone_number');
                                }])
                                ->with(['employeeOfficialDocument' => function ($q1) {
                                    $q1->select('id', 'employee_id', 'official_document_type_id', 'series', 'number')->where('is_active', 1);
                                }]);
                        }]);
                    }]);
            }])
            ->with(['documentSigners' => function ($q) {
                $q->select(
                    'id',
                    'document_id',
                    'staff_id',
                    'taken_datetime',
                    'parent_employee_id',
                    'action_type_id',
                    'assignment',
                    'due_date',
                    'sequence',
                    'signer_employee_id',
                    'description',
                    'status',
                    'sign_type',
                    'is_done',
                    'updated_at',
                    'sign_at',
                    'department',
                    'position',
                    'fio',
                    'signed_date',
                    'control_punkt_id'
                )
                    ->with('parentEmployee')
                    // ->whereNull('signed_date')
                    ->with(['signerEmployee' => function ($q1) {
                        $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                    }])
                    ->with(['staff' => function ($q1) {
                        $q1->select('id')
                            ->with(['employees' => function ($q2) {
                                $q2->select(
                                    'employees.id',
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
                    }])
                    // ->whereNull('control_punkt_id')
                    ->orderBy('sequence', 'asc')
                    ->orderBy('taken_datetime', 'asc');
            }])
            ->with('documentRelation')
            ->with('documentStaff.department:id,name_uz_latin,name_uz_cyril,name_ru,department_code')
            ->with('documentStaff.position:id,name_uz_latin,name_uz_cyril,name_ru')
            ->with('documentChildren')
            ->with(['previousVersion' => function ($q) {
                $q->select('id', 'document_number', 'pdf_file_name');
            }])
            ->where('id', $documentId)
            ->first()
            ->makeVisible(['base64', 'pdf']);

        $edit_attributes = DocumentDetailAttribute::where('is_signer_staff', 1)
            ->where('document_detail_template_id', $document->documentTemplate->documentDetailTemplates[0]->id)
            ->whereHas('signerStaffIds', function ($q) use ($userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds);
            })
            // ->with('signerStaffIds')
            ->get();
        $control_punkts = DocumentControlPunkt::with(['controller' => function ($q) {
            $q->select(
                'id',
                'document_id',
                'staff_id',
                'taken_datetime',
                'parent_employee_id',
                'action_type_id',
                'assignment',
                'due_date',
                'sequence',
                'signer_employee_id',
                'description',
                'status',
                'sign_type',
                'is_done',
                'updated_at',
                'department',
                'position',
                'fio',
                'signed_date',
                'control_punkt_id'
            )
                ->with('parentEmployee')
                ->with(['signerEmployee' => function ($q1) {
                    $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                }])
                ->with(['staff' => function ($q1) {
                    $q1->select('id')
                        ->with(['employees' => function ($q2) {
                            $q2->select(
                                'employees.id',
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
                }])
                // ->whereNull('control_punkt_id')
                ->orderBy('sequence', 'asc')
                ->orderBy('taken_datetime', 'asc');
        }])
            ->with(['documentSigners' => function ($q) {
                $q->select(
                    'id',
                    'document_id',
                    'staff_id',
                    'taken_datetime',
                    'parent_employee_id',
                    'action_type_id',
                    'assignment',
                    'due_date',
                    'sequence',
                    'signer_employee_id',
                    'description',
                    'status',
                    'sign_type',
                    'is_done',
                    'updated_at',
                    'department',
                    'position',
                    'fio',
                    'signed_date',
                    'control_punkt_id'
                )
                    ->with('parentEmployee')
                    ->with(['signerEmployee' => function ($q1) {
                        $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                    }])
                    ->with(['staff' => function ($q1) {
                        $q1->select('id')
                            ->with(['employees' => function ($q2) {
                                $q2->select(
                                    'employees.id',
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
                    }])
                    // ->whereNull('control_punkt_id')
                    ->orderBy('sequence', 'asc')
                    ->orderBy('taken_datetime', 'asc');
            }])->where('document_id', $documentId)->get();

        $document_signers = $this->getSigners($documentId, null, 0);

        $resolutionEmployee = DocumentSigner::where('document_id', $documentId)->where('parent_employee_id', $employee_id)->with('staff.employees')->get();

        foreach ($userStaffIds as $key_staff => $staffId) {
            foreach ($document->documentSigners as $key => $value) {
                // if ($document->status == 7 && $value->staff_id == $staffId && $value->status == 5) {
                //     $document->pre_agreement = true;
                // }
                if ($value->staff_id == $staffId && ($value->signer_employee_id == $employee_id || !$value->signer_employee_id) && $value->taken_datetime != null && !in_array($value->status, [1, 2])) {
                    if ($value->parent_employee_id) {
                        if ($value->signer_employee_id == $employee_id) {
                            $document->parent_employee_id = $value->parent_employee_id;
                            if ($value->sequence != 100) {
                                $document->disable_resolution = true;
                                $document->resolution_show = true;
                                $document->sequence = $value->sequence;
                                $document->reaction_status = $value->status;
                            }
                            if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $document->status != 6) {
                                $document->reaction_show = true;
                                $document->sign_type = $value->sign_type;
                            }
                        }
                        // if($document->id == 2726341){
                        //     $document->reaction_show = false;
                        //     $document->abc = $value->status;
                        // }
                    } else {
                        if ($value->sequence != 100) {
                            $document->disable_resolution = true;
                            $document->sequence = $value->sequence;
                        }
                        if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $document->status != 6) {
                            $document->resolution_show = ($value->sequence != 100) ? true : false;
                            // if($value->signer_employee_id == 12271){
                            //     dd($employee_id);
                            // }
                            $document->reaction_show = ($value->signer_employee_id && $value->signer_employee_id == $employee_id) || $value->signer_employee_id == null ? true : false;
                            $document->sign_type = $value->sign_type;
                            $document->reaction_status = $value->status;
                            if ($document->id == 2300190) {
                                // return ($value->signer_employee_id && $value->signer_employee_id == $employee_id) || $value->signer_employee_id == null ? true : false;
                                $document->reaction_show = true; //($value->signer_employee_id && $value->signer_employee_id == $employee_id) || $value->signer_employee_id == null ? true : false;
                            }
                        }
                    }

                    // if ($value->status != 1 && $value->status != 2) {
                    // }
                    $document->action_type_id = $value->action_type_id;
                    if ($value->action_type_id == 11) {
                        $document->out_of_control = DocumentSigner::where('document_id', $document->id)
                            ->whereNotIn('status', [1, 2])
                            ->where('action_type_id', 4)->count();
                        $document->resolution_show = false;
                    }
                }
                if ($value->signer_employee_id == $employee_id) {
                    if ($value->parent_employee_id != null) {
                        $document->resolution = $value;
                    }
                    if ($value->action_type_id == 6 && $document->status == 4) {
                        $document->confirmation_show = true;
                        $document->sign_type = $value->sign_type;
                    }
                }
            }
        }
        foreach ($resolutionEmployee as $key => $value) {
            if (!($value->status > 0 && $value->status < 3)) {
                $document->reaction_show = false;
            }
        }
        // if($document->id == 2726341){
        //     $document->reaction_show = false;
        // }

        $document_files = File::where('object_id', $documentId)
            ->whereIn('object_type_id', [5, 15])
            ->select('id', 'file_name', 'object_type_id')
            ->get();

        $document_blank_templates = DocumentBlankTemplate::select('id', 'blank_id', 'document_template_id')
            ->with(['blankTemplate' => function ($q) {
                $q->select('id', 'blank_name', 'file_type')
                    ->where('is_active', 1);
            }])
            ->with(['documentBlankAttribute' => function ($q) {
                $q->select('id', 'blank_attribute_id', 'date_format', 'document_blank_id', 'relation_attribute', 'relation_type')
                    ->with(['blankAttributeTemplate' => function ($q1) {
                        $q1->select('id', 'attribute_name', 'parameter_name', 'data_type_id');
                    }]);
            }])
            ->where('document_template_id', $document->document_template_id)
            ->get();

        $forInfoSigner = DocumentSigner::where('signer_employee_id', $employee_id)
            ->where('document_id', $document->id)
            ->whereIn('status', [0, 1])
            ->where('action_type_id', 5)
            ->first();
        //Rezolyutsiya tortish uchun boshliqligini tekshirish
        $staff_idsRes = EmployeeStaff::where('employee_id', $user->employee_id)->where('is_active', 1)->get()->pluck('staff_id');
        $departmentRes = Department::whereIn('manager_staff_id', $staff_idsRes)->first();
        if ($departmentRes || Auth::user()->hasRole('compliance') || Auth::user()->hasPermission('not-manager-resolution')) {
            $is_manager = 1;
        } else {
            $is_manager = 0;
        }
        return [
            'document' => $document,
            'resolutionEmployee' => $resolutionEmployee,
            'document_files' => $document_files,
            'resolutionTypes' => ActionType::whereIn('id', $forInfoSigner ? [5] : [4, 5, 11, 16])->whereIn('is_resolution', [1, 2])->select('id', 'name_ru', 'name_uz_cyril', 'name_uz_latin', 'is_resolution')->get(),
            'edit_attributes' => $edit_attributes,
            'document_blank_templates' => $document_blank_templates,
            'document_signers' => $document_signers,
            'is_manager' => $is_manager,
            'site_id' => config('app.APP_SITE_ID'),
        ];
    }

    public function showTestDocumentNew(Request $request)
    {
        $pdf_file_name = $request->input('pdf_file_name');
        $refresh_pdf = $request->input('refresh_pdf');
        $document = Document::where('pdf_file_name', $request->input('pdf_file_name'))->first();
        // dd($request->input('pdf_file_name'));
        $documentId = $document->id;
        $employee_id = Auth::user()->employee_id;
        $empLocale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
        if (!$documentId) {
            return 0;
        }
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $user = User::with(['roles' => function ($q) {
            $q->whereIn('id', [1, 8, 11, 16, 18, 22]);
        }])->where('id', Auth::id())->first();

        $signer = DocumentSigner::where('document_id', $documentId)->whereIn('staff_id', $userStaffIds);
        $employee = DocumentSigner::where('document_id', $documentId)->where('signer_employee_id', $employee_id);


        if (!($signer->count() ||
            $employee->count() ||
            Auth::user()->hasPermission('all-document-show') ||
            Document::find($documentId)->department_id == Staff::find($userStaffIds[0])->department_id)) {
            return 0;
        }

        if (!$document->pdf_table || $refresh_pdf) {
            Document::savePdf($document->id);
        }

        $document = Document::select()
            ->with(['Staff' => function ($q) {
                $q->leftJoin('departments', 'departments.id', 'staff.department_id')
                    ->leftJoin('positions', 'positions.id', 'staff.position_id')
                    ->select(
                        'staff.id',
                        'staff.department_id',
                        'staff.position_id',
                        'departments.department_code as department_code',
                        'departments.name_ru as department_name_ru',
                        'departments.name_uz_latin as department_name_uz_latin',
                        'departments.name_uz_cyril as department_name_uz_cyril',
                        'positions.name_ru as position_name_ru',
                        'positions.name_uz_latin as position_name_uz_latin',
                        'positions.name_uz_cyril as position_name_uz_cyril'
                    );
            }])
            ->with(['department2' => function ($q) {
                $q->select(
                    'id',
                    'department_code as department_code',
                    'name_ru as department_name_ru',
                    'name_uz_latin as department_name_uz_latin',
                    'name_uz_cyril as department_name_uz_cyril'
                );
            }])
            ->with(['documentDetails' => function ($q) use ($userStaffIds, $empLocale) {
                $q->select('id', 'document_id')
                    ->with(['documentDetailContents' => function ($q1) {
                        $q1->select(
                            'd_d_attribute_id',
                            'document_detail_id',
                            'value'
                        );
                    }])
                    ->with(['documentDetailAttributeValues' => function ($q2) use ($userStaffIds) {
                        $q2->whereHas('documentDetailAttributes', function ($qu) use ($userStaffIds) {
                            $qu->whereNotNull('signer_staff_id')
                                ->whereIn('signer_staff_id', $userStaffIds);
                        })
                            ->with('documentDetailAttributes');
                    }])
                    ->with(['documentDetailSignerAttributes' => function ($q1) {
                        $q1->select(
                            'id',
                            'd_d_attribute_id',
                            'document_detail_id',
                            'staff_id',
                            'emloyee_id',
                            'value'
                        )->with(['documentDetailAttributes' => function ($q2) {
                            $q2->select(
                                'id',
                                'attribute_name_ru',
                                'attribute_name_uz_cyril',
                                'attribute_name_uz_latin'
                            );
                        }])->with('attributeSignerStaff');
                    }])
                    ->with(['documentDetailEmployees' => function ($q1) use ($empLocale) {
                        $q1->select(
                            'employee_id',
                            'document_detail_id',
                            'employee_fio',
                            'employee_department',
                            'employee_position'
                        )->with(['employee' => function ($q2) use ($empLocale) {
                            $q2->select(
                                'id',
                                'tabel',
                                'firstname_' . $empLocale . ' as firsname',
                                'lastname_' . $empLocale . ' as lastname',
                                'middlename_' . $empLocale . ' as middlename',
                                'INN',
                                'INPS'
                            )
                                ->with(['employeePhones' => function ($q1) {
                                    $q1->select('id', 'employee_id', 'phone_type', 'phone_number');
                                }])
                                ->with(['employeeOfficialDocument' => function ($q1) {
                                    $q1->select('id', 'employee_id', 'official_document_type_id', 'series', 'number')->where('is_active', 1);
                                }]);
                        }]);
                    }]);
            }])
            ->with(['documentSigners' => function ($q) {
                $q->select(
                    'id',
                    'document_id',
                    'staff_id',
                    'taken_datetime',
                    'parent_employee_id',
                    'action_type_id',
                    'assignment',
                    'due_date',
                    'sequence',
                    'signer_employee_id',
                    'description',
                    'status',
                    'sign_type',
                    'is_done',
                    'updated_at',
                    'sign_at',
                    'department',
                    'position',
                    'fio',
                    'signed_date',
                    'control_punkt_id'
                )
                    ->with('parentEmployee')
                    ->with(['signerEmployee' => function ($q1) {
                        $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                    }])
                    ->with(['staff' => function ($q1) {
                        $q1->select('id')
                            ->with(['employees' => function ($q2) {
                                $q2->select(
                                    'employees.id',
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
                    }])
                    // ->whereNull('control_punkt_id')
                    ->orderBy('sequence', 'asc')
                    ->orderBy('taken_datetime', 'asc');
            }])
            ->with('documentRelation')
            ->with('documentChildren')
            ->with(['previousVersion' => function ($q) {
                $q->select('id', 'document_number', 'pdf_file_name');
            }])
            ->where('id', $documentId)->first()->makeVisible(['base64', 'pdf']);

        $edit_attributes = DocumentDetailAttribute::where('is_signer_staff', 1)
            ->where('document_detail_template_id', $document->documentTemplate->documentDetailTemplates[0]->id)
            ->whereHas('signerStaffIds', function ($q) use ($userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds);
            })
            // ->with('signerStaffIds')
            ->get();
        // return $document;


        $control_punkts = DocumentControlPunkt::with(['controller' => function ($q) {
            $q->select(
                'id',
                'document_id',
                'staff_id',
                'taken_datetime',
                'parent_employee_id',
                'action_type_id',
                'assignment',
                'due_date',
                'sequence',
                'signer_employee_id',
                'description',
                'status',
                'sign_type',
                'is_done',
                'updated_at',
                'department',
                'position',
                'fio',
                'signed_date',
                'control_punkt_id'
            )
                ->with('parentEmployee')
                ->with(['signerEmployee' => function ($q1) {
                    $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                }])
                ->with(['staff' => function ($q1) {
                    $q1->select('id')
                        ->with(['employees' => function ($q2) {
                            $q2->select(
                                'employees.id',
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
                }])
                // ->whereNull('control_punkt_id')
                ->orderBy('sequence', 'asc')
                ->orderBy('taken_datetime', 'asc');
        }])
            ->with(['documentSigners' => function ($q) {
                $q->select(
                    'id',
                    'document_id',
                    'staff_id',
                    'taken_datetime',
                    'parent_employee_id',
                    'action_type_id',
                    'assignment',
                    'due_date',
                    'sequence',
                    'signer_employee_id',
                    'description',
                    'status',
                    'sign_type',
                    'is_done',
                    'updated_at',
                    'department',
                    'position',
                    'fio',
                    'signed_date',
                    'control_punkt_id'
                )
                    ->with('parentEmployee')
                    ->with(['signerEmployee' => function ($q1) {
                        $q1->select('id', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin');
                    }])
                    ->with(['staff' => function ($q1) {
                        $q1->select('id')
                            ->with(['employees' => function ($q2) {
                                $q2->select(
                                    'employees.id',
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
                    }])
                    // ->whereNull('control_punkt_id')
                    ->orderBy('sequence', 'asc')
                    ->orderBy('taken_datetime', 'asc');
            }])->where('document_id', $documentId)->get();

        // if($document->id == 56862){
        //     dd($documentId);
        // }


        $document_signers = $this->getSigners($documentId, null, 0);
        // if($documentId == 59908){
        //     dd($documentId);
        // }
        // if($document->id == 67277){
        //     dd(1);
        // }

        $resolutionEmployee = DocumentSigner::where('document_id', $documentId)
            ->where('document_signers.parent_employee_id', $employee_id)
            // ->select(
            //     'document_signers.id',
            //     'assignment',
            //     'due_date',
            //     'document_signers.updated_at',
            //     'document_signers.parent_employee_id',
            //     'document_signers.description',
            //     'document_signers.status',
            //     'document_signers.signer_employee_id'
            // )
            ->get();



        foreach ($userStaffIds as $key_staff => $staffId) {
            foreach ($document->documentSigners as $key => $value) {
                if ($document->status == 7 && $value->staff_id == $staffId && $value->status == 5) {
                    $document->pre_agreement = true;
                }
                if ($value->staff_id == $staffId && $value->taken_datetime != null) {
                    if ($value->parent_employee_id) {
                        if ($value->signer_employee_id == $employee_id) {
                            $document->parent_employee_id = $value->parent_employee_id;
                            if ($value->sequence != 100) {
                                $document->disable_resolution = true;
                                $document->resolution_show = true;
                                $document->sequence = $value->sequence;
                                $document->reaction_status = $value->status;
                            }
                            if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $document->status != 6) {
                                $document->reaction_show = true;
                                $document->sign_type = $value->sign_type;
                            }
                        }
                    } else {
                        if ($value->sequence != 100) {
                            $document->disable_resolution = true;
                            $document->sequence = $value->sequence;
                        }
                        if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $document->status != 6) {
                            $document->resolution_show = ($value->sequence != 100) ? true : false;
                            $document->reaction_show = ($value->signer_employee_id && $value->signer_employee_id == $employee_id) || $value->signer_employee_id == null ? true : false;
                            $document->sign_type = $value->sign_type;
                            $document->reaction_status = $value->status;
                        }
                    }
                    // if ($value->status != 1 && $value->status != 2) {
                    // }
                    $document->action_type_id = $value->action_type_id;
                    if ($value->action_type_id == 11) {
                        $document->out_of_control = DocumentSigner::where('document_id', $document->id)
                            ->whereNotIn('status', [1, 2])
                            ->where('action_type_id', 4)->count();
                        $document->resolution_show = false;
                    }
                }
                if ($value->signer_employee_id == $employee_id) {
                    if ($value->parent_employee_id != null) {
                        $document->resolution = $value;
                    }
                    if ($value->action_type_id == 6 && $document->status == 4) {
                        $document->confirmation_show = true;
                        $document->sign_type = $value->sign_type;
                    }
                }
            }
        }



        foreach ($resolutionEmployee as $key => $value) {
            if (!($value->status > 0 && $value->status < 3)) {
                $document->reaction_show = false;
            }
        }

        if ($documentId == 104313) {
            if (Auth::id() == 7) {
                $document_files = File::where('object_id', $documentId)
                    ->where('object_type_id', 5)
                    ->select('id', 'file_name')
                    ->get();
            } else $document_files = [];
        } else {
            $document_files = File::where('object_id', $documentId)
                ->where('object_type_id', 5)
                ->select('id', 'file_name')
                ->get();
        }

        $document_blank_templates = DocumentBlankTemplate::select('id', 'blank_id', 'document_template_id')
            ->with(['blankTemplate' => function ($q) {
                $q->select('id', 'blank_name', 'file_type')
                    ->where('is_active', 1);
            }])
            ->with(['documentBlankAttribute' => function ($q) {
                $q->select('id', 'blank_attribute_id', 'date_format', 'document_blank_id', 'relation_attribute', 'relation_type')
                    ->with(['blankAttributeTemplate' => function ($q1) {
                        $q1->select('id', 'attribute_name', 'parameter_name', 'data_type_id');
                    }]);
            }])
            ->where('document_template_id', $document->document_template_id)
            ->get();

        return [
            'document' => $document,
            'document' => $document,
            'base64' => Document::generateCommentPdf($document->id),
            'resolutionEmployee' => $resolutionEmployee,
            'document_files' => $document_files,
            'resolutionTypes' => ActionType::whereIn('is_resolution', [1, 2])->select('id', 'name_ru', 'name_uz_cyril', 'name_uz_latin', 'is_resolution')->get(),
            'edit_attributes' => $edit_attributes,
            'document_blank_templates' => $document_blank_templates,
            'control_punkts' => $control_punkts,
            'document_signers' => $document_signers,
        ];
    }

    public function getSigners($id, $parent_employee_id, $step = 0)
    {
        $signers = DocumentSigner::with(['comments' => function ($q) {
            $q->select('id', 'document_signer_id', 'comment', 'status', 'created_at')
                ->with('files')
                ->orderBy('id', 'desc');
        }])
            ->select(
                'id',
                'document_id',
                'staff_id',
                'taken_datetime',
                'parent_employee_id',
                'action_type_id',
                'assignment',
                'due_date',
                'sequence',
                'signer_employee_id',
                'description',
                'status',
                'sign_type',
                'is_done',
                'updated_at',
                'sign_at',
                'department',
                'position',
                'fio',
                'signed_date',
                'control_punkt_id'
            )
            ->with(['staff' => function ($q1) {
                $q1->select('id')
                    ->with(['employees' => function ($q2) {
                        $q2->select(
                            'employees.id',
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
            }])
            ->where('document_id', $id)
            ->where('parent_employee_id', $parent_employee_id)
            ->orderBy('taken_datetime', 'desc')
            ->orderBy('signed_date', 'desc')
            ->get();


        $childs = [];

        foreach ($signers as $key => $value) {
            $childs[$key] = $value;
            if ($value->signer_employee_id && $value->signer_employee_id != $parent_employee_id && $step < 5) {
                $childs[$key]['children'] = $this->getSigners($id, $value->signer_employee_id, $step + 1);
            } else {
                $childs[$key]['children'] = [];
            }
        }

        return $childs;
    }

    public function showDocument(Request $request)
    {
        $id = $request->input('id');
        $locale = $request->input('language');
        $staffs = Staff::select('staff.id', 'employees.id as employee_id', 'department_id')
            ->join('employee_staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->join('employees', 'employee_staff.employee_id', '=', 'employees.id')
            ->join('users', 'users.employee_id', '=', 'employees.id')
            ->where('users.id', Auth::id())
            ->where('employee_staff.is_active', 1)
            ->where('employee_staff.deleted_at', null)
            ->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $signer = DocumentSigner::where('document_id', $id)->whereIn('staff_id', $userStaffIds);
        $employee = DocumentSigner::where('document_id', $id)->where('signer_employee_id', Auth::user()->employee_id);

        // return ['signer' => $signer->count(), 'rule' => count($user->roles)];
        // if (!($signer->count() ||
        //     $employee->count() ||
        //     // Auth::user()->hasPermission('all-document-show') ||
        //     Document::find($id)->department_id == Staff::find($userStaffIds[0])->department_id)) {
        //     return 0;
        // }
        $document = Document::with('documentType')
            ->with(['Staff' => function ($q) {
                $q->leftJoin('departments', 'departments.id', 'staff.department_id')
                    ->leftJoin('positions', 'positions.id', 'staff.position_id')
                    ->select(
                        'staff.id',
                        'staff.department_id',
                        'staff.position_id',
                        'departments.department_code as department_code',
                        'departments.name_ru as department_name_ru',
                        'departments.name_uz_latin as department_name_uz_latin',
                        'departments.name_uz_cyril as department_name_uz_cyril',
                        'positions.name_ru as position_name_ru',
                        'positions.name_uz_latin as position_name_uz_latin',
                        'positions.name_uz_cyril as position_name_uz_cyril'
                    );
            }])
            ->with('graphics')
            ->with('department.managerStaff.employeeMainStaff.employee')
            ->with('department.managerStaff.position')
            ->with('documentTemplate.documentSignerTemplates')
            ->with('documentTemplate.signerGroups.signerGroupDetails.staff.department')
            ->with('documentTemplate.signerGroups.signerGroupDetails.staff.position')
            ->with('documentTemplate.signerGroups.signerGroupDetails.staff.employees')
            ->with('employee')
            ->with('documentStaff.department:id,name_uz_latin,name_uz_cyril,name_ru,department_code')
            ->with('documentStaff.position:id,name_uz_latin,name_uz_cyril,name_ru')
            ->with('documentDetails.documentDetailCoefficients.tariffScale')
            ->with('documentDetails.documentDetailEmployees.tariffScale')
            ->with('documentDetails.documentDetailEmployees.range')
            ->with('documentDetails.documentDetailEmployees.otgulDates')
            ->with('documentDetails.documentDetailEmployees.employee.tariffScale')
            ->with('documentDetails.documentDetailEmployees.employee.employeeCoefficients.coefficient')
            ->with(['documentDetails.documentDetailEmployees.employee.employeeStaff' => function ($empStaffquery) {
                $empStaffquery->where('is_active', '=', 1)
                    ->where('is_main_staff', '=', 1)
                    ->with('staff.position')
                    ->with('staff.department');
            }])
            ->with('documentDetails.documentDetailEmployees.employee.staff.department')
            ->with('documentSigners')
            ->with('documentSigners.staff.position')
            ->with('documentSigners.staff.department')
            ->with('documentSigners.actionTypes')
            ->with('documentSigners.signerEmployee')
            ->with('documentSigners.parentEmployee')
            ->with(['documentSigners.employeeStaffs' => function ($q) {
                $q->with('employee')->where('is_active', '=', 1);
            }])
            ->with('documentRelation.employee')
            ->with('documentRelation.documentTemplate')
            // ->with('documentDetails.documentDetailAttributeValues.documentDetailAttributes.dataType')
            // ->with('documentDetails.documentDetailAttributeValues.documentDetailAttributes.tableList')
            ->with(['documentDetails.documentDetailAttributeValues' => function ($q) {
                $q->with('documentDetailAttributes.dataType')
                    ->with('documentDetailAttributes.tableList')
                    // ->orderBy('d_d_attribute_id', 'desc')
                ;
            }])
            ->where('id', $id)
            ->first()
            ->makeVisible(['base64', 'pdf']);

        if (!$document->pdf_table) {
            Document::savePdf($document->id);
            $document = Document::with('documentType')
                ->with('department.managerStaff.employeeMainStaff.employee')
                ->with('department.managerStaff.position')
                ->with('documentTemplate.documentSignerTemplates')
                ->with('documentTemplate.signerGroups.signerGroupDetails.staff.department')
                ->with('documentTemplate.signerGroups.signerGroupDetails.staff.position')
                ->with('documentTemplate.signerGroups.signerGroupDetails.staff.employees')
                ->with('employee')
                ->with('documentDetails.documentDetailAttributeValues.documentDetailAttributes.dataType')
                ->with('documentDetails.documentDetailCoefficients.tariffScale')
                ->with('documentDetails.documentDetailAttributeValues.documentDetailAttributes.tableList')
                ->with('documentDetails.documentDetailEmployees.tariffScale')
                ->with('documentDetails.documentDetailEmployees.range')
                ->with('documentDetails.documentDetailEmployees.employee.tariffScale')
                ->with('documentDetails.documentDetailEmployees.employee.employeeCoefficients.coefficient')
                ->with(['documentDetails.documentDetailEmployees.employee.employeeStaff' => function ($empStaffquery) {
                    $empStaffquery->where('is_active', '=', 1)
                        ->where('is_main_staff', '=', 1)
                        ->with('staff.position')
                        ->with('staff.department');
                }])
                ->with('documentDetails.documentDetailEmployees.employee.staff.department')
                ->with('documentSigners')
                ->with('documentSigners.staff.position')
                ->with('documentSigners.staff.department')
                ->with('documentSigners.actionTypes')
                ->with('documentSigners.signerEmployee')
                ->with('documentSigners.parentEmployee')
                ->with(['documentSigners.employeeStaffs' => function ($q) {
                    $q->with('employee')->where('is_active', '=', 1);
                }])
                ->with('documentRelation.employee')
                ->with('documentRelation.documentTemplate')
                ->where('id', $id)
                ->first()
                ->makeVisible(['base64', 'pdf']);
        }

        $document->disable_resolution = false;
        $document->reaction_show = false;
        $document->confirmation_show = false;
        foreach ($document->documentDetails as $key_detail => $documentDetail) {
            foreach ($documentDetail->documentDetailAttributeValues as $key => $value) {
                if ($value->documentDetailAttributes->table_list_id) {
                    // if($document->id == 2332680 && $key == 7){
                    //     return DB::table($value->documentDetailAttributes->tableList->table_name)->where('id', $value->attribute_value)->get();
                    //     return [$value->documentDetailAttributes->tableList->table_name,$value->attribute_value];
                    // }
                    $document->documentDetails[$key_detail]->documentDetailAttributeValues[$key]->table_lists = DB::table($value->documentDetailAttributes->tableList->table_name)->where('id', $value->attribute_value)->get();
                }
            }
        }

        $resolutionEmployee = DocumentSigner::join('staff', 'document_signers.staff_id', '=', 'staff.id')
            ->join('departments', 'departments.id', '=', 'staff.department_id')
            ->where('document_id', $id)
            ->whereNotNull('parent_employee_id')
            ->where('document_signers.parent_employee_id', Auth::user()->employee_id)
            ->with('parentEmployee')
            ->with('signerEmployee')
            ->select(
                'document_signers.id',
                'assignment',
                'due_date',
                'document_signers.updated_at',
                'document_signers.parent_employee_id',
                'document_signers.description',
                'document_signers.status',
                'document_signers.signer_employee_id'
            )->get();

        $document->sequence = 100;
        foreach ($staffs as $key_staff => $staff) {
            foreach ($document->documentSigners as $key => $value) {
                if ($value->staff_id == $staff->id) {
                    if ($value->parent_employee_id) {
                        if ($value->signer_employee_id == $staff->employee_id) {
                            if ($value->sequence != 100) {
                                $document->disable_resolution = true;
                                $document->resolution_show = true;
                                $document->sequence = $value->sequence;
                            }
                            if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $value->taken_datetime != null && $document->status != 6) {
                                $document->reaction_show = true;
                                $document->sign_type = $value->sign_type;
                            }
                            if ($value->status != 0) {
                                $document->reaction_status = $value->status;
                            }
                        }
                    } else {
                        if ($value->sequence != 100 && $value->taken_datetime != null) {
                            $document->disable_resolution = true;
                            $document->sequence = $value->sequence;
                        }
                        if (($value->status == 0 || $value->status == 3 || $value->status == 4) && $value->taken_datetime != null && $document->status != 6) {
                            $document->resolution_show = ($value->sequence != 100) ? true : false;
                            $document->reaction_show = true;
                            $document->sign_type = $value->sign_type;
                        }
                        if ($value->status != 0) {
                            $document->reaction_status = $value->status;
                            $document->action_type_id = $value->action_type_id;
                        }
                    }
                    $document->action_type_id = $value->taken_datetime != null ? $value->action_type_id : $document->action_type_id;
                    if ($value->action_type_id == 11) {
                        $document->out_of_control = DocumentSigner::where('document_id', $document->id)->whereNotIn('status', [1, 2])->where('action_type_id', 4)->count();
                        $document->resolution_show = false;
                    }
                }
                if ($value->signer_employee_id == $staff->employee_id) {
                    if ($value->parent_employee_id != null) {
                        $document->resolution = $value;
                    }
                    if ($value->action_type_id == 6 && $document->status == 4) {
                        $document->confirmation_show = true;
                        $document->sign_type = $value->sign_type;
                    }
                }
            }
        }
        $reaction_status = DocumentSigner::select('status')
            ->where(function ($q) use ($staffs) {
                foreach ($staffs as $key => $value) {
                    $q->orWhere('staff_id', $value->id);
                }
            })
            ->where(function ($q) use ($staffs) {
                $q->whereNotNull('signer_employee_id')
                    ->where('signer_employee_id', $staffs[0]->employee_id)
                    ->orWhereNull('signer_employee_id');
            })
            ->where('document_id', $id)
            ->whereIn('status', [0, 3])
            ->orderBy('status')->first();

        if ($reaction_status) {
            $document->reaction_status = $reaction_status->status;
        }

        foreach ($resolutionEmployee as $key => $value) {
            if (!($value->status > 0 && $value->status < 3)) {
                $document->reaction_show = false;
            }
        }

        $document->documentSigners->map(function ($parent) {
            if (!$parent->parent_employee_id) {
                return $parent;
            }
        });
        $document->resolution_employee = $resolutionEmployee;
        $employee_parent = Employee::parentDepartments($document->employee->tabel);

        $document_files = File::where('object_id', $id)
            ->whereIn('object_type_id', [5, 15, 17])
            ->get();
        // return $document_files;
        $document_doer = DocumentSigner::where('document_id', $id)
            ->where('action_type_id', 4)
            ->whereNotNull('taken_datetime')
            ->with('staff.position')
            ->with('staff.department')
            ->with('signerEmployee')
            ->with('staff.employeeMainStaff.employee')->get();

        return [
            'document' => $document,
            'from_departament' => $employee_parent['main_department'],
            'document_files' => $document_files,
            'document_doer' => $document_doer,
        ];
    }

    public function showOnlyPdf(Request $request)
    {
        $document = Document::with(['documentSigners' => function ($q) {
            $q->with('parentEmployee')
                ->with('staff.employees')
                ->orderBy('sequence', 'asc');
        }])
            // ->where('id', $request->input('id'))
            ->where('pdf_file_name', $request->input('pdf_file_name'))
            ->first()
            ->makeVisible(['pdf']);
        $document_files = File::where('object_id', $document->id)
            ->where('object_type_id', 5)
            ->get();
        return [
            'document' => $document,
            'document_files' => $document_files
        ];
    }

    public function getResolutionEmployees(Request $request)
    {
        $search = $request->input('search');
        $document_template_id = $request->input('document_template_id');
        $user = User::with('roles')->find(Auth::id());
        $department_ids = Department::select('id')->whereHas('staff', function ($q) use ($user) {
            $q->whereHas('EmployeeStaff', function ($q) use ($user) {
                $q->where('is_active', 1)
                    ->where('employee_id', $user->employee_id);
            });
        })->get();

        if (Auth::user()->hasRole('resolution_all_employee') || $document_template_id == 520) {
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin', 'firstname_uz_cyril', 'lastname_uz_cyril', 'middlename_uz_cyril', 'tariff_scale_id')
                ->with('tariffScale')
                ->with(['employeeStaff' => function ($query) {
                    $query->with(['staff' => function ($staffQuery) {
                        $staffQuery->with('position')->with('department');
                    }])
                        ->where('is_active', '=', 1);
                }])
                ->with('mainStaff.position')
                ->whereNotIn('id', [$user->employee_id, 4, 5]);
        } else {
            $department_idss = [];
            foreach ($department_ids as $key => $department_id) {
                $arr = $this->getDepIds($department_id->id);
                $department_idss = array_merge($department_idss, $arr);
            }

            $department_idss = array_unique($department_idss);
            $staff_ids = $user->employee->staff->pluck('id')->toArray();
            // dd($staff_ids);
            if (Auth::id() == 691) {
                // dd($department_idss);
                // $department_idss = array_slice($department_idss, 700,1200);
            }
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin', 'firstname_uz_cyril', 'lastname_uz_cyril', 'middlename_uz_cyril', 'tariff_scale_id')
                ->with('tariffScale')
                ->with(['employeeStaff' => function ($query) {
                    $query->with(['staff' => function ($staffQuery) {
                        $staffQuery->with('position')->with('department');
                    }])
                        ->where('is_active', 1);
                }])
                ->with('mainStaff.position')
                ->whereHas('staff', function ($q) use ($department_idss, $user, $staff_ids) {
                    $q->whereRaw("department_id in (" . implode(',', $department_idss) . ") ");
                    // $q->whereNotIn('staff.id', $staff_ids);
                })
                ->where('id', '<>', $user->employee_id)
                // ->where('id', '!=', $user->employee_id)
                // ->where()
            ;
        }

        if ($search) {
            $resolutionEmployee->where(function (Builder $query) use ($search) {
                return $query
                    ->where('tabel', 'ilike', '%' . $search . '%')
                    ->orWhere(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%");
            });
        }
        return $resolutionEmployee->paginate(20);
    }


    public function getResolutionEmployeesMobile(Request $request)
    {
        $search = $request->input('search');
        $locale = $request->input('locale');
        $user = User::with('roles')->find(Auth::id());
        $department_ids = Department::select('id')->whereHas('staff', function ($q) use ($user) {
            $q->whereHas('EmployeeStaff', function ($q) use ($user) {
                $q->where('is_active', 1)
                    ->where('employee_id', $user->employee_id);
            });
        })->get();

        if (Auth::user()->hasRole('resolution_all_employee')) {
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_' . $locale . ' as firstname', 'lastname_' . $locale . ' as lastname', 'middlename_' . $locale . ' as middlename')
                ->with(['mainStaff' => function ($query) use ($locale) {
                    $query->select('position_id')
                        ->with('position:id,name_' . $locale . ' as name');
                }])
                ->whereNotIn('id', [$user->employee_id, 4, 5]);
        } else {
            $department_idss = [];
            foreach ($department_ids as $key => $department_id) {
                $arr = $this->getDepIds($department_id->id);
                $department_idss = array_merge($department_idss, $arr);
            }
            // return $department_idss;
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_' . $locale . ' as firstname', 'lastname_' . $locale . ' as lastname', 'middlename_' . $locale . ' as middlename')
                // updated
                ->with(['mainStaff' => function ($query) use ($locale) {
                    $query->select('position_id')
                        ->with('position:id,name_' . $locale . ' as name');
                }])
                ->whereHas('mainStaff', function ($q) use ($department_idss) {
                    $q->whereIn('department_id', $department_idss);
                })
                ->where('id', '!=', $user->employee_id);
        }


        if ($search) {
            $resolutionEmployee->where(function (Builder $query) use ($search) {
                return $query
                    ->where('tabel', 'ilike', '%' . $search . '%')
                    ->orWhere(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%");
            });
        }
        $employee_list = $resolutionEmployee->paginate(1000)->items();
        $resolution =  ActionType::select('id', 'name_' . $locale . ' as name')->where('is_resolution', 1)->get();

        return ['Employees' => $employee_list, 'Resolution_types' => $resolution];
    }

    // Agar $funksional_guruh bo'lsa faqat upralarini ko'rsatadi
    public function getDepIds($dep_id)
    {
        $ids = [$dep_id];
        $deps = Department::select('id')->where('parent_id', $dep_id)->get();
        foreach ($deps as $key => $value) {
            $ids = array_merge($ids, $this->getDepIds($value->id));
        }
        return $ids;
    }

    public function createPdfFile(Request $request)
    {
        $file = base64_decode($request->input('base64'));
        if (true || !Storage::disk('local')->exists('documents_new/pdf/' . $request->input('pdf_file_name'))) {
            Storage::disk('local')->put('documents_new/pdf/' . $request->input('pdf_file_name'), $file);
        } else {
            return 'File alredy exist';
        }
    }

    public function downloadPdfFile($hash)
    {
        // return Redirect::to('https://check.edo.uzautomotors.com/#/documents/' . $hash);
        // $document = Document::where('pdf_file_name', $hash)->first();
        // if ($document) {
        //     return response()->file(storage_path('app\\document\\pdf\\' . $hash));
        //     // return Storage::download('document/pdf/' . $hash, $document->document_number . '.pdf');
        // }
        // return 'Fayl topilmadi.';

        // $file = File::where('id', $id)->first();
        // $path = Storage::path('documents\\' . $file->physical_name);
    }

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }

    public function checkOtpusk(Request $request)
    {
        // return [true,''];
        $period = null;
        // dd($request->all());
        // $document['document_template_id']
        $details = $request->input('document_details');
        $id = $request->input('id');
        foreach ($details as $key => $value) {
            $type = $request['document_template_id'] == 12 ? 'T' : ($request['document_template_id'] == 474 ? 'D' : 'boshqa');
            $day = $value['document_detail_attribute_values'][1]['attribute_value'];
            // $vocation_period = VocationPeriod::find($value['document_detail_attribute_values'][7]['attribute_value']);
            // $period = $vocation_period->period;
            if (count($value['document_detail_employees']) > 1) {
                return [false, 'Faqat bitta hodim biriktirishingiz mumkin.', '', '', ''];
            }
            $emp = Employee::find($value['document_detail_employees'][0]['employee_id']);
            $ids = collect($value['document_detail_employees'])->pluck('employee_id')->toArray();

            //Moddiy javobgar shaxsni tekshirish joyi
            // $moddiyjavobgar = MaterialResponsiblePeople::whereIn('employee_id', $ids)->first();
            // if($moddiyjavobgar){
            //     return [false, "Ushbu hodim moddiy javobgar shaxs bo'lganligi uchun boshqa shablondan foydalaning: "];
            // }
            //Agar mehnat shartnomasini bekor qilish uchun ariza chiqarilgan bo'lsa
            $uvolen = DocumentDetail::query()
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [0, 6]);
                })
                ->wherehas('document', function ($q) use ($id) {
                    $q->where('document_template_id', 587);
                })
                ->orderByDesc('id')
                ->first();
            if ($uvolen) {
                $uvolenDoc = Document::find($uvolen->document_id);
                $uvolenemployee = $uvolenDoc ? $uvolenDoc->employee->getShortname($uvolenDoc->locale) : '';
                if ($uvolenDoc->status == 0) {
                    $uvolenstatus = ' Қоралама ';
                }
                if ($uvolenDoc->status == 1 || $uvolenDoc->status == 2) {
                    $uvolenstatus = ' Жараёнда ';
                }
                if ($uvolenDoc->status == 3 || $uvolenDoc->status == 4 || $uvolenDoc->status == 5) {
                    $uvolenstatus = ' Имзоланган ';
                }
                return [false, "Ushbu hodim bilan mehnat shartnomasini bekor qilish arizasi mavjud: ", $uvolenDoc->id, $uvolenemployee, $uvolenstatus];
            }
            if ($type == 'T') {
                // if(Auth::id()==1){
                //     dd($value['document_detail_attribute_values'][1]['attribute_value']);
                // }
                $dd = DocumentDetail::query()
                    ->where(function ($q) {
                        $q->whereHas('documentDetailAttributeValues', function ($q) {
                            $q->where('d_d_attribute_id', 158);
                            $q->where('attribute_value', 1);
                        })
                            ->orWhereDoesntHave('documentDetailAttributeValues', function ($q) {
                                $q->where('d_d_attribute_id', 158);
                            });
                    })
                    // ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                    //     $q->where('d_d_attribute_id', 160);
                    //     $q->where('attribute_value', '>', 28 - $value['document_detail_attribute_values'][1]['attribute_value']);
                    // })
                    ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                        $q->where('d_d_attribute_id', 421);
                        $q->where('attribute_value', $value['document_detail_attribute_values'][6]['attribute_value']);
                    })
                    ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                        $q->whereIn('employee_id', $ids);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->where('id', $id);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->whereIn('status', [6]);
                    })
                    ->whereHas('document', function ($q) use ($id) {
                        $q->where('document_template_id', 12);
                    })
                    ->get();
                // dd($dd,collect($value['document_detail_attribute_values'])->pluck('d_d_attribute_id','attribute_value'));
                $days_count = 0;
                foreach ($dd as $key => $val) {
                    $ddc = $val->documentDetailContents;
                    $days = $ddc->where('d_d_attribute_id', 160)->first();
                    $days_count += $days->value;
                }

                if ($value['document_detail_attribute_values'][1]['attribute_value'] + $days_count > 28) {
                    if (count($dd) > 0) {
                        $arr = [];
                        foreach ($dd as $k => $v) {
                            $dc = Document::find($v->document_id);
                            array_push($arr, $dc->id);
                        }
                        return [false, "*Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak. " . "Bundan boshqa tatil arizalaringiz ID raqamlari: " . json_encode($arr), '', '', ''];
                    } else {
                        return [false, "Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak.", '', '', ''];
                    }
                }

                // if (Auth::id() == 1) {
                //     dd($days_count);
                // }
                // $dd1 = DocumentDetail::query()
                //     ->whereDoesntHave('documentDetailAttributeValues', function ($q) {
                //         $q->where('d_d_attribute_id', 158);
                //     })
                //     ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                //         $q->where('d_d_attribute_id', 160);
                //         $q->where('attribute_value', '>', 28 - $value['document_detail_attribute_values'][1]['attribute_value']);
                //     })
                //     ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                //         $q->where('d_d_attribute_id', 421);
                //         $q->where('attribute_value', $value['document_detail_attribute_values'][6]['attribute_value']);
                //     })
                //     ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                //         $q->whereIn('employee_id', $ids);
                //     })
                //     ->whereDoesntHave('document', function ($q) use ($id) {
                //         $q->where('id', $id)
                //             // ->whereNull('created_at')
                //         ;
                //     })
                //     ->whereDoesntHave('document', function ($q) use ($id) {
                //         $q->whereIn('status', [6]);
                //     })
                //     ->first();

                // // dd($dd1);
                // if ($dd) {
                //     $doc = Document::find($dd->document_id);
                //     $employee = $doc->employee->getShortname($doc->locale);
                //     if ($doc->status == 0) {
                //         $status = ' Қоралама ';
                //     }
                //     if ($doc->status == 1 || $doc->status == 2) {
                //         $status = ' Жараёнда ';
                //     }
                //     if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                //         $status = ' Имзоланган ';
                //     }
                //     //  dd($employee);
                //     return [false, 'Ушбу ходим ушбу период учун мехнат татили аризаси мавжуд. Ариза ID:', $dd->document_id, $employee, $status];
                // } else if ($dd1) {
                //     // $doc = Document::select('status', 'created_employee_id')->where('id', $dd1->document_id);
                //     $doc = Document::find($dd1->document_id);
                //     $employee = $doc->employee->getShortname($doc->locale);
                //     if ($doc->status == 0) {
                //         $status = ' Қоралама ';
                //     }
                //     if ($doc->status == 1 || $doc->status == 2) {
                //         $status = ' Жараёнда ';
                //     }
                //     if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                //         $status = ' Имзоланган ';
                //     }
                //     //  dd($employee);
                //     return [false, 'Ушбу ходим ушбу период учун мехнат татили аризаси мавжуд. Ариза ID:', $dd1->document_id, $employee, $status];
                // }
                else {
                    return [true, ''];
                }
            } elseif ($type == 'D') {
                $dd = DocumentDetail::query()
                    ->whereHas('documentDetailAttributeValues', function ($q) {
                        $q->where('d_d_attribute_id', 158);
                        $q->where('attribute_value', 2);
                    })
                    ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                        $q->where('d_d_attribute_id', 421);
                        $q->where('attribute_value', $value['document_detail_attribute_values'][2]['attribute_value']);
                    })
                    ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                        $q->whereIn('employee_id', $ids);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->where('id', $id)
                            // ->whereNull('created_at')
                        ;
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->whereIn('status', [6]);
                    });
                $ddNew = DocumentDetail::query()
                    ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                        $q->where('d_d_attribute_id', 1521);
                        $q->where('attribute_value', $value['document_detail_attribute_values'][2]['attribute_value']);
                    })
                    ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                        $q->whereIn('employee_id', $ids);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->where('id', $id)
                            // ->whereNull('created_at')
                        ;
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->whereIn('status', [6]);
                    });
                // dd($value['document_detail_attribute_values'][2]['attribute_value'], $ddNew);
                if ($dd->count() > 0) {
                    $doc = Document::find($dd->first()->document_id);
                    // dd($doc);
                    $employee = $doc ? $doc->employee->getShortname($doc->locale) : '';
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    //  dd($employee);
                    // return [false, "Ушбу ходим ушбу период учун қўшимча татили аризаси мавжуд. Ариза ID:  $doc->id, Юборувчи: $employee, Ҳолати: $status"];
                    return [false, 'Ушбу ходим ушбу период учун қўшимча татили аризаси мавжуд. Ариза ID:', $doc->id, $employee, $status];
                } else if ($ddNew->count() > 0 && $doc = Document::find($ddNew->first()->document_id)) {
                    // dd($ddNew->first()->document_id);
                    $employee = $doc ? $doc->employee->getShortname($doc->locale) : '';
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    //  dd($employee);
                    return [false, 'Ушбу ходим ушбу период учун қўшимча татили аризаси мавжуд. Ариза ID:', $doc->id, $employee, $status];
                    // return [false, 'Ушбу ходим ушбу период учун қўшимча татил аризаси мавжуд. Ариза ID: ', $ddNew->first()->document_id];
                } else {
                    return [true, ''];
                }
            }
            return [true, ''];
        }
    }
    public function checkOtpuskModdiyJavobgar(Request $request)
    {
        $details = $request->input('document_details');
        $id = $request->input('id');
        foreach ($details as $key => $value) {
            $type = $request['document_template_id'];
            $day = $value['document_detail_attribute_values'][1]['attribute_value'];
            // $vocation_period = VocationPeriod::find($value['document_detail_attribute_values'][7]['attribute_value']);
            // $period = $vocation_period->period;
            if (count($value['document_detail_employees']) > 1) {
                return [false, 'Faqat bitta hodim biriktirishingiz mumkin.', '', '', ''];
            }
            $emp = Employee::find($value['document_detail_employees'][0]['employee_id']);
            $ids = collect($value['document_detail_employees'])->pluck('employee_id')->toArray();

            //Moddiy javobgar shaxsni tekshirish joyi
            // $moddiyjavobgar = MaterialResponsiblePeople::whereIn('employee_id', $ids)->first();
            // if($moddiyjavobgar){
            //     return [false, "Ushbu hodim moddiy javobgar shaxs bo'lganligi uchun boshqa shablondan foydalaning: "];
            // }

            //Agar mehnat shartnomasini bekor qilish uchun ariza chiqarilgan bo'lsa
            $uvolen = DocumentDetail::query()
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [0, 6]);
                })
                ->wherehas('document', function ($q) use ($id) {
                    $q->where('document_template_id', 587);
                })
                ->orderByDesc('id')
                ->first();
            if ($uvolen) {
                $uvolenDoc = Document::find($uvolen->document_id);
                $uvolenemployee = $uvolenDoc ? $uvolenDoc->employee->getShortname($uvolenDoc->locale) : '';
                if ($uvolenDoc->status == 0) {
                    $uvolenstatus = ' Қоралама ';
                }
                if ($uvolenDoc->status == 1 || $uvolenDoc->status == 2) {
                    $uvolenstatus = ' Жараёнда ';
                }
                if ($uvolenDoc->status == 3 || $uvolenDoc->status == 4 || $uvolenDoc->status == 5) {
                    $uvolenstatus = ' Имзоланган ';
                }
                return [false, "Ushbu hodim bilan mehnat shartnomasini bekor qilish arizasi mavjud: ", $uvolenDoc->id, $uvolenemployee, $uvolenstatus];
            }
            $dd = DocumentDetail::query()
                ->where(function ($q) {
                    $q->whereHas('documentDetailAttributeValues', function ($q) {
                        $q->where('d_d_attribute_id', 158);
                        $q->where('attribute_value', 1);
                    })
                        ->orWhereDoesntHave('documentDetailAttributeValues', function ($q) {
                            $q->where('d_d_attribute_id', 158);
                        });
                })
                // ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                //     $q->where('d_d_attribute_id', 160);
                //     $q->where('attribute_value', '>', 28 - $value['document_detail_attribute_values'][1]['attribute_value']);
                // })
                ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                    $q->where('d_d_attribute_id', 421);
                    $q->where('attribute_value', $value['document_detail_attribute_values'][5]['attribute_value']);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->whereHas('document', function ($q) use ($id) {
                    $q->where('document_template_id', 12);
                })
                ->get();
            $ddNew = DocumentDetail::query()
                // ->where(function ($q) {
                //     $q->whereHas('documentDetailAttributeValues', function ($q) {
                //         $q->where('d_d_attribute_id', 158);
                //         $q->where('attribute_value', 1);
                //     });
                // })
                // ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                //     $q->where('d_d_attribute_id', 160);
                //     $q->where('attribute_value', '>', 28 - $value['document_detail_attribute_values'][1]['attribute_value']);
                // })
                ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                    $q->where('d_d_attribute_id', 2921);
                    $q->where('attribute_value', $value['document_detail_attribute_values'][5]['attribute_value']);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->whereHas('document', function ($q) use ($id) {
                    $q->where('document_template_id', 661);
                })
                ->get();
            // dd($dd,collect($value['document_detail_attribute_values'])->pluck('d_d_attribute_id','attribute_value'));
            if ($dd) {
                $days_count = 0;
                foreach ($dd as $key => $val) {
                    $ddc = $val->documentDetailContents;
                    $days = $ddc->where('d_d_attribute_id', 160)->first();
                    $days_count += $days->value;
                }

                if ($value['document_detail_attribute_values'][1]['attribute_value'] + $days_count > 28) {
                    if (count($dd) > 0) {
                        $arr = [];
                        foreach ($dd as $k => $v) {
                            $dc = Document::find($v->document_id);
                            array_push($arr, $dc->id);
                        }
                        return [false, "*Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak. " . "Bundan boshqa tatil arizalaringiz ID raqamlari: " . json_encode($arr), '', '', ''];
                    } else {
                        return [false, "Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak.", '', '', ''];
                    }
                }
            }
            if ($ddNew) {
                $days_count = 0;
                foreach ($ddNew as $key => $val) {
                    $ddc = $val->documentDetailContents;
                    $days = $ddc->where('d_d_attribute_id', 2917)->first();
                    $days_count += $days->value;
                }

                if ($value['document_detail_attribute_values'][1]['attribute_value'] + $days_count > 28) {
                    if (count($ddNew) > 0) {
                        $arr = [];
                        foreach ($ddNew as $k => $v) {
                            $dc = Document::find($v->document_id);
                            array_push($arr, $dc->id);
                        }
                        return [false, "*Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak. " . "Bundan boshqa tatil arizalaringiz ID raqamlari: " . json_encode($arr), '', '', ''];
                    } else {
                        return [false, "Ushbu davr uchun ta'til kunlari 28 kundan oshmasligi kerak.", '', '', ''];
                    }
                }
            }
            return [true, ''];
        }
    }
    public function checkOtpuskStaj(Request $request)
    {
        $period = null;
        $details = $request->input('document_details');
        $document_template_id = $request['document_template_id'];
        $id = $request->input('id');
        foreach ($details as $key => $value) {
            $day = $value['document_detail_attribute_values'][1]['attribute_value'];
            if (count($value['document_detail_employees']) > 1) {
                return [false, 'Faqat bitta hodim biriktirishingiz mumkin.', '', '', ''];
            }
            $emp = Employee::find($value['document_detail_employees'][0]['employee_id']);
            $ids = collect($value['document_detail_employees'])->pluck('employee_id')->toArray();

            $uvolen = DocumentDetail::query()
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [0, 6]);
                })
                ->wherehas('document', function ($q) use ($id) {
                    $q->where('document_template_id', 587);
                })
                ->orderByDesc('id')
                ->first();
            if ($uvolen) {
                $uvolenDoc = Document::find($uvolen->document_id);
                $uvolenemployee = $uvolenDoc ? $uvolenDoc->employee->getShortname($uvolenDoc->locale) : '';
                if ($uvolenDoc->status == 0) {
                    $uvolenstatus = ' Қоралама ';
                }
                if ($uvolenDoc->status == 1 || $uvolenDoc->status == 2) {
                    $uvolenstatus = ' Жараёнда ';
                }
                if ($uvolenDoc->status == 3 || $uvolenDoc->status == 4 || $uvolenDoc->status == 5) {
                    $uvolenstatus = ' Имзоланган ';
                }
                return [false, "Ushbu hodim bilan mehnat shartnomasini bekor qilish arizasi mavjud: ", $uvolenDoc->id, $uvolenemployee, $uvolenstatus];
            }


            if ($document_template_id == 592) {
                // if(Auth::id()==1){
                //     dd($value['document_detail_attribute_values'][1]['attribute_value']);
                // }
                $dd = DocumentDetail::query()
                    ->whereHas('documentDetailAttributeValues', function ($q) use ($value) {
                        $q->where('d_d_attribute_id', 2572);
                        $q->where('attribute_value', $value['document_detail_attribute_values'][3]['attribute_value']);
                    })
                    ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                        $q->whereIn('employee_id', $ids);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->where('id', $id);
                    })
                    ->whereDoesntHave('document', function ($q) use ($id) {
                        $q->whereIn('status', [6]);
                    })
                    ->wherehas('document', function ($q) use ($id) {
                        $q->where('document_template_id', 592);
                    })
                    ->first();
                // dd($dd,collect($value['document_detail_attribute_values'])->pluck('d_d_attribute_id','attribute_value'));
                if ($dd) {
                    $doc = Document::find($dd->document_id);
                    $employee = $doc->employee->getShortname($doc->locale);
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    //  dd($employee);
                    return [false, 'Ушбу ходим ушбу период учун стаж татили аризаси мавжуд. Ариза ID:', $dd->document_id, $employee, $status];
                } else {
                    return [true, ''];
                }
            }
            return [true, ''];
        }
    }
    public function checkUvolnitelniy(Request $request)
    {
        // return [true, ''];
        $details = $request->input('document_details');
        $id = $request->input('id');
        foreach ($details as $key => $value) {
            $ids = collect($value['document_detail_employees'])->pluck('employee_id')->toArray();
            $from = $value['document_detail_attribute_values'][0]['attribute_value'];
            $to = $value['document_detail_attribute_values'][1]['attribute_value'];
            $start_datetime = new DateTime($from);
            $diff = $start_datetime->diff(new DateTime($to));

            if ($from >= $to) {
                return [false, 'Бошланиш вақти тугаш вақтидан кичик бўлиши керак.', '', 1];
            } else if ($diff->y > 0 || $diff->m > '0' || $diff->d > 0 || $diff->h > 12) {
                return [false, 'Вақт оралиғи 12 соатдан кам бўлиши керак.', '', 1];
            }

            return [true, '']; // Bazadan validatsiya qilish o'chirildi chunki u bazani osiltirib qo'yyapti.

            $dd = DocumentDetail::query()
                ->whereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 999);
                    $q->where('attribute_value', '>=', $from);
                    $q->where('attribute_value', '<=', $to);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->orWhereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 1000);
                    $q->where('attribute_value', '>=', $from);
                    $q->where('attribute_value', '<=', $to);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->first();

            $dd1 = DocumentDetail::query()
                ->whereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 999);
                    $q->where('attribute_value', '<=', $from);
                })
                ->whereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 1000);
                    $q->where('attribute_value', '>=', $from);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->orWhereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 999);
                    $q->where('attribute_value', '<=', $to);
                })
                ->whereHas('documentDetailAttributeValues', function ($q) use ($from, $to) {
                    $q->where('d_d_attribute_id', 1000);
                    $q->where('attribute_value', '>=', $to);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($ids) {
                    $q->whereIn('employee_id', $ids);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->whereDoesntHave('document', function ($q) use ($id) {
                    $q->whereIn('status', [6]);
                })
                ->first();
            if ($dd) {
                $doc = Document::find($dd->document_id);
                if ($doc) {
                    $employee = $doc->employee ? $doc->employee->getShortname($doc->locale) : '-';
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    // return [false, 'Ушбу ходим ушбу период учун ruhsatnoma аризаси мавжуд. Ариза ID: ', $dd->document_id, 1,];
                    return [false, 'Ушбу ходим ушбу период учун Руҳсатнома аризаси мавжуд. Ариза ID:', $dd->document_id, $employee, $status];
                }
                return [true, ''];
            } elseif ($dd1) {
                $doc = Document::find($dd1->document_id);
                if ($doc) {
                    $employee = $doc->employee ? $doc->employee->getShortname($doc->locale) : '-';
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    // return [false, 'Ушбу ходим ушбу период учун ruhsatnoma аризаси мавжуд. Ариза ID: ', $dd->document_id, 1,];
                    return [false, 'Ушбу ходим ушбу период учун Руҳсатнома аризаси мавжуд. Ариза ID:', $dd1->document_id, $employee, $status];
                }
                return [true, ''];
            } else {

                return [true, ''];
            }
        }
    }

    // Agar atribut unique bo'lsa dublikat ma'lumot kiritilganda habar chiqadi.
    public function checkAttributes($request)
    {
        // return '444';
        $document = $request->all();
        $model = Document::find($document['id']);
        if ($model && ($model->document_template_id == 552 || $model->document_template_id == 553 || $model->document_template_id == 554  || $model->document_template_id == 556 || $model->document_template_id == 557)) {
            return [true, 'OK'];
        }
        if ($document['document_type_id'] != 12 || $document['document_template_id'] < 488) {
            foreach ($document['document_details'] as $key => $value) {
                foreach ($value['document_detail_attribute_values'] as $ddAttributeValueKey => $attributeValue) {
                    // $attValue = DocumentDetailAttributeValue::where('id', $attributeValue['id'])->first();
                    // if (!$attValue) {
                    //     $attValue = new DocumentDetailAttributeValue();
                    // }
                    $unique = $attributeValue['document_detail_attributes']['unique'];
                    // $type = $attributeValue['document_detail_attributes']['type']; // text bo'lishi kerak
                    $d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                    $attribute_value = $attributeValue['attribute_value'];

                    $ddav = DocumentDetailAttributeValue::where('d_d_attribute_id', $d_d_attribute_id)
                        ->where('attribute_value', $attribute_value)
                        ->first();
                    $documentID = $ddav && $ddav->documentDetail && $ddav->documentDetail->document ? $ddav->documentDetail->document->id : 0;
                    if (($model && ($documentID != $model->id) || !$model) && $ddav && $unique) {
                        $attribute_name = $attributeValue['document_detail_attributes']['attribute_name_uz_latin'];
                        $message = "Ushbu [" . $attribute_name . "] maydoniga kiritilgan [" . $attribute_value . "] ma'lumot avval [" . $documentID . "] ID lik dokumentda kiritilgan.";
                        return [false, $message];
                    }
                }
            }
        }
        return [true, 'OK'];
    }

    public function getHoursBeetweenDates($date1, $date2)
    {
        try {

            $date1 = strtotime($date1);
            $date2 = strtotime($date2);
            $datediff = $date2 - $date1;
            return round($datediff / (60 * 60));
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public function update(Request $request)
    {
        $document = $request->all();
        if ($document['document_template_id'] != 554) {
            $checkAttribute = $this->checkAttributes($request);
            // dd($checkAttribute);
            if (!$checkAttribute[0]) {
                // return 2;
                return $checkAttribute[1];
            }
        }
        // Otgul test
        if ($document['document_template_id'] == 597) {
            $dates = $document['start_end_dates'];
            $reduced = 0;
            $reduced_lanch = 0;

            //   this.document.document_details[0].document_detail_attribute_values.find(
            //     (v) => v.d_d_attribute_id == 2597
            //   ).attribute_value;

            $otgul_day_count = collect($document['document_details'][0]['document_detail_attribute_values'])->firstWhere('d_d_attribute_id', 2597)['attribute_value'];

            foreach ($dates as $key => $value) {
                if ($value['start_datetime'] == $value['end_datetime']) {
                    return response()->json("Ishdan tashqari ishlagan soatlaringizni tekshiring.");
                }
                foreach ($dates as $k => $v) {
                    if ($key < $k) {
                        if (
                            $value['start_datetime'] < $v['start_datetime'] && $v['start_datetime'] < $value['end_datetime']
                            || $value['start_datetime'] < $v['end_datetime'] && $v['end_datetime'] < $value['end_datetime']
                            || $v['start_datetime'] < $value['start_datetime'] && $value['start_datetime'] < $v['end_datetime']
                            || $v['start_datetime'] < $value['end_datetime'] && $value['end_datetime'] < $v['end_datetime']
                            || $v['start_datetime'] < $value['start_datetime'] && $value['end_datetime'] < $v['end_datetime']
                            || $value['start_datetime'] == $v['start_datetime']
                            || $value['end_datetime'] == $v['end_datetime']
                            || $value['start_datetime'] == $v['start_datetime'] && $value['end_datetime'] < $v['end_datetime']
                            || $value['start_datetime'] < $v['start_datetime'] && $value['end_datetime'] == $v['end_datetime']
                        ) {
                            return response()->json("* Ishdan tashqari ishlagan soatlaringizni tekshiring.");
                        }
                    }
                }

                $start_lanch_date = substr($value['start_datetime'], 0, 10) . ' 12:00:00';
                $end_lanch_date = substr($value['end_datetime'], 0, 10) . ' 13:00:00';
                $start_night_lanch_date = substr($value['end_datetime'], 0, 10) . ' 00:00:00';
                $end_night_lanch_date = substr($value['end_datetime'], 0, 10) . ' 01:00:00';
                $reduced += $this->getHoursBeetweenDates($value['start_datetime'], $value['end_datetime']);
                if (
                    $value['start_datetime'] <= $start_lanch_date && $end_lanch_date <= $value['end_datetime'] ||
                    $value['start_datetime'] <= $start_night_lanch_date && $end_night_lanch_date <= $value['end_datetime']
                ) {
                    $reduced_lanch++;
                }
            }
            if ($reduced - $reduced_lanch != $otgul_day_count * 8) {
                return response()->json("Ishdan tashqari ishlagan soatlaringiz otgul kunlari uchun mos emas.");
            }
        } else if ($document['document_template_id'] == 4) {
            $otgul_ddav = collect($document['document_details'][0]['document_detail_attribute_values']);
            // dd($otgul_ddav->firstWhere('d_d_attribute_id', 420));
            $otgul_type = $otgul_ddav->firstWhere('d_d_attribute_id', 420);
            if ($otgul_type && $otgul_type['attribute_value'] == 7) {
                $otgul_first_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 5)['attribute_value'], 0, 10);
                $otgul_last_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 7)['attribute_value'], 0, 10);
                // dd($otgul_first_day, $otgul_last_day);
                if ($otgul_last_day < $otgul_first_day) {
                    return response()->json("Sanalarni ketma-ketligini tog'ri belgilang.");
                }
                $otgul_count_day = $otgul_ddav->firstWhere('d_d_attribute_id', 140)['attribute_value'];
                $otgul_first_wokr_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 141)['attribute_value'], 0, 10);
                $otgul_last_wokr_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 142)['attribute_value'], 0, 10);
                if ($otgul_first_day && $otgul_last_day && $otgul_count_day && $otgul_first_wokr_day && $otgul_last_wokr_day) {
                    if ($otgul_first_day < date('Y-m-d', strtotime(date('Y-m-d') . ' -80 day'))) {
                        return response()->json("Kechagi sanadan oldingi sana uchun ariza yozaolmaysiz.");
                    } else if ($otgul_last_day < $otgul_first_day) {
                        return response()->json("Sanalarni ketma-ketligini tog'ri belgilang.");
                    }
                } else {
                    return response()->json("So'ralgan barcha ma'lumotlarni to'ldiring.");
                }
            }
        } else if ($document['document_template_id'] == 287) {
            $otgul_ddav = collect($document['document_details'][0]['document_detail_attribute_values']);
            // dd($otgul_ddav->firstWhere('d_d_attribute_id', 420));
            $otgul_first_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 999)['attribute_value'], 0, 10);
            $otgul_last_day = substr($otgul_ddav->firstWhere('d_d_attribute_id', 1000)['attribute_value'], 0, 10);
            if ($otgul_first_day && $otgul_last_day) {
                if ($otgul_first_day < date('Y-m-d')) {
                    // return response()->json("Joriy sanadan oldingi sana uchun ariza yozaolmaysiz.");
                } else if ($otgul_last_day < $otgul_first_day) {
                    return response()->json("Sanalarni ketma-ketligini tog'ri belgilang.");
                }
            } else {
                return response()->json("So'ralgan barcha ma'lumotlarni to'ldiring.");
            }
        } else if ($document['document_template_id'] == 587) { // Ishdan bo'shatish shabloni
            $details = $document['document_details'];
            // $tabel = $document['document_details'][0]['document_detail_employees'][0]['tabel_number'];
            if (count($details) > 1) {
                return response()->json("Ushbu shablonda bittadan ortiq punkt qo'shish mumkin emas.");
            } else if (count($details[0]['document_detail_employees']) > 1) {
                return response()->json("Ushbu shablonda bittadan ortiq hodim tanlash mumkin emas.");
            }

            // dd($details[0]['document_detail_employees'][0]['employee_id']);

            $my_detail = DocumentDetail::whereDoesntHave('document', function ($q) use ($document) {
                $q->where('id', $document['id']);
            })
                ->whereDoesntHave('document', function ($q) use ($document) {
                    $q->whereIn('status', [0, 6]);
                })
                ->whereHas('documentDetailEmployees', function ($q) use ($details) {
                    $q->where('employee_id', $details[0]['document_detail_employees'][0]['employee_id']);
                })
                ->whereHas('document', function ($q) {
                    $q->where('document_template_id', 587);
                })
                ->first();
            if ($my_detail) {
                return response()->json("Ushbu hodim uchun bunday hujjat avval tayyorlangan. Hujjat ID raqami: " . $my_detail->document_id);
            }
        } else if ($document['document_template_id'] == 592) {
            if (count($document['document_details']) > 1) {
                return response()->json("Bittadan ko'p punkt qo'shish mumkin emas.");
            }
            if (count($document['document_details'][0]['document_detail_employees']) > 1) {
                return response()->json("Bittadan ko'p hodim tanlash mumkin emas.");
            }
            // dd($document['document_details'][0]['document_detail_employees'][0]['tabel_number']);
            $tabel = $document['document_details'][0]['document_detail_employees'][0]['tabel_number'];
            $tabel = strtoupper($tabel);
            $employee_id = $document['document_details'][0]['document_detail_employees'][0]['employee_id'];
            // dd($employee_id);
            $response = Http::withoutVerifying()
                ->post('https://edo-db2.uzautomotors.com/api/get-stage/' . $tabel)->body();
            if ($response != "") {
                $response = json_decode($response);
                $boshqa_joyda_ishlagan_yillari = $response[1];
                $date = substr($response[0], 0, 4) . '-' . substr($response[0], 4, 2) . '-' . substr($response[0], 6, 2);
                $datetime1 = date_create($date);
                $ddc1 = DocumentDetailContent::where('document_detail_id', $document['document_details'][0]['id'])->where('d_d_attribute_id', 2569)->first();
                $abc = $document['document_details'][0]['document_detail_attribute_values'][0]['attribute_value'];
                $datetime2 = date_create($abc);
                $interval = date_diff($datetime1, $datetime2)->format('%y') + $boshqa_joyda_ishlagan_yillari;
                if ($interval < 5) {
                    return response()->json("Ish stajingiz 5 yildan kam bo'lganligi uchun ushbu arizani yozolmaysiz.");
                }

                $my_data =  $document['document_details'][0]['document_detail_attribute_values'][0]['attribute_value']; // 2569
                $my_day =  $document['document_details'][0]['document_detail_attribute_values'][1]['attribute_value']; // 2570
                $my_staj =  $document['document_details'][0]['document_detail_attribute_values'][2]['attribute_value']; // 2571
                $my_period =  $document['document_details'][0]['document_detail_attribute_values'][3]['attribute_value']; // 2572

                if ($my_period < 16) {
                    return response()->json("2022-2023 yildan oldingi period uchun bunday ariza yoza olmaysiz.");
                }

                $my_dd = DocumentDetail::query()
                    ->whereHas('documentDetailAttributeValues', function ($q) use ($my_period) {
                        $q->where('d_d_attribute_id', 2572);
                        $q->where('attribute_value', $my_period);
                    })
                    ->whereHas('documentDetailEmployees', function ($q) use ($employee_id) {
                        $q->where('employee_id', $employee_id);
                    })
                    ->whereDoesntHave('document', function ($q) use ($document) {
                        $q->where('id', $document['id']);
                    })
                    ->whereDoesntHave('document', function ($q) {
                        $q->whereIn('status', [6]);
                    })
                    ->first();
                if ($my_dd) {
                    $doc = Document::find($my_dd->document_id);
                    $employee = $doc->employee->getShortname($doc->locale);
                    if ($doc->status == 0) {
                        $status = ' Қоралама ';
                    }
                    if ($doc->status == 1 || $doc->status == 2) {
                        $status = ' Жараёнда ';
                    }
                    if ($doc->status == 3 || $doc->status == 4 || $doc->status == 5) {
                        $status = ' Имзоланган ';
                    }
                    // return [false, 'Ушбу ходим ушбу период учун ruhsatnoma аризаси мавжуд. Ариза ID: ', $dd->document_id, 1,];
                    return [false, 'Ушбу ходим ушбу период учун "Кўп йиллик иш стажи учун бериладиган қўшимча меҳнат таътили" аризаси мавжуд. Ариза ID:', $my_dd->document_id, $employee, $status];
                }
            }
        }

        if ($document['document_type_id'] == 12 || $document['document_template_id'] >= 488) {
            $result = (new CentrumController)->checkAct($document['id'], $document['document_template_id'], $document['document_details']);
            if ($result != 1) {
                return response()->json("Aktda duplikat aniqlandi. Hujjat raqami: $result[0], Yuboruvchi: $result[1], Holati: $result[2], Dubilkat aniqlangan qator: $result[3]: $result[4]");
            }
        }

        // Noaktiv templatelar
        if (config("app.APP_COMPANY_ID") == 1 && in_array($document['document_template_id'], [211, 154])) {
            return 1;
        }

        // O'zgartirilmaydigan kpi dokumentlari
        if (in_array($document['document_template_id'], [432])) {
            return "Ushbu KPI dokumentni o'zgartira olmaysiz";
        }

        if (!$document['responsible_contact']) {
            return "Masul hodim kontaktlarini kiritish majburiy.";
        }

        $model = Document::find($document['id']);

        $locale = $document['locale']; // == 'ru' ? 'ru' : 'uz_latin';
        if (in_array($document['document_template_id'], [287])) {
            $response = $this->checkUvolnitelniy($request);
            if (!$response[0])
                return $response;
        } elseif (in_array($document['document_template_id'], [12, 474])) {
            $response = $this->checkOtpusk($request);
            if (!$response[0])
                return $response;
            if ($model && $model->status < 3) {
                $response = Http::withoutVerifying()
                    ->post('https://edo-db2.uzautomotors.com/api/as400/check-otpusk', $document);
                if (!$response[0])
                    return $response;
            }
        } elseif (in_array($document['document_template_id'], [661])) {
            $response = $this->checkOtpuskModdiyJavobgar($request);
            if (!$response[0])
                return $response;
            if ($model && $model->status < 3) {
                $response = Http::withoutVerifying()
                    ->post('https://edo-db2.uzautomotors.com/api/as400/check-otpusk', $document);
                if (!$response[0])
                    return $response;
            }
        } else if (in_array($document['document_template_id'], [592])) {
            $response = $this->checkOtpuskStaj($request);
            if (!$response[0])
                return $response;
        } else if (in_array($document['document_template_id'], [552, 553, 554, 555, 556])) {


            //dd($document['document_details'][0]['document_detail_attribute_values'][0]['attribute_value']); 
            $searched_employee_id = $document['document_details'][0]['document_detail_attribute_values'][0]['attribute_value'];

            $materialResponsbile = MaterialResponsiblePeople::where('employee_id', '=', $searched_employee_id)->first();

            if (!$materialResponsbile) {
                return [false, 'Bunday Moddiy Javobgar Yo`q:'];
            }
        } else if (in_array($document['document_template_id'], [637])) {

            //dd(count($document['document_staff']));
            if (count($document['document_staff']) != 1) {
                return [false, 'Shtatni tanlash Majburiy:'];
            }
            //dd($document['document_details'][0]['document_detail_attribute_values'][0]['attribute_value']); 


        } else if (config("app.APP_COMPANY_ID") == 1 && $document['document_template_id'] == 287) {
            $model = Document::with('documentDetails')
                ->with('documentDetails')
                ->with('documentDetails.documentDetailAttributeValues')
                ->with('documentDetails.documentDetailEmployees.employee')
                ->where('id', $document['id'])->first();
            // dd($model->DocumentDetails[0]->documentDetailAttributeValues[0]->attribute_value);
            $data = [];
            $tabel = '';


            foreach ($document['document_details'] as $key => $detail) {
                // dd($detail['document_detail_employees'][$key]['tabel_number']);
                foreach ($detail['document_detail_employees'] as $k => $v) {
                    $tabel = $detail['document_detail_employees'][$k]['tabel_number'];
                    $emp = Employee::where('tabel', $tabel)->first();
                    $user = Auth::user();
                    if (!$emp) {
                        $not_found_message['uz_latin'] = "Tebel nomer notog'ri.";
                        $not_found_message['uz_cyril'] = "Табел номер нотўғри.";
                        $not_found_message['ru'] = 'Неправильный табел номер.';
                        return ['<span style="color:red;">' . $not_found_message[$locale] . '</span>', '', 0];
                    }
                    $accessDep = AccessDepartment::where('employee_id', $user->employee_id)
                        ->where('department_id', $emp->mainStaff[0]->department_id)
                        ->where('access_type_id', 1)
                        ->first();
                    $rangeCode = $emp->mainStaff[0]->range ? $emp->mainStaff[0]->range->code : false;
                    if (!$emp->mainStaff[0]) {
                        return 'Employee has not main staff.';
                    }
                    $fio = '';
                    $user_tabel = $user->employee->tabel;
                    if (strtoupper($user_tabel) != strtoupper($tabel) && !$accessDep) {
                        return "Ushbu tabel(" . $tabel . ") nomerga sizda ruhsat yo'q";
                    }
                }
            }
            // $response = Http::post('https://edo-db2.uzautomotors.com/api/as400/check-otpusk', $document);
            // if(!$response[0])
            // return $response;
        }
        DB::beginTransaction();
        try {
            $model = Document::where('id', $document['id'])->first();
            //update or insert document

            $documentType = DocumentType::find($document['document_type_id']);
            $documentTemplate = DocumentTemplate::find($document['document_template_id']);
            $up = true;
            if ($model) {
                $model->updated_by = Auth::id();
                $up = false;
            } else {
                $model = new Document();
                // $model->created_employee_id = $document['created_employee_id'] ? $document['created_employee_id'] : Auth::user()->employee_id;
                $model->created_employee_id = Auth::user()->employee_id;
                $model->document_date = date("Y-m-d H:i:s");
                $model->pdf_file_name = $this->generateNanoId();
            }
            $model->department_id = $document['department_id'];
            $model->document_type_id = $document['document_type_id'];
            $model->document_type = $documentType['name_' . $locale];
            $model->document_template_id = $document['document_template_id'];
            $model->document_template = $documentTemplate['name_' . $locale];
            $model->locale = $locale;
            if (isset($document['staff_id'])) {
                $model->staff_id = $document['staff_id'];
            } else {
                $model->staff_id = null;
            }
            if (isset($document['department2_id'])) {
                $model->department2_id = $document['department2_id'];
            } else {
                $model->department2_id = null;
            }
            $model->title = isset($document['title']) ? $document['title'] : '';
            $model->responsible_contact = isset($document['responsible_contact']) ? $document['responsible_contact'] : '';

            [
                $model->from_department,
                $model->from_manager,
                $model->from_position,
                $model->to_department,
                $model->to_manager,
                $model->to_position,
            ] = Document::getDocumentDepartmentInfo($model->created_employee_id, $model->department_id, $model->locale);

            if ($document['document_template']['is_from_to_department_show']) {
                if (isset($document['isFromStaff']) && $document['isFromStaff']) {
                    $isFromStaff = $document['isFromStaff'];
                    $model->from_staff_id = $isFromStaff;
                    [
                        $model->from_department,
                        $model->from_manager,
                        $model->from_position,
                    ] = DocumentSigner::getFromTo($isFromStaff, $model->locale);
                } else {
                    $model->from_staff_id = null;
                    [
                        $model->from_department,
                        $model->from_manager,
                        $model->from_position,
                    ] = DocumentSigner::getFromS($model->created_employee_id, $document['document_signers'], $model->locale);
                    // if (in_array(Auth::user()->employee_id, [11385,12794])) {
                    //     [
                    //         $model->from_department,
                    //         $model->from_manager,
                    //         $model->from_position,
                    //     ] = DocumentSigner::getFromS($model->created_employee_id,$document['document_signers'], $model->locale);
                    // }else{
                    //     [
                    //         $model->from_department,
                    //         $model->from_manager,
                    //         $model->from_position,
                    //     ] = DocumentSigner::getFromS($model->created_employee_id,$document['document_signers'], $model->locale);

                    // }
                    // ] = DocumentSigner::getFrom($document['document_signers'], $model->locale);
                }


                if (isset($document['isToStaff']) && $document['isToStaff']) {
                    $isToStaff = $document['isToStaff'];
                    $model->to_staff_id = $isToStaff;
                    [
                        $model->to_department,
                        $model->to_manager,
                        $model->to_position,
                    ] = DocumentSigner::getFromTo($isToStaff, $model->locale);
                } else {
                    // dd(DocumentSigner::getTo($document['document_signers'], $model->locale));
                    $model->to_staff_id = null;
                    if ($document['document_type_id'] != 7) {
                        $data = DocumentSigner::getTo($document['document_signers'], $model->locale);
                        if ($data && isset($data[0]) && $data[0]) {
                            $model->to_department = $data[0];
                        }
                        if ($data && isset($data[1]) && $data[1]) {
                            $model->to_manager = $data[1];
                        }
                        if ($data && isset($data[2]) && $data[2]) {
                            $model->to_position = $data[2];
                        }
                        // $model->to_manager,
                        // $model->to_position,
                        // [
                        //     $model->to_department,
                        //     $model->to_manager,
                        //     $model->to_position,
                        // ] 
                    }
                }
            }

            // // if ( $documentTemplate->is_from_to_department_show == 1 ) {
            // if ((isset($document['isFromStaff']) && $isToStaff = $document['isToStaff'])) {
            //     // if (in_array(Auth::user()->employee_id, [916 ,11385]) && $documentTemplate->is_from_to_department_show == 1 && isset($document['isFromStaff'])) {
            //     // if (in_array(Auth::user()->employee_id, [916, 918, 11385]) && $model->document_template_id == 414) {
            //     // dd($document);
            //     $isFromStaff = $document['isFromStaff'];
            //     $isToStaff = $document['isToStaff'];
            //     $model->from_staff_id = $isFromStaff;
            //     $model->to_staff_id = $isToStaff;
            //     [
            //         $model->from_department,
            //         $model->from_manager,
            //         $model->from_position,
            //         $model->to_department,
            //         $model->to_manager,
            //         $model->to_position,
            //     ] = DocumentSigner::getFromTo($isFromStaff, $isToStaff, $model->locale);
            //     // return
            //     // Document::get
            // } else {
            //     $model->from_staff_id = null;
            //     $model->to_staff_id = null;
            // }
            // if (in_array(Auth::user()->employee_id, [916, 918, 12562]) && $documentTemplate->is_from_to_department_show == 1 && isset($document['sender']) && isset($document['receiver'])) {
            //     [
            //         $model->from_department,
            //         $model->from_manager,
            //         $model->from_position,
            //         $model->to_department,
            //         $model->to_manager,
            //         $model->to_position,
            //     ] = Document::getSignerDepartmentInfo($document['sender'], $document['receiver'], $model->locale);
            // }
            //-------------------
            if ($model->save()) {

                // Grafik shabloni uchun
                if ($document['document_template_id'] == 620) {
                    if ($document['graphics']) {
                        $not_deleted = [];
                        foreach ($document['graphics'] as $key => $value) {
                            $graphic = Graphic::find($value['id']);
                            if (!$graphic) {
                                $graphic = new Graphic();
                            }
                            $graphic->department_id = $value['department_id'];
                            $graphic->document_id = $model->id;
                            $graphic->year = $value['year'];
                            $graphic->month = $value['month'];
                            $graphic->day = isset($value['day']) ? $value['day'] : 0;
                            $end_of_month = date('t', strtotime($graphic->year . '-' . $graphic->month . '-01'));
                            $graphic->shift = $value['shift'];
                            $graphic->all = $value['all'];
                            $graphic->fond = $value['fond'];
                            $graphic->sverx = $value['sverx'];
                            $graphic->shift = $value['shift'];
                            $graphic->abcd = $value['abcd'];
                            $graphic->command = $value['command'];
                            $graphic->d01 = isset($value['d01']) ? $value['d01'] : null;
                            $graphic->d02 = isset($value['d02']) ? $value['d02'] : null;
                            $graphic->d03 = isset($value['d03']) ? $value['d03'] : null;
                            $graphic->d04 = isset($value['d04']) ? $value['d04'] : null;
                            $graphic->d05 = isset($value['d05']) ? $value['d05'] : null;
                            $graphic->d06 = isset($value['d06']) ? $value['d06'] : null;
                            $graphic->d07 = isset($value['d07']) ? $value['d07'] : null;
                            $graphic->d08 = isset($value['d08']) ? $value['d08'] : null;
                            $graphic->d09 = isset($value['d09']) ? $value['d09'] : null;
                            $graphic->d10 = isset($value['d10']) ? $value['d10'] : null;
                            $graphic->d11 = isset($value['d11']) ? $value['d11'] : null;
                            $graphic->d12 = isset($value['d12']) ? $value['d12'] : null;
                            $graphic->d13 = isset($value['d13']) ? $value['d13'] : null;
                            $graphic->d14 = isset($value['d14']) ? $value['d14'] : null;
                            $graphic->d15 = isset($value['d15']) ? $value['d15'] : null;
                            $graphic->d16 = isset($value['d16']) ? $value['d16'] : null;
                            $graphic->d17 = isset($value['d17']) ? $value['d17'] : null;
                            $graphic->d18 = isset($value['d18']) ? $value['d18'] : null;
                            $graphic->d19 = isset($value['d19']) ? $value['d19'] : null;
                            $graphic->d20 = isset($value['d20']) ? $value['d20'] : null;
                            $graphic->d21 = isset($value['d21']) ? $value['d21'] : null;
                            $graphic->d22 = isset($value['d22']) ? $value['d22'] : null;
                            $graphic->d23 = isset($value['d23']) ? $value['d23'] : null;
                            $graphic->d24 = isset($value['d24']) ? $value['d24'] : null;
                            $graphic->d25 = isset($value['d25']) ? $value['d25'] : null;
                            $graphic->d26 = isset($value['d26']) ? $value['d26'] : null;
                            $graphic->d27 = isset($value['d27']) ? $value['d27'] : null;
                            $graphic->d28 = isset($value['d28']) ? $value['d28'] : null;
                            $graphic->d29 = isset($value['d29']) && $end_of_month > 28 ? $value['d29'] : null;
                            $graphic->d30 = isset($value['d30']) && $end_of_month > 29 ? $value['d30'] : null;
                            $graphic->d31 = isset($value['d31']) && $end_of_month > 30 ? $value['d31'] : null;
                            $graphic->save();
                            $not_deleted[] = $graphic->id;
                        }

                        foreach (Graphic::where('document_id', $model->id)->get() as $key => $value) {
                            if (!in_array($value->id, $not_deleted)) {
                                $value->delete();
                            }
                        }
                    }
                }


                $abs_detail = DocumentDetail::where('document_id', $model->id)->get();
                foreach ($abs_detail as $key => $val) {
                    $tmp = true;
                    foreach ($document['document_details'] as $k => $v) {
                        if ($val->id == $v['id']) {
                            $tmp = false;
                        }
                    }
                    if ($tmp) {
                        $docDetail = DocumentDetail::find($val->id);
                        if ($docDetail) {
                            $docDetail->delete();
                        }
                    }
                }
                foreach ($document['document_details'] as $key => $value) {
                    $documentDetail = DocumentDetail::where('id', $value['id'])->first();
                    if (!$documentDetail) {
                        $documentDetail = new DocumentDetail();
                    }
                    $documentDetail->document_id = $model->id;
                    $documentDetail->content = $value['content'];
                    $documentDetail->save();


                    $docDetailEmployees = $value['document_detail_employees'];
                    $abs = DocumentDetailEmployee::where('document_detail_id', $value['id'])->get();
                    foreach ($abs as $key => $val) {
                        $tmp = true;
                        foreach ($docDetailEmployees as $k => $v) {
                            if ($val->employee_id == $v['employee_id']) {
                                $tmp = false;
                            }
                        }
                        if ($tmp) {
                            $docDetailEmployee = DocumentDetailEmployee::where('employee_id', $val->employee_id)->where('document_detail_id', '=', $documentDetail['id'])->first();
                            if ($docDetailEmployee) {
                                $docDetailEmployee->delete();
                            }
                        }
                    }
                    foreach ($docDetailEmployees as $documentDetailKey => $detailEmployee) {
                        $employee = DocumentDetailEmployee::where('document_detail_id', '=', $documentDetail['id'])->where('employee_id', '=', $detailEmployee['employee_id'])->first();
                        if (!$employee) {
                            $employee = new DocumentDetailEmployee();
                            $employee->document_detail_id = $documentDetail->id;
                        }
                        $employee->employee_id = $detailEmployee['employee_id'];
                        $temp_employee = Employee::with('staff.position')
                            ->with('staff.department')
                            ->find($employee->employee_id);
                        $employee->employee_fio = $temp_employee->getFullname($model->locale);
                        if (count($temp_employee->staff)) {
                            $employee->employee_department = $temp_employee['staff'][0]->department['name_' . $model->locale];
                            $employee->employee_department_code = $temp_employee['staff'][0]->department['department_code'];
                            $employee->employee_position = $temp_employee['staff'][0]->position['name_' . $model->locale];
                        }
                        $employee->description = '';
                        $employee->tariff_scale_id = $temp_employee->tariff_scale_id;
                        // $employee->range_id = count($temp_employee->mainStaff)>0 ? $temp_employee->mainStaff[0]->range_id : null;
                        $employee->save();
                    }

                    // ----------------------------- Otgul dates -------------------------------------------
                    if ($document['document_template_id'] == 597) {
                        $otgulDate = OtgulDate::query()->where('document_detail_employee_id', $model->documentDetails[0]->documentDetailEmployees[0]->id)->limit(20)->delete();
                        foreach ($document['start_end_dates'] as $key => $val) {
                            $otgulDate = new OtgulDate();
                            $otgulDate->document_detail_employee_id = $model->documentDetails[0]->documentDetailEmployees[0]->id;
                            $otgulDate->start_date = $val['start_datetime'];
                            $otgulDate->end_date = $val['end_datetime'];
                            $otgulDate->save();
                        }
                    }

                    // -----------------------------coefficients -------------------------------------------
                    // eski koeffitsentlari
                    DocumentDetailCoefficient::where('document_detail_id', $value['id'])->delete();
                    if ($model->documentTemplate->change_staff && isset($value['document_detail_employees']) && count($value['document_detail_employees']) > 0) {
                        $employee = Employee::find($value['document_detail_employees'][0]['employee_id']);
                        $employeeCoefficients = $employee->employeeCoefficients;

                        foreach ($employeeCoefficients as $ecKey => $employeeCoefficient) {
                            $coefficient = new DocumentDetailCoefficient();
                            $coefficient->document_detail_id = $documentDetail->id;
                            $coefficient->tariff_scale_id = $employeeCoefficient->coefficient_id;
                            $coefficient->value = $employeeCoefficient->percent;
                            $coefficient->type = 0;
                            $coefficient->save();
                        }
                    }

                    // Yangi koeffitsent
                    if ($model->documentTemplate->change_staff && isset($value['document_detail_coefficients']) && count($value['document_detail_coefficients']) > 0) {
                        $docDetailCoefficients = $value['document_detail_coefficients'];
                        foreach ($docDetailCoefficients as $documentDetailKey => $docDetailCoefficient) {
                            if ($docDetailCoefficient['type'] == 1 && $docDetailCoefficient['tariff_scale_id']) {
                                $coefficient = new DocumentDetailCoefficient();
                                $coefficient->document_detail_id = $documentDetail->id;
                                $coefficient->tariff_scale_id = $docDetailCoefficient['tariff_scale_id'];
                                $coefficient->value = $docDetailCoefficient['value'];
                                $coefficient->type = 1;
                                $coefficient->save();
                            }
                        }
                    }
                    // ---------------------------coefficients-------------------------------

                    DocumentDetailContent::where('document_detail_id', $documentDetail->id)->delete();
                    foreach ($value['document_detail_attribute_values'] as $ddAttributeValueKey => $attributeValue) {
                        if (isset($attributeValue['table_list_id']) && $attributeValue['table_list_id'] == 14 && $attributeValue['attribute_value'] == 19 && $model->document_template_id != 581) {
                            return "Ushbu perioddan foydalana olmaysiz.";
                        }
                        $attValue = DocumentDetailAttributeValue::where('id', $attributeValue['id'])->first();
                        if (!$attValue) {
                            $attValue = new DocumentDetailAttributeValue();
                        }
                        $attValue->document_detail_id = $documentDetail->id;
                        if ($attributeValue['d_d_attribute_id'] == 1323) {
                            $a = collect($value['document_detail_attribute_values'])->first(function ($val, $key) { // Sardor -> formula
                                return $val['d_d_attribute_id'] == 1322;
                            });
                            $attValue->attribute_value =  $a['attribute_value'] ? ($a['attribute_value'] * 0.6) : 0;
                        } else {
                            $attValue->attribute_value = $attributeValue['attribute_value'];
                        }
                        // if($model->documentTemplate->id == 636){
                        //     dd($value);
                        // }
                        $attValue->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                        $attValue->save();

                        $sequence = 1;

                        if ($attributeValue['table_list_id'] && !empty($attributeValue['attribute_value'])) {
                            // dd($attributeValue['document_detail_attributes']['table_list']['table_name']);
                            // dd($attributeValue['attribute_value']);
                            $table_list = Db::table($attributeValue['document_detail_attributes']['table_list']['table_name'])->find($attributeValue['attribute_value']);
                            $colums = explode(', ', $attributeValue['document_detail_attributes']['table_list']['column_name']);
                            if ($attributeValue['document_detail_attributes']['table_list']['table_view']) {
                                foreach ($colums as $key => $colum) {
                                    $documentDetailContent = new DocumentDetailContent();
                                    $documentDetailContent->document_detail_id = $documentDetail->id;
                                    $documentDetailContent->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                                    $documentDetailContent->table_name = $attributeValue['document_detail_attributes']['table_list']['table_name'];
                                    $documentDetailContent->table_value = $attributeValue['attribute_value'] ? $attributeValue['attribute_value'] : '';
                                    $documentDetailContent->group_sequence = $attributeValue['document_detail_attributes']['sequence'];
                                    $documentDetailContent->sequence = $sequence++;
                                    $documentDetailContent->group_name = $attributeValue['document_detail_attributes']['attribute_name_' . $model->locale];
                                    $documentDetailContent->attribute_name = str_replace('_locale', '', $colum);
                                    $table_colum = str_replace('locale', $model->locale, $colum);
                                    $documentDetailContent->value = $table_list->$table_colum;
                                    $documentDetailContent->save();
                                }
                            } else {
                                $documentDetailContent = new DocumentDetailContent();
                                $documentDetailContent->value = '';
                                if ($attributeValue['d_d_attribute_id'] == 2388) {
                                    $emp = Employee::find($attributeValue['attribute_value']);
                                    if ($emp && count($emp->staff) > 0 && $dep = $emp->staff[0]->department) {

                                        $documentDetailContent->value = $dep['name_' . $model->locale];
                                        $documentDetailContent->value .= '. ' . $emp->getShortname($model->locale);
                                    }
                                } else {
                                    foreach ($colums as $key => $colum) {
                                        $table_colum = str_replace('locale', $model->locale, $colum);
                                        $documentDetailContent->value = $table_list ? $documentDetailContent->value . $table_list->$table_colum . ' ' : ' ';
                                    }
                                }
                                $documentDetailContent->document_detail_id = $documentDetail->id;
                                $documentDetailContent->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                                $documentDetailContent->table_name = $attributeValue['document_detail_attributes']['table_list']['table_name'];
                                $documentDetailContent->table_value = $attributeValue['attribute_value'] ? $attributeValue['attribute_value'] : '';
                                $documentDetailContent->group_sequence = $attributeValue['document_detail_attributes']['sequence'];
                                $documentDetailContent->sequence = $sequence++;
                                $documentDetailContent->attribute_name = $attributeValue['document_detail_attributes']['attribute_name_' . $model->locale];
                                $documentDetailContent->save();
                            }
                        } else {
                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $documentDetail->id;
                            $documentDetailContent->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                            $documentDetailContent->group_sequence = $attributeValue['document_detail_attributes']['sequence'];
                            $documentDetailContent->sequence = $sequence++;
                            $documentDetailContent->attribute_name = $attributeValue['document_detail_attributes']['attribute_name_' . $model->locale];
                            if ($attributeValue['d_d_attribute_id'] == 1323) {
                                $a = collect($value['document_detail_attribute_values'])->first(function ($val, $key) { // Sardor -> formula
                                    return $val['d_d_attribute_id'] == 1322;
                                });
                                $documentDetailContent->value = $a['attribute_value'] * 0.6;
                            } else {
                                $documentDetailContent->value = isset($attributeValue['attribute_value']) ? $attributeValue['attribute_value'] : '';
                            }
                            $documentDetailContent->save();
                        }

                        if (isset($attributeValue['table_list_id']) && $attributeValue['table_list_id'] == 15 && $document['document_template_id'] != 615 && $document['document_template_id'] != 619 && $document['document_template_id'] != 622) {
                            $document_detail_employee = DocumentDetailEmployee::where('document_detail_id', $documentDetail->id)->where('employee_id', $attributeValue['attribute_value'])->first();
                            if (!$document_detail_employee) {
                                $document_detail_employee = new DocumentDetailEmployee();
                                $document_detail_employee->document_detail_id = $documentDetail->id;
                            }
                            // dd($attributeValue['attribute_value']);
                            $temp_employee = Employee::with('staff.position')
                                ->with('staff.department')
                                ->find($attributeValue['attribute_value']);
                            $document_detail_employee->employee_fio = $temp_employee->getFullname($model->locale);
                            if (count($temp_employee->staff)) {
                                $document_detail_employee->employee_department = $temp_employee['staff'][0]->department['name_' . $model->locale];
                                $document_detail_employee->employee_position = $temp_employee['staff'][0]->position['name_' . $model->locale];
                            }
                            $document_detail_employee->employee_id = $attributeValue['attribute_value'];
                            $document_detail_employee->description = '';
                            $document_detail_employee->save();
                        }
                    }
                }
                //update or insert document signers
                foreach ($document['document_signers'] as $documentSignerKey => $documentSignerValue) {
                    $documentSigner = DocumentSigner::find($documentSignerValue['id']);

                    if (!$documentSigner) {
                        $documentSigner = new DocumentSigner();
                    } else {
                        $abs = DocumentSigner::where('document_id', $documentSignerValue['document_id'])->get();
                        foreach ($abs as $key => $val) {
                            $tmp = true;
                            foreach ($document['document_signers'] as $k => $v) {
                                if ($val->id == $v['id']) {
                                    $tmp = false;
                                }
                            }
                            if ($tmp) {
                                $docSigner = DocumentSigner::where('id', $val->id)->first();
                                if ($docSigner) {
                                    $docSigner->delete();
                                }
                            }
                        }
                    }

                    $documentSigner->document_id = $model->id;
                    $documentSigner->action_type_id = $documentSignerValue['action_type_id'];
                    $documentSigner->sequence = $documentSignerValue['sequence'];
                    $documentSigner->staff_id = $documentSignerValue['staff_id'];
                    if (isset($documentSignerValue['signer_group_id'])) {
                        $documentSigner->signer_group_id = $documentSignerValue['signer_group_id'];
                    }
                    $documentSigner->is_registry = isset($documentSignerValue['is_registry']) && $documentSignerValue['is_registry'] == 1 ? 1 : 0;
                    $documentSigner->due_date = isset($documentSignerValue['due_date']) ? $documentSignerValue['due_date'] : null;
                    // $documentSigner->due_date = isset($documentSignerValue['due_date']) ?  date("Y-m-d 18:00:00", strtotime($documentSignerValue['due_date'])) : null;
                    $staff = Staff::with('department')->with('position')->find($documentSigner->staff_id);

                    // if ($documentSigner->sequence == 99 && ($model->document_template_id == 64 || $model->document_template_id == 72 || $model->document_template_id == 81 || $model->document_template_id == 138)) {
                    //     $model->from_department = $staff->department['name_' . $model->locale];
                    //     $from_manager = Employee::whereHas('mainStaff' , function ($q) use ($documentSigner){
                    //         $q->where('staff.id', $documentSigner->staff_id);
                    //     })->first();
                    //     $model->from_manager = $from_manager->getFullName($model->locale);
                    //     $model->from_position = $staff->position['name_' . $model->locale];
                    //     $model->save();
                    // }

                    $documentSigner->department = $staff->department['name_' . $model->locale];
                    $documentSigner->position = $staff->position['name_' . $model->locale];

                    if (isset($documentSignerValue['signer_employee_id'])) {
                        $documentSigner->signer_employee_id = $documentSignerValue['signer_employee_id'];
                        $employee = Employee::find($documentSigner->signer_employee_id);
                        $count = $model->locale == 'uz_latin' ? 1 : 2;
                        $documentSigner->fio = $employee->getShortname($model->locale);
                    }
                    if (isset($documentSignerValue['status'])) {
                        $documentSigner->status = $documentSignerValue['status'];
                    }
                    $documentSigner->sign_type = $documentSignerValue['action_type_id'] == 6 ? 0 : 1;
                    if ($documentSignerValue['action_type_id'] == 13 && Auth::id() == 2785) {
                        $documentSigner->taken_datetime = date('Y-m-d H:i:s');
                        $documentSigner->due_date = date('Y-m-d H:i:s', time() + 86400);
                        $documentSigner->status = 0;
                    }
                    $documentSigner->save();
                    if ($documentSignerValue['action_type_id'] == 6 && $up) {
                        $documentSignerEvent = new DocumentSignerEvent;
                        $documentSignerEvent->document_signer_id = $documentSigner->id;
                        $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                        $documentSignerEvent->comment = 'created';
                        $documentSignerEvent->status = 0;
                        $documentSignerEvent->signer_employee_id = $documentSigner->signer_employee_id;
                        $documentSignerEvent->fio = $documentSigner->fio;
                        $documentSignerEvent->save();
                    } elseif ($documentSignerValue['action_type_id'] == 13 && (isset($documentSignerValue['taken_datetime']) && $documentSignerValue['taken_datetime'])) {
                        $documentSignerEvent = new DocumentSignerEvent;
                        $documentSignerEvent->document_signer_id = $documentSigner->id;
                        $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                        $documentSignerEvent->comment = 'changed';
                        $documentSignerEvent->status = 8;
                        $documentSignerEvent->signer_employee_id = $documentSigner->signer_employee_id;
                        $documentSignerEvent->fio = $documentSigner->fio;
                        $documentSignerEvent->save();
                    } elseif ($documentSignerValue['action_type_id'] == 6 && $document['status'] > 0 && ($documentSigner->signer_employee_id == Auth::user()->employee_id)) {
                        $documentSignerEvent = new DocumentSignerEvent;
                        $documentSignerEvent->document_signer_id = $documentSigner->id;
                        $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                        $documentSignerEvent->comment = 'changed';
                        $documentSignerEvent->status = 8;
                        $documentSignerEvent->signer_employee_id = $documentSigner->signer_employee_id;
                        $documentSignerEvent->fio = $documentSigner->fio;
                        $documentSignerEvent->save();
                    }
                }
                if ($model->documentTemplate->select_staff) {
                    foreach ($document['document_staff'] as $key => $value) {
                        $ds = DocumentStaff::where('document_id', $model->id)->where('staff_id', $value['id'])->first();
                        if (!$ds) {
                            $ds = new DocumentStaff();
                            $ds->document_id = $model->id;
                            $ds->staff_id = $value['id'];
                            $ds->save();
                        }
                    }
                }
                if ($document['document_template_id'] == 615 || $document['document_template_id'] == 619 || $document['document_template_id'] == 622) {
                    foreach ($document['complaens_answers'] as $key => $complaens_item) {
                        // dd($complaens_item);
                        $complaensAnswer = ComplaensAnswer::where('document_id', $model->id)->where('question_id', $complaens_item['question_id'])->first();
                        if (!$complaensAnswer) {
                            $complaensAnswer = new ComplaensAnswer();
                            $complaensAnswer->document_id = $model->id;
                            $complaensAnswer->employee_id = Auth::user()->employee_id;
                            $complaensAnswer->question_id = $complaens_item['question_id'];
                        }

                        if ($complaens_item['question_type'] == 3) { // FAqat komment yoziladigan savol uchun 
                            $complaensAnswer->answer = 1;
                            $complaensAnswer->description = $complaens_item['description'];
                        } else {

                            $complaensAnswer->answer = $complaens_item['answer'];
                        }

                        $complaensAnswer->description = $complaens_item['description'];
                        if ($complaensAnswer->save()) {
                            if ($complaens_item['question_type'] == 1) {

                                foreach ($complaens_item['relatives'] as $key => $value) {
                                    $relative = ComplaensRelative::where('answer_id', $complaensAnswer->id)->where('relative_id', $value['relative_id'])->first();
                                    if (!$relative) {
                                        $relative = new ComplaensRelative();
                                        $relative->answer_id = $complaensAnswer->id;
                                    }
                                    $relative->relative_id = $value['relative_id'];
                                    $relative->relative_type_id = $value['relative_type_id'];
                                    $relative->fio = $value['employee'] ? $value['employee']['firstname_' . $model->locale] . ' ' . $value['employee']['lastname_' . $model->locale] . ' ' . $value['employee']['middlename_' . $model->locale] : '';
                                    $relative->department = ($value['employee'] && $value['employee']['main_staff'][0]) ? $value['employee']['main_staff'][0]['department']['name_' . $model->locale]  : '';
                                    $relative->position = ($value['employee'] && $value['employee']['main_staff'][0]) ? $value['employee']['main_staff'][0]['position']['name_' . $model->locale] : '';
                                    $relative->save();
                                    // $model->locale
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            Document::savePdf($model->id);
            if ($model->document_template_id == 218) {
                Document::stampUploaddedPdf($model->id);
            }
            return ['message' => 'Successfully saved!', 'document_id' => $model->id, 'pdf_file_name' => $model->pdf_file_name];
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function changeRelationStatus($id)
    {
        $model = Document::with('documentDetails')
            ->with('documentDetails.documentDetailAttributeValues')
            ->with('documentDetails.documentDetailEmployees')
            ->where('id', $id)->first();

        foreach ($model->documentRelation as $key => $value) {
            $value->status = 4;
            $value->save();

            $ds = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
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
    }

    public function publish($id)
    {
        DB::beginTransaction();
        try {
            $model = Document::find($id);
            $document_type = DocumentType::where('id', $model->document_type_id)->first();
            if (!$model) {
                return 'Not document!!!';
            }

            // ------iqboljondi shkali document restr uchebniy otpusk

            if ($model->document_template_id == 582) {
                $relationDocument = $model->documentRelation;
                foreach ($relationDocument as $val) {
                    $val->status = 4;
                    $val->save();
                }
                // return ($model->documentRelation);
            }
            // --------iqboljondi shkali ocument restr uchebniy otpusk


            if ($model->documentTemplate->numbering_order == 1) {
                Document::generateDocumentNumberNew2022($id);
            }
            if (in_array($model->document_template_id, [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618])) {

                // return $model->title;
                $month = request()->month['act-month'];

                $document = Document::where('title', $model->title)->where('document_type_id', 12)->whereNotIn('status', [0, 6])->first();
                if ($document) {
                    // return $document->id;
                    return response()->json(['message' => "xato", "message2" => "Bunday akt mavjud. ID: $document->id"], 200);
                };
                $actDate = ActDate::where('document_id', $id)->first();
                if ($actDate) {

                    $actDate->act_date = $month;
                } else {

                    $actDate = new ActDate();
                    $actDate->document_id = $id;
                    $actDate->act_date = $month;
                }
                $actDate->save();
                // dd($actDate);
            }
            $model->status = 1;

            if ($model->save()) {
                foreach (DocumentSigner::where('document_id', $model->id)->whereNotIn('action_type_id', [6, 13]) as $key => $value) {
                    $value->status = 0;
                    $value->save();
                }
                $ii = 0;
                $documentSigners = [];
                // if (DocumentSigner::where('document_id', $model->id)->where('action_type_id', 13)->where('status', 0)->count()) {
                //     $documentSigners = DocumentSigner::where('document_id', $model->id)->whereIn('action_type_id', [6, 12, 13])->get();
                // } else 
                {
                    $documentSigners = DocumentSigner::where(function ($q) use ($model) {
                        $q->where('document_id', $model->id)
                            ->whereIn('action_type_id', [3, 6])
                            ->where('sequence', 100);
                    })
                        // ->orWhere(function ($q) use ($model) {
                        //     $q
                        //         ->where('action_type_id', 12)
                        //         ->where('document_id', $model->id);
                        // })
                        ->get();
                }
                foreach ($documentSigners as $documentSignerKey => $documentSignerValue) {
                    $documentSigner = DocumentSigner::find($documentSignerValue->id);
                    $documentSigner->taken_datetime = date('Y-m-d H:i:s');
                    $documentSigner->due_date = date('Y-m-d H:i:s', time() + 86400);
                    // mail yuborish uchun
                    if ($documentSigner->action_type_id == 6 && $documentSigner->status == 2) {
                        $documentSigner->status = 1;
                    }

                    $employeeStaffs = [];
                    if ($documentSignerValue->action_type_id != 6) {
                        $employeeStaffs = EmployeeStaff::where('staff_id', $documentSignerValue->staff_id)->where('is_active', 1)->get();
                    }
                    if ($documentSignerValue->action_type_id == 1) {
                        $actionType = "Rozilik";
                    } elseif ($documentSignerValue->action_type_id == 2) {
                        $actionType = "Tasdiqlash";
                    } elseif ($documentSignerValue->action_type_id == 3) {
                        $actionType = "Bo'lim ichida rozilik";
                    } elseif ($documentSignerValue->action_type_id == 4) {
                        $actionType = "Ijro uchun";
                    } else {
                        $actionType = "Ko'rib chiqish";
                    }
                    // $actionType = ActionType::find($documentSignerValue['action_type_id']);
                    foreach ($employeeStaffs as $key => $employeeStaff) {
                        $user = User::where('employee_id', $employeeStaff->employee_id)->first();
                        // if ($user && $user->email) {
                        //     $reaction_type = $actionType;
                        //     SendEmail::addToQueue($user->email, $model->id, $reaction_type);
                        // }
                    }
                    // if ($documentSignerValue->action_type_id != 12)
                    $ii++;

                    $documentSigner->save();
                    if ($documentSignerValue->action_type_id == 6) {
                        $documentSignerEvent = new DocumentSignerEvent;
                        $documentSignerEvent->document_signer_id = $documentSigner->id;
                        $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
                        $documentSignerEvent->comment = 'published';
                        $documentSignerEvent->status = 9;
                        $documentSignerEvent->signer_employee_id = $documentSigner->signer_employee_id;
                        $documentSignerEvent->fio = $documentSigner->fio;
                        $documentSignerEvent->save();
                    }
                }
                if ($ii == 1) {
                    $sequence = DocumentSigner::where('document_id', $model->id)
                        ->where('status', 0)
                        // ->where('action_type_id', "!=", 12)
                        ->orderByDesc('sequence')->first();
                    if ($sequence) {

                        $taken = DocumentSigner::where('document_id', $model->id)
                            ->where('status', 0)
                            ->where('sequence', $sequence->sequence);
                        foreach ($taken->get() as $key => $value) {
                            $due_day = DocumentSigner::select('document_signer_templates.due_day_count')
                                ->join('documents', 'documents.id', '=', 'document_signers.document_id')
                                ->join('document_templates', 'document_templates.id', '=', 'documents.document_template_id')
                                ->join('document_signer_templates', 'document_signer_templates.document_template_id', '=', 'document_templates.id')
                                ->where('document_signers.id', $value->id)->first();
                            $value->taken_datetime = date("Y-m-d H:i:s");
                            $value->due_date = isset($due_day->due_day_count) ? date("Y-m-d H:i:s", time() + 3600 * $due_day->due_day_count) : date("Y-m-d H:i:s", time() + 86400);
                            if ($value->action_type_id == 5) {
                                $value->status = 1;
                            }
                            $value->save();

                            $employeeStaffs = [];
                            if ($documentSignerValue->action_type_id != 6) {
                                $employeeStaffs = EmployeeStaff::where('staff_id', $documentSignerValue->staff_id)->where('is_active', 1)->get();
                            }
                            if ($documentSignerValue->action_type_id == 1) {
                                $actionType = "Rozilik";
                            } elseif ($documentSignerValue->action_type_id == 2) {
                                $actionType = "Tasdiqlash";
                            } elseif ($documentSignerValue->action_type_id == 3) {
                                $actionType = "Bo'lim ichida rozilik";
                            } else {
                                $actionType = "Ko'rib chiqish";
                            }
                            // foreach ($employeeStaffs as $key => $employeeStaff) {
                            //     $user = User::where('employee_id', $employeeStaff->employee_id)->first();
                            // if ($user && $user->email) {
                            //     $document_template = DocumentTemplate::find($model->document_template_id);
                            //     $mailQueue = new MailQueue();
                            //     $mailQueue->address = $user->email;

                            //     $employee = Employee::find($model->created_employee_id);
                            //     $content = [
                            //         'Hujjat turi' => $document_type->name_uz_latin,
                            //         'Raqami' => $model->document_number,
                            //         'Sanasi' => $model->document_date,
                            //         'Yaratuvchi' => $employee->getFullname('uz_latin'),
                            //         "Bo'lim" => Employee::parentDepartments($employee->tabel)['main_department'] ? Employee::parentDepartments($employee->tabel)['main_department']->name_uz_latin : '',
                            //         "Talab etiladigan amal" => $actionType,
                            //         'Link' => "http://edo.uzautomotors.com/#/document/" . $model->pdf_file_name,
                            //     ];

                            //     $mailQueue->content = json_encode($content);
                            //     $mailQueue->title = $document_template->name_uz_latin;
                            //     $mailQueue->save();
                            // }
                            // }
                        }
                        $model->status = 2;
                        $model->save();
                    }
                }

                if ($model->document_template_id == 114 || $model->document_template_id == 226) {
                    $this->changeRelationStatus($id);
                }
                // if ($model->document_template_id == 287 && $model->status != 6) {
                //     $this->uvolnitelniyCreate($model->id);
                // }
                DB::commit();
                $ds = DocumentSigner::where('document_id', $id)
                    ->where('sequence', '>', 98)
                    ->where('status', '!=', 1)
                    ->first();
                if (!$ds && $model->documentTemplate->numbering_order == 2) {
                    Document::generateDocumentNumberNew2022($id);
                }
                if ($model->document_type_id == 7) {
                    // $this->lspAddComplaense($model->id);
                }
                if (!in_array($model->document_template_id, [287, 558])) {
                    $this->allAddComplaense($model->id);
                }
                if ($model->document_template_id == 574 || $model->document_template_id == 587 || $model->document_template_id == 399) {
                    $this->changeStatusDocumentRelation($id);
                }

                Document::savePdf($model->id);
                return ['message' => 'Successfully saved!', 'document_id' => $model->id];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function allAddComplaense($document_id)
    {
        $document = Document::find($document_id);
        $staff_ids = EmployeeStaff::where('employee_id', $document->created_employee_id)->where('is_active', 1)->get()->pluck('staff_id');
        $department = Department::where(function ($q) {
            $q->where('department_code', 'ilike', '46%')
                // ->orWhere('department_code', 'ilike', '43212%')
            ;
        })
            ->whereHas('staff', function ($q) use ($staff_ids) {
                $q->whereIn('id', $staff_ids);
            })->first();
        $document_signer = DocumentSigner::where('staff_id', 4632)->where('document_id', $document_id)->where('action_type_id', 3)->first();
        $direktor = DocumentSigner::where('staff_id', 3873)->where('document_id', $document_id)->where('action_type_id', 6)->first();

        if ($department && !$document_signer && !$direktor) {
            $ds = new DocumentSigner();
            //Kudrat Sobirov Xamidovich
            $ds->staff_id = 4632;
            $ds->action_type_id = 3;
            $ds->sequence = 100;
            $ds->document_id = $document_id;
            $ds->status = 0;
            $ds->taken_datetime = date('Y-m-d H:i:s');
            $ds->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $ds->sign_type = 1;
            $ds->signer_group_id = 0;
            $ds->is_registry = 0;
            $ds->save();
        }
    }

    public function lspAddComplaense($document_id)
    {
        $document = Document::find($document_id);
        $staff_ids = EmployeeStaff::where('employee_id', $document->created_employee_id)->where('is_active', 1)->get()->pluck('staff_id');
        $department = Department::where(function ($q) {
            $q->where('department_code', 'ilike', '41%')
                ->orWhere('department_code', 'ilike', '42%')
                ->orWhere('department_code', 'ilike', '71%')
                ->orWhere('department_code', 'ilike', '73%')
                ->orWhere('department_code', 'ilike', '14%')
                ->orWhere('department_code', 'ilike', '13%')
                ->orWhere('department_code', 'ilike', '72%')
                ->orWhere('department_code', 'ilike', '85%')
                ->orWhere('department_code', 'ilike', '22%')
                // ->orWhere('department_code','ilike','43%')
                ->orWhere('department_code', 'ilike', '12%');
        })
            // ->where('department_code', '<>', '53099')
            // ->where('department_code', 'not like', '___99%')
            ->whereNotIn('department_code', ['16099', '53099'])
            ->whereHas('staff', function ($q) use ($staff_ids) {
                $q->whereIn('id', $staff_ids);
            })->first();
        try {
            $signer = DocumentSigners::where('staff_id', 5687)->where('action_type_id', 12)->first();
            if (!$department && !$signer) {
                $ds = new DocumentSigner();
                // Laziz Oripov
                $ds->staff_id = 5687;
                $ds->action_type_id = 12;
                $ds->sequence = 2;
                $ds->document_id = $document_id;
                $ds->status = 0;
                $ds->sign_type = 1;
                $ds->signer_group_id = 0;
                $ds->is_registry = 0;
                $ds->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
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
        // if($id == 179793){
        //     dd($response);
        // }
        return $response;
    }
    public function manualFileUpload($manual_files, $id)
    {
        DB::beginTransaction();
        try {
            $object_id = $id;
            if ($manual_files) {
                foreach ($manual_files as $key => $value) {
                    $filename = time() . rand();
                    Storage::putFileAs(
                        'documents_new',
                        $value,
                        $filename
                    );
                    $file = new File();
                    $file->object_type_id = 17;
                    $file->file_name = $value->getClientOriginalName();
                    $file->physical_name = $filename;
                    $file->object_id = $object_id;
                    $file->created_by = Auth::id();
                    $file->save();
                }
            }
            $manual_file = File::where('object_id', $id)->where('object_type_id', 17)->first();
            $file = storage_path('app/documents_new/' . $manual_file->physical_name);
            if (file_exists($file)) {
                Document::savePdf($id);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        // return 'sas';
    }

    public function updateFile(Request $request, $id)
    {
        DB::beginTransaction();
        $files = $request->file('files');
        $gmk_files = $request->file('gmk_files');
        $manual_files = $request->file('manual_files');
        // if ($manual_files) {
        //         $this->manualFileUpload($manual_files, $id);
        // }
        //  if($id == 2665385){
        //     return
        //     // $request; 
        //     55;
        //     // json_decode($request->input('types'));
        // }
        if ($files || $gmk_files || $manual_files) {
            try {
                $object_type_id = 5;
                $object_id = $id;
                $description = $request->input('description');
                if ($gmk_files) {
                    foreach ($gmk_files as $key => $value) {
                        $filename = time() . rand();
                        Storage::putFileAs(
                            'documents_new',
                            $value,
                            $filename
                        );
                        $file = new File();
                        $file->object_type_id = 15;
                        $file->file_name = $value->getClientOriginalName();
                        $file->physical_name = $filename;
                        $file->object_id = $object_id;
                        // $file->description = $description;
                        $file->created_by = Auth::id();
                        $file->save();
                    }
                }
                if ($files) {

                    foreach ($files as $key => $value) {
                        $filename = time() . rand();
                        Storage::putFileAs(
                            'documents_new',
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
                }
                if ($manual_files) {
                    // dd(5);
                    $this->manualFileUpload($manual_files, $id);
                }
                DB::commit();
                return ['message' => 'Successfully saved!', 'document_id' => $object_id];
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }

    public function commentFile(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $files = $request->file('files');
            $object_type_id = 6;
            $object_id = $id;

            foreach ($files as $key => $value) {
                $filename = time() . rand();
                Storage::putFileAs(
                    'documents_new',
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
            return ['message' => 'Successfully saved!', 'object_id' => $object_id];
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function updateDocumentRelation(Request $request, $id)
    {
        $parent_document = $request->all();
        foreach ($parent_document as $key => $value) {
            $model = DocumentRelation::where('document_id', $id)->where('parent_document_id', $value['parent_document_id'])->first();
            if (!$model) {
                $model = new DocumentRelation();
            }
            $model->document_id = $id;
            $model->parent_document_id = $value['parent_document_id'];
            $model->save();
        }
        return 'Successfully saved!';
    }

    public function destroyDocumentDetailEmployee($id)
    {
        DocumentDetailEmployee::find($id)->delete();
    }

    public function destroyDocumentSigner($id)
    {
        DocumentSigner::find($id)->delete();
    }

    public function destroy($id)
    {
        // DocumentDetail::where('document_id');
        DB::beginTransaction();
        try {
            $document = Document::find($id);

            if (substr($document->document_number, 0, 1) == 2) {
                $document->status = 6;
                $document->save();
                $ds = DocumentSigner::where('document_id', $id)->where('action_type_id', 6)->first();
                $ds->status = 2;
                $ds->signed_date = time();
                $ds->sign_at = date('Y-m-d H:i:s');
                $ds->save();
                DB::commit();
                return 'Successfully deleted!';
            }

            $signers = DocumentSigner::where('document_id', $id)->get();
            foreach ($signers as $key => $value) {
                DocumentSignerEvent::where('document_signer_id', $value->id)->delete();
            }
            DocumentSigner::where('document_id', $id)->delete();
            $documentDetail = DocumentDetail::where('document_id', $id)->get();
            foreach ($documentDetail as $key => $value) {
                DocumentDetailEmployee::where('document_detail_id', $value->id)->delete();
                DocumentDetailAttributeValue::where('document_detail_id', $value->id)->delete();
            }
            DocumentDetail::where('document_id', $id)->delete();
            Document::find($id)->delete();

            DB::commit();
            return 'Successfully deleted!';
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function destroyFile($id)
    {
        File::find($id)->delete();
    }

    public function destroyRelation(Request $request)
    {
        $relation = $request->all();
        DocumentRelation::where('document_id', $relation['document_id'])->where('parent_document_id', $relation['parent_document_id'])->delete();
        return 'deleted';
    }

    public function destroyDocumentStaff(Request $request)
    {
        $data = $request->all();
        DocumentStaff::where('document_id', $data['document_id'])->where('staff_id', $data['staff_id'])->delete();
        return 'deleted';
    }

    public function getNotification($locale)
    {
        $user = User::with('roles.permissions:name')->where('id', Auth::id())->first();
        // $user->online_at = date("Y-m-d H:i:s");
        // $user->save();
        $userStaff = EmployeeStaff::where('employee_id', '=', $user['employee_id'])->where('is_active', 1)->select('staff_id', 'employee_id')->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

        $documents = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotIn('action_type_id', [4, 11])
                    ->whereNotNull('taken_datetime')
                    // ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->where('status', 0);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $resolutions = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 2);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $signer_group_id = [21, 22, 23, 24, 143, 161, 241, 274, 275, 278];
        $one_date = date('Y-m-d', strtotime('+2 days'));
        $two_date = date('Y-m-d', strtotime('+3 days'));
        $three_date = date('Y-m-d', strtotime('+4 days'));
        // $three_date = date('2022-02-17 14:17:56');

        $kpi_plan_doc = Document::where('document_template_id', 431)
            ->whereIn('status', [0, 7, 9])
            ->where('created_employee_id', Auth::user()->employee->id)
            ->count();

        $length_kpi_resolution_new = KpiResolutionComission::where('resolution_id', Auth::user()->employee->id)->whereNull('comments')
            // ->where('created_at', '<', date('Y-m-d', strtotime('+20 days')))
            ->count();
        $length_kpi_resolution = KpiResolutionComission::where('resolution_id', Auth::user()->employee->id)
            // ->where('created_at', '<', date('Y-m-d', strtotime('+20 days')))
            ->count();


        $document_out_three = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $three_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [$two_date, $three_date])
                    ->whereIn('status', [0, 3]);
            })

            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $document_out_two = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $one_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [$one_date, $two_date])
                    ->whereIn('status', [0, 3]);;
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);
        $document_out_one = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $one_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereIn('status', [0, 3]);
            })

            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);


        $resolution_results = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 1);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $prosesing = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 0);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $expected = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($q) {
                $q->whereHas('documentSigners', function ($q1) {
                    $q1->select('id')
                        ->where('staff_id', 1)
                        ->whereNull('taken_datetime')
                        ->where('document_signers.status', 0);
                });
            })->whereIn('documents.status', [1, 2])
            ->orderBy('documents.document_date', 'desc')
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_number',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $star = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereIn('id', collect(DocumentBookmark::where('user_id', Auth::id())->get())->pluck('document_id'))
            // ->where(function ($q)  {
            //     $q->whereHas('documentSigners', function ($q1) {
            //         $q1->select('id')
            //         ->where('staff_id', 1)
            //         ->whereNull('taken_datetime')
            //             ->where('document_signers.status', 0);
            //     });
            // })->whereIn('documents.status',[1,2])
            ->orderBy('documents.document_date', 'desc')
            // ->whereNotIn('status', [0, 6])
            // ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_number',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $substantiate = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 4)
                        ->whereIn('is_done', [0, 1]);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $executor = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where('is_done', 0)
                    ->where('action_type_id', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('status', 0);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $info = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds)
                        ->where('action_type_id', 5);
                })
                    // ->whereHas('documentSigners', function ($q) {
                    //     $q->select('id')->whereIn('status', [0, 3, 4]);
                    // })
                ;
            })
            ->whereNotIn('status', [0, 5, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $nazorat = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 11)
                    ->whereIn('status', [0, 3, 4])
                    ->orWhereHas('parentNazorat')
                    ->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 3, 4]);
            })
            ->whereNotIn('status', [0, 4, 5, 6])
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);


        $mehmon = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    // ->where('action_type_id', '!=', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('action_type_id', 4)
                    ->where('sequence', 0)
                    ->whereIn('status', [0, 3]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->where('document_template_id', 558)
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $expired = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    // ->where('action_type_id', '!=', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '<', date("Y-m-d H:i:s"))
                    ->where('is_done', '<>', 2)
                    ->whereIn('status', [0, 3]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);
        $watcher = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    foreach ($userStaff as $key => $value) {
                        $q->orWhere('staff_id', $value->staff_id);
                    }
                    return $q;
                })
                    ->where(function ($q) use ($userStaff) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $userStaff[0]->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($userStaff) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $userStaff[0]->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->where('action_type_id', 11)
                    ->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3, 4]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $canceled = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    foreach ($userStaff as $key => $value) {
                        $q->orWhere('staff_id', $value->staff_id);
                    }
                    return $q;
                });
            })
            ->whereHas('documentSigners', function (Builder $query) {
                $query->where('status', 2)
                    ->whereNull('parent_employee_id')
                    ->where('updated_at', '>', date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month')));
            })
            ->whereHas('documentSigners', function ($q) {
                $q->select('id')
                    ->where('signer_employee_id', Auth::user()->employee_id);
            })
            ->whereDoesntHave('CancelledDocument', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 6)
            ->where('created_at', '>', '2024-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $agreement = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($q) use ($userStaffIds) {
                $q->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                    $query->select('id')->whereIn('staff_id', $userStaffIds)->where('status', 5);
                })
                    ->orWhere(function ($q1) use ($userStaffIds) {
                        $q1->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                            $query->select('id')->whereIn('staff_id', $userStaffIds)->where('action_type_id', 6);
                        })
                            ->whereDoesntHave('documentSigners', function (Builder $query) {
                                $query->where('status', 5);
                            });
                    });
            })
            ->where('status', 99)
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $employee_id = Auth::user()->employee_id;
        $akt = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where('created_employee_id', $employee_id)
            ->whereHas('documentSigners', function ($q) {
                $q->select('id')
                    ->where('action_type_id', 12)
                    ->whereNull('taken_datetime')
                    ->whereNotNull('due_date')
                    ->where('sequence', 98);
            })
            ->where('status', 0)
            ->where('document_type_id', 12)
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        return [
            // Sekin ishlayotgan querylar
            'mehmon' => $mehmon->count(),
            'length_nazorat' => $nazorat->count(),
            'nazorat' => [], // $nazorat->take(5)->get(),
            'length' => $documents->count(),
            'document' => [], // $documents->take(5)->get(),
            'executor' => [], // $executor->take(5)->get(),
            'length_executor' =>  $executor->count(),
            'expired' => [], // $expired->take(5)->get(),
            'length_expired' => $expired->count(),
            // 'unblocked_user' => config("app.APP_COMPANY_ID") == 1 && UnblockedUser::where('user_id', Auth::id())->first() ? 1 : 0,
            'information' => [], // $info->take(5)->get(),
            'length_info' => $info->count(),
            'canceled' => [], // $canceled->take(10)->get(),
            'length_canceled' => $canceled->count(),
            'agreement' => [], // $agreement->take(5)->get(),
            'length_agreement' => $agreement->count(),

            // Tez ishlayotgan querylar
            'length_akt' => $akt->count(),
            'akt' => $akt->take(5)->get(),
            'length_kpi_resolution_new' => $length_kpi_resolution_new,
            'length_kpi_resolution' => $length_kpi_resolution,
            'length_document_out_one' => $document_out_one->count(),
            'document_out_one' => [], // $document_out_one->take(5)->get(),
            'length_document_out_two' => $document_out_two->count(),
            'document_out_two' => $document_out_two->get(),
            'length_document_out_three' => $document_out_three->count(),
            'document_out_three' => [], // $document_out_three->take(5)->get(),
            'resolution_results' => [], // $resolution_results->take(3)->get(),
            'length_results' => $resolution_results->count(),
            'resolutions' => [], // $resolutions->take(5)->get(),
            'length_resolutions' => $resolutions->count(),
            'prosesing' => [], // $prosesing->take(5)->get(),
            'length_expected' => $expected->count(),
            'expected' => [], // $expected->take(5)->get(),
            'length_star' => $star->count(),
            'kpi_plan_doc' => $kpi_plan_doc,
            'star' => [], // $star->take(30)->get(),
            'length_prosesing' => $prosesing->count(),
            'substantiate' => [], // $substantiate->take(5)->get(),
            'length_substantiate' => $substantiate->count(),
            'watcher' => [], // $watcher->take(5)->get(),
            'length_watcher' => $watcher->count(),
            'online' => User::where('online_at', '>', date("Y-m-d H:i:s", time() - 60))->count(),
            'alert' => Notification::select('content')->where('is_active', 1)->get(),
        ];
    }

    public function getNotificationNew($locale)
    {
        $user = User::with('roles.permissions:name')->where('id', Auth::id())->first();
        $userStaff = EmployeeStaff::where('employee_id', '=', $user['employee_id'])->where('is_active', 1)->select('staff_id', 'employee_id')->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

        $documents = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->whereNotIn('action_type_id', [4, 11])
                    ->whereNotNull('taken_datetime')
                    // ->where('due_date', '>', date("Y-m-d H:i:s"))
                    ->where('status', 0);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $resolutions = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 2);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $signer_group_id = [21, 22, 23, 24, 143, 161, 241, 274, 275, 278];
        $one_date = date('Y-m-d', strtotime('+2 days'));
        $two_date = date('Y-m-d', strtotime('+3 days'));
        $three_date = date('Y-m-d', strtotime('+4 days'));
        // $three_date = date('2022-02-17 14:17:56');

        $kpi_plan_doc = Document::where('document_template_id', 431)
            ->whereIn('status', [0, 7, 9])
            ->where('created_employee_id', Auth::user()->employee->id);

        $kpi_resolution_new = KpiResolutionComission::where('resolution_id', Auth::user()->employee->id)->whereNull('comments');
        $document_out_three = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $three_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [$two_date, $three_date])
                    ->whereIn('status', [0, 3]);
            })

            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $document_out_two = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $one_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [$one_date, $two_date])
                    ->whereIn('status', [0, 3]);;
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);
        $document_out_one = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('DocumentSigners', function ($qr) use ($userStaff, $signer_group_id, $one_date, $two_date) {
                $qr
                    // ->whereIn('signer_group_id', $signer_group_id)
                    ->where('signer_employee_id', Auth::user()->employee_id)
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereIn('status', [0, 3]);
            })

            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);


        $resolution_results = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 1);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $prosesing = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 3)
                        ->where('is_done', 0);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $expected = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($q) {
                $q->whereHas('documentSigners', function ($q1) {
                    $q1->select('id')
                        ->where('staff_id', 1)
                        ->whereNull('taken_datetime')
                        ->where('document_signers.status', 0);
                });
            })->whereIn('documents.status', [1, 2])
            ->orderBy('documents.document_date', 'desc')
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_number',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $star = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereIn('id', collect(DocumentBookmark::where('user_id', Auth::id())->get())->pluck('document_id'))
            // ->where(function ($q)  {
            //     $q->whereHas('documentSigners', function ($q1) {
            //         $q1->select('id')
            //         ->where('staff_id', 1)
            //         ->whereNull('taken_datetime')
            //             ->where('document_signers.status', 0);
            //     });
            // })->whereIn('documents.status',[1,2])
            ->orderBy('documents.document_date', 'desc')
            // ->whereNotIn('status', [0, 6])
            // ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_number',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $substantiate = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 4)
                        ->whereIn('is_done', [0, 1]);
                });
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $executor = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where('is_done', 0)
                    ->where('action_type_id', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('status', 0);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $info = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($query) use ($userStaffIds) {
                $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                    $q->select('id')->whereIn('staff_id', $userStaffIds)
                        ->where('action_type_id', 5);
                })
                    // ->whereHas('documentSigners', function ($q) {
                    //     $q->select('id')->whereIn('status', [0, 3, 4]);
                    // })
                ;
            })
            ->whereNotIn('status', [0, 5, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $nazorat = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 11)
                    ->whereIn('status', [0, 3, 4])
                    ->orWhereHas('parentNazorat')
                    ->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 3, 4]);
            })
            ->whereNotIn('status', [0, 4, 5, 6])
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);


        $mehmon = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    // ->where('action_type_id', '!=', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('action_type_id', 4)
                    ->where('sequence', 0)
                    ->whereIn('status', [0, 3]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->where('document_template_id', 558)
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $expired = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where(function ($q) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', Auth::user()->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    // ->where('action_type_id', '!=', 4)
                    ->whereNotNull('taken_datetime')
                    ->where('due_date', '<', date("Y-m-d H:i:s"))
                    ->where('is_done', '<>', 2)
                    ->whereIn('status', [0, 3]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);
        $watcher = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    foreach ($userStaff as $key => $value) {
                        $q->orWhere('staff_id', $value->staff_id);
                    }
                    return $q;
                })
                    ->where(function ($q) use ($userStaff) {
                        $q->whereNotNull('signer_employee_id')
                            ->where('signer_employee_id', $userStaff[0]->employee_id)
                            ->orWhereNull('signer_employee_id');
                    })
                    ->where(function ($q) use ($userStaff) {
                        $q->whereNotNull('parent_employee_id')
                            ->where('signer_employee_id', $userStaff[0]->employee_id)
                            ->orWhereNull('parent_employee_id');
                    })
                    ->where('action_type_id', 11)
                    ->whereNotNull('taken_datetime')
                    ->whereIn('status', [0, 3, 4]);
            })
            ->whereNotIn('status', [0, 6])
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $canceled = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->whereHas('documentSigners', function (Builder $query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    foreach ($userStaff as $key => $value) {
                        $q->orWhere('staff_id', $value->staff_id);
                    }
                    return $q;
                });
            })
            ->whereHas('documentSigners', function (Builder $query) {
                $query->where('status', 2)
                    ->whereNull('parent_employee_id')
                    ->where('updated_at', '>', date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month')));
            })
            ->whereHas('documentSigners', function ($q) {
                $q->select('id')
                    ->where('signer_employee_id', Auth::user()->employee_id);
            })
            ->whereDoesntHave('CancelledDocument', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 6)
            ->where('created_at', '>', '2024-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $agreement = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where(function ($q) use ($userStaffIds) {
                $q->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                    $query->select('id')->whereIn('staff_id', $userStaffIds)->where('status', 5);
                })
                    ->orWhere(function ($q1) use ($userStaffIds) {
                        $q1->whereHas('documentSigners', function (Builder $query) use ($userStaffIds) {
                            $query->select('id')->whereIn('staff_id', $userStaffIds)->where('action_type_id', 6);
                        })
                            ->whereDoesntHave('documentSigners', function (Builder $query) {
                                $query->where('status', 5);
                            });
                    });
            })
            ->where('status', 99)
            ->where('created_at', '>', '2023-01-01 00:00:01')
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $employee_id = Auth::user()->employee_id;
        $akt = Document::with('documentType:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentTemplate:id,name_ru,name_uz_cyril,name_uz_latin')
            ->with('documentSigners:document_id,staff_id')
            ->where('created_employee_id', $employee_id)
            ->whereHas('documentSigners', function ($q) {
                $q->select('id')
                    ->where('action_type_id', 12)
                    ->whereNull('taken_datetime')
                    ->whereNotNull('due_date')
                    ->where('sequence', 98);
            })
            ->where('status', 0)
            ->where('document_type_id', 12)
            ->select([
                'id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'document_type_id',
                'document_template_id',
                'pdf_file_name'
            ]);

        $data = [
            [
                'count' => $mehmon->count(), 
                'link' => '/documents/list/mehmon/0',
                'color' => 'red darken-4',
                'name' => 'Mehmon', 'icon' => 'mdi-alpha-m-box-outline'],
            [
                'count' => $nazorat->count(), 
                'link' => '/documents/list/nazorat/0',
                'color' => 'red',
                'name' => 'length_nazorat', 'icon' => 'mdi-order-bool-ascending-variant'],
            [
                'count' => $documents->count(), 
                'link' => '/documents/list/notification/0',
                'color' => 'green',
                'name' => 'news_document', 'icon' => 'mdi-email-outline'],
            [
                'count' =>  $executor->count(), 
                'link' => '/documents/list/executor/0',
                'color' => 'pink darken-4',
                'name' => 'length_executor', 'icon' => 'mdi-lightning-bolt-outline'],
            [
                'count' => $expired->count(), 
                'link' => '/documents/list/expired/0',
                'color' => 'red darken-2',
                'name' => 'length_expired', 'icon' => 'mdi-fire'],
            [
                'count' => $info->count(), 
                'link' => '/documents/list/information/0',
                'color' => 'grey darken-2',
                'name' => 'length_info', 'icon' => 'mdi-flag'],
            [
                'count' => $canceled->count(), 
                'link' => '/documents/list/canceled/0',
                'color' => 'red darken-2',
                'name' => 'length_canceled', 'icon' => 'mdi-file-cancel-outline'],
            [
                'count' => $agreement->count(), 
                'link' => '/documents/list/agreement/0',
                'color' => 'light-green darken-2',
                'name' => 'length_agreement', 'icon' => 'mdi-file-check-outline'],
            [
                'count' => $akt->count(), 
                'link' => '/documents/list/nazorat/0',
                'color' => 'orange',
                'name' => 'length_akt', 'icon' => 'mdi-repeat'],
            [
                'count' => $document_out_one->count(), 
                'link' => '',
                'color' => '',
                'name' => 'length_document_out_one', 'icon' => 'mdi-numeric-1-box-multiple'],
            [
                'count' => $document_out_two->count(), 
                'link' => '/documents/list/document_out_one/0',
                'color' => 'red',
                'name' => 'length_document_out_two', 'icon' => 'mdi-numeric-2-box-multiple'],
            [
                'count' => $document_out_three->count(), 
                'link' => '/documents/list/document_out_three/0',
                'color' => 'light-blue',
                'name' => 'length_document_out_three', 'icon' => 'mdi-numeric-3-box-multiple'],
            [
                'count' => $resolution_results->count(), 
                'link' => '/documents/list/resolution_results/0',
                'color' => 'light-blue darken-4',
                'name' => 'length_results', 'icon' => 'mdi-bell-check-outline'],
            [
                'count' => $resolutions->count(), 
                'link' => '/documents/list/resolutions/0',
                'color' => 'light-blue darken-4',
                'name' => 'length_resolutions', 'icon' => 'mdi-bell-plus-outline'],
            //['count' => $expected->count(), [linkcount' =>'',[color' =>'','name'=>'length_expected', 'icon'=> 'mdi-email-outline','color' => 'black'], // ishlatilmayapti
            [
                'count' => $star->count(), 
                'link' => '/documents/list/star/0',
                'color' => '#85d5ff',
                'name' => 'length_star', 'icon' => 'mdi-star-outline'],
            [
                'count' => $prosesing->count(), 
                'link' => '',
                'color' => '',
                'name' => 'length_prosesing', 'icon' => 'mdi-timer-sand'],
            [
                'count' => $substantiate->count(), 
                'link' => '/documents/list/processing/0',
                'color' => 'deep-purple darken-2',
                'name' => 'length_substantiate', 'icon' => 'mdi-alert-outline'],
            [
                'count' => $watcher->count(), 
                'link' => '/documents/list/watcher/0',
                'color' => 'success darken-2',
                'name' => 'length_watcher', 'icon' => 'mdi-magnify'],
            [
                'count' => $kpi_resolution_new->count(), 
                'link' => '/kpi-assistant',
                'color' => 'light-blue',
                'name' => 'length_kpi_resolution_new', 'icon' => 'mdi-finance'],
            [
                'count' => $kpi_plan_doc->count(),
                'link' => '/documents/list/kpi_plan_doc/0',
                'color' => 'light-blue',
                'name' => 'kpi_plan_doc', 'icon' => 'mdi-finance', 'color' => 'red'],
        ];

        return ['notifications' => $data, 'other' => [
            'online' => User::where('online_at', '>', date("Y-m-d H:i:s", time() - 60))->count(),
            'alert' => Notification::select('content')->where('is_active', 1)->get(),
        ]];
    }

    public function signing(Request $request)
    {
        return '8';
    }

    public function getStaff()
    {
        $staffs = Staff::select('staff.id', 'employees.id as employee_id')
            ->join('employee_staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->join('employees', 'employee_staff.employee_id', '=', 'employees.id')
            ->join('users', 'users.employee_id', '=', 'employees.id')
            ->where('users.id', Auth::id())
            ->where('employee_staff.is_active', 1)
            ->where('employee_staff.deleted_at', null)
            ->get();

        $get_staff = Staff::with(['department.staff.employeeStaff' => function ($query) {
            $query->with('employee')
                ->where('is_active', '=', 1);
        }])
            ->with('department.children.staff.employees')
            ->with('department.children.staff.position')
            ->with('department.staff.position');
        foreach ($staffs as $key_staff => $staff) {
            $get_staff->orWhere('id', $staff->id);
        }

        return $get_staff->get();
    }

    public function getHistory($id)
    {
        // return 0;
        // return ['history' => $this->getTree($id, null), 'tree_ids' => DocumentSignerEvent::join('document_signers', 'document_signer_events.document_signer_id', 'document_signers.id')
        //     ->select('document_signer_events.id')
        //     ->where('document_signers.document_id', $id)->pluck('id')->toArray()];

        return ['history' => $this->getTree($id, null), 'tree_ids' => DocumentSigner::where('document_id', $id)->pluck('id')->toArray()];
    }

    public function getTree($id, $parent_employee_id, $step = 0)
    {
        $signers = DocumentSigner::with(['comments' => function ($q) {
            $q->select('id', 'document_signer_id', 'comment', 'status', 'created_at')
                ->with('files')
                ->orderBy('id', 'desc');
        }])
            ->select('id', 'status', 'fio', 'parent_employee_id', 'signer_employee_id')
            ->where('document_id', $id)
            ->whereHas('comments')
            //->whereNotNull('signer_employee_id')
            //->where('status', '!=', 0)
            ->where('parent_employee_id', $parent_employee_id)
            ->orderBy('taken_datetime', 'desc')
            ->orderBy('signed_date', 'desc')
            ->get();
        $childs = [];
        foreach ($signers as $key => $value) {
            $childs[$key] = $value;
            if ($value->signer_employee_id && $value->signer_employee_id != $parent_employee_id && $step < 200) {
                $childs[$key]['children'] = $this->getTree($id, $value->signer_employee_id, $step + 1);
            } else {
                $childs[$key]['children'] = [];
            }
        }
        return $childs;
    }

    public function getFile($id)
    {
        $document_files = File::where('object_id', $id)
            ->whereIn('object_type_id', [5, 15])
            ->get();
        return $document_files;
    }

    public function getListOld()
    {
        $document_types = DocumentType::select('id', 'name_ru', 'name_uz_cyril', 'name_uz_latin')->orderBy('name_uz_latin')->get();
        $document_types_array = $document_types->toArray();
        $user = User::with('roles')->find(Auth::id());

        $document_list = array(
            0 => array(
                'count' => null,
                'route' => '/documents/list/inbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'inbox',
                'document_types' => $document_types_array,
            ),
            1 => array(
                'count' => null,
                'route' => '/documents/list/outbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'outbox',
                'document_types' => $document_types->toArray(),
            ),
            2 => array(
                'count' => null,
                'route' => '/documents/list/draft',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'draft',
                'document_types' => $document_types_array,
            ),
            3 => array(
                'count' => null,
                'route' => '/documents/list/cancel',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'canceled',
                'document_types' => $document_types_array,
            ),
            // 4 => array(
            //     'count' => null,
            //     'route' => '/documents/list/all',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'all',
            // ),
            // 5 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allhr',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allhr',
            // ),
            // 6 => array(
            //     'count' => null,
            //     'route' => '/documents/list/alllsp',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'alllsp',
            // ),
            // 7 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allznz',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allznz',
            // ),
        );

        // $staffs = Staff::select('id')
        //     ->whereHas('employeeStaff', function (Builder $query) use ($user) {
        //         $query->select('id')->where('employee_id', $user->employee_id)
        //             ->where('is_active', 1);
        //     })
        //     ->get();

        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');

        foreach ($document_list as $key_list => $filter) {
            foreach ($document_types as $key_type => $value_type) {
                if ($filter['menu_item'] == 'inbox') {
                    $documents = Document::select('id')
                        ->whereHas('documentSigners', function ($q) use ($userStaffIds, $user) {
                            $q->where(function ($qu) use ($userStaffIds) {
                                $qu->whereIn('staff_id', $userStaffIds);
                            })
                                ->whereNotIn('action_type_id', [3, 6])
                                ->where(function ($q) use ($user) {
                                    return $q->whereNotNull('signer_employee_id')
                                        ->where('signer_employee_id', $user->employee_id)
                                        ->orWhereNull('signer_employee_id');
                                })
                                ->whereNotNull('taken_datetime');
                        })
                        ->where('document_type_id', $value_type->id)
                        ->whereNotIn('status', [0, 6]);

                    $document_list[$key_list]['document_types'][$key_type]['count'] = $documents->first() ? 1 : 0;

                    $documents = Document::whereHas('documentSigners', function ($q) use ($userStaffIds, $user) {
                        $q->select('id')->where(function ($qu) use ($userStaffIds) {
                            $qu->whereIn('staff_id', $userStaffIds);
                            // foreach ($staffs as $key => $value) {
                            //     $qu->orWhere('staff_id', $value->id);
                            // }
                        })
                            ->where('action_type_id', '!=', 3)
                            ->where('action_type_id', '!=', 6)
                            ->where(function ($q) use ($user) {
                                return $q->whereNotNull('signer_employee_id')
                                    ->where('signer_employee_id', $user->employee_id)
                                    ->orWhereNull('signer_employee_id');
                            })
                            ->whereNotNull('taken_datetime')
                            ->where(function ($qu) {
                                $qu->whereIn('status', [0, 3, 4]);
                            });
                    })
                        ->where('document_type_id', $value_type->id)
                        ->whereNotIn('status', [0, 6]);
                    $document_list[$key_list]['document_types'][$key_type]['count_new'] = $documents->count();
                } elseif ($filter['menu_item'] == 'outbox') {
                    $documents = Document::whereHas('documentSigners', function ($q) use ($userStaffIds, $user) {
                        $q->select('id')->where(function ($qu) use ($userStaffIds) {
                            $qu->whereIn('staff_id', $userStaffIds);
                            // foreach ($staffs as $key => $value) {
                            //     $qu->orWhere('staff_id', $value->id);
                            // }
                        })
                            ->where(function ($q) {
                                $q->where('action_type_id', 3)
                                    ->orWhere('action_type_id', 6);
                            })
                            ->where(function ($q) use ($user) {
                                return $q->whereNotNull('signer_employee_id')
                                    ->where('signer_employee_id', $user->employee_id)
                                    ->orWhereNull('signer_employee_id');
                            })
                            ->whereNotNull('taken_datetime');
                    })
                        ->where('document_type_id', $value_type->id)
                        ->whereNotIn('status', [0, 6]);

                    $document_list[$key_list]['document_types'][$key_type]['count'] = $documents->count();

                    $documents = Document::whereHas('documentSigners', function ($q) use ($userStaffIds, $user) {
                        $q->select('id')->where(function ($qu) use ($userStaffIds) {
                            $qu->whereIn('staff_id', $userStaffIds);
                            // foreach ($staffs as $key => $value) {
                            //     $qu->orWhere('staff_id', $value->id);
                            // }
                        })
                            ->where(function ($q) {
                                $q->where('action_type_id', 3)
                                    ->orWhere('action_type_id', 6);
                            })
                            ->where(function ($q) use ($user) {
                                return $q->whereNotNull('signer_employee_id')
                                    ->where('signer_employee_id', $user->employee_id)
                                    ->orWhereNull('signer_employee_id');
                            })
                            ->whereNotNull('taken_datetime')
                            ->where(function ($qu) {
                                $qu->whereIn('status', [0, 3, 4]);
                            });
                    })
                        ->where('document_type_id', $value_type->id)
                        ->whereNotIn('status', [0, 6]);

                    $document_list[$key_list]['document_types'][$key_type]['count_new'] = $documents->count();
                } elseif ($filter['menu_item'] == 'draft') {
                    $documents = Document::where('created_employee_id', $user->employee_id)
                        ->where('document_type_id', $value_type->id)
                        ->where('status', '=', 0);

                    $document_list[$key_list]['document_types'][$key_type]['count'] = $documents->count();
                } elseif ($filter['menu_item'] == 'canceled') {
                    $documents = Document::whereHas('documentSigners', function ($q) use ($user, $userStaffIds) {
                        $q->select('id')
                            ->whereIn('staff_id', $userStaffIds)
                            ->where('signer_employee_id', $user->employee_id);
                    })
                        ->where('document_type_id', $value_type->id)
                        ->where('status', '=', 6);

                    $document_list[$key_list]['document_types'][$key_type]['count'] = $documents->count();
                }
            }
        }

        return $document_list;
    }

    public function getList()
    {
        $user = Auth::user();
        $document_types = DocumentType::select('id', 'name_ru', 'name_uz_cyril', 'name_uz_latin')->orderBy('name_uz_latin')->get();
        $document_types_array = $document_types->toArray();
        // $user = User::with('roles')->find($user->id);
        $employee_id = $user->employee_id;


        $document_list = array(
            0 => array(
                'count' => null,
                'route' => '/documents/list/inbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'inbox',
                // 'document_types' => $document_types_array,
            ),
            1 => array(
                'count' => null,
                'route' => '/documents/list/outbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'outbox',
                // 'document_types' => $document_types->toArray(),
            ),
            2 => array(
                'count' => null,
                'route' => '/documents/list/draft',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'draft',
                // 'document_types' => $document_types_array,
            ),
            3 => array(
                'count' => null,
                'route' => '/documents/list/cancel',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'canceled',
                // 'document_types' => $document_types_array,
            ),
            // 4 => array(
            //     'count' => null,
            //     'route' => '/documents/list/all',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'all',
            // ),
            // 5 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allhr',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allhr',
            // ),
            // 6 => array(
            //     'count' => null,
            //     'route' => '/documents/list/alllsp',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'alllsp',
            // ),
            // 7 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allznz',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allznz',
            // ),
        );

        // Inbox ---------------------------------------------------------------------------------
        $inbox = DB::select("select dt.id, d.id docid, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, dt.name_uz_cyril, dt.name_ru,
            count(ds.id) count,
            sum(case when ds.status in (0,3,4) then 1 else 0 end) count_new
            from document_signers ds
            inner join documents d on d.id=ds.document_id
            INNER join document_types dt on dt.id=d.document_type_id
            inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
            where es.employee_id=" . $employee_id . " and
                ds.action_type_id not in (3,6) and 
                d.status not in (0,6) and
                ds.taken_datetime is not null and 
                (ds.signer_employee_id is null or ds.signer_employee_id = " . $employee_id . ")
            group by dt.id");
        // return 1;
        $document_list[0]['document_types'] = $inbox;
        // // Outbox --------------------------------------------------------------------------------
        $outbox = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, dt.name_uz_cyril, dt.name_ru,
                    count(ds.id) count,
                    sum(case when ds.status in (0,3,4) then 1 else 0 end) count_new
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
                    where es.employee_id=" . $employee_id . " and
                        ds.action_type_id in (3,6) and 
                        d.status not in (0,6) and
                        ds.taken_datetime is not null and 
                        (ds.signer_employee_id is null or ds.signer_employee_id = " . $employee_id . ")
                    group by dt.id");
        $document_list[1]['document_types'] = $outbox;
        // // Cancel --------------------------------------------------------------------------------
        $cancel = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, count(ds.id) count
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
                    where es.employee_id=" . $employee_id . " and d.status=6 and ds.signer_employee_id = " . $employee_id . "
                    group by dt.id");
        $document_list[3]['document_types'] = $cancel;
        // // Draft ---------------------------------------------------------------------------------
        $draft = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, count(ds.id) count
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    where d.status=0 and d.created_employee_id = " . $employee_id . "
                    group by dt.id");
        $document_list[2]['document_types'] = $draft;

        return $document_list;
    }

    public function getListNew()
    {
        $user = Auth::user();
        $document_types = DocumentType::select('id', 'name_ru', 'name_uz_cyril', 'name_uz_latin')->orderBy('name_uz_latin')->get();
        $document_types_array = $document_types->toArray();
        // $user = User::with('roles')->find($user->id);
        $employee_id = $user->employee_id;


        $document_list = array(
            0 => array(
                'count' => null,
                'route' => '/documents/list/inbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'inbox',
                // 'document_types' => $document_types_array,
            ),
            1 => array(
                'count' => null,
                'route' => '/documents/list/outbox',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'outbox',
                // 'document_types' => $document_types->toArray(),
            ),
            2 => array(
                'count' => null,
                'route' => '/documents/list/draft',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'draft',
                // 'document_types' => $document_types_array,
            ),
            3 => array(
                'count' => null,
                'route' => '/documents/list/cancel',
                'icon' => 'mdi-file-document-outline',
                'menu_item' => 'canceled',
                // 'document_types' => $document_types_array,
            ),
            // 4 => array(
            //     'count' => null,
            //     'route' => '/documents/list/all',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'all',
            // ),
            // 5 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allhr',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allhr',
            // ),
            // 6 => array(
            //     'count' => null,
            //     'route' => '/documents/list/alllsp',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'alllsp',
            // ),
            // 7 => array(
            //     'count' => null,
            //     'route' => '/documents/list/allznz',
            //     'icon' => 'mdi-file-document-outline',
            //     'menu_item' => 'allznz',
            // ),
        );

        // Inbox ---------------------------------------------------------------------------------
        $inbox = DB::select("select dt.id, min(d.id) docid, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, dt.name_uz_cyril, dt.name_ru,
            count(ds.id) count,
            sum(case when ds.status in (0,3,4) then 1 else 0 end) count_new
            from document_signers ds
            inner join documents d on d.id=ds.document_id
            INNER join document_types dt on dt.id=d.document_type_id
            inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
            where es.employee_id=" . $employee_id . " and
                ds.action_type_id not in (3,6) and 
                ds.is_done <> 2 and 
                d.status not in (0,6) and
                ds.taken_datetime is not null and 
                (ds.signer_employee_id is null or ds.signer_employee_id = " . $employee_id . ")
            group by dt.id");
        // return 1;
        $document_list[0]['document_types'] = $inbox;
        // // Outbox --------------------------------------------------------------------------------
        $outbox = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, dt.name_uz_cyril, dt.name_ru,
                    count(ds.id) count,
                    sum(case when ds.status in (0,3,4) then 1 else 0 end) count_new
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
                    where es.employee_id=" . $employee_id . " and
                        ds.action_type_id in (3,6) and 
                        d.status not in (0,6) and
                        ds.taken_datetime is not null and 
                        (ds.signer_employee_id is null or ds.signer_employee_id = " . $employee_id . ")
                    group by dt.id");
        $document_list[1]['document_types'] = $outbox;
        // // Cancel --------------------------------------------------------------------------------
        $cancel = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, count(ds.id) count
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    inner join employee_staff es on es.staff_id=ds.staff_id and es.is_active = true
                    where es.employee_id=" . $employee_id . " and d.status=6 and ds.signer_employee_id = " . $employee_id . "
                    group by dt.id");
        $document_list[3]['document_types'] = $cancel;
        // // Draft ---------------------------------------------------------------------------------
        $draft = DB::select("select dt.id, dt.name_uz_latin, dt.name_uz_cyril, dt.name_ru, count(ds.id) count
                    from document_signers ds
                    inner join documents d on d.id=ds.document_id
                    INNER join document_types dt on dt.id=d.document_type_id
                    where d.status=0 and d.created_employee_id = " . $employee_id . "
                    group by dt.id");
        $document_list[2]['document_types'] = $draft;

        return $document_list;
    }

    public function getReport(Request $request)
    {
        return 0;
        $document_signer = DocumentSigner::select(
            'document_signers.document_id',
            'document_signers.staff_id',
            'document_signers.taken_datetime',
            'document_signers.signer_employee_id',
            'document_signers.status as sign_status',
            'documents.status as doc_status'
        )
            ->whereNotNull('taken_datetime')
            ->with(['staff.employeeStaff' => function ($q) {
                $q->where('is_active', 1);
            }])
            ->leftjoin('documents', 'document_signers.document_id', 'documents.id')
            ->where(function ($q) {
                $q->where('document_signers.status', '!=', 0)
                    ->orWhere('documents.status', '!=', 6);
            })
            // ->where('documents.status', '!=', 6)
            ->get();

        $report = [];

        foreach ($document_signer as $key => $value) {
            if ($value->signer_employee_id) {
                $arr = [
                    'employee_id' => $value->signer_employee_id,
                    'staff_id' => $value->staff_id,
                ];
                if (!in_array($arr, $report, true)) {
                    $report[] = $arr;
                }
            } else {
                foreach ($value->staff->employeeStaff as $key_staff => $value_staff) {
                    $arr = [
                        'employee_id' => $value_staff->employee_id,
                        'staff_id' => $value->staff_id,
                    ];
                    if (!in_array($arr, $report, true)) {
                        $report[] = $arr;
                    }
                }
            }
        }
        foreach ($report as $key => $value) {
            $report[$key]['id'] = $key + 1;
            $report[$key]['employee'] = Employee::find($value['employee_id']);
            $report[$key]['staff'] = Staff::with('position')->with('department')->find($value['staff_id']);
            $create_document = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where('document_signers.action_type_id', 6)
                ->where('document_signers.signer_employee_id', $value['employee_id'])
                ->whereNotNull('document_signers.taken_datetime')
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $draft_document = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where('document_signers.action_type_id', 6)
                ->where('document_signers.signer_employee_id', $value['employee_id'])
                ->whereNull('document_signers.taken_datetime')
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $expired = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where('document_signers.status', '!=', 1)
                ->where('document_signers.status', '!=', 2)
                // ->where('document_signers.due_date', '<', date("Y-m-d H:i:s"))
                ->where(function ($q) use ($value) {
                    return $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', $value['employee_id'])
                        ->orWhereNull('signer_employee_id');
                })
                ->where('document_signers.staff_id', $value['staff_id'])
                ->whereHas('documents', function (Builder $query) {
                    $query->where('status', '!=', 6);
                })
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $waiting = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where('document_signers.status', 0)
                ->where('document_signers.due_date', '>', date("Y-m-d H:i:s"))
                ->where(function ($q) use ($value) {
                    return $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', $value['employee_id'])
                        ->orWhereNull('signer_employee_id');
                })
                ->where('document_signers.staff_id', $value['staff_id'])
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id')
                ->whereHas('documents', function (Builder $query) {
                    $query->where('status', '!=', 6);
                });
            $prosesing = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where('document_signers.status', 3)
                ->where('document_signers.due_date', '>', date("Y-m-d H:i:s"))
                ->where(function ($q) use ($value) {
                    return $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', $value['employee_id'])
                        ->orWhereNull('signer_employee_id');
                })
                ->where('document_signers.staff_id', $value['staff_id'])
                ->whereHas('documents', function (Builder $query) {
                    $query->where('status', '!=', 6);
                })
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $completed_on_time = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->where(function ($query) {
                    return $query->where('document_signers.status', 1)
                        ->orWhere('document_signers.status', 2);
                })
                ->where('document_signers.due_date', '>', 'document_signers.updated_at')
                ->where('document_signers.signer_employee_id', $value['employee_id'])
                ->where('document_signers.staff_id', $value['staff_id'])
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $failed_in_time = DocumentSigner::select('document_signers.document_id', 'documents.document_number')
                ->whereIn('document_signers.status', [1, 2])
                ->where('document_signers.due_date', '<', 'document_signers.updated_at')
                ->where('document_signers.signer_employee_id', $value['employee_id'])
                ->where('document_signers.staff_id', $value['staff_id'])
                ->leftjoin('documents', 'document_signers.document_id', 'documents.id');
            $report[$key]['create_document'] = $create_document->count();
            $report[$key]['create_documents'] = $create_document->get();
            $report[$key]['waiting'] = $waiting->count();
            $report[$key]['waiting_documents'] = $waiting->get();
            $report[$key]['prosesing'] = $prosesing->count();
            $report[$key]['prosesing_documents'] = $prosesing->get();
            $report[$key]['expired'] = $expired->count();
            $report[$key]['expired_documents'] = $expired->get();
            $report[$key]['draft'] = $draft_document->count();
            $report[$key]['draft_documents'] = $draft_document->get();
            $report[$key]['completed_on_time'] = $completed_on_time->count();
            $report[$key]['completed_on_time_documents'] = $completed_on_time->get();
            $report[$key]['failed_in_time'] = $failed_in_time->count();
            $report[$key]['failed_in_time_documents'] = $failed_in_time->get();
        }
        return $report;
    }
    public function getDocumentReports(Request $request)
    {
        // $all_count = 0;
        // $doc_eri = 0;
        // $doc_ad = 0;
        //All document's number
        // $all_count = DB::select("SELECT count('*') as all_count FROM documents
        // WHERE documents.status != 0 AND documents.status !=1");
        // $doc_eri = DB::select("SELECT COUNT('*') as doc_eri from documents left JOIN document_signers ON documents.id = document_signers.document_id WHERE documents.status != 0 AND documents.status !=1 AND document_signers.sign_type = 1");

        $all_count = DB::select("SELECT count('*') as all_count FROM document_signers WHERE document_signers.action_type_id != 6");

        $doc_eri = DB::select("SELECT count('*') as doc_eri FROM document_signers WHERE document_signers.sign_type = 1");

        $doc_ad = DB::select("SELECT count('*') as ddoc_adoc_eri FROM document_signers WHERE document_signers.sign_type = 0");

        // $doc_ad = ($all_count - $doc_eri);

        // return [
        //     'all_count' => $all_count,
        //     'doc_eri' => $doc_eri,
        //     'doc_ad' => $doc_ad,
        // ];
    }
    public function getDocumentSignerReport(Request $request)
    {
        $all_count = 0;
        $doc_eri = 0;
        $doc_ad = 0;

        // $all_count = DB::select("SELECT count('*') as all_count FROM document_signers WHERE document_signers.action_type_id != 6  AND document_signers.status in(1,2)");

        // $doc_eri = DB::select("SELECT count(id) as doc_eri FROM document_signers WHERE document_signers.action_type_id != 6  AND document_signers.status in(1,2) and document_signers.sign_type=1");

        // $doc_ad = DB::select("SELECT count(id) as doc_ad FROM document_signers WHERE document_signers.action_type_id != 6  AND document_signers.status in(1,2) and document_signers.sign_type=0");

        return ['all_count' => $all_count, 'doc_eri' => $doc_eri, 'doc_ad' => $doc_ad];
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

    public function vacationRegistryDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }
    public function categoryChangeRegistryDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }

    public function ishRejimiRegistryDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }

    public function otgulRegistryDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }

    public function businessTripDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }

    public function educationRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(582);
            $model = new Document();
            $model->document_template_id = 582;
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

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'O`quv Tatil buyruq.';
                        $dde_new->save();

                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        foreach ($ddas as $ddak => $dda) {
                            $emp = Employee::find($dde->employee_id);
                            // $parent_department = Employee::parentDepartments($emp->tabel);
                            // $main_department = $parent_department['main_department'];
                            $department = $dde->employee->staff[0]->department;
                            // try {
                            //     $department = $dde->employee->staff[0]->department;
                            // } catch (\Throwable $th) {
                            //     dd($dde->employee);
                            // }
                            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                            $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            if ($dda->id == 2495) { // bolim kodi
                                $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            }
                            // elseif ($dda->id == 439) { // Xat nomeri
                            //     $document_detail_attribute_value->attribute_value = $value->document_number;
                            // } 
                            elseif ($dda->id == 2496) { // tabel
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 2497) { // fio
                                $document_detail_attribute_value->attribute_value =
                                    $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                            } elseif ($dda->id == 2498) { // chiqish
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2488])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 2499) { // qachongacha
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 2501) { // qaytish
                                $chiqish = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                                // $day = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                                // return [$chiqish->attribute_value, $day->attribute_value];
                                $return_date = date('Y-m-d', strtotime($chiqish->attribute_value . '+1 day'));
                                $document_detail_attribute_value->attribute_value = $return_date ? $return_date : '';
                            }
                            // elseif ($dda->id == 359) { // tatil davri
                            //     $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [421, 1521])->first();
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            // } 
                            elseif ($dda->id == 2500) { // tatil kuni
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            }
                            // elseif ($dda->id == 363) { // Qo'shimcha to'lov
                            //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 325);
                            //     $document_detail_attribute_value->attribute_value = $attr && $attr->attribute_value && $attr->attribute_value == 1 ? '1 оклад' : '';
                            // } elseif ($dda->id == 362) { // tatil turi
                            //     // return $document_detail_attribute_value;
                            //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 158);
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            //     if (!$attr) {
                            //         if ($value->document_template_id == 12) {
                            //             $document_detail_attribute_value->attribute_value = 1;
                            //         } else if ($value->document_template_id == 474) {
                            //             $document_detail_attribute_value->attribute_value = 2;
                            //         }
                            //     }
                            //     // return $dda;
                            // }
                            $document_detail_attribute_value->save();


                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;

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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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
    public function categoryChangeRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(642);
            $model = new Document();
            $model->document_template_id = 642;
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
                $lastcategory = null;
                // dd($value->documentDetails[0]->documentDetailSignerAttributes);
                foreach ($value->documentDetails[0]->documentDetailSignerAttributes as $keyddsa => $ddsa) {
                    if ($ddsa->d_d_attribute_id == 2900) {
                        $lastcategory = $ddsa->value;
                    }
                }
                if ($lastcategory) {
                    $ts = TariffScale::find($lastcategory);
                    $lastcategory1 = $ts ? $ts->category : '';
                }
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

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'Kategoriya o`zgartirish buyruq.';
                        $dde_new->save();

                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        foreach ($ddas as $ddak => $dda) {
                            $emp = Employee::find($dde->employee_id);
                            // $parent_department = Employee::parentDepartments($emp->tabel);
                            // $main_department = $parent_department['main_department'];
                            $department = $dde->employee->staff[0]->department;
                            $position = $dde->employee_position;
                            $toifa = $dde->tariffScale->category;
                            $range = $dde->employee->staff[0]->range->code;
                            // try {
                            //     $department = $dde->employee->staff[0]->department;
                            // } catch (\Throwable $th) {
                            //     dd($dde->employee);
                            // }
                            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                            $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            if ($dda->id == 2904) { // bolim kodi
                                $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            }
                            if ($dda->id == 2905) { // bolim nomi
                                $document_detail_attribute_value->attribute_value = $department ? $department['name_' . $leng] : '';
                            }
                            // elseif ($dda->id == 439) { // Xat nomeri
                            //     $document_detail_attribute_value->attribute_value = $value->document_number;
                            // } 
                            elseif ($dda->id == 2903) { // tabel
                                $document_detail_attribute_value->attribute_value = $position;
                            } elseif ($dda->id == 2901) { // tabel
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 2902) { // fio
                                $document_detail_attribute_value->attribute_value =
                                    $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                            } elseif ($dda->id == 2906) { // diapazon
                                $document_detail_attribute_value->attribute_value = $range;
                            } elseif ($dda->id == 2907) { // toifa
                                $document_detail_attribute_value->attribute_value = $toifa;
                            } elseif ($dda->id == 2908) { // ornatilgan
                                $document_detail_attribute_value->attribute_value = $lastcategory ? $lastcategory1 : '';
                            }
                            //  elseif ($dda->id == 2499) { // qachongacha
                            //     $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            // }
                            $document_detail_attribute_value->save();


                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;

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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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
    public function tabelRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $docCount = count($request->input('ids'));
            // return $docCount;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(631);
            $model = new Document();
            $model->document_template_id = 631;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = $request->input('locale');
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            $table = '';
            $table .= '<table border="1" style="width:100%; ">';
            $table .= '<thead>';
            $table .= '<tr>';
            $table .= '<th>#</th>';
            $table .= '<th >' . 'FISH' . '</th>';
            $table .= '<th >' . 'Lavozim' . '</th>';
            $table .= '<th >' . 'Bo`lim kodi' . '</th>';
            $table .= '<th >' . 'Bo`lim' . '</th>';
            $table .= '</tr>';
            $table .= '</thead>';
            $table .= '<tbody>';
            foreach ($documents as $key => $value) {
                $dr = new DocumentRelation();
                $dr->document_id = $model->id;
                $dr->parent_document_id = $value->id;
                $dr->save();


                foreach ($value->documentDetails as $dk => $dd) {
                    foreach ($dd->documentDetailEmployees as $ddek => $dde) {

                        // return $document_template->documentDetailTemplates[0]['content_'.$model->locale];
                        $table .= '<tr>';
                        $table .= '<td style="width: 5px;">';
                        $table .= $key + 1;
                        $table .= '</td>';
                        $table .= '<td style="width: 30%;">';
                        $table .= $dde->employee_fio;
                        $table .= '</td>';
                        $table .= '<td style="width: 20%;">';
                        $table .= $dde->employee_position;
                        $table .= '</td>';
                        $table .= '<td style="width: 15%;">';
                        $table .= $dde->employee_department_code;
                        $table .= '</td>';
                        $table .= '<td style="width: 30%;">';
                        $table .= $dde->employee_department;
                        $table .= '</td>';
                        $table .= '</tr>';
                        if ($key == $docCount - 1) {
                            $table .= '</tbody>';
                            $table .= '</table>';
                            $document_detail = new DocumentDetail();
                            $document_detail->document_id = $model->id;

                            $content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                            if (strpos($content, '@tab')) {
                                $content = str_replace('@tab', $table, $content);
                            }

                            $document_detail->content = $content;
                            $document_detail->save();
                            $dde_new = new DocumentDetailEmployee();
                            $dde_new->document_detail_id = $document_detail->id;
                            $dde_new->employee_id = $dde->employee_id;
                            $dde_new->description = 'Tabel buyruq.';
                            $dde_new->save();
                        }
                        // else {
                        //     $document_detail->content = '';
                        // }


                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        // foreach ($ddas as $ddak => $dda) {
                        //     $emp = Employee::find($dde->employee_id);
                        //     // $parent_department = Employee::parentDepartments($emp->tabel);
                        //     // $main_department = $parent_department['main_department'];
                        //     $department = $dde->employee->staff[0]->department;
                        //     // try {
                        //     //     $department = $dde->employee->staff[0]->department;
                        //     // } catch (\Throwable $th) {
                        //     //     dd($dde->employee);
                        //     // }
                        //     $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                        //     $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                        //     $document_detail_attribute_value = new DocumentDetailAttributeValue();
                        //     $document_detail_attribute_value->document_detail_id = $document_detail->id;
                        //     $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                        //     if ($dda->id == 2495) { // bolim kodi
                        //         $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                        //     }
                        //     // elseif ($dda->id == 439) { // Xat nomeri
                        //     //     $document_detail_attribute_value->attribute_value = $value->document_number;
                        //     // } 
                        //     elseif ($dda->id == 2496) { // tabel
                        //         $document_detail_attribute_value->attribute_value = $emp->tabel;
                        //     } elseif ($dda->id == 2497) { // fio
                        //         $document_detail_attribute_value->attribute_value =
                        //             $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                        //     } elseif ($dda->id == 2498) { // chiqish
                        //         $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2488])->first();
                        //         $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        //     } elseif ($dda->id == 2499) { // qachongacha
                        //         $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                        //         $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        //     } elseif ($dda->id == 2501) { // qaytish
                        //         $chiqish = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                        //         // $day = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                        //         // return [$chiqish->attribute_value, $day->attribute_value];
                        //         $return_date = date('Y-m-d', strtotime($chiqish->attribute_value . '+1 day'));
                        //         $document_detail_attribute_value->attribute_value = $return_date ? $return_date : '';
                        //     }
                        //     // elseif ($dda->id == 359) { // tatil davri
                        //     //     $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [421, 1521])->first();
                        //     //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        //     // } 
                        //     elseif ($dda->id == 2500) { // tatil kuni
                        //         $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                        //         $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        //     }
                        //     // elseif ($dda->id == 363) { // Qo'shimcha to'lov
                        //     //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 325);
                        //     //     $document_detail_attribute_value->attribute_value = $attr && $attr->attribute_value && $attr->attribute_value == 1 ? '1 оклад' : '';
                        //     // } elseif ($dda->id == 362) { // tatil turi
                        //     //     // return $document_detail_attribute_value;
                        //     //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 158);
                        //     //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        //     //     if (!$attr) {
                        //     //         if ($value->document_template_id == 12) {
                        //     //             $document_detail_attribute_value->attribute_value = 1;
                        //     //         } else if ($value->document_template_id == 474) {
                        //     //             $document_detail_attribute_value->attribute_value = 2;
                        //     //         }
                        //     //     }
                        //     //     // return $dda;
                        //     // }
                        //     $document_detail_attribute_value->save();


                        //     $documentDetailContent = new DocumentDetailContent();
                        //     $documentDetailContent->document_detail_id = $document_detail->id;
                        //     $documentDetailContent->d_d_attribute_id = $dda->id;
                        //     $documentDetailContent->group_sequence = 1;
                        //     $documentDetailContent->sequence = 1;
                        //     $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                        //     $documentDetailContent->value = $document_detail_attribute_value->attribute_value;

                        //     $documentDetailContent->save();
                        // }
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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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
    public function lspRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->with('documentDetails.documentDetailContents')
                ->with('documentDetails.documentDetailSignerAttributes')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(610);
            $model = new Document();
            $model->document_template_id = 610;
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
                    $document_detail = new DocumentDetail();
                    $document_detail->document_id = $model->id;

                    // return $document_template->documentDetailTemplates[0]['content_'.$model->locale];
                    if ($key == 0) {
                        $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                    } else {
                        $document_detail->content = '';
                    }
                    $document_detail->save();

                    // echo $dd->documentDetailAttributeValues;
                    $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                    // return $ddas;
                    foreach ($ddas as $ddak => $dda) {
                        $document_detail_attribute_value = new DocumentDetailAttributeValue();
                        $document_detail_attribute_value->document_detail_id = $document_detail->id;
                        $document_detail_attribute_value->d_d_attribute_id = $dda->id;

                        if ($dda->id == 2705) { // To'lov kelishuv varogi Num
                            $document_detail_attribute_value->attribute_value = $value->document_number;
                        } elseif ($dda->id == 2706) { // To'lov kelishuv varogi sanasi
                            $document_detail_attribute_value->attribute_value = $value->document_date;
                        } elseif ($dda->id == 2707) { // KOntragent nomi
                            $attr = collect($dd->documentDetailContents)->whereIn('d_d_attribute_id', [366, 381, 605, 632, 806, 1397, 2454, 2471, 2535, 2552, 2635, 2654, 2745, 2804, 2849])->where('attribute_name', 'name')->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->value : '';
                        } elseif ($dda->id == 2708) { // To'lov maqsadi
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [367, 382, 606, 629, 807, 1394, 2457, 2474, 2538, 2555, 2640, 2659, 2750, 2809, 2854])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2709) { // Shartnoma raqami
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [371, 386, 610, 633, 811, 1398, 2460, 2477, 2541, 2558, 2643, 2662, 2753, 2812, 2857])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2710) { // To'lov amalga oshirilishi kerak bolgan oxirgi sana
                            $attr = collect($dd->documentDetailSignerAttributes)->whereIn('d_d_attribute_id', [2679, 2680, 2681, 2682, 2683, 2684, 2685, 2686, 2688, 2689, 2726, 2728, 2762, 2821, 2866])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->value : '';
                            // $document_detail_attribute_value->attribute_value = 1;
                        } elseif ($dda->id == 2711) { // Shartnoma summasi
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [374, 389, 613, 636, 814, 1402, 2464, 2481, 2545, 2562, 2647, 2666, 2757, 2816, 2861])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2712) { // Shartnoma oldindan to'lov
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [375, 390, 614, 637, 815, 1403, 2465, 2482, 2546, 2563, 2648, 2667, 2758, 2817, 2862])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2713) { // Shartnoma joriy to'lov
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [376, 391, 615, 638, 816, 1404, 2466, 2483, 2547, 2564, 2649, 2668, 2759, 2818, 2863])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2714) { // Qoldiq
                            $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [378, 393, 617, 640, 818, 1406, 2468, 2485, 2549, 2566, 2651, 2670, 2761, 2820, 2865])->first();
                            $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                        } elseif ($dda->id == 2715) { // Reyestrga kiritilgan sana
                            $document_detail_attribute_value->attribute_value = date('d.m.Y');
                        }

                        $document_detail_attribute_value->save();


                        $documentDetailContent = new DocumentDetailContent();
                        $documentDetailContent->document_detail_id = $document_detail->id;
                        $documentDetailContent->d_d_attribute_id = $dda->id;
                        $documentDetailContent->group_sequence = 1;
                        $documentDetailContent->sequence = 1;
                        $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                        $documentDetailContent->value = $document_detail_attribute_value->attribute_value;

                        $documentDetailContent->save();
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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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

    public function otgulRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(606);
            $model = new Document();
            $model->document_template_id = 606;
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

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'Otgul (3 kungacha) buyruq.';
                        $dde_new->save();

                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        foreach ($ddas as $ddak => $dda) {
                            $emp = Employee::find($dde->employee_id);
                            // $parent_department = Employee::parentDepartments($emp->tabel);
                            // $main_department = $parent_department['main_department'];
                            $department = $dde->employee->staff[0]->department;
                            // try {
                            //     $department = $dde->employee->staff[0]->department;
                            // } catch (\Throwable $th) {
                            //     dd($dde->employee);
                            // }
                            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                            $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            // if ($dda->id == 2495) { // bolim kodi
                            //     $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            // }
                            // elseif ($dda->id == 439) { // Xat nomeri
                            //     $document_detail_attribute_value->attribute_value = $value->document_number;
                            // } 
                            if ($dda->id == 2674) { // tabel
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 2673) { // fio
                                $document_detail_attribute_value->attribute_value =
                                    $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                            } elseif ($dda->id == 2675) { // chiqish
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2595])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 2676) { // qachongacha
                                $chiqish = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2595])->first();
                                $day = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2597])->first();
                                // return [$chiqish->attribute_value, $day->attribute_value];
                                $return_date = $this->getReturnDate($chiqish->attribute_value, $day->attribute_value);
                                $document_detail_attribute_value->attribute_value = $return_date ? $return_date : '';
                            } elseif ($dda->id == 2677) { // ishlagan soati
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2597])->first();
                                $soat = '';
                                if ($attr) {
                                    $soat = $attr->attribute_value * 8;
                                }
                                $document_detail_attribute_value->attribute_value = $soat;
                            } elseif ($dda->id == 2678) { // necha kunga
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2597])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            }
                            // elseif ($dda->id == 2501) { // qaytish
                            //     $chiqish = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2489])->first();
                            //     // $day = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                            //     // return [$chiqish->attribute_value, $day->attribute_value];
                            //     $return_date = date('Y-m-d', strtotime($chiqish->attribute_value . '+1 day'));
                            //     $document_detail_attribute_value->attribute_value = $return_date ? $return_date : '';
                            // }
                            // elseif ($dda->id == 359) { // tatil davri
                            //     $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [421, 1521])->first();
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            // } 
                            // elseif ($dda->id == 2500) { // tatil kuni
                            //     $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [2490])->first();
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            // }
                            // elseif ($dda->id == 363) { // Qo'shimcha to'lov
                            //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 325);
                            //     $document_detail_attribute_value->attribute_value = $attr && $attr->attribute_value && $attr->attribute_value == 1 ? '1 оклад' : '';
                            // } elseif ($dda->id == 362) { // tatil turi
                            //     // return $document_detail_attribute_value;
                            //     $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 158);
                            //     $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            //     if (!$attr) {
                            //         if ($value->document_template_id == 12) {
                            //             $document_detail_attribute_value->attribute_value = 1;
                            //         } else if ($value->document_template_id == 474) {
                            //             $document_detail_attribute_value->attribute_value = 2;
                            //         }
                            //     }
                            //     // return $dda;
                            // }
                            $document_detail_attribute_value->save();


                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;

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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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
    public function vacationRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(114);
            $model = new Document();
            $model->document_template_id = 114;
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

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'Tatil buyruq.';
                        $dde_new->save();

                        // echo $dd->documentDetailAttributeValues;
                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        // return $ddas;
                        foreach ($ddas as $ddak => $dda) {
                            $emp = Employee::find($dde->employee_id);
                            // $parent_department = Employee::parentDepartments($emp->tabel);
                            // $main_department = $parent_department['main_department'];
                            $department = $dde->employee->staff[0]->department;
                            // try {
                            //     $department = $dde->employee->staff[0]->department;
                            // } catch (\Throwable $th) {
                            //     dd($dde->employee);
                            // }
                            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                            $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            if ($dda->id == 354) { // bolim kodi
                                $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            } elseif ($dda->id == 439) { // Xat nomeri
                                $document_detail_attribute_value->attribute_value = $value->document_number;
                            } elseif ($dda->id == 355) { // tabel
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 356) { // fio
                                $document_detail_attribute_value->attribute_value =
                                    $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                            } elseif ($dda->id == 357) { // chiqish
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [159, 1519, 2569])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 358) { // qaytish
                                $chiqish = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [159, 1519, 2569])->first();
                                $day = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [160, 1520, 2570])->first();
                                // return [$chiqish->attribute_value, $day->attribute_value];
                                $return_date = $this->getReturnDate($chiqish->attribute_value, $day->attribute_value);
                                $document_detail_attribute_value->attribute_value = $return_date ? $return_date : '';
                            } elseif ($dda->id == 359) { // tatil davri
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [421, 1521, 2572])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 360) { // tatil kuni
                                $attr = collect($dd->documentDetailAttributeValues)->whereIn('d_d_attribute_id', [160, 1520, 2570])->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 363) { // Qo'shimcha to'lov
                                $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 325);
                                $document_detail_attribute_value->attribute_value = $attr && $attr->attribute_value && $attr->attribute_value == 1 ? '1 оклад' : '';
                            } elseif ($dda->id == 362) { // tatil turi
                                // return $document_detail_attribute_value;
                                $attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 158);
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                if (!$attr) {
                                    if ($value->document_template_id == 12) {
                                        $document_detail_attribute_value->attribute_value = 1;
                                    } else if ($value->document_template_id == 474) {
                                        $document_detail_attribute_value->attribute_value = 2;
                                    } else if ($value->document_template_id == 592) {
                                        $document_detail_attribute_value->attribute_value = 16;
                                    }
                                }
                                // return $dda;
                            }
                            $document_detail_attribute_value->save();


                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            if ($dda->id == 362) {
                                $table_list = DB::table('vocations_prikaz')->find($document_detail_attribute_value->attribute_value);
                                $column_name = 'name_' . $model->locale;
                                $documentDetailContent->value = $table_list ? $table_list->$column_name : '';
                            } else if ($dda->id == 359) {
                                $table_list = DB::table('vocation_periods')->find($document_detail_attribute_value->attribute_value);
                                $column_name = 'period';
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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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

    public function ishRejimiRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::find(399);
            // $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(399);
            $model = new Document();
            $model->document_template_id = 399;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = $request->input('locale');
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            foreach ($documents as $key => $value) {
                $dr = new DocumentRelation();
                $dr->document_id = $value->id;
                $dr->parent_document_id = $model->id;
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

                        $dde_new = new DocumentDetailEmployee();
                        $dde_new->document_detail_id = $document_detail->id;
                        $dde_new->employee_id = $dde->employee_id;
                        $dde_new->description = 'Tatil buyruq.';
                        $dde_new->save();

                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        foreach ($ddas as $ddak => $dda) {
                            $emp = Employee::find($dde->employee_id);
                            $department = $dde->employee->staff[0]->department;

                            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                            $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            if ($dda->id == 2826) { // dep kodi
                                $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            } elseif ($dda->id == 2827) { // Tab nomeri
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 2828) { // Fio
                                $document_detail_attribute_value->attribute_value = $emp->getShortname($model->locale);
                            } elseif ($dda->id == 2829) { // start date
                                $attr = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 93)->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                            } elseif ($dda->id == 2830) { // Ish vaqti
                                $attr = collect($dd->documentDetailContents)->where('d_d_attribute_id', 2801)->first();
                                $document_detail_attribute_value->attribute_value = $attr ? $attr->value : '';
                            } elseif ($dda->id == 2831) { // end date
                                $attr1 = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 93)->first();
                                $attr2 = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 94)->first();
                                if ($attr1->attribute_value == $attr2->attribute_value) {
                                    $document_detail_attribute_value->attribute_value = ' ';
                                } else {
                                    $attr = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 94)->first();
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->attribute_value : '';
                                }
                            } elseif ($dda->id == 2832) { // Ish vaqti2
                                $attr1 = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 93)->first();
                                $attr2 = collect($dd->documentDetailAttributeValues)->where('d_d_attribute_id', 94)->first();
                                if ($attr1->attribute_value == $attr2->attribute_value) {
                                    $document_detail_attribute_value->attribute_value = ' ';
                                } else {
                                    $attr = collect($dd->documentDetailContents)->where('d_d_attribute_id', 2802)->first();
                                    $document_detail_attribute_value->attribute_value = $attr ? $attr->value : '';
                                }
                            } elseif ($dda->id == 2833) { // Asos
                                $document_detail_attribute_value->attribute_value = $value->document_number_reg ? $value->document_number_reg : $value->document_number;
                            }
                            $document_detail_attribute_value->save();


                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $dda->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                            // if ($dda->id == 362) {
                            //     $table_list = DB::table('vocations_prikaz')->find($document_detail_attribute_value->attribute_value);
                            //     $column_name = 'name_' . $model->locale;
                            //     $documentDetailContent->value = $table_list ? $table_list->$column_name : '';
                            // } else if ($dda->id == 359) {
                            //     $table_list = DB::table('vocation_periods')->find($document_detail_attribute_value->attribute_value);
                            //     $column_name = 'period';
                            //     $documentDetailContent->value = $table_list ? $table_list->$column_name : '';
                            // } else {
                            // }
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
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
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
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
            // throw $th;
            // dd($th->getMessage());

            return response()->json(["message" => $th->getMessage(), "data" => $th], 500);
        }
    }

    public function getReturnDate($date, $day)
    {
        // return [$date,$day];
        // $date = '2020-10-01';
        // $day=3;
        // return $date;
        $wc = WorkCalendar::where('calendar_date', $date)->first();
        $step = 0;

        while (!$wc->is_work_day) {
            $date = date('Y-m-d', strtotime($date . '+1 day'));
            $wc = WorkCalendar::where('calendar_date', $date)->first();
            if (++$step > 10) {
                throw new Exception("Ish kunini belgilashda hatolik yuz berdi.1");
            }
        }
        $wc = WorkCalendar::where('calendar_date', $date)->first();
        $step = 0;
        while ($day > 0) {
            $date = date('Y-m-d', strtotime($date . '+1 day'));
            $wc = WorkCalendar::where('calendar_date', $date)->first();
            if (/*date('D', strtotime($date)) != 'Sun' && */(!$wc || $wc->is_work_day == 1)) {
                $day--;
            }
            if (++$step > 50) {
                throw new \Exception("Ish kunini belgilashda hatolik yuz berdi.2");
            }
        }
        $step = 0;
        while (/*date('D', strtotime($date)) == 'Sun' || */!!$wc && $wc->is_work_day != 1) {
            $date = date('Y-m-d', strtotime($date . '+1 day'));
            $wc = WorkCalendar::where('calendar_date', $date)->first();
            if (++$step > 35) {
                throw new \Exception("Ish kunini belgilashda hatolik yuz berdi.3");
            }
        }
        return $date;

        // $wd = $wc->work_day_sequence;
        // $wc = WorkCalendar::where('work_day_sequence', $wd+$day)->first();
        // return $wc->calendar_date;
    }
    public function getEducationReturnDate($date)
    {
        $date = date('Y-m-d', strtotime($date . '+1 day'));
        $wc = WorkCalendar::where('calendar_date', $date)->first();
        // $step = 0;
        for ($i = 0; $i < 3; $i++) {
            if ($wc->is_weekend) {
                $date = date('Y-m-d', strtotime($date . '+1 day'));
                $wc = WorkCalendar::where('calendar_date', $date)->first();
            } else {
                return $date;
            }
        }
        // while (!$wc->is_weekend) {
        //     $date = date('Y-m-d', strtotime($date . '+1 day'));
        //     $wc = WorkCalendar::where('calendar_date', $date)->first();
        // }
        // return $date;
    }
    public function editAttribute(Request $request)
    {

        // $att = $request->input()
        $edit_attribute = DocumentDetailAttributeValue::find($request['id']);
        if ($edit_attribute) {
            $edit_attribute->attribute_value = $request['attribute_value'];
            $edit_attribute->save();

            return "Successfully seved";
        }
        return 0;
    }

    public function salaryInfo($tabel, $locale)
    {
        $response = Http::get('http://edo-db2.uzautomotors.com/api/salary-cart/view/' . $tabel . '/' . $locale);
        $emp = Employee::where('tabel', $tabel)->first();
        $user = Auth::user();
        if (!$emp) {
            $not_found_message['uz_latin'] = "Tebel nomer notog'ri.";
            $not_found_message['uz_cyril'] = "Табел номер нотўғри.";
            $not_found_message['ru'] = 'Неправильный табел номер.';
            return ['<span style="color:red;">' . $not_found_message[$locale] . '</span>', '', 0];
        }
        $accessDep = AccessDepartment::where('employee_id', $user->employee_id)
            ->where('department_id', $emp->staff[0]->department_id)
            ->where('access_type_id', 1)
            ->first();
        // dd($emp->staff[0]->department_id);
        $rangeCode = $emp->staff[0]->range ? $emp->staff[0]->range->code : false;
        if (!$emp->staff[0]) {
            return 'Server Error.';
        }
        $fio = '';
        $user_tabel = $user->employee->tabel;
        if ($user_tabel != '9592' && $user_tabel != $tabel && !($accessDep && (in_array(substr($rangeCode, 0, 1), ['L', 'F']) || !$rangeCode)) && $user_tabel != 'D561') {

            $not_found_message['uz_latin'] = "Ushbu tebel nomer uchun sizda ruhsat yo'q.";
            $not_found_message['uz_cyril'] = "Ушбу табел номер учун сизда рухсат йўқ. ";
            $not_found_message['ru'] = 'У вас нет разрешения для этого табел номера';
            return ['<span style="color:red;">' . $not_found_message[$locale] . '</span>', $fio, 0];
        }
        return $response;
        $tabel = strtoupper($tabel);
        $table = Z502ptpf::where('z502tn', $tabel)->first() ? 'Z500' : 'Z01';
        $dep_type_id = ($emp ? $emp->staff[0]->department->department_type_id : 0);
        if ($table == 'Z01') {
            $fio = Z01ptpf::where($table . 'tn', $tabel)->select($table . 'fio as fio')->latest($table . 'yy')->first();
        } else {
            $fio = Z500ptpf::where($table . 'tn', $tabel)->select($table . 'fio as fio')->latest($table . 'yy')->first();
        }


        if (date('d') < 9) {
            $date = date('Y-m', strtotime(date('Y-m-d') . ' -13 month'));
        } else {
            $date = date('Y-m', strtotime(date('Y-m-d') . ' -12 month'));
        }
        // return [$y = substr($date, 0,4),$m = substr($date, 5,2)];
        $sum = [$locale == 'uz_latin' ? 'Jami:' : ($locale == 'uz_cyril' ? 'Жами:' : 'Итого:'), 0, 0, 0, 0, 0, 0, 0];
        $headers['ru'] = [
            'Год месяц',
            'Заработная плата',
            'Премия',
            'Другие выплаты, (за питание и др.)',
            'Льготный подоходный налог',
            'Подоходный налог',
            'Прочие суммы (жил.облигации, удержание по исполнительным листам, алименты и другие)',
            'Итого виплат',
        ];
        $headers['uz_latin'] = [
            'Yil, oy',
            'Ish haqi',
            'Mukofot',
            "Boshqa to'lovlar (Oziq ovqat va boshqalar.)",
            "Imtiyozli daromad solig'i",
            "Daromad solig'i",
            'Boshqa summalar (uy-joy majburiyatlari, ijro varaqasidan ushlab qolish, aliment va boshqalar)',
            "Jami to'lov",
        ];
        $headers['uz_cyril'] = [
            'Йил, ой',
            'Иш ҳақи',
            'Мукофот',
            'Бошқа тўловлар (Озиқ овқат ва бошқалар)',
            'Имтиёзли даромад солиғи',
            'Даромад солиғи',
            'Бошқа суммалар (Уй-жой мажбуриятлари, ижро варақасидан ушлаб қолиш, алимент ва бошқалар)',
            'Жами тўлов',
        ];
        $months['uz_latin'] = ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'];
        $months['uz_cyril'] = ['Январ', 'Февраль', 'Март', 'Апрель', 'Май', 'Июн', 'Июл', 'Август', 'Сентябр', 'Октябр', 'Ноябр', 'Декабр'];
        $months['ru'] = ['Январ', 'Февраль', 'Март', 'Апрель', 'Май', 'Июн', 'Июл', 'Август', 'Сентябр', 'Октябр', 'Ноябр', 'Декабр'];
        $html = '<table style="border-collapse: collapse; width: 100%; height: 44px;" border="1"><tbody><tr>';
        $html .= '<td style="font-weight:bold;width:12%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][0] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:12%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][1] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:12%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][2] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:13%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][3] . '</span></td>';
        // $html .= '<td style="font-weight:bold;width:13%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][4] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:13%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][5] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:13%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][6] . '</span></td>';
        $html .= '<td style="font-weight:bold;width:10%;text-align:center;"><span style="font-size: 8pt;">' . $headers[$locale][7] . '</span></td>';
        $html .= '</tr>';
        $tabel = substr($tabel, 0, 4);
        for ($i = 0; $i < 12; $i++) {
            $y = substr($date, 0, 4);
            $m = substr($date, 5, 2);
            $month_name = $months[$locale][intval($m) - 1];
            if ($table == 'Z01') {
                $tmp0 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select([$table . 'YY as fild01', $table . 'MM as fild02'])->latest($table . 'yy')->first();
                if ($tmp0) {
                    $html .= '<tr>';
                    $tmp1 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z01vnach-z01fild2-z01fild13 as fild11'))->latest($table . 'yy')->first();
                    $tmp2 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z01fild2+z01fild13 as fild21'))->latest($table . 'yy')->first();
                    $tmp3 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z01j as fild31'))->latest($table . 'yy')->first();
                    // $tmp4 = Z01ptpf::where($table.'tn', $tabel)->where($table.'yy', $y)->where($table.'mm', $m)->select(DB::raw('z01fild8 as fild41'))->latest($table.'yy')->first();
                    $tmp5 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z01pntm as fild51'))->latest($table . 'yy')->first();
                    $tmp6 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('Z01PRFV+Z01GAZ+Z01ELEKTR+Z01SUV+Z01KOEF+Z01ALM+Z01GOSP+Z01FILD5+Z01POSHLIN+Z01NAK+Z01KOMRAS+Z01PRUD+Z01FILD3+Z01FILD12+Z01ALTSL as fild61'))->latest($table . 'yy')->first();
                    $tmp7 = Z01ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z01vnach-(z01fild8+z01pntm+z01penf+Z01PRFV+Z01GAZ+Z01ELEKTR+Z01SUV+Z01KOEF+Z01ALM+Z01GOSP+Z01FILD5+Z01POSHLIN+Z01NAK+Z01KOMRAS+Z01PRUD+Z01FILD3+Z01FILD12+Z01ALTSL) as fild71'))->latest($table . 'yy')->first();

                    $sum[1] += $tmp1['fild11'];
                    $sum[2] += $tmp2['fild21'];
                    $sum[3] += $tmp3['fild31'];
                    // $sum[4] += $tmp4['fild41'];
                    $sum[5] += $tmp5['fild51'];
                    $sum[6] += $tmp6['fild61'];
                    $sum[7] += $tmp7['fild71'];

                    $html .= '<td style="text-align:left;padding:2px;"><span style="font-size: 9pt;">' . $tmp0['fild01'] . '-' . $month_name . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp1['fild11'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp2['fild21'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp3['fild31'], 0, '', ',') . '</span></td>';
                    // $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp4['fild41'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp5['fild51'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp6['fild61'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp7['fild71'], 0, '', ',') . '</span></td>';

                    $html .= '</tr>';
                }
            } else {
                $tmp0 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select([$table . 'YY as fild01', $table . 'MM as fild02'])->latest($table . 'yy')->first();
                if ($tmp0) {
                    $html .= '<tr>';
                    $tmp1 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z500vnach-z500fild2-z500fild13 as fild11'))->latest($table . 'yy')->first();
                    $tmp2 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z500fild2+z500fild13 as fild21'))->latest($table . 'yy')->first();
                    $tmp3 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z500j as fild31'))->latest($table . 'yy')->first();
                    // $tmp4 = Z500ptpf::where($table.'tn', $tabel)->where($table.'yy', $y)->where($table.'mm', $m)->select(DB::raw('z01fild8 as fild41'))->latest($table.'yy')->first();
                    $tmp5 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z500pntm as fild51'))->latest($table . 'yy')->first();
                    $tmp6 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('Z500PRFV+Z500GAZ+Z500ELEKTR+Z500SUV+Z500KOEF+Z500ALM+Z500GOSP+Z500FILD5+Z500POSHLI+Z500NAK+Z500KOMRAS+Z500PRUD+Z500FILD3+Z500FILD12+Z500ALTSL as fild61'))->latest($table . 'yy')->first();
                    $tmp7 = Z500ptpf::where($table . 'tn', $tabel)->where($table . 'yy', $y)->where($table . 'mm', $m)->select(DB::raw('z500vnach-(z500fild8+z500pntm+z500penf+Z500PRFV+Z500GAZ+Z500ELEKTR+Z500SUV+Z500KOEF+Z500ALM+Z500GOSP+Z500FILD5+Z500POSHLI+Z500NAK+Z500KOMRAS+Z500PRUD+Z500FILD3+Z500FILD12+Z500ALTSL) as fild71'))->latest($table . 'yy')->first();

                    $sum[1] += $tmp1['fild11'];
                    $sum[2] += $tmp2['fild21'];
                    $sum[3] += $tmp3['fild31'];
                    // $sum[4] += $tmp4['fild41'];
                    $sum[5] += $tmp5['fild51'];
                    $sum[6] += $tmp6['fild61'];
                    $sum[7] += $tmp7['fild71'];

                    $html .= '<td style="text-align:left;padding:2px;"><span style="font-size: 9pt;">' . $tmp0['fild01'] . '-' . $month_name . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp1['fild11'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp2['fild21'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp3['fild31'], 0, '', ',') . '</span></td>';
                    // $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp4['fild41'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp5['fild51'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp6['fild61'], 0, '', ',') . '</span></td>';
                    $html .= '<td style="text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($tmp7['fild71'], 0, '', ',') . '</span></td>';

                    $html .= '</tr>';
                }
            }
            $date = date('Y-m', strtotime($date . ' +1 month'));
        }
        $html .= '<tr>';
        $html .= '<td style="font-weight:bold;text-align:left;"><span style="font-size: 9pt;">' . $sum[0] . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[1], 0, '', ',') . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[2], 0, '', ',') . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[3], 0, '', ',') . '</span></td>';
        // $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[4], 0, '', ',') . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[5], 0, '', ',') . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[6], 0, '', ',') . '</span></td>';
        $html .= '<td style="font-weight:bold;text-align:right;"><span style="font-size: 9pt;margin-right:1px;">' . number_format($sum[7], 0, '', ',') . '</span></td>';
        $html .= '</tr></tbody></table>';
        return [$html, $fio, 1];
    }

    public function salaryCreate($tabel, $locale)
    {
        $tabel = strtoupper($tabel);
        $user = Auth::user();
        $emp = Employee::where('tabel', $tabel)->first();
        if (!$emp) {
            $not_found_message['uz_latin'] = "Tebel nomer notog'ri.";
            $not_found_message['uz_cyril'] = "Табел номер нотўғри.";
            $not_found_message['ru'] = 'Неправильный табел номер.';
            return ['<span style="color:red;letter-spacing:1px;">' . $not_found_message[$locale] . '</span>', '', 0];
        }
        $dep_type_id = ($emp ? $emp->staff[0]->department->department_type_id : 0);
        $user_tabel = $user->employee->tabel;
        $response = Http::get('http://edo-db2.uzautomotors.com/api/salary-cart/view/' . $tabel . '/' . $locale);
        $fio = $response[1];
        $accessDep = AccessDepartment::where('employee_id', $user->employee_id)
            ->where('department_id', $emp->staff[0]->department_id)
            ->where('access_type_id', 1)
            ->first();

        $rangeCode = $emp->staff[0]->range ? $emp->staff[0]->range->code : false;
        if (!$emp->staff[0]) {
            return 'Server Error.';
        }
        if ($user_tabel != '9592' && $user_tabel != $tabel && !($accessDep && (in_array(substr($rangeCode, 0, 1), ['L', 'F']) || !$rangeCode)) && $user_tabel != 'D561') {
            $not_found_message['uz_latin'] = "Ushbu tebel nomer uchun sizda ruhsat yo'q.";
            $not_found_message['uz_cyril'] = "Ушбу табел номер учун сизда рухсат йўқ. ";
            $not_found_message['ru'] = 'У вас нет разрешения для этого табел номера';
            return ['<span style="color:red;letter-spacing:1px;">' . $not_found_message[$locale] . '</span>', $fio, 0];
        }

        $template = DocumentTemplate::find(134);
        $documentDetailAttributes = DocumentDetailAttribute::where('document_detail_template_id', $template->documentDetailTemplates[0]->id)->get();
        DB::beginTransaction();
        try {
            $document = new Document();
            $document->document_template_id = $template->id;
            $document->created_employee_id = Auth::user()->employee_id;
            $document->department_id = $template->department_id;
            $document->document_type_id = $template->document_type_id;
            $document->locale = $locale;
            $document->document_date = date('Y-m-d H:i:s');
            $document->pdf_file_name = $this->generateNanoId();
            $document->status = 1;
            $document->save();

            if (date('d') < 9) {
                $date = date('Y-m', strtotime(date('Y-m-d') . ' -13 month'));
            } else {
                $date = date('Y-m', strtotime(date('Y-m-d') . ' -12 month'));
            }

            //----------Document details--------------
            $detail = new DocumentDetail();
            $detail->document_id = $document->id;
            $detail->created_at = date('Y-m-d H:i:s');
            $detail->content = $locale == 'ru' ? $template->documentDetailTemplates[0]->content_ru : ($locale == 'uz_latin' ? $template->documentDetailTemplates[0]->content_uz_latin : $template->documentDetailTemplates[0]->content_uz_cyril);
            $detail->content2 = $response[0] . '<p>' . $response[3] . '<br>' . $response[4] . '<br>' . $response[5] . '<br>' . $response[6] . '</p>';
            $detail->save();

            // --------- document employee ----------
            $de = new DocumentDetailEmployee();
            $de->document_detail_id = $detail->id;
            $de->employee_id = $emp->id;
            $temp_employee = Employee::with('staff.position')
                ->with('staff.department')
                ->find($emp->id);
            $de->employee_fio = $temp_employee->getFullname($locale);
            if (count($temp_employee->staff)) {
                $de->employee_department = $temp_employee['staff'][0]->department['name_' . $locale];
                $de->employee_position = $temp_employee['staff'][0]->position['name_' . $locale];
            }
            $de->description = ' ';
            $de->save();

            //----------Document Signers
            $document_signer = new DocumentSigner();
            $document_signer->document_id = $document->id;
            $document_signer->staff_id = Auth::user()->employee->staff[0]->id;
            $document_signer->signer_employee_id = $user->employee_id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->status = 1;
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $document_signer->department = Auth::user()->employee->staff[0]->department['name_' . $document->locale];
            $document_signer->position = Auth::user()->employee->staff[0]->position['name_' . $document->locale];
            $document_signer->fio = Auth::user()->employee->getShortName($document->locale);
            $document_signer->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = 6;
            $document_signer_event->comment = 'created';
            $document_signer_event->status = 0;
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            foreach ($template->documentSignerTemplates as $key => $value) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $document->id;
                $document_signer->staff_id = $value->staff_id;
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
                $document_signer->action_type_id = $value->action_type_id;
                $document_signer->sequence = $value->sequence;
                $document_signer->status = 0;
                $document_signer->sign_type = $value->sign_type;
                $document_signer->save();
            }
            $this->publish($document->id);
            DB::commit();
            return ['', '', $document->id, $document->pdf_file_name];
        } catch (\Throwable $th) {
            return $th;
            // DB::rollback();
            return ['', $th, 0];
        }
    }

    public function updateFormalDocument(Request $request, $locale)
    {
        $content = $request->input('content');
        $where_to = $request->input('where_to');
        $attribute_contacts = $request->input('attribute_contacts');
        $attribute_employee = $request->input('attribute_employee');
        $template = DocumentTemplate::find($request->input('template_id'));
        $documentDetailAttributes = DocumentDetailAttribute::where('document_detail_template_id', $template->documentDetailTemplates[0]->id)->get();
        $document = Document::find($request->input('id'));
        if (!$document) {
            $document = new Document();
            $document->created_employee_id = Auth::user()->employee_id;
            $document->pdf_file_name = $this->generateNanoId();
            $document->document_date = date('Y-m-d H:i:s');
        }
        $document->document_template_id = $template->id;
        $document->department_id = $template->department_id;
        $document->document_type_id = $template->document_type_id;
        $document->updated_by = Auth::id();
        $document->locale = $locale;
        $document->status = 0;
        $document->save();

        //----------Document details--------------
        $detail = DocumentDetail::where('document_id', $document->id)->first();
        if (!$detail) {
            $detail = new DocumentDetail();
            $detail->created_at = date('Y-m-d H:i:s');
        }
        $detail->document_id = $document->id;
        $detail->content = $content;
        // $detail->content2 = 0;
        $detail->updated_at = date('Y-m-d H:i:s');
        $detail->save();

        // kimga qayerga
        $ddav = new DocumentDetailAttributeValue();
        $ddav->document_detail_id = $detail->id;
        $ddav->d_d_attribute_id = 599;
        $ddav->attribute_value = $where_to;
        $ddav->created_at = date('Y-m-d H:i:s');
        $ddav->save();

        // bajaruvchi
        $ddav = new DocumentDetailAttributeValue();
        $ddav->document_detail_id = $detail->id;
        $ddav->d_d_attribute_id = 600;
        $ddav->attribute_value = $attribute_employee;
        $ddav->created_at = date('Y-m-d H:i:s');
        $ddav->save();

        // bajaruvchi kontakti
        $ddav = new DocumentDetailAttributeValue();
        $ddav->document_detail_id = $detail->id;
        $ddav->d_d_attribute_id = 601;
        $ddav->attribute_value = $attribute_contacts;
        $ddav->created_at = date('Y-m-d H:i:s');
        $ddav->save();

        // --------- document employee ----------
        $de = DocumentDetailEmployee::where('document_detail_id', $detail->id)->first();
        if (!$de) {
            $de = new DocumentDetailEmployee();
        }
        $de->document_detail_id = $detail->id;
        $de->employee_id = Auth::user()->employee_id;
        $de->description = ' ';
        $de->save();

        //----------Document Signers
        $document_signer = DocumentSigner::where('document_id', $document->id)->first();
        if (!$document_signer) {
            $document_signer = new DocumentSigner();
        }
        $document_signer->document_id = $document->id;
        $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
        $document_signer->taken_datetime = date('Y-m-d H:i:s');
        $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
        $document_signer->action_type_id = 6;
        $document_signer->sequence = 100;
        $document_signer->status = 1;
        $document_signer->signer_employee_id = Auth::user()->employee_id;
        $document_signer->department = Auth::user()->employee->mainStaff[0]->department['name_' . $document->locale];
        $document_signer->position = Auth::user()->employee->mainStaff[0]->position['name_' . $document->locale];
        $document_signer->fio = Auth::user()->employee->getShortName($document->locale);
        $document_signer->save();

        //----------Document Signer Events
        $document_signer_event = DocumentSignerEvent::where('document_signer_id', $document_signer->id)->first();
        if (!$document_signer_event) {
            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->created_at = date('Y-m-d H:i:s');
        }
        $document_signer_event->document_signer_id = $document_signer->id;
        $document_signer_event->action_type_id = 6;
        $document_signer_event->comment = 'created';
        $document_signer_event->status = 0;
        $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
        $document_signer_event->fio = $document_signer->fio;
        $document_signer_event->save();

        foreach ($template->documentSignerTemplates as $key => $value) {
            $document_signer = new DocumentSigner();
            $document_signer->document_id = $document->id;
            $document_signer->staff_id = $value->staff_id;
            // $document_signer->taken_datetime = date('Y-m-d H:i:s');
            // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
            $document_signer->action_type_id = $value->action_type_id;
            $document_signer->sequence = $value->sequence;
            $document_signer->status = 0;
            $document_signer->sign_type = $value->sign_type;
            $document_signer->save();
        }
        return $document->id;
    }

    public function outOnControl(Request $request)
    {
        $document = Document::find($request['document_id']);
        if (!$document) {
            return 0;
        }
        // return $request;
        $document->status = 3;
        $document->save();

        $documentSigners = DocumentSigner::where('document_id', $document->id)->where('action_type_id', 11)->get();
        foreach ($documentSigners as $key => $documentSigner) {
            $documentSigner->status = 0;
            $documentSigner->signed_date = null;
            $documentSigner->sign_at = null;
            $documentSigner->fio = null;
            $documentSigner->signer_employee_id = null;
            $documentSigner->save();
        }
        return 'outOnControl';
    }

    public function outOfControl(Request $request)
    {
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $employee_id = Auth::user()->employee_id;
        $document = Document::find($request['document_id']);
        $outofcontrolcomment = $request['comment'];
        if (!$document) {
            return 0;
        }
        $document->status = 5;
        $document->save();
        $documentSigners = DocumentSigner::where('document_id', $document->id)->where('action_type_id', 11)->get();
        foreach ($documentSigners as $key => $documentSigner) {
            $documentSigner->status = 1;
            $documentSigner->save();
        }
        $documentSigner = DocumentSigner::where('document_id', $document->id)->whereIn('staff_id', $userStaffIds)->where('action_type_id', 11)->first();
        if (!$documentSigner) {
            $documentSigner = new DocumentSigner();
            $documentSigner->staff_id = Auth::user()->employee->mainStaff[0]->id;
            $documentSigner->taken_datetime = date('Y-m-d H:i:s');
            $documentSigner->due_date = date('Y-m-d H:i:s');
            $documentSigner->document_id = $document->id;
            $documentSigner->action_type_id = 11;
            $documentSigner->sequence = 0;
            $documentSigner->status = 0;
            $documentSigner->signer_group_id = 0;
            $documentSigner->is_registry = 0;
            $documentSigner->save();
        }
        $documentSigner->signer_employee_id = $employee_id;
        $documentSigner->fio = Auth::user()->employee->getShortName($document->locale);
        $documentSigner->signed_date = time();
        $documentSigner->sign_at = date('Y-m-d H:i:s');
        $documentSigner->save();

        $documentSignerEvent = new DocumentSignerEvent;
        $documentSignerEvent->document_signer_id = $documentSigner->id;
        $documentSignerEvent->action_type_id = $documentSigner->action_type_id;
        if (isset($outofcontrolcomment)) {

            $documentSignerEvent->comment = 'Хужжат назоратдан ечилди. ' . $outofcontrolcomment;
        } else {

            $documentSignerEvent->comment = 'Хужжат назоратдан ечилди';
        }
        $documentSignerEvent->status = 13;
        $documentSignerEvent->signer_employee_id = $employee_id;
        $documentSignerEvent->fio = $documentSigner->fio;
        $documentSignerEvent->save();


        $signers = DocumentSigner::where('document_id', $document->id)->whereIn('action_type_id', [4, 11, 12, 13, 14])->whereIn('status', [0, 3])->get();
        foreach ($signers as $key => $signer) {
            $signer->status = 1;
            $signer->signed_date = time();
            $signer->sign_at = date('Y-m-d H:i:s');
            $signer->save();

            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $signer->id;
            $documentSignerEvent->action_type_id = $signer->action_type_id;
            $documentSignerEvent->comment = 'Хужжат назоратчи тамонидан якунланди.';
            $documentSignerEvent->status = 13;
            $documentSignerEvent->signer_employee_id = $employee_id;
            $documentSignerEvent->fio = $signer->fio;
            $documentSignerEvent->save();
        }


        return "SuccessFully saved";
    }

    public function restoreDocument(Request $request)
    {
        if (Auth::user()->hasPermission('okd_kanselyariya')) {
            $document = Document::find($request['document_id']);
            if (!$document) {
                return 1;
            }
            DB::beginTransaction();
            try {
                $document->status = 2;
                $document->save();
                $document_signer = DocumentSigner::where('document_id', $document->id)->where('status', 2)->first();
                $document_signer->status = 0;
                $document_signer->save();
                $document_signer_event = DocumentSignerEvent::where('document_signer_id', $document_signer->id)->orderBy('id', 'Desc')->first();
                $document_signer_event->status = 2;
                $document_signer_event->save();

                $document_signer_changed = DocumentSigner::where('signer_employee_id', Auth::user()->employee_id)->where('document_id', $document->id)->first();
                if (!$document_signer_changed) {
                    $document_signer_changed = new DocumentSigner();
                    $new_signer = EmployeeStaff::where('employee_id', Auth::user()->employee_id)
                        ->where('is_main_staff', 1)->first();
                    $document_signer_changed->document_id = $document->id;
                    $document_signer_changed->staff_id = $new_signer->staff_id;
                    $document_signer_changed->taken_datetime = date('Y-m-d H:i:s');
                    $document_signer_changed->action_type_id = 4;
                    $document_signer_changed->due_date = date('Y-m-d H:i:s', time() + 86400);
                    $document_signer_changed->sequence = 0;
                    $document_signer_changed->signer_employee_id = Auth::user()->employee_id;
                    $employee = Auth::user()->employee;
                    $document = Document::find($document->id);
                    $count = $document->locale == 'uz_latin' ? 1 : 2;
                    $document_signer_changed->department = $employee->staff[0]->department['name_' . $document->locale];
                    $document_signer_changed->position = $employee->staff[0]->position['name_' . $document->locale];
                    $document_signer_changed->fio = $employee->getShortname($document->locale);
                    $document_signer_changed->is_done = 0;
                    $document_signer_changed->status = 1;
                    $document_signer_changed->description = $request['comment'];
                    $document_signer_changed->save();
                }
                $document_signer_event_changed = new DocumentSignerEvent();
                $document_signer_event_changed->document_signer_id = $document_signer_changed->id;
                $document_signer_event_changed->action_type_id = $document_signer_changed->action_type_id;
                $document_signer_event_changed->comment = $request['comment'];
                $document_signer_event_changed->status = 14;
                $document_signer_event_changed->signer_employee_id = $document_signer_changed->signer_employee_id;
                $document_signer_event_changed->fio = $document_signer_changed->fio;
                $document_signer_event_changed->save();

                DB::commit();
                return $document_signer_event_changed;
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        } else {
            return 0;
        }
    }

    public function copyDocument(Request $request)
    {
        $document = Document::find($request['document_id']);
        if (!$document) {
            return 0;
        }
        $model = new Document();
        $model->created_employee_id = Auth::user()->employee_id;
        // $model->document_date = date("Y-m-d H:i:s");
        $model->pdf_file_name = $this->generateNanoId();
        $model->department_id = $document->department_id;
        $model->title = $document->title;
        $model->document_type_id = $document->document_type_id;
        $model->document_type = $document->document_type;
        $model->document_template_id = $document->document_template_id;
        $model->document_template = $document->document_template;
        $model->locale = $document->locale;
        $model->from_department = $document->from_department;
        $model->from_manager = $document->from_manager;
        $model->from_position = $document->from_position;
        $model->to_department = $document->to_department;
        $model->to_manager = $document->to_manager;
        $model->to_position = $document->to_position;
        // $model->old_document_id = $request['restore'] ? $request['document_id'] : null;
        $model->old_document_id = null;
        // $model->document_number = $request['restore'] ? $document->document_number : 'YYXX-0000-0000';
        $model->document_number = 'YYXX-0000-0000';
        // $model->document_date = $request['restore'] ? $document->document_date : date("Y-m-d H:i:s");
        $model->document_date = date("Y-m-d H:i:s");
        $model->version = $request['restore'] ? $document->version + 1 : null;
        if ($model->save()) {
            $document->restore = $request['restore'] ? 0 : $document->restore;
            $document->save();
            foreach ($document->documentDetails as $key => $value) {
                $documentDetail = new DocumentDetail();
                $documentDetail->document_id = $model->id;
                $documentDetail->content = $value->content;
                $documentDetail->save();

                $docDetailEmployees = $value->documentDetailEmployees;
                foreach ($docDetailEmployees as $documentDetailKey => $detailEmployee) {
                    $temp_employee = Employee::with('staff.position')
                        ->with('staff.department')
                        ->find($detailEmployee->employee_id);
                    $employee = new DocumentDetailEmployee();
                    $employee->document_detail_id = $documentDetail->id;
                    $employee->employee_id = $detailEmployee->employee_id;
                    $employee->employee_fio = $detailEmployee->employee_fio;
                    $employee->employee_department = $detailEmployee->employee_department;
                    $employee->employee_position = $detailEmployee->employee_position;
                    $employee->description = '';
                    $employee->tariff_scale_id = $temp_employee->tariff_scale_id;
                    $employee->save();
                }

                if (
                    $document->documentTemplate->change_staff
                    && isset($value->documentDetailCoefficients)
                    && count($value->documentDetailCoefficients) > 0
                ) {
                    $docDetailCoefficients = $value->documentDetailCoefficients;
                    foreach ($docDetailCoefficients as $documentDetailKey => $docDetailCoefficient) {
                        if ($docDetailCoefficient['type'] == 1) {
                            $coefficient = new DocumentDetailCoefficient();
                            $coefficient->document_detail_id = $documentDetail->id;
                            $coefficient->tariff_scale_id = $docDetailCoefficient->tariff_scale_id;
                            $coefficient->value = $docDetailCoefficient->value;
                            $coefficient->type = 1;
                            $coefficient->save();
                        }
                    }
                }

                foreach ($value->documentDetailAttributeValues as $ddAttributeValueKey => $attributeValue) {
                    $attValue = new DocumentDetailAttributeValue();
                    $attValue->document_detail_id = $documentDetail->id;
                    $attValue->attribute_value = $attributeValue->attribute_value;
                    $attValue->d_d_attribute_id = $attributeValue->d_d_attribute_id;
                    $attValue->save();
                }
                $documentDetailContents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)->get();
                foreach ($documentDetailContents as $key => $content) {
                    $documentDetailContent = new DocumentDetailContent();
                    $documentDetailContent->document_detail_id = $content->document_detail_id;
                    $documentDetailContent->d_d_attribute_id = $content->d_d_attribute_id;
                    $documentDetailContent->table_name = $content->table_name;
                    $documentDetailContent->table_value = $content->table_value;
                    $documentDetailContent->group_sequence = $content->group_sequence;
                    $documentDetailContent->sequence = $content->sequence;
                    $documentDetailContent->group_name = $content->group_name;
                    $documentDetailContent->attribute_name = $content->attribute_name;
                    $documentDetailContent->value = $content->value;
                    $documentDetailContent->save();
                }
            }

            // Creator
            $creator = DocumentSigner::where('document_id', $document->id)->where('action_type_id', 6)->first();
            $documentSigner = new DocumentSigner();
            $documentSigner->document_id = $model->id;
            $documentSigner->action_type_id = $creator->action_type_id;
            $documentSigner->sequence = $creator->sequence;
            $documentSigner->staff_id = $creator->staff_id;
            $documentSigner->department = $creator->department;
            $documentSigner->position = $creator->position;
            $documentSigner->signer_employee_id = $creator->signer_employee_id;
            $documentSigner->fio = $creator->fio;
            $documentSigner->status = $creator->status;
            $documentSigner->signed_date = time();
            $documentSigner->sign_at = date('Y-m-d H:i:s');
            $documentSigner->save();

            foreach ($document->documentSigners as $key => $signer) {
                if (!$model->documentSigners()->where('staff_id', $signer->staff_id)->where('action_type_id', $signer->action_type_id)->first() && !$signer->parent_employee_id) {
                    $documentSigner = new DocumentSigner();
                    $documentSigner->document_id = $model->id;
                    $documentSigner->action_type_id = $signer->action_type_id;
                    $documentSigner->signer_group_id = $signer->signer_group_id;
                    $documentSigner->is_registry = $signer->is_registry;
                    $documentSigner->sequence = $signer->sequence;
                    $documentSigner->staff_id = $signer->staff_id;
                    $documentSigner->department = $signer->department;
                    $documentSigner->position = $signer->position;
                    $documentSigner->save();
                }
            }
        }
        return $model->id;
    }

    public function attribute()
    {
        return 'To`yinga ayt!!!';
        $documents = Document::where('id', '<=', 18000)->where('id', '>', 16000)->get();

        foreach ($documents as $key => $document) {
            echo $document->id . ' ' . $document->created_employee_id . '<hr>';
            foreach ($document->documentDetails as $key => $documentDetail) {
                foreach ($documentDetail->documentDetailEmployees as $key => $documentDetailEmployee) {
                    $temp_employee = Employee::with('staff.position')
                        ->with('staff.department')
                        ->find($documentDetailEmployee->employee_id);
                    $documentDetailEmployee->employee_fio = $temp_employee->getFullname($document->locale);
                    if (count($temp_employee->staff)) {
                        $documentDetailEmployee->employee_department = $temp_employee['staff'][0]->department['name_' . $document->locale];
                        $documentDetailEmployee->employee_position = $temp_employee['staff'][0]->position['name_' . $document->locale];
                    }
                    $documentDetailEmployee->save();
                }

                DocumentDetailContent::where('document_detail_id', $documentDetail->id)->delete();
                foreach ($documentDetail->documentDetailAttributeValues as $key => $attributeValue) {
                    $sequence = 1;
                    if ($attributeValue->documentDetailAttributes->table_list_id) {
                        // return $attributeValue->attribute_value;
                        $table = TableList::find($attributeValue->documentDetailAttributes->table_list_id);
                        if ($table->table_view) {
                            $table_list = DB::table($table->table_name)->find($attributeValue->attribute_value);
                            $colums = explode(', ', $table->column_name);
                            foreach ($colums as $key => $colum) {
                                $documentDetailContent = new DocumentDetailContent();
                                $documentDetailContent->document_detail_id = $documentDetail->id;
                                $documentDetailContent->d_d_attribute_id = $attributeValue->d_d_attribute_id;
                                $documentDetailContent->table_name = $table->table_name;
                                $documentDetailContent->table_value = $attributeValue->attribute_value;
                                $documentDetailContent->group_sequence = $attributeValue->documentDetailAttributes->sequence;
                                $documentDetailContent->sequence = $sequence++;
                                $documentDetailContent->group_name = $attributeValue->documentDetailAttributes['attribute_name_' . $document->locale];
                                $documentDetailContent->attribute_name = str_replace('_locale', '', $colum);
                                $table_colum = str_replace('locale', $document->locale, $colum);
                                $documentDetailContent->value = $table_list ? $table_list->$table_colum : '';
                                $documentDetailContent->save();
                            }
                        } else {
                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->value = '';
                            $documentDetailContent->value = $attributeValue->attribute_value;
                            $documentDetailContent->document_detail_id = $documentDetail->id;
                            $documentDetailContent->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                            $documentDetailContent->table_name = $table->table_name;
                            $documentDetailContent->group_sequence = $attributeValue->documentDetailAttributes->sequence;
                            $documentDetailContent->sequence = $sequence++;
                            $documentDetailContent->attribute_name = $attributeValue->documentDetailAttributes['attribute_name_' . $document->locale];
                            $documentDetailContent->save();
                        }
                    } else {
                        $documentDetailContent = new DocumentDetailContent();
                        $documentDetailContent->document_detail_id = $documentDetail->id;
                        $documentDetailContent->d_d_attribute_id = $attributeValue['d_d_attribute_id'];
                        $documentDetailContent->group_sequence = $attributeValue->documentDetailAttributes->sequence;
                        $documentDetailContent->sequence = $sequence++;
                        $documentDetailContent->attribute_name = $attributeValue->documentDetailAttributes['attribute_name_' . $document->locale];
                        $documentDetailContent->value = $attributeValue->attribute_value;
                        $documentDetailContent->save();
                    }

                    if ($attributeValue->documentDetailAttributes->table_list_id == 15) {
                        $document_detail_employee = DocumentDetailEmployee::where('document_detail_id', $documentDetail->id)->where('employee_id', $attributeValue->attribute_value)->first();
                        if (!$document_detail_employee) {
                            $document_detail_employee = new DocumentDetailEmployee();
                            $document_detail_employee->document_detail_id = $documentDetail->id;
                        }
                        $temp_employee = Employee::with('staff.position')
                            ->with('staff.department')
                            ->find($attributeValue->attribute_value);
                        $document_detail_employee->employee_fio = $temp_employee->getFullname($document->locale);
                        if (count($temp_employee->staff)) {
                            $document_detail_employee->employee_department = $temp_employee['staff'][0]->department['name_' . $document->locale];
                            $document_detail_employee->employee_position = $temp_employee['staff'][0]->position['name_' . $document->locale];
                        }
                        $document_detail_employee->employee_id = $attributeValue->attribute_value;
                        $document_detail_employee->description = '';
                        $document_detail_employee->save();
                    }
                }
            }
        }
    }

    public function countDocument(Request $request)
    {
        $document_count = DocumentSigner::where('staff_id', $request['staff_id'])
            ->where('signer_employee_id', $request['employee_id'])
            ->whereDoesntHave('documents', function ($q) {
                $q->whereIn('status', [0, 6, 5]);
            })
            ->whereIn('status', [3, 4])
            ->orWhereNotNull('parent_employee_id')
            ->whereDoesntHave('documents', function ($q) {
                $q->whereIn('status', [0, 6, 5]);
            })
            ->where('staff_id', $request['staff_id'])
            ->where('signer_employee_id', $request['employee_id'])
            ->whereIn('status', [0])
            ->count();
        $employees = EmployeeStaff::with('employee')
            ->where('staff_id', $request['staff_id'])
            ->where('is_active', 1)
            ->where('employee_id', '!=', $request['employee_id'])->get();
        $staffs = Staff::select('id', 'department_id', 'position_id')
            // ->with('department:id,department_code,name_' . $request['locale'])
            ->with('position:id,name_' . $request['locale'])
            // ->where('employee_id', $request['employee_id'])
            ->where('is_active', 1)
            ->whereHas('employeeStaff', function ($q) use ($request) {
                $q->where('is_main_staff', 1);
            })
            ->whereHas('department')
            // ->where('id',4273)
            // ->where('staff_id', '!=', $request['staff_id'])
            ->orderBy('id', 'desc')
            ->get();
        $staff = [];
        foreach ($staffs as $key => $value) {
            $dep = Department::find($value->department_id);
            if ($dep) {
                $value->department = $dep;
            }
            $staff[$key] = $value;
        }
        // if($document_count > 0 && $employees == [] && $staffs == []){
        //     foreach ($document_count as $key => $value) {
        //         $value->signer_employee_id == null;
        //         $value->status == 0;
        //         $value->save();
        //     }
        //     return ['document_count' => 0, 'employees' => $employees, 'staffs' => $staffs];
        // }
        return ['document_count' => $document_count, 'employees' => $employees, 'staffs' => $staff];
    }

    public function transferDocument(Request $request)
    {
        $employeeStaff = EmployeeStaff::find($request['old_employee_staff_id']);
        $document_signers = DocumentSigner::where('staff_id', $employeeStaff->staff_id)
            ->where('signer_employee_id', $employeeStaff->employee_id)
            ->whereIn('status', [3, 4])
            ->orWhereNotNull('parent_employee_id')
            ->where('staff_id', $employeeStaff->staff_id)
            ->where('signer_employee_id', $employeeStaff->employee_id)
            ->where('status', 0)
            ->get();
        foreach ($document_signers as $key => $document_signer) {
            if ($request['type'] == 1) {
                $document_signer->signer_employee_id = $request['transfer_employee_id'];
                $document_signer->save();
                $parent_document_signers = DocumentSigner::where('document_id', $document_signer->document_id)->where('parent_employee_id', $employeeStaff->employee_id)->get();
                foreach ($parent_document_signers as $key => $parent_document_signer) {
                    $parent_document_signer->parent_employee_id = $request['transfer_employee_id'];
                    $parent_document_signer->save();
                }
            }
            if ($request['type'] == 2) {
                $document_signer->staff_id = $request['transfer_staff_id'];
                $document_signer->save();
            }
        }
        return 'Successfully saved!';
    }

    public function eimzoinfo($id)
    {
        $document = Document::find($id)->makeVisible(['eimzoinfo']);
        return $document ? json_encode($document->eimzoinfo) : false;
    }

    public function editDocumentTitle(Request $request)
    {
        $document = Document::find($request['document_id']);
        $document->title = $request['title'];
        $document->save();
        return 'Successfully saved!';
    }

    public function isStar(Request $request)
    {
        $id = $request->input('id');
        $user_id = Auth::id();
        $db = DocumentBookmark::where('document_id', $id)->where('user_id', $user_id)->first();
        if ($db) {
            return 1;
        }
        return 0;
    }

    public function star(Request $request)
    {
        $id = $request->input('id');
        $user_id = Auth::id();
        $db = DocumentBookmark::where('document_id', $id)->where('user_id', $user_id)->first();
        if ($db) {
            $db = DocumentBookmark::where('document_id', $id)->where('user_id', $user_id)->delete();
            // if($db){
            //     $db->delete();
            // }
            return 0;
        } else {
            $db = new DocumentBookmark();
            $db->user_id = $user_id;
            $db->document_id = $id;
            $db->save();
            return 1;
        }
    }

    public function removeCancelledDocument(Request $request)
    {
        $cancelledDocument = CancelledDocument::where('document_id', $request->input('id'))->where('user_id', Auth::id())->first();
        if ($cancelledDocument) {
            return 1;
        }
        try {
            $cancelledDocument = new CancelledDocument();
            $cancelledDocument->document_id = $request->input('id');
            $cancelledDocument->user_id = Auth::id();
            $cancelledDocument->save();
            return 1;
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function getPdfWithComments($id)
    {
        return Document::savePdf($id, true);
    }

    public function preAgreement($id)
    {
        $document = Document::find($id);
        if ($document->status == 0) {
            $document->status = 7;
            $document->save();
            $documentSigners = DocumentSigner::where('document_id', $id)
                ->whereNotIn('action_type_id', [5, 6, 13])
                ->whereNull('control_punkt_id')
                ->get();
            foreach ($documentSigners as $key => $documentSigner) {
                $documentSigner->status = 5;
                $documentSigner->save();

                $employeeStaffs = [];
                if ($documentSigner->action_type_id != 6) {
                    $employeeStaffs = EmployeeStaff::where('staff_id', $documentSigner->staff_id)->where('is_active', 1)->get();
                }
                $actionType = "Для предсогласование";
                // $actionType = ActionType::find($documentSignerValue['action_type_id']);
                foreach ($employeeStaffs as $key => $employeeStaff) {
                    $user = User::where('employee_id', $employeeStaff->employee_id)->first();
                    // if ($user && $user->email) {
                    //     $reaction_type = $actionType;
                    //     SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                    // }
                }
            }
        } else {
            return 0;
        }
    }

    public function KpiFactCreate(Request $request)
    {
        $user = Auth::user();
        $document_details = $request['document_details'];
        $quarter = $request['filter']['quarter'];
        $years = $request['filter']['year'];

        $dep_id = $user->employee->mainStaff[0]->department->id;
        $doc = Document::find($document_details[0]['document_id']);
        // if($user->id == 272){
        //     return $doc;
        // }
        $kpi_object = KpiObject::where('years', $years)
            ->where('quarter', $quarter)
            ->where('doc_id', $document_details[0]['document_id'])
            // ->where('doc_id', $dep_id)
            ->first();

        if ($kpi_object->report_doc_id) {
            $document = Document::select('id', 'status')->where('id', $kpi_object->report_doc_id)->whereIn('status', [1, 2, 3, 4, 5])->first();
            if ($document) {
                return 'duble';
            }
        }

        // if($user->id == 1){
        //     return [$request];
        //     // return [$user->id, $quarter, $years, $user_dep];
        // }

        $document_details = $request->input('document_details');
        $chorak = $request->input('filter')['quarter'];
        $content = '';
        $choraklar = [
            [1325, 1326, 1327,    1321, 1322, 1323, 1324],
            [1328, 1329, 1330,    2078, 2079, 2080, 2081],
            [1331, 1332, 1333,    2082, 2083, 2084, 2085],
            [1334, 1335, 1336,    2086, 2087, 2088, 2089]
        ];

        $document = Document::find($document_details[0]['document_id']);

        $content .= '<table border="1" style="border-collapse: separate;">';
        $content .= '<thead>';
        $content .= '<tr>';
        $content .= '<th>';
        $content .= '#';
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Ko'rsatkichlar";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Ko'rsatkichlar salmog'i";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Mukofot miqdori";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "O'lchov birligi";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Min";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Maqsad";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Max";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Fakt";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Maqsadga erishish";
        $content .= '</th>';
        $content .= '<th>';
        $content .= "Mukofot miqdori";
        $content .= '</th>';
        $content .= '</tr>';
        $content .= '</thead>';
        $content .= '<tbody>';
        $sum = 0;
        foreach ($document_details as $key => $detail) {
            // return $detail['document_detail_attribute_values'];
            $kursatkich = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][3];
                    // return $item['d_d_attribute_id'] == 1321;
                })['attribute_value'];
            $salmog = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][4];
                    // return $item['d_d_attribute_id'] == 1322;
                })['attribute_value'];
            $mukofot_miqdori = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][5];
                    // return $item['d_d_attribute_id'] == 1323;
                })['attribute_value'];

            // detail.document_detail_contents.find(
            //     (v) => v.d_d_attribute_id == 1324
            //   ).value
            $ulchov_birligi = collect($detail['document_detail_contents'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] ==  $choraklar[$chorak - 1][6];
                    // return $item['d_d_attribute_id'] == 1324;
                })['value'];
            // $ulchov_birligi = '%';
            $min = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][0];
                })['attribute_value'];
            $maqsad = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][1];
                })['attribute_value'];
            $max = collect($detail['document_detail_attribute_values'])
                ->first(function ($item) use ($choraklar, $chorak) {
                    return $item['d_d_attribute_id'] == $choraklar[$chorak - 1][2];
                })['attribute_value'];
            $Fact = DocumentDetailFakt::where('d_d_id', $detail['id'])
                ->where('year', $years)
                ->where('quarter', $quarter)
                ->first();
            $fakt = $Fact->fakt;
            $maqsad_fact = $Fact->achieving_goal;
            $mukofot_miqdori_fakt = $Fact->reward_amount;
            $sum += $mukofot_miqdori_fakt;
            // Tablitsa yasash boshlandi
            $content .= '<tr>';
            $content .= '<td>';
            $content .= ($key + 1);
            $content .= '</td>';
            $content .= '<th>';
            $content .= $kursatkich;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $salmog;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $mukofot_miqdori;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $ulchov_birligi;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $min;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $maqsad;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $max;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $fakt;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $maqsad_fact;
            $content .= '</th>';
            $content .= '<th>';
            $content .= $mukofot_miqdori_fakt;
            $content .= '</th>';
            $content .= '</tr>';
        }

        $content .= '<tr>';
        $content .= '<th colspan="10">';
        $content .= 'Umumiy mukofot miqdori:';
        $content .= '</th>';
        $content .= '<th>';
        $content .= $sum;
        $content .= '</th>';
        $content .= '</tr>';
        $content .= '</tbody>';
        $content .= '</table>';


        DB::beginTransaction();
        try {
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(432);
            $model = new Document();
            $model->document_template_id = 432;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->department2_id = $document->department2_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'uz_latin'; //$request->input('locale');
            $model->document_date = date('Y-m-d H:i:s');
            $model->title = "KPI";
            $model->status = 1;
            $model->pdf_file_name = $this->generateNanoId();

            $a = $document_template->documentDetailTemplates[0]['content_' . $model->locale];

            $model->save();


            $document_detail = new DocumentDetail();
            $document_detail->document_id = $model->id;
            $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
            $dep = Department::find($document->department2_id);
            $document_detail->content = str_replace('__________________________________', ' ' . $dep->name_uz_latin . " (" . $dep->department_code . ")", $document_detail->content);
            $document_detail->content = str_replace('______', ' ' . $request->input('filter')['year'], $document_detail->content);
            $document_detail->content = str_replace('____', ' ' . ($chorak == 1 ? 'I' : ($chorak == 2 ? 'II' : ($chorak == 3 ? 'III' : 'IV'))), $document_detail->content);


            $document_detail->content .= '<br>';
            $document_detail->content .= $content;
            $document_detail->save();

            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = Auth::user()->employee->staff[0]->id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->sign_at = date('Y-m-d H:i:s');
            $document_signer->signed_date = time();
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $document_signer->status = 1;
            $document_signer->department = Auth::user()->employee->staff[0]->department['name_' . $model->locale];
            $document_signer->position = Auth::user()->employee->staff[0]->position['name_' . $model->locale];
            $document_signer->fio = Auth::user()->employee->getShortName($model->locale);
            $document_signer->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = 6;
            $document_signer_event->comment = 'Created and published';
            $document_signer_event->status = 0;
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            $fact = DocumentDetailFakt::find($document_details[0]['document_detail_fakt']['id']);
            foreach ($fact->comissions as $comission) {
                $employee = Employee::find($comission['employee_id']);
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $employee->staff[0]->id;
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+2 day'));
                $document_signer->action_type_id = 8;
                $document_signer->sequence = 2;
                $document_signer->status = 0;
                $document_signer->sign_type = 1;
                $document_signer->save();
            }

            $raxbar = KpiObject::getDepID(Auth::user()->employee->tabel);
            if ($raxbar) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $raxbar->manager_staff_id;
                $document_signer->taken_datetime = date('Y-m-d H:i:s');
                $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
                $document_signer->action_type_id = 1;
                $document_signer->sequence = 97;
                $document_signer->status = 0;
                $document_signer->sign_type = 1;
                $document_signer->save();
            }

            foreach ($document_template->documentSignerTemplates as $key => $value) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $value->staff_id;
                if ($value->sequence != 1) {
                    $document_signer->taken_datetime = date('Y-m-d H:i:s');
                    $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
                }
                $document_signer->action_type_id = $value->action_type_id;
                $document_signer->sequence = $value->sequence;
                $document_signer->status = 0;
                $document_signer->sign_type = $value->sign_type;
                $document_signer->save();
            }
            $kpi_object->report_doc_id = $model->id;
            $kpi_object->save();

            DB::commit();
            return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
    }

    public function addStamp($from, $to)
    {
        $documents = Document::where('id', '>=', $from)->where('id', '<=', $to)->whereIn('document_template_id', [305, 357])->whereNotIn('status', [0, 6])->get();
        foreach ($documents as $key => $document) {
            if ($document && !$document->stamped && ($document->document_template_id == 305 || $document->document_template_id == 357)) {
                try {
                    Document::stampDocument($document->id);
                    echo $document->id . ' - ' . substr($document->document_date, 0, 10) . '<br>';
                } catch (\Throwable $th) {
                    echo "<h1 style='border:1px solid red;'>Error: </h1>" . $document->id . '<br>';
                }
            }
        }
        return 'Successfully';
    }

    public function addStampOne($id)
    {
        $documents = Document::where('id', $id)->whereIn('document_template_id', [305, 333, 357])->whereNotIn('status', [0, 6])->get();
        foreach ($documents as $key => $document) {
            if ($document && !$document->stamped && ($document->document_template_id == 305 || $document->document_template_id == 333 || $document->document_template_id == 357)) {
                try {
                    Document::stampDocument($document->id);
                    echo $document->id . ' - ' . substr($document->document_date, 0, 10) . '<br>';
                } catch (\Throwable $th) {
                    echo "<h1 style='border:1px solid red;'>Error: </h1>" . $document->id . '<br>';
                }
            }
        }
        return 'Successfully';
    }

    public function annulirovan($id)
    {
        $document = Document::find($id);
        if (in_array($document->status, [3, 4, 5])) {
            $document->status = 8;
            $document->save();

            $user = Auth::user();
            $signer = new DocumentSigner;
            $signer->document_id = $id;
            $signer->action_type_id = 4;
            $signer->status = 1;
            $signer->taken_datetime = date('Y-m-d H:i:s');
            $signer->due_date = date('Y-m-d H:i:s');
            $signer->status = 1;
            $signer->sequence = 0;
            $signer->is_registry = 0;
            $signer->signer_group_id = 0;
            $signer->signer_employee_id = $user->employee_id;
            $signer->fio = $user->employee->getShortname($document->locale);
            $signer->staff_id = $user->employee->staff[0]->id;
            $signer->save();

            $event = new DocumentSignerEvent;
            $event->comment = "Hujjat bekor qilindi.";
            $event->document_signer_id = $signer->id;
            $event->action_type_id = 4;
            $event->status = 2;
            $event->signer_employee_id = $user->employee_id;
            $event->fio = $user->employee->getShortname($document->locale);
            $event->updated_at = date('Y-m-d H:i:s');
            $event->save();
        }
        // return $signer;
        Document::savePdf($id);
        return "Tasdiqlangan dokument bo'lishi shart";
    }
    public function TengeDocumentCreate(Request $request)
    {
        // return response()->json(["message" => "Edodan hujjat chiqarildi", "data" => 12345, "pdf_file_name" => 'asdsa']);
        // return $request;
        $fio = $request->input('fio');
        $pinfl = $request->input('pinfl');
        $contractNumber = $request->input('contract_number');
        $contractDate = $request->input('contract_date');
        $automodel = $request->input('model');
        $vin = $request->input('vin');
        $price = $request->input('price');
        $payedSum = $request->input('payed_sum');
        $returnSum = $request->input('return_sum');
        // $dilerName = $request->input('dilerName');
        $bankDocumentId = $request->input('rekvizit');
        // return $bankDocumentId;
        $dillerCode = substr($contractNumber, 2, 3);
        if ($dillerCode == 666) {
            $dillerCode = 216;
        } else if ($dillerCode == 649) {
            $dillerCode = 242;
        } else if ($dillerCode == 333) {
            $dillerCode = 216;
        } else if ($dillerCode == 444) {
            $dillerCode = 216;
        } else if ($dillerCode == 555) {
            $dillerCode = 216;
        }


        $checkContract = DocumentDetailAttributeValue::where('d_d_attribute_id', 2426)
            ->whereHas('documentDetail', function ($q) {
                $q->whereHas('document', function ($q1) {
                    $q1->where('status', '<>', 6);
                });
            })
            ->where('attribute_value', $contractNumber)->first();
        if ($checkContract) {
            return response()->json(["message" => "Bu shartnoma raqami ro'yxatdan o'tkazilgan"], 200);
        }
        $department = Department::where('department_code', $dillerCode)->with('staff.employees', 'staff.position')->first();
        // return $department;
        DB::beginTransaction();
        try {
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(564);
            // return $document_template;
            $model = new Document();
            $model->document_template_id = 564;
            $model->created_employee_id = $department->staff[0]->employees[0]->id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'uz_latin';
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->status = 1;
            $model->save();

            $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
            // return $ddas;
            $from = '';
            $to = '';
            $document_detail = new DocumentDetail();
            $document_detail->document_id = $model->id;
            $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
            $document_detail->save();
            foreach ($ddas as $ddak => $dda) {

                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                if ($dda->id == 2424) { // fio
                    $document_detail_attribute_value->attribute_value = $fio;
                } elseif ($dda->id == 2425) { // PINFL
                    $document_detail_attribute_value->attribute_value = $pinfl;
                } elseif ($dda->id == 2426) { // Shartnoma raqami
                    $document_detail_attribute_value->attribute_value = $contractNumber;
                } elseif ($dda->id == 2427) { // Shartnoma sanasi
                    $document_detail_attribute_value->attribute_value = $contractDate;
                } elseif ($dda->id == 2428) { // Avtomobil modeli
                    $document_detail_attribute_value->attribute_value = $automodel;
                } elseif ($dda->id == 2429) { // Vin raqami
                    $document_detail_attribute_value->attribute_value = $vin;
                } elseif ($dda->id == 2430) { // Avtomobil narxi
                    $document_detail_attribute_value->attribute_value = $price;
                } elseif ($dda->id == 2431) { // To'langan summa
                    $document_detail_attribute_value->attribute_value = $payedSum;
                } elseif ($dda->id == 2432) { // Qaytarilgan summa
                    $document_detail_attribute_value->attribute_value = $returnSum;
                } elseif ($dda->id == 2433) { // Diller korxonasi nomi
                    $document_detail_attribute_value->attribute_value = $department->name_uz_latin;
                } elseif ($dda->id == 2434) { // Bank hujjat ID
                    $document_detail_attribute_value->attribute_value = $bankDocumentId;
                }

                $document_detail_attribute_value->save();

                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = $dda->id;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $documentDetailContent->attribute_name = $dda['attribute_name_' . $model->locale];
                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                $documentDetailContent->save();
            }

            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = $department->staff[0]->id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = $department->staff[0]->employees[0]->id;
            $document_signer->status = 1;
            $document_signer->department = $department->name_uz_latin;
            $document_signer->position = $department->staff[0]->position->name_uz_latin;
            $document_signer->fio = $department->staff[0]->employees[0]->lastname_uz_latin . ' ' . $department->staff[0]->employees[0]->firstname_uz_latin . ' ' . $department->staff[0]->employees[0]->middlename_uz_latin;

            $document_signer->save();

            $document_signer2 = new DocumentSigner();
            $document_signer2->document_id = $model->id;
            $document_signer2->staff_id = $department->staff[0]->id;
            $document_signer2->taken_datetime = date('Y-m-d H:i:s');
            $document_signer2->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer2->action_type_id = 1;
            $document_signer2->sequence = 98;
            $document_signer2->signer_employee_id = $department->staff[0]->employees[0]->id;
            $document_signer2->status = 0;
            $document_signer2->department = $department->name_uz_latin;
            $document_signer2->position = $department->staff[0]->position->name_uz_latin;
            $document_signer2->fio = $department->staff[0]->employees[0]->lastname_uz_latin . ' ' . $department->staff[0]->employees[0]->firstname_uz_latin . ' ' . $department->staff[0]->employees[0]->middlename_uz_latin;

            $document_signer2->save();

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
                // if($value->sequence == 2){
                //     $document_signer->taken_datetime = date('Y-m-d H:i:s');
                //     $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
                // }
                $document_signer->status = 0;
                $document_signer->sign_type = $value->sign_type;
                $document_signer->save();
            }

            DB::commit();
            return response()->json(["message" => "Edodan hujjat chiqarildi", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
        // return $document;
    }
    public function checkTengeDocumentStatus(Request $request)
    {
        $documents = $request->all();
        $document_status = [];
        foreach ($documents as $key => $document_id) {
            $document = Document::find($document_id);
            if ($document) {
                $document_status[$key]["document_id"] = $document->id;
                $document_status[$key]["status"] = $document->status;
            }
        }
        return $document_status;
    }
    public function TengeLspCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::whereIn('id', $request->input('ids'))->get();
            // return $documents;
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(574);
            $model = new Document();
            $model->document_template_id = 574;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'uz_latin';
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            // Tablitsa Headerini yasash

            $content = $document_template->documentDetailTemplates[0]['content_uz_latin'];

            $content .= '<table border="1" style="border-collapse: separate;">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>';
            $content .= '#';
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Diler nomi";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Mijoz";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Shartnoma raqami";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Shartnoma sanasi";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "JShSHIR/STIR";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Qaytariladigan summa";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "X/r";
            $content .= '</th>';
            $content .= '<th>';
            $content .= "Asos (ariza raqami)";
            $content .= '</th>';
            $content .= '</thead>';
            $content .= '<tbody>';

            $all_sum = 0;

            foreach ($documents as $key => $value) {
                $doc = Document::where('id', $value->id)->with('documentRelation.employee')
                    ->with(['documentDetails' => function ($q) {
                        $q->with(['documentDetailAttributeValues' =>  function ($q1) {
                            $q1->with(['documentDetailAttributes' => function ($q2) {
                                $q2->with('dataType');
                            }]);
                        }])
                            ->with(['documentDetailEmployees' => function ($q1) {
                                $q1->with(['employee' => function ($q2) {
                                    $q2->with(['staff' => function ($q3) {
                                        $q3->with('position')->with('department');
                                    }]);
                                }]);
                            }]);
                    }])->first();

                // Aloqador Hujjatlarni birkitirish
                $dr = new DocumentRelation();
                $dr->document_id = $value->id;
                $dr->parent_document_id = $model->id;
                $dr->save();


                // return [$doc, $doc->documentDetails ];
                foreach ($doc->documentDetails[0]->documentDetailAttributeValues as $doc_key => $doc_attr) {
                    if ($doc_attr->d_d_attribute_id == 2424) {
                        $fio = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2425) {
                        $pinfl = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2426) {
                        $contract_num = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2427) {
                        $contract_date = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2428) {
                        $automodel = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2429) {
                        $vin = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2430) {
                        $price = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2431) {
                        $payed_sum = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2432) {
                        $return_sum = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2433) {
                        $diller_name = $doc_attr->attribute_value;
                    }
                    if ($doc_attr->d_d_attribute_id == 2434) {
                        $rekvizit = $doc_attr->attribute_value;
                    }
                }
                $content .= '<tr>';
                $content .= '<td style="width:3%; font-size: 14px; padding-top:8px;">';
                $content .= $key + 1;
                $content .= '</td>';
                $content .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $content .= $diller_name;
                $content .= '</td>';
                $content .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $content .= $fio;
                $content .= '</td>';
                $content .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $content .= $contract_num;
                $content .= '</td>';
                $content .= '<td style="width:10%; font-size: 14px; padding-top:8px;">';
                $content .= substr($contract_date, 0, 10);
                $content .= '</td>';
                $content .= '<td style="width:10%; font-size: 14px; padding-top:8px;">';
                $content .= $pinfl;
                $content .= '</td>';
                $content .= '<td style="width:5%; font-size: 14px; padding-top:8px;">';
                $content .= $return_sum;
                $content .= '</td>';
                $content .= '<td style="width:10%; font-size: 14px; padding-top:8px;">';
                $content .= $rekvizit;
                $content .= '</td>';
                $content .= '<td style="width:30%; font-size: 14px; padding-top:8px;">';
                $content .= $doc->document_number;
                $content .= '</td>';
                $content .= '</tr>';

                $all_sum += $return_sum;
            }
            $document_detail = new DocumentDetail();
            $document_detail->document_id = $model->id;
            $content .= '</tbody>';
            $content .= '</table>';
            $content .= '<p> <b>Jami: ';
            $content .= $all_sum;
            $content .= '</b></p>';
            $document_detail->content = $content;
            $document_detail->save();


            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = Auth::user()->employee_id;;
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
    public function TengeDocumentsList()
    {
        // $documents = Document::where('document_template_id', 564)->where('status', 1)->with('documentSigners', 'documentDetails.documentDetailAttributeValues')->get();
        // return $documents;
        $documents = Document::select('documents.*')
            ->limit(500)
            ->where('documents.status', 3)
            ->whereIn('documents.document_template_id', [564])
            ->with('documentType')
            ->with(['department' => function ($q) {
                $q->with(['managerStaff' => function ($q1) {
                    $q1->with('position')->with(['employeeMainStaff' => function ($q2) {
                        $q2->with('employee');
                    }]);
                }]);
            }])
            ->with(['documentTemplate.documentDetailTemplates.documentDetailAttributes' => function ($q) {
                $q->where('is_active', 1);
            }])
            ->with('documentRelation.employee')
            ->with('documentRelation.documentTemplate')
            ->with(['employee' => function ($q) {
                $q->with(['mainStaff' => function ($q1) {
                    $q1->with('department');
                }]);
            }])
            ->with(['documentDetails' => function ($q) {
                $q->with(['documentDetailAttributeValues' =>  function ($q1) {
                    $q1->with(['documentDetailAttributes' => function ($q2) {
                        $q2->with('dataType');
                    }]);
                }])
                    ->with(['documentDetailEmployees' => function ($q1) {
                        $q1->with(['employee' => function ($q2) {
                            $q2->with(['staff' => function ($q3) {
                                $q3->with('position')->with('department');
                            }]);
                        }]);
                    }]);
            }])
            ->with(['documentSigners' => function ($q) {
                $q->with(['staffs' =>  function ($q1) {
                    $q1->with('position')->with('department');
                }])
                    ->with('actionTypes')
                    ->with('signerEmployee')
                    ->with('parentEmployee')
                    ->with(['employeeStaffs' => function ($q2) {
                        $q2->with('employee')
                            ->where('is_active', '=', 1);
                    }]);
            }])
            ->get()->makeVisible(['pdf']);

        foreach ($documents as $key_document => $document) {
            if (!$document->pdf_table) {
                Document::savePdf($document->id);
            }
            foreach ($document->documentDetails as $key_detail => $documentDetail) {
                foreach ($documentDetail->documentDetailAttributeValues as $key => $value) {
                    if ($value->documentDetailAttributes->table_list_id) {
                        $documents[$key_document]->documentDetails[$key_detail]->documentDetailAttributeValues[$key]->table_lists = DB::table($value->documentDetailAttributes->tableList->table_name)->where('id', $value->attribute_value)->get();
                    }
                }
            }
        }

        foreach ($documents as $key => $value) {
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function changeStatusDocumentRelation($id)
    {
        $relationDocuments = DocumentRelation::where('parent_document_id', $id)->get();
        // dd($relationDocuments);
        foreach ($relationDocuments as $key => $value) {
            $document = Document::find($value->document_id);
            $document->status = 4;
            $document->save();
        }
    }

    public function statusReady($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3 && $document->document_type_id == 72) {
            $document->status = 4;
            $document->save();
        }
    }

    public function getGraphic(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $data = [];
        $end_of_month = date('t', strtotime($year . '-' . $month . '-01'));

        // foreach (range(1, $end_of_month) as $key => $value) {
        //     $data[] = 'd' . str_pad($value, 2, '0', STR_PAD_LEFT);
        // }

        // return $data;

        foreach (range(1, $end_of_month) as $key => $value) {

            $sana = $year . '-' . $month . '-' . str_pad($value, 2, '0', STR_PAD_LEFT);

            $workCalendar = WorkCalendar::where('calendar_date', $sana)->first();

            $is_holiday = $workCalendar->is_holiday;
            $is_weekend = $workCalendar->is_weekend;
            $is_work_day = $workCalendar->is_work_day;
            $week_days = ['Ya', 'Du', 'Se', 'Ch', 'Pa', 'Ju', 'Sh'];

            $data[] =
                [
                    'd' . str_pad($value, 2, '0', STR_PAD_LEFT),
                    $is_holiday,
                    $is_weekend,
                    $is_work_day,
                    $week_days[date('w', strtotime($sana))],
                ];
        }

        return $data;
    }

    public function tengeDocumentCheck(Request $request)
    {
        $resultArray = [];
        //return $request->all();
        foreach ($request->all() as $singleRequest) {

            $document = Document::where('id', '=', $singleRequest['document_number'])->where('document_template_id', '=', 564)->where('status', '=', 5)->first();
            if ($document) {
                $resultArray[] = ['id' => $document->id, 'status' => $document->status];
            }
        }



        return $resultArray;
    }
    public function MaterialResponsibleDocumentCreate(Request $request)
    {
        // return response()->json(["message" => "Edodan hujjat chiqarildi", "data" => 12345, "pdf_file_name" => 'asdsa']);
        // return $request;
        $department = Auth::user()->employee->staff[0]->department->name_uz_latin;
        $employee = Auth::user()->employee;
        // dd($employee);
        $fullname = substr($employee->firstname_uz_latin, 0,1) . "." . substr($employee->middlename_uz_latin,0,1) . "." . $employee->lastname_uz_latin;
        $tabel = $employee->tabel;
        $content1 = $request->input('content1');
        $contentsvod = $request->input('content2');
        $filter  = $request->input('filter');
        $currentDate = $filter['currentDate'];
        $from_date = $currentDate . '-01';
        $to_date = date('Y-m-t', strtotime($currentDate));
        $warehouse_code = $filter['ware'][0]['code'];
        $warehouse_name = $filter['ware'][0]['name'];
        // return $from_date;
        // $department = Department::where('department_code', $dillerCode)->with('staff.employees', 'staff.position')->first();
        // return $department;
        DB::beginTransaction();
        try {
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(625);
            $model = new Document();
            $model->document_template_id = 625;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'ru';
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            $content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
            $content .= '<center style="font-size: 20pt;margin-top:25px; font-weight:bold; text-transform: uppercase;">' . 'MATERIAL REPORT' . '</center><hr id="line10"><hr id="line1" style="margin-bottom:20px;margin-top:-7px;">';
            //Header qo'yish joyi
            $content .= '<div style="display:flex; justify-content: space-between; font-size:12pt;">';
            $content .= '<div style=" font-size:12pt;">';
            $content .= '<p style="font-size:12pt;">';
            $content .= 'Ombor kodi: ';
            $content .= $warehouse_code;
            $content .= '</p>';
            $content .= '<p style=" font-size:12pt;">';
            $content .= 'Ombor nomi: ';
            $content .= $warehouse_name;
            // $content .= date('d', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
            $content .= '</p>';
            $content .= '<p style="font-size:12pt;">';
            $content .= 'Period: ';
            $content .= $from_date;
            $content .= '</p>';
            //  $content .= '\n';
            $content .= $to_date;
            $content .= '</div>';
            $content .= '<div style="position: absolute; right: 0; top: 95; font-size:12pt;">';
            $content .= '<p style="min-width: 300px">';
            $content .= 'Javobgar shaxs: ';
            $content .= $fullname;
            $content .= '</p>';
            $content .= '<p style="">';
            $content .= 'Tebel raqami: ';
            $content .= $tabel;
            $content .= '</p>';
            $content .= '<p style=" ">';
            $content .= 'Boshqarma: ';
            $content .= $department;
            $content .= '</p>';
            $content .= '</div>';
            $content .= '</div>';

            $content3 = '<div>';
            $content3 .= $contentsvod;
            $content3 .= '</div>';

            $content2 = '<div>';
            $content2 .= $content1;
            $content2 .= '</div>';

            $content .= '<center style="font-size: 14pt;margin-top:25px; font-weight:bold; text-transform: uppercase;">' . 'СВОД' . '</center>';
            $content .= $content3;
            $content .= '<br />';
            $content .= '<center style="font-size: 14pt;margin-top:25px; font-weight:bold; text-transform: uppercase;">' . 'ДЕТАЛИЗАЦИЯ' . '</center>';
            $content .= $content2;
            $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);

            $document_detail = new DocumentDetail();
            $document_detail->document_id = $model->id;
            $document_detail->content = $content;
            $document_detail->save();


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

            // $document_signer2 = new DocumentSigner();
            // $document_signer2->document_id = $model->id;
            // $document_signer2->staff_id = $department->staff[0]->id;
            // $document_signer2->taken_datetime = date('Y-m-d H:i:s');
            // $document_signer2->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            // $document_signer2->action_type_id = 1;
            // $document_signer2->sequence = 98;
            // $document_signer2->signer_employee_id = $department->staff[0]->employees[0]->id;
            // $document_signer2->status = 0;
            // $document_signer2->department = $department->name_uz_latin;
            // $document_signer2->position = $department->staff[0]->position->name_uz_latin;
            // $document_signer2->fio = $department->staff[0]->employees[0]->lastname_uz_latin . ' ' . $department->staff[0]->employees[0]->firstname_uz_latin . ' ' . $department->staff[0]->employees[0]->middlename_uz_latin;

            // $document_signer2->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = 6;
            $document_signer_event->comment = 'created';
            $document_signer_event->status = 0;
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            // foreach ($document_template->documentSignerTemplates as $key => $value) {
            //     $document_signer = new DocumentSigner();
            //     $document_signer->document_id = $model->id;
            //     $document_signer->staff_id = $value->staff_id;
            //     $document_signer->action_type_id = $value->action_type_id;
            //     $document_signer->sequence = $value->sequence;
            //     // if($value->sequence == 2){
            //     //     $document_signer->taken_datetime = date('Y-m-d H:i:s');
            //     //     $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            //     // }
            //     $document_signer->status = 0;
            //     $document_signer->sign_type = $value->sign_type;
            //     $document_signer->save();
            // }

            DB::commit();
            return response()->json(["message" => "Edodan hujjat chiqarildi", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
        // return $document;
    }


    public function CreateEmployeeDepartment(Request $request)
    {
        //$employeesObject=Employee::select('*')->where('is_active', true)->with('employeeStaff')->with('staff')->limit(10)->get();   

        $employeesObject = Employee::select('*')->where('is_active', true)->whereHas('staff')->get();

        //return $employeesObject;
        $today = Carbon::now('Asia/Tashkent')->format('Y-m-d');
        DB::beginTransaction();
        try {
            //return $employeesObject;
            foreach ($employeesObject as $singleEmployee) {
                $employeeId = $singleEmployee->id;
                $departmentId = $singleEmployee->staff[0]->department->id;
                $positionId = $singleEmployee->staff[0]->position->id;
                $employee = EmployeeDepartmentPosition::where('employee_id', $employeeId)->whereDate('created_at', '=', $today)->first();
                //return $employee;
                if (!$employee) {
                    $employeeDepartmentObj = new EmployeeDepartmentPosition();
                    $employeeDepartmentObj->employee_id = $employeeId;
                    $employeeDepartmentObj->department_id = $departmentId;
                    $employeeDepartmentObj->position_id = $positionId;
                    $employeeDepartmentObj->status = 1;
                    $employeeDepartmentObj->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
                    $employeeDepartmentObj->save();
                }
                //return $singleEmployee->staff[0]->department;
                //return $employee;	  
            }
            DB::commit();



            $oldEmployee = EmployeeDepartmentPosition::whereDate('created_at', '<', $today)->forceDelete();


            //$oldemployee=EmployeeDepartmentPosition::where('created_at', $today)->get();
            // foreach($oldEmployee as $singleEmployee){
            //     $singleEmployee->forceDelete();
            // }
            //return $oldEmployee;

            return "Successfully saved!";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}
