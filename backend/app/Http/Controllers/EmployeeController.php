<?php

namespace App\Http\Controllers;

use App\Http\Models\AddressType;
use App\Http\Models\KpiResolutionComission;
use App\Http\Models\Coefficient;
use App\Http\Models\ChiefHelper;
use App\Http\Models\Company;
use App\Http\Models\Department;
use App\Http\Models\Country;
use App\Http\Models\District;
use App\Http\Models\PositionType;
use App\Http\Models\Branch;
use App\Http\Models\Employee;
use App\Http\Models\SignerGroup;
use App\Http\Models\EmployeeAddress;
use App\Http\Models\EmployeeCoefficient;
use App\Http\Models\EmployeePhone;
use App\Http\Models\EmployeeLanguage;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\HrStudyType;
use App\Http\Models\HrUniversity;
use App\Http\Models\HrMajor;
use App\Http\Models\HrStudyDegree;
use App\Http\Models\LeavingReason;
use App\Http\Models\Nationality;
use App\Http\Models\Region;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentDetail;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetailEmployee;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\DocumentDetailAttribute;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\MaterialResponsiblePeople;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Staff;
use App\Http\Models\TariffScale;
use App\Services\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Http;


class EmployeeController extends Controller
{
    public function createDocumentTransfer(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(261);
            $model = new Document();
            $model->title = 'Перевод Сотрудника';
            $model->document_template_id = 261;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = $request->input('locale');
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();



            $managerStaff = [];
            foreach ($request->input('items') as $key => $value) {
                $staff = $value['staff'];
                $employee = $value['employee'];
                $lang = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
                $firstLetter = $model->locale == 'uz_latin' ? 1 : 2;

                // New Document Detail
                $document_detail = new DocumentDetail();
                $document_detail->document_id = $model->id;
                if ($key == 0) {
                    $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                } else {
                    $document_detail->content = '';
                }
                $document_detail->save();

                // New Document Detail Employee
                $dde_new = new DocumentDetailEmployee();
                $dde_new->document_detail_id = $document_detail->id;
                $dde_new->employee_id = $employee['id'];
                $dde_new->description = "Yangi lavozimga o'tkazish.";
                $dde_new->employee_fio = $employee['lastname_' . $lang] . ' ' . substr($employee['firstname_' . $lang], 0, $firstLetter) . '.' . substr($employee['middlename_' . $lang], 0, $firstLetter) . '.';
                $department = count($employee['main_staff']) > 0 && $employee['main_staff'][0]['department'] ? $employee['main_staff'][0]['department'] : null;
                $department = $department ? $department : (count($employee['additional_staff']) > 0 && $employee['additional_staff'][0]['department'] ? $employee['additional_staff'][0]['department'] : null);
                $position = count($employee['main_staff']) > 0 && $employee['main_staff'][0]['position'] ? $employee['main_staff'][0]['position'] : null;
                $position = $position ? $position : (count($employee['additional_staff']) > 0 && $employee['additional_staff'][0]['position'] ? $employee['additional_staff'][0]['position'] : null);
                $dde_new->employee_department = $department ? $department['name_' . $model->locale] : '';
                $dde_new->employee_position = $position ? $position['name_' . $model->locale] : '';
                $dde_new->save();

                //Employee rahbarlarini qo'shish
                $parentDepartments = Employee::parentDepartments($employee['tabel']);
                $managerStaffNew = $parentDepartments['manager_staff'];
                // return ($managerStaffNew);
                foreach ($managerStaffNew as $k => $v) {
                    $isContain = collect($managerStaff)->contains(function ($dep, $depKey) use ($v) {
                        return $dep['managerStaff']['id'] == $v['managerStaff']['id'];
                    });
                    if ($v['managerStaff'] && !$isContain) {
                        $managerStaff[] = $v;
                    }
                }

                // new department:d_d_attribute_id: 959
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 959;
                $document_detail_attribute_value->attribute_value = $staff['department']['id'];
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 959;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(959);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = $staff['department']['name_' . $model->locale];
                $documentDetailContent->save();

                // old department:d_d_attribute_id: 960
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 960;
                $document_detail_attribute_value->attribute_value = $department['id'];
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 960;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(960);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = $department['name_' . $model->locale];
                $documentDetailContent->save();
                // new position:d_d_attribute_id: 961
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 961;
                $document_detail_attribute_value->attribute_value = $department['id'];
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 961;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(961);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = $staff['position']['name_' . $model->locale];
                $documentDetailContent->save();
                // old position:d_d_attribute_id: 962
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 962;
                $document_detail_attribute_value->attribute_value = $position['id'];
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 962;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(962);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = $position['name_' . $model->locale];
                $documentDetailContent->save();
                // new staff_id:d_d_attribute_id: 963
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 963;
                $document_detail_attribute_value->attribute_value = $staff['id'];
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 963;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(963);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = $staff['id'];
                $documentDetailContent->save();
                // old staff_id:d_d_attribute_id: 964
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 964;
                $document_detail_attribute_value->attribute_value = count($employee['main_staff']) > 0 ? $employee['main_staff'][0]['id'] : (count($employee['additional_staff']) > 0 ? $employee['additional_staff'][0]['id'] : '');
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 964;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(964);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = count($employee['main_staff']) > 0 ? $employee['main_staff'][0]['id'] : (count($employee['additional_staff']) > 0 ? $employee['additional_staff'][0]['id'] : '');
                $documentDetailContent->save();
                // old position:d_d_attribute_id: 983
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 983;
                $document_detail_attribute_value->attribute_value = '';
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 983;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(983);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = '';
                $documentDetailContent->save();
                // old position:d_d_attribute_id: 984
                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = 984;
                $document_detail_attribute_value->attribute_value = '';
                $document_detail_attribute_value->save();
                $documentDetailContent = new DocumentDetailContent();
                $documentDetailContent->document_detail_id = $document_detail->id;
                $documentDetailContent->d_d_attribute_id = 984;
                $documentDetailContent->group_sequence = 1;
                $documentDetailContent->sequence = 1;
                $d_d_attribute = DocumentDetailAttribute::find(984);
                $documentDetailContent->attribute_name = $d_d_attribute['attribute_name_' . $model->locale];
                $documentDetailContent->value = '';
                $documentDetailContent->save();
            }



            //Document Creator
            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $document_signer->status = 0;
            $document_signer->department = Auth::user()->employee->mainStaff[0]->department['name_' . $model->locale];
            $document_signer->position = Auth::user()->employee->mainStaff[0]->position['name_' . $model->locale];
            $document_signer->fio = Auth::user()->employee->getShortName($model->locale);
            $document_signer->save();

            [
                $model->from_department,
                $model->from_manager,
                $model->from_position,
                $model->to_department,
                $model->to_manager,
                $model->to_position,
            ] = Document::getDocumentDepartmentInfo(Auth::user()->employee_id, $model->department_id, $model->locale);
            $model->save();

            // return $managerStaff;
            // $managerStaff = $managerStaff
            foreach ($managerStaff as $key => $value) {
                if ($value['department_type_id'] != 2) {
                    $document_signer = new DocumentSigner();
                    $document_signer->document_id = $model->id;
                    $document_signer->staff_id = $value['managerStaff']['id'];
                    $document_signer->taken_datetime = null;
                    $document_signer->due_date = null;
                    $document_signer->action_type_id = 3;
                    $document_signer->sequence = 100;
                    $document_signer->signer_employee_id = null;
                    $document_signer->status = 0;
                    $document_signer->department = $value['name_' . $model->locale];
                    $document_signer->position = $value['manager_staff']['position']['name_' . $model->locale];
                    $emp = Employee::find($value['managerStaff']['employees'][0]['id']);
                    // dd($value['managerStaff']['employees'][0]['id']);
                    $document_signer->fio = $emp->getShortName($model->locale);
                    $document_signer->save();
                }
            }

            $signerGroup = SignerGroup::with('signerGroupDetails')->find(76);

            foreach ($signerGroup->signerGroupDetails as $key => $value) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $value->staff_id;
                $document_signer->taken_datetime = null;
                $document_signer->due_date = null;
                $document_signer->action_type_id = $value->action_type_id;
                $document_signer->sequence = $value->sequence;
                $document_signer->signer_employee_id = null;
                $document_signer->status = 0;
                $staff = Staff::with('department')->with('position')->find($value->staff_id);
                $document_signer->department = $staff->department['name_' . $model->locale];
                $document_signer->position = $staff->position['name_' . $model->locale];
                // $emp = Employee::find($value['managerStaff']['employees'][0]['id']);
                // dd($value['managerStaff']['employees'][0]['id']);
                // $document_signer->fio = $emp->getShortName($model->locale);
                $document_signer->save();
            }



            // $document_signer_event = new DocumentSignerEvent();
            // $document_signer_event->document_signer_id = $document_signer->id;
            // $document_signer_event->action_type_id = 6;
            // $document_signer_event->comment = 'created';
            // $document_signer_event->status = 0;
            // $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            // $document_signer_event->fio = $document_signer->fio;
            // $document_signer_event->save();

            // foreach ($document_template->documentSignerTemplates as $key => $value) {
            //     $document_signer = new DocumentSigner();
            //     $document_signer->document_id = $model->id;
            //     $document_signer->staff_id = $value->staff_id;
            //     // $document_signer->taken_datetime = date('Y-m-d H:i:s');
            //     // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
            //     $document_signer->action_type_id = $value->action_type_id;
            //     $document_signer->sequence = $value->sequence;
            //     $document_signer->status = 0;
            //     $document_signer->sign_type = $value->sign_type;
            //     $document_signer->save();
            // }

