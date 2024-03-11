<?php

namespace App\Services;

use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerTemplate;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentType;
use App\Http\Models\Employee;
use App\Http\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard
{
    private $user;
    private $locale;
    public function __construct($locale)
    {
        $this->user = Auth::user();
        $this->locale = $locale;
    }

    public function dashboardData()
    {
        return json_encode([
            'my_reysters' => $this->getReyster(),
            'staff' => $this->getStaff(),
            'main_staff' => $this->getMainStaff(),
            'additional_staff' => $this->getAdditionalStaff(),
            'boxes' => $this->getBoxes(),
            // 'report_by_date' => $this->getReportByDate(),
            'document_types' => $this->getDocumentTypes(),
            'departments' => $this->getDepartments(),
        ]);
    }

    public function dashboardDataMobile()
    {
        return json_encode([
            'boxes' => $this->getBoxes(),
        ]);
    }

    public function getDepartments()
    {
        $user = Auth::user();
        $employee_id = $user->employee_id;
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        $department_ids = Staff::whereIn('id', $staff_ids)->pluck('department_id')->toArray();
        $departments = [];
        foreach ($department_ids as $key => $value) {
            $departments[] = $this->getChildDepartments($value);
        }
        return $departments;
    }

    public function getChildDepartments($id)
    {
        $departments = Department::where('parent_id', $id)->get();
        foreach ($departments as $key => $value) {
            $departments[] = $this->getChildDepartments($value->id);
        }
        return $departments;
    }

    public function getDocumentsByTemplateId($id)
    {
        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        $documents = Document::select('documents.*')
            ->join('document_signers', 'document_signers.document_id', '=', 'documents.id')
            ->where('documents.document_template_id', $id)
            ->whereIn('documents.status', [1, 2])
            ->whereIn('document_signers.staff_id', $staff_ids)
            ->whereNotNull('document_signers.taken_datetime')
            ->whereNotNull('document_signers.due_date')
            ->where(function ($q) use ($user) {
                $q->where('document_signers.status', 3)
                    ->where('document_signers.signer_employee_id', $user->employee->id)
                    ->orWhere('document_signers.status', 0)
                    // ->whereNull('document_signers.signer_employee_id')
                ;
            })
            ->get();
        foreach ($documents as $key_document => $document) {
            if (!$document->pdf_table) {
                Document::savePdf($document->id);
            }
        }
        $documents = Document::query()
            ->whereIn('id', $documents->pluck('id')->toArray())
            // ->with('documentType')
            // ->with(['department' => function ($q) {
            //     $q->with(['managerStaff' => function ($q1) {
            //         $q1->with('position')->with(['employeeMainStaff' => function ($q2) {
            //             $q2->with('employee');
            //         }]);
            //     }]);
            // }])
            ->with('documentTemplate.documentDetailTemplates.documentDetailAttributes')
            // ->with('documentRelation.employee')
            // ->with('documentRelation.documentTemplate')
            ->with(['employee' => function ($q) {
                $q->with(['mainStaff' => function ($q1) {
                    $q1->with('department');
                }]);
            }])
            ->with(['documentDetails' => function ($q) {
                $q->with(['documentDetailAttributeValues' =>  function ($q1) {
                    $q1->with(['documentDetailAttributes' => function ($q2) {
                        $q2->with('dataType')->with('tableList');
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
            ->get()->makeVisible(['base64', 'pdf']);

        foreach ($documents as $key_document => $document) {
            // foreach ($staff_ids as $key_staff => $staff) {
            //     foreach ($document->documentSigners as $key => $value) {
            //         if (
            //             $value->parent_employee_id == null &&
            //             $value->staff_id == $staff && ($value->status == 0 || $value->status == 3) && $value->taken_datetime != null && $document->status != 6
            //         ) {
            //             $document->reaction_staff_id = $staff;
            //         }
            //     }
            // }
            foreach ($document->documentDetails as $key_detail => $documentDetail) {
                foreach ($documentDetail->documentDetailAttributeValues as $key => $value) {
                    if ($value->documentDetailAttributes->table_list_id) {
                        $documents[$key_document]->documentDetails[$key_detail]->documentDetailAttributeValues[$key]->table_lists = DB::table($value->documentDetailAttributes->tableList->table_name)->where('id', $value->attribute_value)->get();
                    }
                }
            }
        }
        // foreach ($documents as $key => $value) {
        //     $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        // }

        return $documents;
    }

    public function getReyster()
    {
        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        $document_template_ids = DocumentSignerTemplate::whereIn('staff_id', $staff_ids)->where('is_registry', 1)->pluck('document_template_id')->toArray();
        // dd($document_template_ids);
        $document_template_ids1 = Document::select()
            ->whereHas('documentSigners', function ($q) use ($staff_ids) {
                $q->where('is_registry', 1)
                    ->where('status', 0)
                    ->whereNotNull('taken_datetime')
                    // ->whereNotNull('due_date')
                    ->whereIn('staff_id', $staff_ids);
            })
            ->pluck('document_template_id')->toArray();
        $document_template_ids = array_merge($document_template_ids, $document_template_ids1);
        $document_templates = DocumentTemplate::query()
            ->whereIn('document_templates.id', $document_template_ids)
            ->withCount(['documents' => function ($q) use ($staff_ids, $user) {
                $q->whereIn('status', [1, 2])
                    ->whereHas('documentSigners', function ($q1) use ($staff_ids, $user) {
                        $q1->whereIn('staff_id', $staff_ids)
                            ->whereNotNull('taken_datetime')
                            // ->whereIn('status',[0,3])
                            ->where(function ($q2) use ($user) {
                                $q2->where('status', 3)
                                    ->where('signer_employee_id', $user->employee->id)
                                    ->orWhere('status', 0)
                                    // ->whereNull('signer_employee_id')
                                ;
                            });
                    });
                // $q->join('document_signers', 'document_signers.document_id', '=', 'documents.id')
                // ->whereIn('documents.status', [1, 2])
                // ->whereIn('document_signers.staff_id', $staff_ids)
                // ->whereNotNull('document_signers.taken_datetime')
                // ->whereNotNull('document_signers.due_date')
                // ->where(function ($q) use ($user) {
                //     $q->where('document_signers.status', 3)
                //         ->where('document_signers.signer_employee_id', $user->employee->id)
                //         ->orWhere('document_signers.status', 0)
                //         ->whereNull('document_signers.signer_employee_id');
                // });
            }])
            ->with('department')
            ->get();
        return $document_templates;
    }

    public function educationRegistry()
    {
        $documents = Document::select('documents.*')
            ->limit(100)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [581])
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
            }]);

        if (Auth::user()->can('education-registry-xorazm')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->where('signer_group_id', 622)
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else if (Auth::user()->hasPermission('education-registry-asaka')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->whereIn('signer_group_id', [621])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0)
                    ->orWhere('signer_group_id', 0)
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else if (Auth::user()->can('education-registry-toshkent')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->whereIn('signer_group_id', [623])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else {
            return [];
        }
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        // return $documents;

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
            // if(substr($value->document_date,0,4) == '2020'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function categoryChangeRegistry()
    {
        $documents = Document::select('documents.*')
            ->limit(100)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [636])
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
                                $q3->with('position')->with('department')
                                    ->with('range');
                            }]);
                        }])
                        ->with('tariffScale');
                    }])
                    ->with(['documentDetailSignerAttributes' => function ($q1) {
                        $q1->with(['documentDetailAttributes'])->with('attributeSignerStaff');
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
            }]);

        // if (Auth::user()->can('education-registry-xorazm')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->where('signer_group_id', 622)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->hasPermission('education-registry-asaka')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [621])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0)
        //             ->orWhere('signer_group_id', 0)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->can('education-registry-toshkent')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [623])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else {
        //     return [];
        // }
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        // return $documents;

        foreach ($documents as $key_document => $document) {
            if (!$document->pdf_table) {
                Document::savePdf($document->id);
            }
            foreach ($document->documentDetails as $key_detail => $documentDetail) {
                foreach ($documentDetail->documentDetailSignerAttributes as $key => $value) {
                    if ($value->documentDetailAttributes->table_list_id && $value->d_d_attribute_id==2900) {
                        $documents[$key_document]->documentDetails[$key_detail]->documentDetailSignerAttributes[$key]->table_lists = DB::table($value->documentDetailAttributes->tableList->table_name)->where('id', $value->value)->first();
                    }
                }
            }
        }

        foreach ($documents as $key => $value) {
            // if(substr($value->document_date,0,4) == '2020'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function tabelRegistry()
    {
        $documents = Document::select('documents.*')
            ->limit(100)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [630])
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
            }]);

        // if (Auth::user()->can('tabel-registry-xorazm')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->where('signer_group_id', 622)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->hasPermission('tabel-registry-asaka')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [621])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0)
        //             ->orWhere('signer_group_id', 0)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->can('tabel-registry-toshkent')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [623])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else {
        //     return [];
        // }
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        // return $documents;

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
            // if(substr($value->document_date,0,4) == '2020'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function vacationRegistry()
    {
        $documents = Document::select('documents.*')
            ->limit(150)
            // ->whereIn('documents.document_template_id', [592])
            // ->whereIn('documents.status', [1])
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [12, 474, 592])
            // ->whereIn('documents.document_template_id', [12, 474, 592])
            ->whereDoesntHave('documentDetails', function ($q) {
                $q->whereHas('documentDetailAttributeValues', function ($q1) {
                    $q1->where('d_d_attribute_id', 158)->whereNotIn('attribute_value', [1, 2]);
                });
            })
            // ->orWhere('id',2329789)
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
            }]);
        if (Auth::user()->can('vacation-registry-xorazm')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->where('signer_group_id', 622)
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else if (Auth::user()->can('vacation-registry-asaka')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->whereIn('signer_group_id', [621, 642])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0)
                    ->orWhere('signer_group_id', 0)
                    ->whereIn('signer_group_id', [621, 642])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else if (Auth::user()->can('vacation-registry-toshkent')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->whereIn('signer_group_id', [623, 643])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else if (Auth::user()->can('vacation-registry-angren')) {
            $documents->whereHas('documentSigners', function ($q) {
                $q
                    ->whereIn('signer_group_id', [627])
                    ->where('action_type_id', 4)
                    ->where('sequence', 0);
            });
        } else {
            return [];
        }
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        // return $documents;

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
            // if(substr($value->document_date,0,4) == '2020'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function ishRejimiRegistry()
    {
        $documents = Document::limit(150)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [38])

            ->with('employee')
            ->with(['documentDetails' => function ($q) {
                $q->with('documentDetailContents')
                    ->with(['documentDetailEmployees' => function ($q1) {
                        $q1->with(['employee' => function ($q2) {
                            $q2->with(['staff' => function ($q3) {
                                $q3->with('position')->with('department');
                            }]);
                        }]);
                    }]);
            }])
            ->whereHas('documentDetails.documentDetailEmployees');
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        return $documents;
    }
    public function otgulRegistry()
    {
        $documents = Document::select('documents.*')
            ->limit(100)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [597])
            // ->whereDoesntHave('documentDetails', function ($q) {
            //     $q->whereHas('documentDetailAttributeValues', function ($q1) {
            //         $q1->where('d_d_attribute_id', 158)->whereNotIn('attribute_value', [1, 2]);
            //     });
            // })
            // ->orWhere('id',2329789)
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
            }]);

        // if (Auth::user()->can('education-registry-xorazm')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->where('signer_group_id', 622)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->hasPermission('education-registry-asaka')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [621])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0)
        //             ->orWhere('signer_group_id', 0)
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else if (Auth::user()->can('education-registry-toshkent')) {
        //     $documents->whereHas('documentSigners', function ($q) {
        //         $q
        //             ->whereIn('signer_group_id', [623])
        //             ->where('action_type_id', 4)
        //             ->where('sequence', 0);
        //     });
        // } else {
        //     return [];
        // }

        // Uch kundan ko'p Otgullar ro'yxatini olish
        // $documents->whereHas('documentDetails', function($q){
        //     $q->whereHas('documentDetailAttributeValues', function($q1){
        //         $q1->where('d_d_attribute_id', 2597)->where('attribute_value', '>', 3);
        //     });
        // });
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);

        // return $documents;

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
            // if(substr($value->document_date,0,4) == '2020'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }
    public function businessTripRegistry()
    {
        $signer_group_ids = [];
        // if(Auth::user()->hasPermission('business_trip'))
        {
            $signer_group_ids[] = 35;
            $signer_group_ids[] = 37;
        }
        // if(Auth::user()->hasPermission('business_trip_tashkent'))
        {
            $signer_group_ids[] = 36;
            $signer_group_ids[] = 38;
            $signer_group_ids[] = 39;
        }

        $documents = Document::select('documents.*')
            ->limit(500)
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', [9, 427])
            // ->whereHas('documentSigners', function($q) use($signer_group_ids){
            //     $q->whereIn('signer_group_id', $signer_group_ids);
            // })
            // ->whereHas('documentDetails', function($q){
            //     $q->whereHas('documentDetailAttributeValues', function($q1){
            //         $q1->where('d_d_attribute_id',158)->whereIn('attribute_value',[1,2]);
            //     });
            // })
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
            // if(substr($value->document_date,0,4) == '2020' || substr($value->document_date,0,7) == '2021-01' || $value->document_number == '21AXX-3200-0028' || $value->document_number == '21AXX-3102-0025'){
            //     $doc = Document::find($value->id);
            //     $value->status = 4;
            //     $value->save();
            // }
            $value->from_department = Employee::parentDepartments($value->employee->tabel)['main_department'];
        }

        return $documents;
    }

    public function getReportByDate()
    {
        $date = date('Y-m-d H:i:s');
        $employee = $this->getEmployee();

        $document_signers = DocumentSigner::selectRaw(
            "case WHEN document_signers.updated_at is null and document_signers.due_date < '" . $date . "' and document_signers.status = 0 THEN 1 else 0 end as expired,
                case WHEN document_signers.due_date < document_signers.updated_at and document_signers.status != 0 THEN 1 else 0 end as expired_done,
                case WHEN document_signers.updated_at is null and document_signers.due_date > '" . $date . "' and document_signers.status = 0 THEN 1 else 0 end as pending,
                case WHEN document_signers.due_date >= document_signers.updated_at and document_signers.status != 0 THEN 1 else 0 end as done"
        )
            // o'tgan			updated_at is null && due_date < $date && status = 0
            // o'tib bajarilgan	due_date < update_at && status != 0
            // kelgan			updated_at is null && due_date > $date && status = 0
            // bajarilgan		due_date >= update_at && status != 0
            ->join('staff', 'staff.id', '=', 'document_signers.staff_id')
            ->join('employee_staff', 'staff.id', '=', 'employee_staff.staff_id')
            ->join('employees', 'employees.id', '=', 'employee_staff.employee_id')
            ->whereNotNull('document_signers.taken_datetime')
            ->where('employees.id', $employee->id)
            ->get();
        $collection = collect($document_signers);

        $result = $collection->pipe(function ($collection) {
            return collect([
                'expired' => $collection->sum('expired'),
                'expired_done' => $collection->sum('expired_done'),
                'pending' => $collection->sum('pending'),
                'done' => $collection->sum('done'),
            ]);
        });
        return $result;
        // $document_signers = DocumentSigner::whereNotNull('taken_datetime')
        //     // ->join('staff', 'staff.id = document_signers.staff_id')
        //     // ->join('employee_staff', 'staff.id = employee_staff.staff_id')
        //     ->with(['staff.employeeStaff' => function ($q) {
        //         $q->where('is_active', 1);
        //     }])
        //     ->whereHas('staff.employeeStaff', function ($query) use ($employee) {
        //         $query->where('employee_id', $employee->id);
        //     })
        //     ->where('signer_employee_id', $employee->id)
        //     ->get();
        // $report = [];
        // foreach ($document_signers as $key => $value) {
        //     if ($value->signer_employee_id) {
        //         $arr = [
        //             'id' => 0,
        //             'employee_id' => $value->signer_employee_id,
        //             'employee' => '',
        //             'staff_id' => $value->staff_id,
        //             'staff' => '',
        //             'create_document' => null,
        //             'waiting' => null,
        //             'prosesing' => null,
        //             'expired' => null,
        //             'draft' => null,
        //         ];
        //         if (!in_array($arr, $report, true)) {
        //             $report[] = $arr;
        //         }
        //     } else {
        //         foreach ($value->staff->employeeStaff as $key_staff => $value_staff) {
        //             $arr = [
        //                 'id' => 0,
        //                 'employee_id' => $value_staff->employee_id,
        //                 'employee' => '',
        //                 'staff_id' => $value->staff_id,
        //                 'staff' => '',
        //                 'create_document' => null,
        //                 'waiting' => null,
        //                 'prosesing' => null,
        //                 'expired' => null,
        //                 'draft' => null,
        //             ];
        //             if (!in_array($arr, $report, true)) {
        //                 $report[] = $arr;
        //             }
        //         }
        //     }
        // }
        // foreach ($report as $key => $value) {
        //     $report[$key]['id'] = $key + 1;
        //     $report[$key]['employee'] = Employee::find($value['employee_id']);
        //     $report[$key]['staff'] = Staff::with('position')->with('department')->find($value['staff_id']);
        //     $create_document = DocumentSigner::where('action_type_id', 6)->where('signer_employee_id', $value['employee_id'])->whereNotNull('taken_datetime')->count();
        //     $draft_document = DocumentSigner::where('action_type_id', 6)->where('signer_employee_id', $value['employee_id'])->whereNull('taken_datetime')->count();
        //     $expired = DocumentSigner::where('status', '!=', 1)
        //         ->where('status', '!=', 2)
        //         ->where('due_date', '<', date("Y-m-d H:i:s"))
        //         ->where(function ($query) use ($value) {
        //             return $query->where('signer_employee_id', $value['employee_id'])
        //                 ->orWhere('staff_id', $value['staff_id']);
        //         })->count();
        //     $waiting = DocumentSigner::where('status', 0)
        //         ->where('due_date', '>', date("Y-m-d H:i:s"))
        //         ->where(function ($query) use ($value) {
        //             return $query->where('signer_employee_id', $value['employee_id'])
        //                 ->orWhere('staff_id', $value['staff_id']);
        //         })->count();
        //     $prosesing = DocumentSigner::where('status', 3)
        //         ->where('due_date', '>', date("Y-m-d H:i:s"))
        //         ->where(function ($query) use ($value) {
        //             return $query->where('signer_employee_id', $value['employee_id'])
        //                 ->orWhere('staff_id', $value['staff_id']);
        //         })->count();
        //     $report[$key]['create_document'] = $create_document;
        //     $report[$key]['waiting'] = $waiting;
        //     $report[$key]['prosesing'] = $prosesing;
        //     $report[$key]['expired'] = $expired;
        //     $report[$key]['draft'] = $draft_document;
        // }
        // return $report;
    }

    public function getDocumentTypes()
    {
        $document_types = DocumentType::get();
        foreach ($document_types as $key => $value) {
            $documentTemplates = DocumentTemplate::where('document_type_id', $value->id);
            $document_types[$key]->count = $documentTemplates->count();
            $permissions = [];
            foreach ($documentTemplates->get() as $value_temp) {
                $name = str_replace(' ', '_', $value_temp->name_uz_latin);
                $name = str_replace("'", "", $name);
                $name = str_replace(",", "", $name);
                $name = str_replace("?", "", $name);
                $name = str_replace("(", "", $name);
                $name = str_replace(")", "", $name);
                $name = str_replace("`", "", $name);
                $name = str_replace("!", "", $name);
                $name = str_replace('"', '', $name);
                $name = str_replace("\\", "", $name);
                $name = str_replace("/", "", $name);
                $name = strtolower($name);
                $permissions[] = $name . '-create';
            }
            $document_types[$key]->permissions = $permissions;
        }
        return $document_types;
    }

    public function getBoxes()
    {
        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        $new_count_inbox = Document::join('document_signers', 'document_signers.document_id', '=', 'documents.id')
            ->whereIn('document_signers.staff_id', $staff_ids)
            ->whereIn('document_signers.status', [0, 3])
            ->whereNotNull('document_signers.taken_datetime')
            ->whereIn('documents.status', [1, 2, 3, 4])
            ->whereNotIn('document_signers.action_type_id', [3, 5, 6])
            ->where(function ($q) use ($user) {
                return $q->whereNotNull('document_signers.parent_employee_id')
                    ->where('document_signers.signer_employee_id', $user->employee_id)
                    ->orWhereNull('document_signers.parent_employee_id');
            })
            ->count();
        $new_count_outbox = Document::join('document_signers', 'document_signers.document_id', '=', 'documents.id')
            ->whereIn('document_signers.staff_id', $staff_ids)
            ->whereIn('document_signers.status', [0, 3])
            ->whereNotNull('document_signers.taken_datetime')
            ->whereIn('documents.status', [1, 2, 3, 4])
            ->where('document_signers.sequence', 100)
            ->where(function ($q) use ($user) {
                return $q->whereNotNull('document_signers.parent_employee_id')
                    ->where('document_signers.signer_employee_id', $user->employee_id)
                    ->orWhereNull('document_signers.parent_employee_id');
            })
            ->count();

        $staffs = Staff::select('staff.id', 'employees.id as employee_id')
            ->join('employee_staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->join('employees', 'employee_staff.employee_id', '=', 'employees.id')
            ->join('users', 'users.employee_id', '=', 'employees.id')
            ->where('users.id', Auth::id())
            ->where('employee_staff.is_active', 1)
            ->get();


        $document_list = array(
            0 => array(
                'count' => 0,
                'new_count' => $new_count_inbox,
                'name' => 'inbox',
            ),
            1 => array(
                'count' => 0,
                'new_count' => $new_count_outbox,
                'name' => 'outbox',
            ),
            2 => array(
                'count' => 0,
                'new_count' => 0,
                'name' => 'draft',
            ),
            3 => array(
                'count' => 0,
                'new_count' => 0,
                'name' => 'cancel',
            ),
        );
        foreach ($document_list as $key_list => $filter) {
            $documents = Document::with(['documentSigners' => function ($q) use ($staffs) {
                $q->where(function ($q) use ($staffs) {
                    return $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', $staffs[0]['employee_id'])
                        ->orWhereNull('signer_employee_id');
                });
            }]);
            if ($filter['name'] == 'inbox') {
                $documents->whereHas('documentSigners', function ($q) use ($staffs, $user) {
                    $q->where(function ($qu) use ($staffs) {
                        foreach ($staffs as $key => $value) {
                            $qu->orWhere('staff_id', $value->id);
                        }
                    })
                        ->whereNotIn('action_type_id', [3, 5, 6])
                        ->where(function ($q) use ($user) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $user->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime');
                })
                    ->whereNotIn('status', [0, 6]);
            } elseif ($filter['name'] == 'outbox') {
                $documents->whereHas('documentSigners', function ($q) use ($staffs, $user) {
                    $q->where(function ($qu) use ($staffs) {
                        foreach ($staffs as $key => $value) {
                            $qu->orWhere('staff_id', $value->id);
                        }
                    })
                        ->where(function ($q) {
                            $q->whereIn('action_type_id', [3, 6]);
                        })
                        ->where(function ($q) use ($user) {
                            return $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $user->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->whereNotNull('taken_datetime');
                })
                    ->whereNotIn('status', [0, 6]);
            } elseif ($filter['name'] == 'draft') {
                $documents->where('created_employee_id', $user->employee_id)->where('status', 0);
            } elseif ($filter['name'] == 'cancel') {
                $documents->whereHas('documentSigners', function ($q) use ($user) {
                    $q->where('signer_employee_id', $user->employee_id);
                })
                    ->whereHas('documentSigners', function ($query) {
                        $query->where('status', 2)
                            ->whereNull('parent_employee_id');
                    })
                    ->where('status', '=', 6);
            }
            $document_list[$key_list]['count'] = $documents->count() ? $documents->count() : 0;
        }
        $document_list[2]['new_count'] = $document_list[2]['count'];
        $document_list[3]['new_count'] = $document_list[3]['count'];
        return $document_list;
    }

    public function userStaffs()
    {
        return Staff::select('staff.*', 'employees.id as employee_id')
            ->join('employee_staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->join('employees', 'employee_staff.employee_id', '=', 'employees.id')
            ->join('users', 'users.employee_id', '=', 'employees.id')
            ->where('users.id', $this->user->id)
            ->where('employee_staff.is_main_staff', 1)
            ->where('employee_staff.deleted_at', null)
            ->get();
    }

    public function getEmployee()
    {
        return $this->user->employee;
    }

    public function getMainStaff()
    {
        return $this->user->employee->mainStaff;
    }

    public function getStaff()
    {
        return $this->user->employee->staff;
    }

    public function getAdditionalStaff()
    {
        return $this->user->employee->additionalStaff;
    }
}
