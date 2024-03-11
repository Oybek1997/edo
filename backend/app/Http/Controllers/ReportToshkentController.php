<?php

namespace App\Http\Controllers;

use Adldap\Query\Builder;
use Illuminate\Http\Request;
use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Staff;
use App\Http\Models\Employee;
use App\Http\Models\ReportTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\SelectedTemplates;
use App\Http\Models\Mailing;
use App\User;
use Carbon\Traits\Timestamp;
use DocumentTypes;
use Illuminate\Support\Facades\Http;

class ReportToshkentController extends Controller
{
    public function test(){
        return 
        $mainDep = Department::select('id')
        ->where('parent_id', 1)
        ->whereRaw('SUBSTRING(department_code, 1,  1) = 9')
        ->orderBy('department_type_id')->get()->pluck('id')->toArray();
    }

    // OKD uchun xisobot Yangisi
    public function okdReportFull(Request $request)
    {
        if(Auth::id() != 2571){
            return 'OKD report vaqtinchalik to\'htatildi.';
        }
        $from_date =  $request['search']['from_date'];

        $to_date =  $request['search']['to_date'];

        $incoming_journal =  $request['search']['incoming_journal_id']; substr("Hello world",0,1);

        $mainDep = Department::select('id')
        // ->whereRaw('SUBSTRING(department_code, 1,  1) != 9')
        // ->whereRaw('SUBSTRING(department_code, -2) != "GM"')
        ->where('parent_id', 1)
        ->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')
        ->whereRaw('SUBSTRING(department_code, 1,  1) != 9')
        ->whereRaw('SUBSTRING(department_code, -1) != "D"') 
        ->whereIn('parent_id', $mainDep)
        ->get()->pluck('id')->toArray();

        $state_ids = [];
        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->where('department_id', $value)->get()->pluck('id')->toArray();
        }
        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }

        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');


        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');

        $model = [];

        

        foreach ($state_ids as $key => $item) :
            $it = $state_ids[$key];
            // $it = $state_ids[3];
            $document_all = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->whereIn('status', [3, 5])
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();


            $document_comp = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 5)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();


            $document_comp_in_due_date = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '>=', 'sign_at')
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 5)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_without_due_date = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '<', 'sign_at')
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 5)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_signer = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_signer_comp = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $it)
                        ->where('status', 1);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $ten_date = date('Y-m-d', strtotime('-10 days'));
            $one_month = date('Y-m-d', strtotime('-1 month'));
            $two_month = date('Y-m-d', strtotime('-2 month'));

            $document_out_ten = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it, $ten_date) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$ten_date, date('Y-m-d')])
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_out_one_month = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it, $ten_date,  $one_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$one_month, $ten_date])
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_out_two_month = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it, $two_month,  $one_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$two_month, $one_month])
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();

            $document_out = Document::select('id')
                ->whereHas('DocumentSigners', function ($query) use ($parent, $it, $two_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->where('due_date', '<', $two_month)
                        ->whereIn('staff_id', $it);
                })
                ->whereHas('DocumentSigners', function ($query) {
                    $query->where('action_type_id', 11);
                })
                ->whereHas('documentTemplate', function ($query) use ($template_id) {
                    $query->whereIn('id',  $template_id);
                })
                ->where('status', 3)
                ->whereBetween('document_date', [$from_date, $to_date])
                ->pluck('id')->toarray();



            $count = [
                count($document_all),
                count($document_comp),
                count($document_comp_in_due_date),
                count($document_without_due_date),
                count($document_signer),
                count($document_signer_comp),
                count($document_out_ten),
                count($document_out_one_month),
                count($document_out_two_month),
                count($document_out)
            ];
            if (!empty($document_all)) :
                $dep = Department::where('id', $key)->with(['managerStaff' => function ($q){
                    $q->with('employees');
                }])->get();
                $model[] = [$dep, $count];
            endif;

        // $dep = Department::where('id', $key)->get();
        // $model[] = [$dep, $count];


        endforeach;

        return $model;
    }

    public function OkdReportItemFull(Request $request)
    {
        $filter = $request->input('filter');

        $from_date =  $request['route_array'][2];

        $to_date =  $request['route_array'][3];

        $incoming_journal =  $request['route_array'][4];

        $incoming_journal = ($incoming_journal) ? explode(",", $incoming_journal) : '';

        $dep_id = intval($request['route_array'][0]);
        // $dep_id = 181;

        $row = intval($request['route_array'][1]);
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');

        return $doc = $this->templateFull($row, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $parent, $template_id, $incoming_journal);
    }

    public function templateFull($request, $dep_id, $lang, $from_date, $to_date, $filter,  $locale, $parent, $template_id, $incoming_journal)
    {
        $ten_date = date('Y-m-d', strtotime('-10 days'));
        $one_month = date('Y-m-d', strtotime('-1 month'));
        $two_month = date('Y-m-d', strtotime('-2 month'));

        // $mainDep = Department::select('id')->where('parent_id', 1)->get()->pluck('id');
        $mainDep = Department::select('id')->where('parent_id', 1)->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')->whereIn('parent_id', $mainDep)->get()->pluck('id');

        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->whereIn('department_id', [$value])->get()->pluck('id')->toArray();
        }

        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }
        $template[] = Department::select('id', 'name_uz_cyril', 'name_uz_latin', 'name_ru')->where('id', $dep_id)->get();
        $dep_id = $state_ids[$dep_id];
        // $dep_id = $state_ids[3];

        $documents = Document::whereHas('DocumentSigners', function ($query) {
            $query->where('action_type_id', 11);
        })
            ->whereHas('documentTemplate', function ($query) use ($template_id) {
                $query->whereIn('id',  $template_id);
            })
            ->whereBetween('document_date', [$from_date, $to_date])
            ->with(['DocumentSigners' => function ($q) use ($parent,$dep_id, $lang) {
                $q->with(['signerEmployee' => function ($q2) use ($lang) {
                    $q2->select(
                        'id',
                        'lastname_' . $lang,
                        'middlename_' . $lang,
                        'firstname_' . $lang,
                        'tabel'
                    );
                }])
                ->whereIn('parent_employee_id', $parent)
                ->whereIn('staff_id', $dep_id);
            }])
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])
            ->with(['documentDetails' => function ($query) {
                $query->select(
                    'id',
                    'document_id',
                    'content'
                );
            }])
            ->with(['employee' => function ($query) use ($lang) {
                $query->select(
                    'id',
                    'lastname_' . $lang,
                    'middlename_' . $lang,
                    'firstname_' . $lang,
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                );
                            }])
                            ->where('is_active', 1);
                    }]);
            }]);


        switch ($request) {
            case 0:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $dep_id);
                })->whereIn('status', [3, 5]);
                break;
            case 1:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 5);
                break;
            case 2:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '>=', 'sign_at')
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 5);

                break;
            case 3:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '<', 'sign_at')
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 5);
                break;
            case 4:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 3);
                break;
            case 5:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id, $ten_date) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereIn('staff_id', $dep_id)
                        ->where('status', 1);
                })->where('status', 3);
                break;
            case 6:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id, $ten_date) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$ten_date, date('Y-m-d')])
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 3);
                break;
            case 7:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id, $ten_date,  $one_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$one_month, $ten_date])
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 3);
                break;
            case 8:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id, $two_month,  $one_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->whereBetween('due_date', [$two_month, $one_month])
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 3);
                break;
            case 9:
                $documents->whereHas('DocumentSigners', function ($query) use ($parent, $dep_id, $two_month) {
                    $query->whereIn('parent_employee_id', $parent)
                        ->where('due_date', '<', $two_month)
                        ->whereIn('staff_id', $dep_id);
                })->where('status', 3);
                break;
        }

        if (isset($filter['document_type_id']) && ($filter['document_type_id'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('document_type_id', $filter['document_type_id']);
            });
        }

        if (isset($filter['document_number']) && ($filter['document_number'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('document_number', 'like', '%' . $filter['document_number'] . '%');
            });
        }
        if (isset($filter['status_signer']) && ($filter['status_signer'])) {
            $documents->where('status', 'like', '%' . $filter['status_signer'] . '%');
        }

        if (isset($filter['id']) && ($filter['id'])) {
            $documents->whereHas('Documents', function ($q) use ($filter) {
                $q->where('id', $filter['id']);
            });
        }

        $template[] = $documents->get();
        $template[] = $request;
        return $template;
    }


    public function getStaffIds($deps)
    {
        return Staff::select('id')->whereIn('department_id', $deps)->get()->pluck('id')->toArray();
    }

    public function getChilds($ids)
    {
        $deps = Department::select('id')->whereIn('parent_id', $ids)->get()->pluck('id')->toArray();

        if (count($deps) > 0) {
            $childs = $this->getChilds($deps);
            $deps = array_merge($deps, $childs);
        }

        return $deps;
    }



    // OKD detalizatsiya

    public function okdReportTabFull(Request $request)
    {
        $from_date =  $request['route_array'][1];

        $to_date =  $request['route_array'][2];

        // $mainDep = Department::select('id')->where('parent_id', 1)->get()->pluck('id');
        $mainDep = Department::select('id')->where('parent_id', 1)->orderBy('department_type_id')->get()->pluck('id')->toArray();

        $deps = Department::select('id')->whereIn('parent_id', $mainDep)->get()->pluck('id');

        foreach ($mainDep as $key => $value) {
            $state_ids[$value] = Staff::select('id')->whereIn('department_id', [$value])->get()->pluck('id')->toArray();
        }

        foreach ($deps as $key => $value) {
            $state_ids[$value] = $this->getStaffIds(array_merge([$value], $this->getChilds([$value])));
        }

        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');
        $parent = Mailing::select('employee_id')->get()->pluck('employee_id');
        $template_id =  SelectedTemplates::select('document_template_id')->get()->pluck('document_template_id');

        $row = $request['route_array'][0];
        $model = [];


        foreach ($state_ids as $key => $item) :
            $it = $state_ids[$key];

            $dep = Department::where('id', $key)->get();

            switch ($row) {
                case 2:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->whereIn('parent_employee_id', $parent)
                        ->where('status', 1)
                        ->whereColumn('due_date', '>=', 'sign_at')
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
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
                        }]);

                    $count = $document->get();
                    break;
                case 3:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->where('status', 1)
                        ->whereIn('parent_employee_id', $parent)
                        ->whereColumn('due_date', '<', 'sign_at')
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
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
                        }]);

                    // $count = (!empty($document_without_due_date))? $document_without_due_date->get(): null;
                    $count =  $document->get();
                    break;
                case 8:
                    $document = DocumentSigner::whereIn('staff_id', $it)
                        ->where('action_type_id', 4)
                        ->whereIn('status', [0, 4, 3])
                        ->whereIn('parent_employee_id', $parent)
                        ->where('due_date', '<', date('Y-m-d'))
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->whereHas('Documents', function ($q) use ($template_id) {
                            $q->whereHas('documentTemplate', function ($query) use ($template_id) {
                                $query->whereIn('id',  $template_id);
                            })
                                ->whereHas('DocumentSigners', function ($query) {
                                    $query->where('staff_id', 3360)->where('action_type_id', 11);
                                });
                        })
                        ->whereDoesntHave('Documents', function ($q) {
                            $q->whereIn('status', [0, 6]);
                        })
                        ->with(['Documents' => function ($q) use ($locale, $lang) {
                            $q->with(['documentType' => function ($q) use ($locale) {
                                $q->select(
                                    'id',
                                    'name_' . $locale
                                );
                            }])
                                ->with(['documentDetails' => function ($query) {
                                    $query->select(
                                        'id',
                                        'document_id',
                                        'content'
                                    )->with(['documentDetailContents' => function ($query) {
                                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                                    }]);
                                }])
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_' . $lang,
                                        'middlename_' . $lang,
                                        'firstname_' . $lang,
                                        'tabel'
                                    )
                                        ->with(['employeeStaff' => function ($query) use ($lang) {
                                            $query->select(
                                                'employee_id',
                                                'staff_id'
                                            )
                                                ->with(['staff' => function ($query) use ($lang) {
                                                    $query->select(
                                                        'id',
                                                        'position_id',
                                                        'department_id'
                                                    );
                                                }])
                                                ->where('is_active', 1);
                                        }]);
                                }])
                                ->with(['documentSigners' => function ($q) use ($lang,  $locale) {
                                    $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                                        ->whereNotNull('taken_datetime')
                                        ->where('parent_employee_id', 978)
                                        ->where('action_type_id', 4)
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
                        }]);

                    $count =  $document->get();
                    break;
            }

            // return (!empty($count))?1:0;
            // return count($count);

            // $model[] = [$dep, $count];

            if (count($count) != 0) :
                $model[] = [$dep, $count];
            endif;

        endforeach;

        return ($model);
        // return ('sasas');
    }

    // Boshqalar uchun xisobot


    public function documentReportEmployee(Request $request)
    {
        $from_date = !empty($request['search']['from_date']) ?
            $request['search']['from_date'] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['search']['to_date']) ?
            $request['search']['to_date'] :
            date('Y-m-d');


        $user_employee_id = ($request['status_report'] == 0) ?
            Auth::user()->employee_id :  Auth::user()->employee->dr_employee_id;
        // $user_employee_id = 916;
        // return $user_employee_id;
        $model = [];

        $r_employe = DocumentSigner::select('signer_employee_id')
            ->where('parent_employee_id', $user_employee_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->groupBy('signer_employee_id')
            ->get()->pluck('signer_employee_id');
        foreach ($r_employe as $key => $it) {

            $document_all = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', '!=', 2)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })
                ->pluck('id')->toarray();


            $document_comp = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_comp_in_due_date = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereColumn('due_date', '>=', 'sign_at')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();



            $document_without_due_date = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->where('status', 1)
                ->whereColumn('due_date', '<', 'sign_at')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();


            $document_signer = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();


            $one_date = date('Y-m-d', strtotime('+1 days'));
            $two_date = date('Y-m-d', strtotime('+2 days'));
            $three_date = date('Y-m-d', strtotime('+3 days'));

            $document_do_one = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_do_three = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereBetween('due_date', [$one_date, $three_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out_three = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->whereBetween('created_at', [$from_date, $to_date])
                ->where('due_date', '>=', $three_date)
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $document_out = DocumentSigner::select('id')
                ->where('signer_employee_id', $it)
                ->where('action_type_id', 4)
                ->where('parent_employee_id', $user_employee_id)
                ->whereIn('status', [0, 4, 3])
                ->where('due_date', '<', date('Y-m-d'))
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereDoesntHave('Documents', function ($q) {
                    $q->whereIn('status', [0, 6]);
                })->pluck('id')->toarray();

            $eployee = Employee::where('id', $it)->get();


            $count = [
                count($document_all),
                count($document_comp),
                count($document_comp_in_due_date),
                count($document_without_due_date),
                count($document_signer),
                count($document_do_one),
                count($document_do_three),
                count($document_out_three),
                count($document_out)
            ];


            if (!empty($document_all)) :
                $model[] = [$eployee, $count];
            endif;
        }
        return $model;
    }

    public function documentReportEmployeeItem(Request $request)
    {

        $filter = $request->input('filter');
        $from_date = !empty($request['route_array'][2]) ?
            $request['route_array'][2] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['route_array'][3]) ?
            $request['route_array'][3] :
            date('Y-m-d');

        $dep_id = intval($request['route_array'][0]);

        $row = intval($request['route_array'][1]);
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');

        $dr_employee_id = $request['route_array'][4];

        return $doc = $this->Employeetemplate($row, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $dr_employee_id);
    }


    public function Employeetemplate($request, $dep_id, $lang, $from_date, $to_date, $filter, $locale, $dr_employee_id)
    {

        $user_employee_id = ($dr_employee_id == 1) ?
            Auth::user()->employee->dr_employee_id : Auth::user()->employee_id;

        // $user_employee_id = 916;
        // return $user_employee_id;

        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));

        $template[] = Employee::select('id', 'firstname_' . $lang, 'lastname_' . $lang, 'middlename_' . $lang)
            ->where('id', $dep_id)->get();

        switch ($request) {
            case 0:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', '!=', 2)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 1:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 2:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '>=', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 3:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '<', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 4:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 5:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 6:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [$one_date, $three_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 7:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->where('due_date', '>=', $three_date)
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 8:
                $documents = DocumentSigner::where('signer_employee_id', $dep_id)
                    ->where('parent_employee_id', $user_employee_id)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->where('due_date', '<', date('Y-m-d'))
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
        }
        if (isset($filter['document_type_id']) && $filter['document_type_id']) {
            $documents->where('document_type_id', $filter['document_type_id']);
        }

        if (isset($filter['document_number']) && $filter['document_number']) {
            $documents->where('document_number', 'like', '%' . $filter['document_number'] . '%');
        }

        if (isset($filter['id'])) :
            $documents->where('id', $filter['id']);
        endif;
        $template[] = $documents->get();
        $template[] = $request;
        return $template;
    }
    public function documentReportMy(Request $request)
    {
        $from_date = !empty($request['search']['from_date']) ?
            $request['search']['from_date'] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['search']['to_date']) ?
            $request['search']['to_date'] :
            date('Y-m-d');
        // $myDoc = [0 => 'Chiquvchi barcha xujjatlar', 1 => 'Kiruvchi barcha xujjatlar'];
        $myDoc = [0];
        $user_employee_id = ($request['status_report'] == false) ?
            Auth::user()->employee_id :  Auth::user()->employee->dr_employee_id;

        $model = [];
        // foreach ($myDoc as $key => $t) {

        $document_all =  DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', '!=', 2)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })
            ->pluck('id')->toarray();

        $document_comp = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_comp_in_due_date = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereColumn('due_date', '>=', 'sign_at')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();



        $document_without_due_date = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->where('status', 1)
            ->whereColumn('due_date', '<', 'sign_at')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();


        $document_signer = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();


        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));

        $document_do_one = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereBetween('due_date', [date('Y-m-d'), $one_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_do_three = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereBetween('due_date', [$one_date, $three_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_out_three = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->whereBetween('created_at', [$from_date, $to_date])
            ->where('due_date', '>=', $three_date)
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $document_out = DocumentSigner::select('id')
            ->where('signer_employee_id', $user_employee_id)
            ->where('action_type_id', 4)
            ->whereNotNull('parent_employee_id')
            ->whereIn('status', [0, 4, 3])
            ->where('due_date', '<', date('Y-m-d'))
            ->whereBetween('created_at', [$from_date, $to_date])
            ->whereDoesntHave('Documents', function ($q) {
                $q->whereIn('status', [0, 6]);
            })->pluck('id')->toarray();

        $count = [
            count($document_all),
            count($document_comp),
            count($document_comp_in_due_date),
            count($document_without_due_date),
            count($document_signer),
            count($document_do_one),
            count($document_do_three),
            count($document_out_three),
            count($document_out)
        ];
        $model[] = [$myDoc[0], $count];
        // $model = [$myDoc, [0, 1, 2, 3, 4, 5, 6, 7, 8]];
        // $moyDoc = 
        // }
        return $model;
    }

    public function documentReportMyItem(Request $request)
    {
        $filter = $request->input('filter');
        $from_date = !empty($request['route_array'][2]) ?
            $request['route_array'][2] :
            date('Y-m-d', strtotime('-1 month'));

        $to_date = !empty($request['route_array'][3]) ?
            $request['route_array'][3] :
            date('Y-m-d');
        $locale = $request->input('language');
        $lang = $request['language'] == 'ru' ? 'uz_cyril' : $request->input('language');

        $row = intval($request['route_array'][1]);

        $dr_employee_id = $request['route_array'][4];
        $user_employee_id = ($dr_employee_id == 1) ? Auth::user()->employee->dr_employee_id : Auth::user()->employee_id;
        $one_date = date('Y-m-d', strtotime('+1 days'));
        $two_date = date('Y-m-d', strtotime('+2 days'));
        $three_date = date('Y-m-d', strtotime('+3 days'));
        switch ($row) {
            case 0:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', '!=', 2)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 1:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 2:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '>=', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);
                break;
            case 3:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->where('status', 1)
                    ->whereColumn('due_date', '<', 'sign_at')
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 4:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 5:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 6:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereBetween('due_date', [$one_date, $three_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 7:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->where('due_date', '>=', $three_date)
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
            case 8:
                $documents = DocumentSigner::where('signer_employee_id', $user_employee_id)
                    ->whereNotNull('parent_employee_id')
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 4, 3])
                    ->where('due_date', '<', date('Y-m-d'))
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereDoesntHave('Documents', function ($q) {
                        $q->whereIn('status', [0, 6]);
                    })
                    ->with(['Documents' => function ($q) use ($locale, $lang) {
                        $q->with(['documentType' => function ($q) use ($locale) {
                            $q->select(
                                'id',
                                'name_' . $locale
                            );
                        }])
                            ->with(['documentDetails' => function ($query) {
                                $query->select(
                                    'id',
                                    'document_id',
                                    'content'
                                );
                            }])
                            ->with(['employee' => function ($query) use ($lang) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                )
                                    ->with(['employeeStaff' => function ($query) use ($lang) {
                                        $query->select(
                                            'employee_id',
                                            'staff_id'
                                        )
                                            ->with(['staff' => function ($query) use ($lang) {
                                                $query->select(
                                                    'id',
                                                    'position_id',
                                                    'department_id'
                                                );
                                            }])
                                            ->where('is_active', 1);
                                    }]);
                            }]);
                    }]);

                break;
        }

        $template[] = $documents->get();
        $template[] = $row;
        return $template;
    }
}