            DB::commit();
            return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
    }

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }

    public function searchEmployee(Request $request)
    {
        $search = $request->input('search');
        // $search = '9592 g';
        $employees = Employee::query();
        $employees->with('mainStaff.department');
        $employees->with('mainStaff.position');
        $employees->with('additionalStaff.department');
        $employees->with('additionalStaff.position');
        $employees->whereHas('employeeStaff');
        $employees->with('employeeStaff');
        $employees->where(DB::raw("concat(tabel, ' ', employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
            ->orWhere(DB::raw("concat(tabel, ' ', employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%")
            ->orWhere(DB::raw("concat(tabel, ' ', employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $search . "%")
            ->orWhere(DB::raw("concat(tabel, ' ', employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $search . "%");
        return $employees->paginate(100);
    }

    public function searchStaff(Request $request)
    {
        $search = $request->input('search');
        // $search = '1 toif';
        $staff = Staff::query();
        $staff->select('id', 'department_id', 'position_id', 'rate_count');
        $staff->with('department');
        $staff->with('position');
        $staff->with(['employeeStaff' => function ($q) {
            $q->where('employee_staff.is_active', 1);
        }]);
        $staff->whereHas('department', function ($q) use ($search) {
            $q->where("departments.department_code", 'like', "%" . $search . "%")
                ->orWhere("departments.name_uz_latin", 'like', "%" . $search . "%")
                ->orWhere("departments.name_uz_cyril", 'like', "%" . $search . "%")
                ->orWhere("departments.name_ru", 'like', "%" . $search . "%");
        });
        $staff->orWhereHas('position', function ($q) use ($search) {
            $q->where("positions.name_ru", 'like', "%" . $search . "%")
                ->orWhere("positions.name_uz_latin", 'like', "%" . $search . "%")
                ->orWhere("positions.name_uz_cyril", 'like', "%" . $search . "%");
        });
        // $staff->whereIn('staff.id', DB::raw('select id from staff where id < 10'));

        return $staff->paginate(100);
    }

    public function index()
    {
        $employees = Employee::with('company')
            ->with('country')
            ->with('region')
            ->with('district')
            ->with('nationality')
            ->get();
        return [
            'employees' => $employees,
            'companies' => Company::get(),
            'countries' => Country::get(),
            'regions' => Region::get(),
            'districts' => District::get(),
            'nationalities' => Nationality::get(),
        ];
    }

    public function indexView(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $branches = User::hrRoles();
        $employees = Employee::with('company')
            ->with('country')
            ->with('nationality')
            ->with('region')
            ->with('district')
            ->with('user')
            ->with('tariffScale')
            ->with(['employeeStaff' => function ($query) {
                $query->with(['staff' => function ($staffQuery) {
                    $staffQuery->with('position')->with('department');
                }])
                    ->where('is_active', '=', 1);
            }])
            ->with(['employeeCoefficients' => function ($q) {
                $q->with('coefficient');
            }])
            ->with(['employeeAddresses' => function ($q) {
                $q->with('country')
                    ->with('region')
                    ->with('district');
            }])
            ->with('employeePhones')
            ->with(['employeeLanguages' => function ($q) {
                $q->with('hrLanguage');
            }])
            ->with(['employeeMilitaryRanks' => function ($q) {
                $q->with('hrMilitaryRank');
            }])
            ->with(['employeeStateAwards' => function ($q) {
                $q->with('hrStateAward');
            }])
            ->with(['employeeParties' => function ($q) {
                $q->with('hrParty');
            }])
            ->with(['employeeOfficialDocument' => function ($q) {
                $q->with('officialDocumentType');
            }])
            ->with(['employeeRelative' => function ($q) {
                $q->with('familyRelative');
            }])
            ->with('staffCritical')
            ->with('employeeCapital')
            ->with('employeeWorkHistories')
            ->with('employeeEducationHistories.studyType')
            ->with('employeeEducationHistories.university')
            ->with('employeeEducationHistories.major')
            ->with('employeeEducationHistories.studyDegree')
            ->leftJoin(
                'employee_staff',
                function ($q) {
                    $q->on('employee_staff.employee_id', '=', 'employees.id')
                        ->where('employee_staff.is_active', 1);
                }
            )
            ->leftJoin('staff', 'staff.id', '=', 'employee_staff.staff_id')
            // ->leftJoin('positions', 'positions.id', '=', 'staff.position_id')
            ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
            // ->leftJoin('tariff_scales', 'tariff_scales.id', '=', 'employees.tariff_scale_id')
            ->where('employees.is_active', 1)
            // ->where('employee_staff.is_active', 1)
            // ->whereIn('departments.branch_id', $branches)
            // ->where(function ($q) use ($branches) {
            //     $q
            //     //->whereIn('departments.branch_id', $branches)
            //     ->orWhereDoesntHave('staff')
            //     ->orWhereDoesntHave('employeeStaff');
            // })
            ->orderBy(DB::raw('case when employee_staff.id is null then 0 else 1 end'), 'asc')
            ->orderBy('departments.department_code', 'asc')
            ;
        if (isset($filter['department_code']) || isset($filter['department_name'])) {
            if (isset($filter['department_name'])) {
                $employees->whereHas('staff', function (Builder $query) use ($filter) {
                    $query->whereHas('department', function (Builder $q) use ($filter) {
                        $q->where('departments.name_ru', 'ilike', "%" . $filter['department_name'] . "%")
                            ->orWhere('departments.name_uz_latin', 'ilike', "%" . $filter['department_name'] . "%")
                            ->orWhere('departments.name_uz_cyril', 'ilike', "%" . $filter['department_name'] . "%");
                    });
                });
            }
            if (isset($filter['department_code'])) {
                $employees->whereHas('staff', function (Builder $query) use ($filter) {
                    $query->whereHas('department', function (Builder $q) use ($filter) {
                        $q->where('department_code', 'ilike', "%" . $filter['department_code'] . "%");
                    });
                });
            }
        }
        if (isset($filter['info'])) {
            $employees->where(function (Builder $query) use ($filter) {
                return $query

                    ->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $filter['info'] . "%");
            });
        }
        if (isset($filter['category'])) {
            $employees->where(function (Builder $query) use ($filter) {
                return $query->where('tariff_scales.category', 'ilike', "%" . $filter['category'] . "%");
            });
        }
        if (isset($filter['first_work_date'])) {
            $employees->where(function (Builder $query) use ($filter) {
                return $query->where('first_work_date', 'ilike', "%" . $filter['first_work_date'] . "%");
            });
        }
        if (isset($filter['position'])) {
            $employees->whereHas('staff', function (Builder $q) use ($filter) {
                return $q->whereHas('position', function (Builder $query) use ($filter) {
                    return $query->where('name_ru', 'ilike', "%" . $filter['position'] . "%")
                        ->orWhere('name_uz_latin', 'ilike', "%" . $filter['position'] . "%")
                        ->orWhere('name_uz_cyril', 'ilike', "%" . $filter['position'] . "%");
                });
            });
            // $employees->where(function (Builder $query) use ($filter) {
            //     return $query->where('positions.name_ru', 'ilike', "%" . $filter['position'] . "%")
            //         ->orWhere('positions.name_uz_latin', 'ilike', "%" . $filter['position'] . "%")
            //         ->orWhere('positions.name_uz_cyril', 'ilike', "%" . $filter['position'] . "%");
            // });
        }
        if (isset($filter['gender'])) {
            $employees->where(function (Builder $query) use ($filter) {
                return $query->where('gender', 'ilike', "%" . $filter['gender'] . "%");
            });
        }
        if (isset($filter['nationality_id'])) {
            $employees->where('nationality_id', $filter['nationality_id']);
        }
        if (isset($filter['tabel'])) {
            $employees->where('tabel', 'ilike', '%' . $filter['tabel'] . '%');
        }
        if (isset($filter['INPS'])) {
            $employees->where('INPS', 'ilike', '%' . $filter['INPS'] . '%');
        }
        if (isset($filter['INN'])) {
            $employees->where('INN', 'ilike', '%' . $filter['INN'] . '%');
        }
        if (isset($filter['born_date_from'])) {
            $employees->where('born_date', '>=', $filter['born_date_from']);
        }
        if (isset($filter['born_date_to'])) {
            $employees->where('born_date', '<=', $filter['born_date_to']);
        }
        if (!Auth::user()->isAbleTo('employee-index')) {
            $employees->where('employees.id', Auth::user()->employee_id);
        }
        return json_encode([
            'employees' => $employees->select('employees.*')->paginate($itemsPerPage == '-1' ? 20000 : $itemsPerPage, ['employees.id'], 'page name', $page),
            // 'employees' => $employees->select('employees.*')->distinct()->orderBy('employees.id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['employees.id'], 'page name', $page),
            // 'employees' => $employees->select('employees.*')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['employees.id'], 'page name', $page),
        ]);
    }

    public function dismissedEmployeeView(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $employees = Employee::with('company')
            ->with('country')
            ->with('nationality')
            ->with('region')
            ->with('district')
            ->with(['employeeStaffWithInactive' => function ($query) {
                $query->with('tariffScale')
                    ->with('leavingReason')
                    ->with(['staff' => function ($staffQuery) {
                        $staffQuery->with('position')->with('department');
                    }]);
            }])
            ->with(['employeeCoefficients' => function ($q) {
                $q->with('coefficient');
            }])
            ->with(['employeeAddresses' => function ($q) {
                $q->with('country')
                    ->with('region')
                    ->with('district');
            }])
            ->with('employeePhones')
            ->with(['employeeLanguages' => function ($q) {
                $q->with('hrLanguage');
            }])
            ->with(['employeeMilitaryRanks' => function ($q) {
                $q->with('hrMilitaryRank');
            }])
            ->with(['employeeStateAwards' => function ($q) {
                $q->with('hrStateAward');
            }])
            ->with(['employeeParties' => function ($q) {
                $q->with('hrParty');
            }])
            ->with(['employeeStaffAll' => function ($q) {
                $q->with('staffLeaving')
                    ->whereHas('staffLeaving');
            }])
            ->with(['employeeOfficialDocument' => function ($q) {
                $q->with('officialDocumentType');
            }])
            ->select('employees.*')
            ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
            ->where(function ($q) {
                $q->where('employees.is_active', 0)
                    ->orWhereNull('employees.is_active');
            })
            // ->whereDoesntHave('employeeStaff')
            ->orderBy('employees.is_active', 'DESC')
            ->orderBy('employees.tabel', 'ASC');
        if (isset($filter['info'])) {
            $employees->where(function (Builder $query) use ($filter) {
                return $query
                    ->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $filter['info'] . "%");
            });
        }
        if (isset($filter['tabel'])) {
            $employees->where('tabel', 'ilike', '%' . $filter['tabel'] . '%');
        }
        if (isset($filter['leaving_reason'])) {
            $employees->whereHas('employeeStaffAll', function ($q) use ($filter) {
                $q->whereHas('staffLeaving', function ($q) use ($filter) {
                    $q
                        ->where('name_uz_latin', 'ilike', '%' . $filter['leaving_reason'] . '%')
                        ->orWhere('name_uz_cyril', 'ilike', '%' . $filter['leaving_reason'] . '%')
                        ->orWhere('name_ru', 'ilike', '%' . $filter['leaving_reason'] . '%');
                });
            });
        }
        if (isset($filter['leaving_reason_id'])) {
            $employees->whereHas('employeeStaff', function ($q) use ($filter) {
                $q->where('leaving_reason_id', 'ilike', '%' . $filter['leaving_reason_id'] . '%');
            });
        }
        if (isset($filter['leave_date'])) {
            $employees->whereHas('employeeStaff', function ($q) use ($filter) {
                $q->where('leave_date', 'ilike', '%' . $filter['leave_date'] . '%');
            });
        }
        if (isset($filter['leave_order_date'])) {
            $employees->whereHas('employeeStaff', function ($q) use ($filter) {
                $q->where('leave_order_date', 'ilike', '%' . $filter['leave_order_date'] . '%');
            });
        }
        if (isset($filter['leave_order_number'])) {
            $employees->whereHas('employeeStaff', function ($q) use ($filter) {
                $q->where('leave_order_number', 'ilike', '%' . $filter['leave_order_number'] . '%');
            });
        }
        return json_encode([
            'employees' => $employees->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
        ]);
    }

    public function getRef($locale)
    {

        //if (Cache::has('employeeGetRef'))
        //    return Cache::get('employeeGetRef');
        //else
        //    return Cache::remember('employeeGetRef', 120, function () use($locale) {
        $staff = Staff::query()
            // with(['department' => function ($query) use ($locale) {
            //     $query->select(['id', 'department_code', 'name_' . $locale]);
            // }])
            ->with(['position' => function ($q) use ($locale) {
                $q->select(['id', 'company_id', 'position_type_id', 'code', 'name_' . $locale]);
            }])
            ->with('range')
            ->where('is_active', 1)
            ->get();
        $staffs = [];
        foreach ($staff as $key => $value) {
            $dep = Department::select(['id', 'department_code', 'name_' . $locale])->find($value->department_id);
            $staffs[$key] = $value;
            $staffs[$key]['department'] = $dep;
        }
        return [
            'staff' => $staffs,
            'leaving_reasons' => LeavingReason::select('id', 'name_' . $locale)->get(),
            'tariff_scales' => TariffScale::select('id', 'category', 'salary', 'hourly_salary', 'description')->get(),
            'companies' => Company::select('id', 'name', 'phone')->get(),
            'countries' => Country::select('id', 'country_code', 'name_' . $locale)->get(),
            'regions' => Region::select('id', 'country_id', 'name_' . $locale)->get(),
            'districts' => District::select('id', 'region_id', 'name_' . $locale)->get(),
            'nationalities' => Nationality::select('id', 'name_' . $locale)->get(),
            'employee_address' => EmployeeAddress::select('id', 'employee_id', 'address_type_id', 'country_id', 'region_id', 'district_id', 'street_address_' . $locale, 'home_address_' . $locale)->get(),
            'address_types' => AddressType::select('id', 'name_' . $locale)->get(),
            'coefficients' => Coefficient::select('id', 'code', 'description')->get(),
            'study_types' => HrStudyType::select('id', 'name_' . $locale)->get(),
            'universities' => HrUniversity::select('id', 'name_' . $locale)->get(),
            'majors' => HrMajor::select('id', 'name_' . $locale)->get(),
            'study_degrees' => HrStudyDegree::select('id', 'name_' . $locale)->get(),
        ];
        //  });
    }

    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $locale = $request->input('locale') == 'ru' ? 'uz_cyril' : $request->input('locale');
        $_locale = $request->input('locale');
        $perPage = $request->input('perPage');
        $employees = Employee::with(['employeeStaffWithInactive' => function ($query) {
            $query->with('tariffScale')
                ->with(['staff' => function ($staffQuery) {
                    $staffQuery->with('position')->with('department');
                }]);
            // ->where('is_active', '=', 1);
        }])
            ->select('employees.*')
            ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
            ->leftJoin('staff', 'staff.id', '=', 'employee_staff.staff_id')
            ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
            // ->where('employee_staff.deleted_at')
            ->with(['staff' => function ($q) {
                $q->select('staff.id', 'position_id', 'department_id')
                    ->with(['position' => function ($q1) {
                        $q1->select('positions.id', 'positions.name_ru', 'positions.name_uz_cyril', 'positions.name_uz_latin');
                    }])
                    ->with(['department' => function ($q1) {
                        $q1->select('departments.id', 'departments.name_ru', 'departments.name_uz_cyril', 'departments.name_uz_latin', 'department_code');
                    }]);
            }])
            ->orderByRaw('departments.department_code ASC')
            // ->groupBy('employees.tabel')
            ->distinct('employees.tabel')
            ->paginate($perPage, ['*'], 'page name', $page);
        $excel = [];
        $department_code = '';
        $category = '';
        $department = '';
        $position = '';
        $first_work_date = '';
        $leave_date = '';
        return $employees;
        foreach ($employees as $key => $value) {
            foreach ($value->employeeStaffWithInactive as $key_codes => $value_codes) {
                if ($value_codes->is_main_staff == 1) {
                    $department_code = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department->department_code : '';
                    $department = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department['name_' . $_locale] : '';
                    $category = $value_codes->tariffScale ? $value_codes->tariffScale->category : '';
                    $position = $value_codes->staff && $value_codes->staff->position ? $value_codes->staff->position['name_' . $_locale] : "";
                    $first_work_date = $value_codes->first_work_date;
                    $leave_date = $value_codes->leave_date ? $value_codes->leave_date : '';
                }
            }
            array_push($excel, (object)[
                "№" => $key + 1 + $page * $perPage - $perPage,
                "Код подразделения" => $department_code,
                "Табелный номер" => '*' . $value->tabel,
                "Сотрудник" => $value['firstname_' . $locale] . ' ' . $value['lastname_' . $locale] . ' ' . $value['middlename_' . $locale],
                "Категория" => $category,
                "Подразделения" => $department,
                "Должность" => $position,
                "Дата приема на работу" => $first_work_date,
                "Дата увольнения" => $leave_date,
            ]);
        }
        return $excel;
    }

    public function update(Request $request, Employee $Employee)
    {
        $model = Employee::find($request->input('id'));
        if (!$model) {
            $model = new Employee();
            $model->created_by = Auth::id();
            $model->is_active = 1;
        } else {
            $model->updated_by = Auth::id();
        }
        $model->company_id = Auth::user()->employee->company_id;
        $model->nationality_id = $request['nationality_id'];
        $model->tabel = $request['tabel'];
        $model->tariff_scale_id = $request['tariff_scale_id'];
        $model->firstname_uz_latin = $request['firstname_uz_latin'];
        $model->lastname_uz_latin = $request['lastname_uz_latin'];
        $model->middlename_uz_latin = $request['middlename_uz_latin'];
        $model->firstname_uz_cyril = $request['firstname_uz_cyril'];
        $model->lastname_uz_cyril = $request['lastname_uz_cyril'];
        $model->middlename_uz_cyril = $request['middlename_uz_cyril'];
        $model->firstname_ru = $request['firstname_uz_cyril'];
        $model->lastname_ru = $request['lastname_uz_cyril'];
        $model->middlename_ru = $request['middlename_uz_cyril'];
        $model->born_date = $request['born_date'];
        $model->gender = $request['gender'];
        $model->inn = $request['INN'];
        $model->inps = $request['INPS'];
        if (isset($request['dr_tb']) && $request['dr_tb']) {
            $dr_employee = Employee::where('tabel', $request['dr_tb'])->get();
            if ($dr_employee) {
                $model->dr_employee_id = $dr_employee[0]->id;
            }
        }
        $model->save();
    }

    public function updateEmployeeAddress(Request $request, Employee $Employee)
    {
        $model = EmployeeAddress::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeAddress();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->address_type_id = $request['address_type_id'];
        $model->country_id = $request['country_id'];
        $model->region_id = $request['region_id'];
        $model->district_id = $request['district_id'];
        $model->street_address_uz_latin = $request['street_address_uz_latin'];
        $model->home_address_uz_latin = $request['home_address_uz_latin'];
        $model->street_address_uz_cyril = $request['street_address_uz_cyril'];
        $model->home_address_uz_cyril = $request['home_address_uz_cyril'];
        $model->street_address_ru = $request['street_address_uz_cyril'];
        $model->home_address_ru = $request['home_address_uz_cyril'];
        $model->description = $request['description'];
        $model->save();
        return EmployeeAddress::with('addressType')
            ->with('country')
            ->with('region')
            ->with('district')
            ->find($model->id);
    }

    public function transferDocument($staff_id1, $staff_id2, $employee_id)
    {
        $document_signers = DocumentSigner::where('staff_id', $staff_id1)
            ->where('signer_employee_id', $employee_id)
            ->whereIn('status', [3, 4])
            ->orWhere('staff_id', $staff_id1)
            ->where('signer_employee_id', $employee_id)
            ->where('action_type_id', 5)
            ->orWhereNotNull('parent_employee_id')
            ->where('staff_id', $staff_id1)
            ->where('signer_employee_id', $employee_id)
            ->where('status', 0)
            ->update(['staff_id' => $staff_id2]);
        return 'Successfully saved!';
    }

    public function updateEmployeeStaff(Request $request, Employee $Employee)
    {
        $document_transfer_new_staff = $request->input('document_transfer_new_staff');
        if ($request->input('document_transfer_new_staff') == true) {
            $staff_id2 = $request->input('staff_id');
            $employee_id = $request->input('employee_id');
            $is_main_staff = $request->input('is_main_staff');
            $empStaff1 = EmployeeStaff::where('employee_id', $employee_id)
                ->where('is_main_staff', $is_main_staff)
                ->where('is_active', 1)
                ->first();
            $this->transferDocument($empStaff1->staff_id, $staff_id2, $employee_id);
        }

        $model = EmployeeStaff::find($request->input('id'));
        if (!$model) {
            if (EmployeeStaff::where('is_active', 1)->where('employee_id', $request['employee_id'])->where('staff_id', $request['staff_id'])->count()) {
                return 0;
            }
            $model = new EmployeeStaff();
            $mainEmployeeStaff = EmployeeStaff::where('employee_id', $request['employee_id'])->where('is_main_staff', 1)->where('is_active', 1)->first();
            if (!$mainEmployeeStaff) {
                $model->is_main_staff = 1;
            } else {
                $model->is_main_staff = 0;
            }
        } else {
            $model->updated_by = Auth::id();
            $model->is_active = 0;
            $is_main_staff = $model->is_main_staff;
            $model->save();
            if (!$request->input('is_active')) {
                return EmployeeStaff::with('tariffScale')
                    ->with('employee')
                    ->with('staff')
                    ->with('staff.position')
                    ->with('staff.department')
                    ->find($model->id);
            }
            $model = new EmployeeStaff();
            $model->is_main_staff = $is_main_staff;
        }
        $model->created_by = Auth::id();
        $model->is_active = 1;
        $model->employee_id = $request['employee_id'];
        $model->staff_id = $request['staff_id'];
        $model->tariff_scale_id = $request['tariff_scale_id'];
        $model->contract_number = $request['contract_number'];
        $model->contract_date = $request['contract_date'];
        $model->enter_order_number = $request['enter_order_number'];
        $model->enter_order_date = $request['enter_order_date'];
        $model->first_work_date = $request['first_work_date'];
        $model->leave_order_number = $request['leave_order_number'];
        $model->leave_order_date = $request['leave_order_date'];
        if ($model->is_main_staff) {
            $employee = Employee::find($model->employee_id);
            $employee->staff_id = $model->staff_id;
            $employee->contract_number = $model->contract_number;
            $employee->contract_date = $model->contract_date;
            $employee->enter_order_number = $model->enter_order_number;
            $employee->enter_order_date = $model->enter_order_date;
            $employee->first_work_date = $model->first_work_date;
            $employee->tariff_scale_id = $model->tariff_scale_id;
            $employee->is_active = 1;
            $employee->save();
        }
        $model->save();
        return EmployeeStaff::with('tariffScale')
            ->with('employee')
            ->with('staff')
            ->with('staff.position')
            ->with('staff.department')
            ->find($model->id);
    }

    public function updateEmployeeHistoryStaff(Request $request, Employee $Employee)
    {
        $model = EmployeeStaff::find($request->input('id'));
        // if (!$model) {
        //     if (EmployeeStaff::where('is_active', 1)->where('employee_id', $request['employee_id'])->where('staff_id', $request['staff_id'])->count()) {
        //         return 0;
        //     }
        //     $model = new EmployeeStaff();
        //     $mainEmployeeStaff = EmployeeStaff::where('employee_id', $request['employee_id'])->where('is_main_staff', 1)->where('is_active', 1)->first();
        //     if (!$mainEmployeeStaff) {
        //         $model->is_main_staff = 1;
        //     } else {
        //         $model->is_main_staff = 0;
        //     }
        // } else {
        //     $model->updated_by = Auth::id();
        //     $model->is_active = 0;
        //     $is_main_staff = $model->is_main_staff;
        //     $model->save();
        //     if (!$request->input('is_active')) {
        //         return EmployeeStaff::with('tariffScale')
        //             ->with('employee')
        //             ->with('staff')
        //             ->with('staff.position')
        //             ->with('staff.department')
        //             ->find($model->id);
        //     }
        //     $model = new EmployeeStaff();
        //     $model->is_main_staff = $is_main_staff;
        // }
        // $model->created_by = Auth::id();
        $model->is_active = 1;
        $model->employee_id = $request['employee_id'];
        $model->staff_id = $request['staff_id'];
        $model->tariff_scale_id = $request['tariff_scale_id'];
        $model->contract_number = $request['contract_number'];
        $model->contract_date = $request['contract_date'];
        $model->enter_order_number = $request['enter_order_number'];
        $model->enter_order_date = $request['enter_order_date'];
        $model->first_work_date = $request['first_work_date'];
        $model->leave_order_number = $request['leave_order_number'];
        $model->leave_order_date = $request['leave_order_date'];
        if ($model->is_main_staff) {
            $employee = Employee::find($model->employee_id);
            $employee->staff_id = $model->staff_id;
            $employee->contract_number = $model->contract_number;
            $employee->contract_date = $model->contract_date;
            $employee->enter_order_number = $model->enter_order_number;
            $employee->enter_order_date = $model->enter_order_date;
            $employee->first_work_date = $model->first_work_date;
            $employee->tariff_scale_id = $model->tariff_scale_id;
            $employee->is_active = 1;
            $employee->save();
        }
        $model->save();
        return EmployeeStaff::with('tariffScale')
            ->with('employee')
            ->with('staff')
            ->with('staff.position')
            ->with('staff.department')
            ->find($model->id);
    }

    public function updateEmployeeCoefficient(Request $request, Employee $Employee)
    {
        $model = EmployeeCoefficient::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeCoefficient();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->percent = $request['percent'];
        $model->coefficient_id = $request['coefficient_id'];
        $model->begin_date = $request['begin_date'];
        $model->end_date = $request['end_date'];
        $model->order_number = $request['order_number'];
        $model->order_date = $request['order_date'];
        $model->description = $request['description'];
        $model->status = $request['status'];
        $model->save();
        return EmployeeCoefficient::with('coefficient')->find($model->id);
    }

    public function updateEmployeePhone(Request $request, Employee $Employee)
    {
        $model = EmployeePhone::find($request->input('id'));
        if (!$model) {
            $model = new EmployeePhone();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->phone_type = $request['phone_type'];
        $model->phone_number = $request['phone_number'];
        $model->description = $request['description'];
        $model->save();
        return EmployeePhone::find($model->id);
    }
    public function updateEmployeeLanguage(Request $request, Employee $Employee)
    {
        $model = EmployeeLanguage::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeLanguage();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_language_id = $request['hr_language_id'];
        $model->level = $request['level'];
        $model->description = $request['description'];
        $model->save();
        return EmployeeLanguage::find($model->id);
    }
    public function updateEmployeeParty(Request $request, Employee $Employee)
    {
        $model = EmployeeParty::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeParty();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_party_id = $request['hr_party_id'];
        $model->description = $request['description'];
        $model->save();
        return EmployeeParty::find($model->id);
    }

    public function destroy($id)
    {
        $model = Employee::find($id);
        $model->delete();
    }

    public function deleteAddress($id)
    {
        $model = EmployeeAddress::find($id);
        $model->delete();
    }

    public function deleteEmployeeStaff($id)
    {
        $model = EmployeeStaff::find($id);
        $model->is_active = 0;
        $model->save();
    }

    public function deleteEmployeeCoefficient($id)
    {
        $model = EmployeeCoefficient::find($id);
        $model->delete();
    }

    public function getEmployee(Request $request, Employee $Employee)
    {
        $access_type = $request->input('access_type');
        $language = $request->input('language');
        $tabel = strtoupper($request->input(['tabel']));
        $document_template_id = ($request->input(['document_template_id'])) ? $request->input(['document_template_id']) : '';

        $user = Auth::user();
        $employee = Employee::where('tabel', $tabel)->first();

        $interval = 0;
        $jazo = 0;
        $absentisim = 0;
        $categotiyaold = 0;
        if($document_template_id == 636){
            $date = date('Y-m-d');
            try {
                $response = Http::withoutVerifying()
                    ->get('https://edo-db2.uzautomotors.com/api/get-skud-for-kategoriya/' . $tabel.'/'.$date)->body();
                if ($response != "") {
                    // return
                    $response = json_decode($response);
                    
                    if(isset($response[0])){
                        $jazo_res = $response[0];
                        $date = substr($jazo_res->z109prdate, 0, 4) . '-' . substr($jazo_res->z109prdate, 4, 2) . '-' . substr($jazo_res->z109prdate, 6, 2);
                        $jazo =  trim($jazo_res->z109prrem).' '.trim($jazo_res->z109prnom).' '.$date;
                    }
                    if(isset($response[1])){
                        $absentisim_res = $response[1];
                        $absentisim =  $absentisim_res->z55abs;
                    }
                    if(isset($response[2])){
                        $categotiya_res = $response[2];

                        // return
                        $cdate = $categotiya_res->z61yy . '-' . $categotiya_res->z61mm;
                        // $categotiyaold =  trim($categotiya_res->z61kat);
                        $categotiyaold =  trim($categotiya_res->z61kat).' '.$cdate;
                    } else {
                        $categotiyaold = 'katetogriyasi o\'zgarmagan';
                    }
                 
                    
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if(in_array($document_template_id, [592, 636])){
            try {
                $response = Http::withoutVerifying()
                    ->post('https://edo-db2.uzautomotors.com/api/get-stage/' . $tabel)->body();
                if ($response != "") {
                    $response = json_decode($response);
                    $boshqa_joyda_ishlagan_yillari = $response[1];
                    $date = substr($response[0], 0, 4) . '-' . substr($response[0], 4, 2) . '-' . substr($response[0], 6, 2);
                    $datetime1 = date_create($date);
                    $datetime2 = date_create(date('Y-m-d'));
                    $interval = date_diff($datetime1, $datetime2)->format('%y') + $boshqa_joyda_ishlagan_yillari;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if (true) {
            $employee = Employee::where(['tabel' => $tabel])
                ->with('tariffScale')
                ->where('is_active', '!=', 0)
                ->with('staff.department')
                ->with('staff.position')
                ->with('staff.range')
                ->with(['mainStaff' => function ($q) {
                    $q->with('range');
                }])
                ->with(['employeeStaff' => function ($query) {
                    $query
                        // ->with('tariffScale')
                        ->where('is_active', '=', 1)->orderBy('is_main_staff', 'desc');
                }])
                ->select([
                    'id', 'tariff_scale_id', 'firstname_' . $language . ' as firstname',
                    'lastname_' . $language . ' as lastname', 'middlename_' . $language . ' as middlename'
                ]);

            if ($request->input('document_template_id') && in_array($document_template_id, [415])) { //E8 larni ko'rolmaydi
                $employee
                    ->with(['employeeCoefficients' => function ($query) {
                        $query->with('coefficient');
                    }])
                    ->whereHas('tariffScale', function ($q) {
                        $q->whereNotIn('id', [37, 38, 39]);
                    });
            } elseif ($request->input('document_template_id') && in_array($document_template_id, [419])) { //E8 dan boshqalardi ko'rolmaydi
                $employee

                    ->with(['employeeCoefficients' => function ($query) {
                        $query->with('coefficient');
                    }])
                    ->whereHas('tariffScale', function ($q) {
                        $q->whereIn('id', [37, 38, 39]);
                    });
            } elseif ($request->input('document_template_id') && in_array($document_template_id, [420])) { //E8 dan boshqalardi ko'rolmaydi
                $employee
                    ->with(['employeeCoefficients' => function ($query) {
                        $query->with('coefficient');
                    }])
                    // ->whereHas('tariffScale', function ($q) {
                    //     $q->whereNotIn('id', [37, 38, 39]);
                    // })
                    ->whereHas('staff', function ($q) {
                        $q->whereHas('department', function ($q2) {
                            $q2->where('branch_id', 3);
                        });
                    });
            }
            return (['employee' => $employee->get(), 'parents' => Employee::parentDepartments($tabel), 'staj' => $interval, 'forcategoriya' => [$jazo, $absentisim,$categotiyaold]]);
        }
    }

    public function getEmployeeReport()
    {
        //Start
        // $employees =  DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // GROUP BY  branches.name");


        // $directors = DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND personal_types.id = 1
        // AND employees.tariff_scale_id IN (37, 38, 39)
        // GROUP BY branches.name");


        // $itrs = DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND personal_types.id = 1
        // AND tariff_scales.category BETWEEN 'E%' AND 'E8%'
        // GROUP BY branches.name");

        // $restItrs = DB::select("SELECT branches.name, branches.id, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND personal_types.id != 1
        // AND tariff_scales.category BETWEEN 'E%' AND 'E8%'
        // GROUP BY branches.name, branches.id");

        // $masters =  DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND employees.tariff_scale_id IN (13, 14, 15)
        // GROUP BY branches.name");

        // $brigadirs = DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND employees.tariff_scale_id  IN (10, 11, 12)
        // GROUP BY branches.name");

        // $workers =  DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND tariff_scales.category BETWEEN 'L%' AND 'L4%'
        // GROUP BY branches.name");

        // $women =  DB::select("SELECT branches.name, COUNT(*) as count FROM employees
        // LEFT JOIN employee_staff ON employee_staff.employee_id = employees.id
        // LEFT JOIN staff on staff.id = employee_staff.staff_id
        // LEFT JOIN departments on staff.department_id = departments.id
        // LEFT JOIN tariff_scales on employees.tariff_scale_id = tariff_scales.id
        // LEFT JOIN personal_types on staff.personal_type_id = personal_types.id
        // LEFT JOIN branches on departments.branch_id = branches.id
        // WHERE employees.is_active = 1
        // AND employees.deleted_at is null
        // AND employees.gender = 2
        // GROUP BY branches.name");

        // return ['employees' => $employees, 'directors' => $directors, 'itrs' => $itrs, 'restItrs' => $restItrs,
        // 'masters' => $masters, 'brigadirs' => $brigadirs, 'workers' => $workers, 'women' => $women ];

        //End

        $position_types = PositionType::get();
        foreach ($position_types as $key => $position_type) {
            $count = [];
            foreach (Branch::get() as $key => $branch) {
                $count[] = [
                    'data' => DB::select('SELECT COUNT(*) as `count` FROM `employee_staff`
                WHERE `staff_id` in (SELECT `id` FROM `staff`
                WHERE `position_id` in (SELECT `id` FROM `positions`
                WHERE `position_type_id` = ' . $position_type->id . ') and `branch_id` = ' . $branch->id . ') and `is_active` = 1'),
                    'branch' => $branch->name
                ];
            }
            $position_type->employee_count = $count;
        }

        $branch = Branch::get();

        return ['position_types' => $position_types, 'branch' => $branch];

        // $result = DB::select("SELECT b.name branch,
        // CASE
        //     WHEN substr(ts.category,1,2) = 'E8' AND pt.id = 1  THEN 'DIRECTOR'
        //     WHEN substr(ts.category,1,2) != 'E8' AND substr(ts.category,1,1) = 'E' AND pt.id = 1 THEN 'ITR'
        //     WHEN substr(ts.category,1,2) != 'E8' AND substr(ts.category,1,1) = 'E' AND pt.id != 1 THEN 'restITr'
        //     WHEN substr(ts.category,1,1) = 'F' THEN 'Master'
        //     WHEN substr(ts.category,1,2) = 'L4' THEN 'Brigadir'
        //     WHEN substr(ts.category,1,1) = 'L' and substr(ts.category,1,2) != 'L4' THEN 'Rabochiy'
        //     WHEN e.gender = 2 THEN 'Ayollar'
        // END tp,
        // COUNT(*) count
        // FROM employees e
        // LEFT JOIN employee_staff es ON es.employee_id = e.id and es.is_active = 1
        // LEFT JOIN staff s ON es.staff_id = s.id
        // LEFT JOIN departments d ON s.department_id = d.id
        // LEFT JOIN tariff_scales ts on e.tariff_scale_id = ts.id
        // LEFT JOIN personal_types pt on s.personal_type_id = pt.id
        // LEFT JOIN branches b on d.branch_id = b.id
        // where e.is_active = 1
        // GROUP BY branch, tp");
        // return ['result' => $result, 'employees' => $employees];
    }

    public function showEmployee($id)
    {
        if (Auth::user()->employee_id != $id && !Auth::user()->hasRole('show_profile')) {
            dd(Auth::user()->employee_id, $id);
            return null;
        }

        $model = Employee::with('employeeStaff')
            ->with('employeeStaff.tariffScale')
            ->with('staff')
            ->with('devices')
            ->with('staff.position')
            ->with('staff.department')
            ->with('employeeCoefficients')
            ->with('employeeAddresses.country')
            ->with('employeeAddresses.region')
            ->with('employeeAddresses.district')
            ->with('employeePhones')
            ->with('employeeLanguages.hrLanguage')
            ->with('employeeMilitaryRanks.hrMilitaryRank')
            ->with('employeeStateAwards.hrStateAward')
            ->with('employeeParties.hrParty')
            ->with('company')
            ->with('country')
            ->with('nationality')
            ->with('region')
            ->with('district')
            ->with('employeeOfficialDocument.officialDocumentType')
            ->with('user.roles')
            ->with('employeeWorkHistories')
            ->with('employeeEducationHistories.studyType')
            ->with('employeeEducationHistories.university')
            ->with('employeeEducationHistories.major')
            ->with('employeeEducationHistories.studyDegree')
            // ->with(['employeeEducationHistories' => function ($q1) {
            //     $q1->with('studyType');
            // }])
            ->with(['employeeRelative' => function ($q) {
                $q->with('familyRelative');
            }])
            ->where('is_active', 1)
            ->where('id', $id)->first();
        return $model;
    }

    public function getAvatar($id)
    {
        $emp = Employee::find($id);
        if (Storage::exists('avatars/' . $emp->tabel . ".jpg")) {
            $image = Storage::get('avatars/' . $emp->tabel . ".jpg");
            $base64 = base64_encode($image);
            return $base64;
        } elseif (Storage::exists('avatars/' . $emp->tabel . ".JPG")) {
            $image = Storage::get('avatars/' . $emp->tabel . ".JPG");
            $base64 = base64_encode($image);
            return $base64;
        } else {
            return null;
        }
    }

    public function infoLang()
    {
        return [
            'uz_latin' => [
                'malumotnoma' => "Ma'lumotnoma",
                'born_date' => "Tug'ilgan yili",
                'born_place' => "Tug'ilgan joyi",
                'nationality' => "Millati",
                'party' => "Partiyaviyligi",
                'information' => "Ma'lumoti",
                'completed' => "Tamomlagan",
                'specialty_edu' => "Ma'lumoti bo'yicha mutaxassisligi",
                'academic_degree' => "Ilmiy darajasi",
                'academic_title' => "Ilmiy unvoni",
                'foreign_languages' => "Qaysi chet tillarini biladi",
                'military_title' => "Harbiy (maxsus) unvoni",
                'state_award' => "Davlat mukofotlari bilan taqdirlanganmi(qanaqa)",
                'phone' => "Telefon raqami",
                'work_history' => "Mehnat faoliyati",
                'mp' => "Xalq deputatlari, respublika, viloyat, shahar va tuman Kengashi deputatimi yoki boshqa saylanadigon organlarning a'zosimi (to'liq ko'rsatilishi lozim)",
                'employee_relative' => "yaqin qarindoshlari haqida",
                'info' => "Ma'lumot",
                'relative' => "Qarindoshligi",
                'full_name' => "Familiyasi, ismi va otasining ismi",
                'place_of_birth' => "Tug'ilgan yili va joyi",
                'place_of_work' => "Ish joyi va lavozimi",
                'living_place' => "Turar joyi",
                'no' => "yo'q",
                'year' => "y",
                'dan' => "dan:",
            ],
            'uz_cyril' => [
                'malumotnoma' => "Маьлумотнома",
                'born_date' => "Туғилган йили",
                'born_place' => "Туғилган жойи",
                'nationality' => "Миллати",
                'party' => "Партиявийлиги",
                'information' => "Маълумоти",
                'completed' => "Тамомлаган",
                'specialty_edu' => "Маълумоти бўйича мутахассислиги",
                'academic_degree' => "Илмий даражаси",
                'academic_title' => "Илмий унвони",
                'foreign_languages' => "Қайси чет тилларини билади",
                'military_title' => "Ҳарбий (махсус) унвони",
                'state_award' => "Давлат мукофотлари билан тақдирланганми(қанақа)",
                'phone' => "Телефон рақами",
                'work_history' => "Меҳнат фаолияти",
                'mp' => "Халқ депутатлари, республика, вилоят, шаҳар ва туман Кенгаши депутатими ёки бошқа сайланадигон органларнинг аъзосими (тўлиқ кўрсатилиши лозим)",
                'employee_relative' => "яқин қариндошлари ҳақида",
                'info' => "Маълумот",
                'relative' => "Қариндошлиги",
                'full_name' => "Фамилияси, исми ва отасининг исми",
                'place_of_birth' => "Туғилган йили ва жойи",
                'place_of_work' => "Иш жойи ва лавозими",
                'living_place' => "Турар жойи",
                'no' => "йўқ",
                'year' => "й",
                'dan' => "дан:",
            ],
            'ru' => [
                'malumotnoma' => "Справка",
                'born_date' => "Год рождения",
                'born_place' => "Место рождения",
                'nationality' => "Национальность",
                'party' => "Партия",
                'information' => "Образование",
                'completed' => "Завершенный",
                'specialty_edu' => "Специальность в образовании",
                'academic_degree' => "Илмий даражаси",
                'academic_degree' => "Ученая степень",
                'academic_title' => "Ученое звание",
                'foreign_languages' => "Какие иностранные языки знает",
                'military_title' => "Военное (специальное) звание",
                'state_award' => "Награжден государственными наградами",
                'phone' => "Телефон номер",
                'work_history' => "История работы",
                'mp' => "Народный депутат, депутат республиканского, областного, городского и районного Кенгаша или член других выборных органов (указать полностью)",
                'employee_relative' => "О близких родственниках",
                'info' => "Информация",
                'relative' => "Относительный",
                'full_name' => "Фамилия, имя и отчество",
                'place_of_birth' => "Год и место рождения",
                'place_of_work' => "Место работы и должность",
                'living_place' => "Места жительства",
                'no' => "нет",
                'year' => "гю",
                'dan' => "и:",
            ],
        ];
    }

    public function getPdf($employee_id, $locale)
    {
        $employee = Employee::find($employee_id);
        $lang = $this->infoLang();
        // $history =  EmployeeStaff::where('employee_id', $employee_id)
        // ->with('staff.department')
        // ->with('staff.position')
        // ->with('department')
        // ->with('tariffScale')->get();
        $history = Employee::getStaffHistory($employee_id, $locale);
        $avatar = $this->getAvatar($employee_id);

        //************************************************************************* */

        $content = '<div class="profileFont">';
        $content = '<div style="letter-spacing:1px; font-family: Times New Roman">';
        $content .= '<div style="position: absolute; right: 0; top: 0;">';
        // $content .= '<img v-if="employee.base64" :src="'data:application/jpg;base64,' + employee.base64" />'
        // $content .= '<img src="../../assets/User-Default.jpg" />';
        $content .= '<img style="width:154px; height:154px;  image-orientation: from-image; " src="data:image/jpg;base64,' . $avatar . '" />';
        $content .= '</div>';
        // $content = Document::style();
        $content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
        @font-face{
            font-family:  times;
            font-style: normal;
            font-weight: normal;
            src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
        }
        @font-face{
            font-family:  times;
            font-style: bold;
            font-weight: normal;
            src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
        }
        @font-face{
            font-family:  times;
            font-style: italic;
            font-weight: normal;
            src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
        }
        @font-face{
            font-family:  times;
            font-style: bolditalic;
            font-weight: normal;
            src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
        }
        body{
                font-family: "times";
            //font-family: "freeserif";
            letter-spacing:1px;
            // text-rendering: auto;
            // text-rendering: optimizeSpeed;
            // text-rendering: optimizeLegibility;
            text-rendering: geometricPrecision;
        }
        </style>';
        $content .= '<h2 style="font-family:  times; font-size: 16px;text-transform: uppercase;text-align: center;">' . $lang[$locale]['malumotnoma'] . '</h2>';
        $content .= '<p style="font-size: 16px;text-align: center;font-weight: 900;">';
        $content .= $employee['lastname_' . $locale] . ' ';
        $content .= $employee['firstname_' . $locale] . ' ';
        $content .= $employee['middlename_' . $locale];
        $content .= '</p>';
        $content .= '<p style="margin: 0px;font-size: 14px;">';
        // $content .= $employee['enter_order_date'] ? $employee['enter_order_date'] .' ' .$lang[$locale]['dan'] : ' ';
        $content .= $history[count($history) - 1]['enterOrderDate'] ? $history[count($history) - 1]['enterOrderDate'] . $lang[$locale]['dan'] : ' ';
        $content .= '</p>';

        $content .= '<p style="max-width:550px; word-wrap: break-word; font-size: 14px; font-weight: 600; padding-top:0px; margin:0px;">';
        $content .= $employee->company['name'] ? $employee->company['name'] . ', ' : ' ';
        $content .= $history[count($history) - 1]['parent'] ? $history[count($history) - 1]['parent'] . ', ' : ' ';
        $content .= $history[count($history) - 1]['department'] ? $history[count($history) - 1]['department'] . ', ' : ' ';
        $content .= $history[count($history) - 1]['position'] ? $history[count($history) - 1]['position'] . ' ' : ' ';
        $content .= '</p>';

        $content .= '<table style="width:100%;">';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['born_date'] . ':</b><br>';
        $content .= $employee['born_date'] ? $employee['born_date'] : ' ';
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['born_place'] . ':</b><br>';
        $content .= $employee->employeeAddresses->where('address_type_id', 1)->first() ? $employee->employeeAddresses->where('address_type_id', 1)->first()->region['name_' . $locale] : ' ';
        $content .= $employee->employeeAddresses->where('address_type_id', 1)->first() ? $employee->employeeAddresses->where('address_type_id', 1)->first()->district['name_' . $locale] : ' ';
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['nationality'] . ':</b><br>';
        if (!($employee->nationality)) {
            $content .= ' - ';
        } else {
            $content .= $employee->nationality['name_' . $locale];
        }
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['party'] . ':</b><br>';
        if (!($employee->employeeParties && $employee->employeeParties->hrParty)) {
            $content .= $lang[$locale]['no'];
        } else {
            $content .= $employee->employeeParties->hrParty['name_' . $locale];
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['information'] . ':</b><br>';
        if (!count($employee->employeeEducationHistories)) {
            $content .= ' - ';
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                $content .= $value->studyDegree['name_' . $locale] . '<br>';
            }
        }
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['completed'] . ':</b><br>';
        if (!count($employee->employeeEducationHistories)) {
            $content .= ' - ';
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                // $content .=  $value->end_date . ' ' . $lang[$locale]['year'] . ' ';
                $content .= substr($value['end_date'], 0, 4) . '  ' . $lang[$locale]['year'] . ' ';
                $content .=  $value->university['name_' . $locale];
                $content .= ' (' . $value->studyType['name_' . $locale] . ') <br>';
            }
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['specialty_edu'] . ': </b></td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;">';
        if (!count($employee->employeeEducationHistories)) {
            $content .= ' - ';
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                $content .= $value->major['name_' . $locale] . '<br>';
            }
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['academic_degree'] . ':</b><br>';
        // $content .= $lang[$locale]['no'];
        if (!count($employee->employeeEducationHistories)) {
            $content .= $lang[$locale]['no'] . '<br>';
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                $content .= $value->academic_degree ? $value->academic_degree : $lang[$locale]['no'] . '<br>';
            }
        }
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['academic_title'] . ':</b><br>';
        if (!count($employee->employeeEducationHistories)) {
            $content .= $lang[$locale]['no'] . '<br>';
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                $content .= $value->academic_title ? $value->academic_title : $lang[$locale]['no'] . '<br>';
            }
        }
        // forea
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['foreign_languages'] . ':</b><br>';
        if (!count($employee->employeeLanguages)) {
            $content .= ' - ';
        } else {
            foreach ($employee->employeeLanguages as $key => $value) {
                $content .= $value->hrLanguage['name_' . $locale] . ' ';
            }
        }
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['military_title'] . ':</b><br>';
        if (!count($employee->employeeMilitaryRanks)) {
            $content .= $lang[$locale]['no'];
        } else {
            foreach ($employee->employeeEducationHistories as $key => $value) {
                $content .= $value->hrMilitaryRank['name_' . $locale];
            }
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['state_award'] . ':</b><br>';
        if (!count($employee->employeeStateAwards)) {
            $content .= $lang[$locale]['no'];
        } else {
            foreach ($employee->employeeStateAwards as $key => $value) {
                $content .= $value->hrstateAward['name_' . $locale];
            }
        }
        $content .= '</td>';
        $content .= '<td style="width:50%; font-size: 14px; padding-top:8px;"><b>' . $lang[$locale]['phone'] . ':</b><br>';
        if (!count($employee->employeePhones)) {
            $content .= ' - ';
        } else {
            foreach ($employee->employeePhones as $key => $value) {
                $content .= $value->phone_number;
            }
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '<p style="font-size: 14px; padding-top:8px; margin:0px;"><b>' . $lang[$locale]['mp'] . ': </b><br>';
        $content .= $employee->employeeParties && $employee->employeeParties->hrParty ? $employee->employeeParties->hrParty['name_' . $locale] : $lang[$locale]['no'];
        $content .= '</p>';
        if (!count($employee->employeeWorkHistories) && !$history) {
            $content .= ' ';
        } else {
            $content .= '<h2 style="font-size: 14px;text-transform: uppercase;text-align: center;">' . $lang[$locale]['work_history'] . '</h2>';
        }

        if (!count($employee->employeeWorkHistories)) {
            $content .= ' ';
        } else {
            foreach ($employee->employeeWorkHistories as $key => $value) {
                $content .= '<p style="font-size: 14px; padding-top:8px; margin:0px;">';
                $content .= $value->begin_date ? substr($value->begin_date, 0, 4) . ' - ' : ' ';
                $content .= $value->end_date ? substr($value->end_date, 0, 4) . ' ' . $lang[$locale]['year'] . $lang[$locale]['year'] . ' ' . ' - ' : ' ' . ' ' . $lang[$locale]['year'] . ' ';
                $content .= $value->work_place ? $value->work_place . ', ' : ' ';
                $content .= $value->position ? $value->position : ' ';
                $content .= '</p>';
            }
        }

        // $content .= '<p style="font-size: 14px; padding-top:8px; margin:0px;">';
        // $content .= json_encode($history);
        // $content .= '</p>';
        // dd($history);

        foreach ($history as $key => $value) {
            // dd($value);
            $content .= '<p style="font-size: 14px; padding-top:8px; margin:0px;">';
            if ($value['enterOrderDate'] && $value['enterOrderDate'] != null) {
                $content .= substr($value['enterOrderDate'], 0, 4) . ' - ';
            } elseif (!$value['enterOrderDate']) {
                $content .= substr($value['createdAt'], 0, 4) . ' - ';
            }
            if (count($history) - 1 == $key) {
                $value['updatedAt'] = '';
            }
            // $content .= $value['enterOrderDate'] ? substr($value['enterOrderDate'], 0, 4) . ' - ' : ' ';
            if ($value['leaveOrderDate']) {
                $content .= substr($value['leaveOrderDate'], 0, 4) . '  ';
            } elseif (!$value['leaveOrderDate']) {
                $content .= substr($value['updatedAt'], 0, 4) . '  ';
            }

            // else {
            //     $content .= $history[$key + 1]['enterOrderDate'] ? substr($history[$key + 1]['enterOrderDate'], 0, 4) . ' - ' : ' ';
            // }
            $content .= $employee->company['name'] ? $employee->company['name'] . ', ' : ' ';
            $content .= $value['parent'] ? $value['parent'] . ', ' : ' ';
            $content .= $value['department'] ? $value['department'] . ', ' : ' ';
            $content .= $value['position'] ? $value['position'] . ' ' : ' ';
            $content .= '</p>';
        }

        $content .= count($employee->employeeRelative) ? '<p style=" margin-top:50px; font-size: 16px;text-align: center;font-weight: 600; page-break-before: always;">' : ' ';
        $content .= count($employee->employeeRelative) ? $employee['lastname_' . $locale] . ' ' : ' ';
        $content .= count($employee->employeeRelative) ? $employee['firstname_' . $locale] . ' ' : ' ';
        $content .= count($employee->employeeRelative) ? $employee['middlename_' . $locale] . ' ' : ' ';
        $content .= count($employee->employeeRelative) ? $lang[$locale]['employee_relative'] : ' ';
        $content .= $employee->employeeRelative ? '</p>' : ' ';
        $content .= count($employee->employeeRelative) ? '<h2 style="font-size: 16px;text-transform: uppercase;text-align: center;">' . $lang[$locale]['info'] . '</h2>' : ' ';
        $content .= '<table style="width:100%; font-size:16px; text-align:center; border: 1px solid black; border-collapse: collapse;">';

        if (!count($employee->employeeRelative)) {
            $content .= ' ';
        } else {
            $content .= '<tr style="border: 1px solid black; border-collapse: collapse;">';
            $content .= '<th style="border: 1px solid black; border-collapse: collapse;">';
            $content .= $lang[$locale]['relative'];
            $content .= '</th>';
            $content .= '<th style="border: 1px solid black; border-collapse: collapse;">';
            $content .= $lang[$locale]['full_name'];
            $content .= '</th>';
            $content .= '<th style="border: 1px solid black; border-collapse: collapse;">';
            $content .= $lang[$locale]['place_of_birth'];
            $content .= '</th>';
            $content .= '<th style="border: 1px solid black; border-collapse: collapse;">';
            $content .= $lang[$locale]['place_of_work'];
            $content .= '</th>';
            $content .= '<th style="border: 1px solid black; border-collapse: collapse;">';
            $content .= $lang[$locale]['living_place'];
            $content .= '</th>';
            $content .= '</tr>';
        }
        foreach ($employee->employeeRelative as $key => $value) {
            $content .= $employee->employeeRelative ? '<tr style="border: 1px solid black; border-collapse: collapse;">' : ' ';
            $content .= $employee->employeeRelative ? '<td style="border: 1px solid black; border-collapse: collapse; font-weight: 900;">' : ' ';
            $content .= $employee->employeeRelative ? $value->familyRelative['name_' . $locale] : ' ';
            $content .= $employee->employeeRelative ? '</td>' : ' ';
            $content .= $employee->employeeRelative ? '<td style="border: 1px solid black; border-collapse: collapse;">' : ' ';
            $content .= $employee->employeeRelative ? $value['last_name'] . ' ' : ' ';
            $content .= $employee->employeeRelative ? $value['first_name'] . ' ' : ' ';
            $content .= $employee->employeeRelative ? $value['middle_name'] : ' ';
            $content .= $employee->employeeRelative ? '</td>' : ' ';
            $content .= $employee->employeeRelative ? '<td style="border: 1px solid black; border-collapse: collapse;">' : ' ';
            $content .= $employee->employeeRelative ? $value['born_date'] . ', <br>' : ' ';
            $content .= $employee->employeeRelative ? $value['born_place'] : ' ';
            $content .= $employee->employeeRelative ? '</td>' : ' ';
            $content .= $employee->employeeRelative ? '<td style="border: 1px solid black; border-collapse: collapse;">' : ' ';
            $content .= $employee->employeeRelative ? $value['work_place'] : ' ';
            $content .= $employee->employeeRelative ? '</td>' : ' ';
            $content .= $employee->employeeRelative ? '<td style="border: 1px solid black; border-collapse: collapse;">' : ' ';
            $content .= $employee->employeeRelative ? $value['living_place'] : ' ';
            $content .= $employee->employeeRelative ? '</td>' : ' ';
            $content .= $employee->employeeRelative ? '</tr>' : ' ';
        }

        $content .= $employee->employeeRelative ? '</table>' : ' ';
        $content .= '</div>';
        $content .= '</div>';

        // return $content;
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('images', true)
            ->setOption('footer-right', '[page] / [topage]')
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public function getAllEmployees($tabel)
    {
        $empInfo = Employee::select(
            'employee_addresses.home_address_uz_latin',
            'employee_addresses.home_address_uz_cyril',
            'employee_addresses.home_address_ru',
            'departments.department_code',
            'departments.name_uz_latin as profession',
            'employees.id',
            'employees.tabel',
            'employees.firstname_uz_latin',
            'employees.lastname_uz_latin',
            'employees.middlename_uz_latin',
            'employees.born_date',
            'positions.name_uz_latin'
        )
            ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
            ->leftJoin('staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
            ->leftJoin('employee_addresses', 'employee_addresses.employee_id', '=', 'employees.id')
            ->leftJoin('positions', 'positions.id', '=', 'staff.position_id')
            ->where('tabel', $tabel)
            ->where('employee_staff.is_active', 1)
            // ->limit(10)
            ->orderBy('is_main_staff','DESC')
            ->first()
            ;
        return $empInfo;
    }
    public function getHistory($id, $locale)
    {
        return Employee::getStaffHistory($id, $locale);
    }

    public function getEmployeeWithStaff($id)
    {
        $employee = Employee::with(['staff' => function ($q) {
            $q->with('position')
                ->with('department');
        }])->find($id);
        if ($employee) {
            return json_encode($employee);
        }
        return null;
    }

    public function getChiefs($helper_employee_id)
    {
        $chiefHelper = ChiefHelper::where('helper_employee_id', $helper_employee_id)->get();
        $employees = Employee::select([
            'id',
            'firstname_uz_latin',
            'firstname_uz_cyril',
            'firstname_ru',
            'middlename_uz_latin',
            'middlename_uz_cyril',
            'middlename_ru',
            'lastname_uz_latin',
            'lastname_uz_cyril',
            'lastname_ru',
            'tabel',
        ])
            ->whereIn('id', collect($chiefHelper)->pluck('chief_employee_id'))->get();
        return $employees;
    }

    public function updateChiefs(Request $request)
    {
        $model = ChiefHelper::find($request->input('id'));
        if (!$model) {
            $model = new ChiefHelper();
        }
        $model->chief_employee_id = $request->input('chief_employee_id');
        $model->helper_employee_id = $request->input('helper_employee_id');
        $model->save();
    }

    public function deleteChiefs($id)
    {
        $model = ChiefHelper::find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function getChiefEmployee(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        return ChiefHelper::with('chiefEmployee')->with('helperEmployee')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, '*', 'page name', $page);
    }


    public function pdiEmployee($tabel)
    {
        return
            Employee::select(
                'id',
                'tabel',
                'firstname_uz_latin',
                'lastname_uz_latin',
                'middlename_uz_latin',
                'born_date',
                'born_date',
                'born_date'
            )
            ->with(['employeefile' =>  function ($q) {
                $q->select(
                    'id',
                    'object_type_id',
                    'object_id',
                    'physical_name',
                    'file_name'
                )->where('object_type_id', 8);
            }])
            ->with(['mainStaff' => function ($q) {
                $q->with('position')->with('department');
            }])
            ->where('tabel', $tabel)
            ->first();
        //  $tabel;
    }
    public function getOutEmployee(Request $request)
    {
        // $request;
        $arr = $request['request'];
        $client_secret2 = $request['client_secret'];
        $client_secret1 = 'dAkXImghyu2UeiX0EOA4NsgCUpM6cRvjauQdwdfL';
        if ($client_secret1 == $client_secret2) {
            return
                $employee = Employee::select('id', 'tabel', 'firstname_uz_latin', 'lastname_uz_latin', 'middlename_uz_latin', 'born_date', 'is_active')
                ->whereIn('tabel', $arr)
                ->with(['employeeStaffAll' => function ($q) {
                    $q->select(
                        'id',
                        'employee_id',
                        'staff_id'
                    )
                        ->with(['staff' => function ($qu) {
                            $qu->select(
                                'id',
                                'position_id',
                                'department_id',
                                'is_active'
                            )
                                ->with(['department' => function ($que) {
                                    $que->select('id', 'department_code', 'name_uz_latin');
                                }])
                                ->with(['position' => function ($que) {
                                    $que->select('id', 'name_uz_latin');
                                }]);
                        }])
                        ->where('is_active', 1)
                        ->where('is_main_staff', 1);
                }])
                ->get();
        }

        return 'no data';
    }
    public function employeeSkud()
    {
        // $user = Employee::where('id', 916)->first();
        // return $user->mainStaff[0];
        $staff = Auth::user()->employee->mainStaff[0]->id;
        $dep_id = Auth::user()->employee->mainStaff[0]->department->id;
        $is_manager = Department::where('id', $dep_id)->where('manager_staff_id', $staff)->first();
        if ($is_manager) {
            // return $is_manager;
            $dep_list =  $this->getDepList(array($dep_id));
            $staffs = Staff::whereIn('department_id', $dep_list)->where('is_active', 1)->pluck('id')->toArray();
            $employees_list = EmployeeStaff::whereIn('staff_id', $staffs)->where('is_main_staff', 1)->where('is_active', 1)->pluck('employee_id')->toArray();
            $employees = Employee::whereIn('id', $employees_list)->where('is_active', 1)->get();
            return $employees;
        } else {
            return 1;
        }
    }
    public function getDepList(array $id)
    {
        // $all_deps = [];
        $department = Department::whereIn('parent_id', $id)->pluck('id')->toArray();
        $mydep = Department::whereIn('id', $id)->pluck('id')->toArray();
        if (count($department) > 0) {
            $department = array_merge($department, $this->getDepList($department));
            $department = array_merge($department, $mydep);
        } else {
            $department = Department::where('id', $id)->pluck('id')->toArray();
        }
        return $department;
    }
    public function empBirthday()
    {
        // $all_deps = [];
        $otchet = DB::select("SELECT tabel, born_date, lastname_uz_latin, firstname_uz_latin, middlename_uz_latin
        FROM employees
        WHERE MONTH(born_date) = MONTH(CURRENT_DATE())
          AND DAY(born_date) = DAY(CURRENT_DATE());");
        return $otchet;
    }
    public function employeeVacationDaysInfo(Request $request){
        // return $request;
        $tabel = strtoupper($request->input('tabel'));
        $tabels = Employee::where('tabel', $tabel)->pluck('tabel')->toArray();
        // return $tabels;
        $response = Http::withoutVerifying()
                ->post('https://edo-db2.uzautomotors.com/api/employee/getVacationInfo', [
                    'tabel' => $tabels,
                ]);
        // $data = json_decode($response[0], true);
        $equalInterval1Arrays = [];
        $uniqueInterval1Values = [];
        $data = $response[0];
        
        foreach ($data['usedVocation'] as $vocation) {
            $interval1 = $vocation['interval1'];
        
            if (!in_array($interval1, $uniqueInterval1Values)) {
                $equalInterval1Arrays[$interval1] = [$vocation];
                $uniqueInterval1Values[] = $interval1;
            } else {
                $equalInterval1Arrays[$interval1][] = $vocation;
            }
        }
        
        // Convert the associative array values to a list for equalInterval1Arrays
        $equalInterval1Arrays = array_values($equalInterval1Arrays);
        $data['usedVocation'] = $equalInterval1Arrays;
        $staj = $data['extra']['experience'] / 5;
        $staj = substr($staj, 0,1) * 2;
        if($data['extra']['experience']>=20){
            $staj = 8;
        }
        foreach ($data['usedVocation'] as &$vocations) {
            foreach ($vocations as &$vocation) {
                if ($vocation['vocationtype'] == 'T' && substr($vocation['take1'], 0, 6)<202305) {
                    $vocation['max_day'] = 24; // You can set the desired value here
                }
                else if($vocation['vocationtype'] == 'T'){
                    $vocation['max_day'] = 28;
                }
                else if($vocation['vocationtype'] == 'S'){
                    $vocation['max_day'] = $staj;
                }
            }
        }
        
        // Unset references to avoid potential side effects
        unset($vocation);
        unset($vocations);
        return $data;
        // return $response->body();
    }
    public function isResponsibleEmployee($id){
        $moddiyjavobgar = MaterialResponsiblePeople::where('employee_id', $id)->first();
        if($moddiyjavobgar){
            return 1;
        }
        else{
            return 0;
        }
    }
}
