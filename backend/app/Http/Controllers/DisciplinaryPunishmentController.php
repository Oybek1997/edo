<?php

namespace App\Http\Controllers;

use App\Http\Models\MaterialResponsiblePeople;
use App\Http\Models\EDI\BusinessPartnerType;
use App\Http\Models\Employee;
use App\Http\Models\DocumentDetailEmployee;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\Document;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentRelation;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;


class DisciplinaryPunishmentController extends Controller
{
    public function index(Request $request)
    {
        //return 2222222;
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');

        $materialResponsibles = MaterialResponsiblePeople::with([
                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'employee'
            ]
        );

        if (isset($search)) {
            $materialResponsibles->where(function ($query) use ($search) {
                return $query
                    ->where('firstname_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('tabel', 'ilike', "%" . $search . "%");
            });
        }
        return $materialResponsibles->orderBY('employee_id', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function update(Request $request, MaterialResponsiblePeople $materialResponsible)
    {
        //return $request->input('first_name');
        //return $request->input('id');
        $model = MaterialResponsiblePeople::find($request->input('id'));
        if (!$model) {
            $model = new MaterialResponsiblePeople();
            $model->created_by = Auth::id();
            $model->created_at = date('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $employeeObject=Employee::where('tabel','=',$request->input('tabno'))->first();
        $firstName=$employeeObject->firstname_uz_latin;
        //return $firstName;

        $model->employee_id = $employeeObject->id;

        $model->save();
        return $model;
    }


    public function getRef(Request $request)
    {
        return [
            'businessPartnerTypes' => BusinessPartnerType::select('id as value', 'name as text')->get()
        ];
    }

    public function destroy($id)
    {
        //return $id;
        $model = MaterialResponsiblePeople::find($id);
        // return $model;

        $model->delete();
    }

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }

    public function DisciplinePunishmentLspCreate(Request $request)
    {

        //dd('1111111');
        DB::beginTransaction();
        try {
            $documents = Document::whereIn('id', $request->input('ids'))->orderByDesc('created_at')->get();
            // dd($documents);
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(643);
            //dd($documents);
            $model = new Document();
            $model->document_template_id = 643;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'uz_latin';
            $model->document_date = date('Y-m-d H:i:s');
            //I skipped this Part temporarily
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            // Tablitsa Headerini yasash

            $content = $document_template->documentDetailTemplates[0]['content_uz_latin'];

            $firstTable= '';

            $firstTable .= '<table border="1" style="border-collapse: separate;">';
            $firstTable .= '<thead>';
            $firstTable .= '<tr>';
            $firstTable .= '<th>';
            $firstTable .= '#';
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Xodim";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Tabel";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Bo'lim";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Bo'lim Kodi";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Lavozimi";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Qoidabuzarlik Turi";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Aniqlangan sana";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Sodir Etilgan sana";
            $firstTable .= '</th>';
            $firstTable .= '<th>';
            $firstTable .= "Asos";
            $firstTable .= '</th>';
            $firstTable .= '</thead>';
            $firstTable .= '<tbody>';

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
                $employee_name='';
                $tabel='';
                $situation_description='';
                $occuring_time='';
                $date_of_detection='';
                $reason='';
                $position= '';
                $department= '';
                $departmentCode= '';
                // return [$doc, $doc->documentDetails ];
                foreach ($doc->documentDetails as $doc_key => $doc_attr) {
                    //dd($doc_attr->documentDetailEmployees->employee_fio);
                    foreach($doc_attr->documentDetailEmployees as $singleEmployee){
                        //dd($singleEmployee->employee_fio);
                        $employee_name .= $singleEmployee->employee_fio ;
                        $employee_name .= '<br>';
                        $employee_name .= '<br>';
                        $employee_name .= ' ';
                        $tabel .= $singleEmployee->employee->tabel;
                        $tabel .= '<br>';
                        $tabel .= '<br>';
                        $tabel .= '<br>';
                        $tabel .= '<br>';
                        $tabel .= ' ';
                        //dd($singleEmployee->employee->staff->position);
                        foreach($singleEmployee->employee->staff as $innerEmployee){
                            //dd($innerEmployee->position->name_uz_latin);
                            $position .= $innerEmployee->position->name_uz_latin;
                            $position .= '<br>';
                            $position .= '<br>';
                            $position .= ' ';
                            $department .= $innerEmployee->department->name_uz_latin;
                            $department .= '<br>';
                            $department .= '<br>';
                            $department .= ' ';
                            //dd($innerEmployee->department);
                            $departmentCode .= $innerEmployee->department->department_code;
                            $departmentCode .= '<br>';
                            $departmentCode .= '<br>';
                            $departmentCode .= ' ';
                        }
                    }
                    if ($doc_attr->documentDetailAttributeValues[0]->d_d_attribute_id == 593) {
                        $situation_description =$doc_attr->documentDetailAttributeValues[0]->attribute_value;
                    }
                    $occuring_time = $doc_attr->documentDetailAttributeValues[1]->attribute_value;
                    $date_of_detection = $doc_attr->documentDetailAttributeValues[2]->attribute_value;
                    $reason = $doc_attr->documentDetailAttributeValues[3]->attribute_value;
                }

                $firstTable .= '<tr>';
                $firstTable .= '<td style="width:3%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $key + 1;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $employee_name;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $tabel;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $department;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $departmentCode;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $position;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $situation_description;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $occuring_time;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $date_of_detection;
                $firstTable .= '</td>';
                $firstTable .= '<td style="width:20%; font-size: 14px; padding-top:8px;">';
                $firstTable .= $reason;
                $firstTable .= '</td>';
                //$firstTable .= $doc->document_number;
                $firstTable .= '</td>';
                $firstTable .= '</tr>';

                //$all_sum += 1;
            }
            ///////////////////



            $firstTable .= '</tbody>';
            $firstTable .= '</table>';

            /*
            $content .= '<br>';
            $content .= 'Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqadot Matn';
            $content .= '<br>';
            $content .= 'Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn';
            $content .= '<br>';
            $content .= 'Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn Aloqador Matn';
            $content .= '<br>';
           */


            //$content = str_replace('@table1', $secondTable, $content);
            $content = str_replace('@table', $firstTable, $content);

            //$content .= '<p> <b>Jami: ';
            //$content .= $all_sum;
            $content .= '</b></p>';
            //$document_detail->content = $content;
            //$document_detail->save();
////////////////////////////////////////////////



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
                            //$document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                            $document_detail->content = $content;
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
                            if ($dda->id == 2909) { // Tabel
                                $document_detail_attribute_value->attribute_value = $emp->tabel;
                                
                            }
                            // elseif ($dda->id == 439) { // Xat nomeri
                            //     $document_detail_attribute_value->attribute_value = $value->document_number;
                            // }
                            elseif ($dda->id == 2910) { // fio
                             $document_detail_attribute_value->attribute_value =
                                    $emp['lastname_' . $leng] . ' ' . substr($emp['firstname_' . $leng], 0, $firstLetter) . '.' . substr($emp['middlename_' . $leng], 0, $firstLetter) . '.';
                                      //$document_detail_attribute_value->attribute_value = $emp->tabel;
                            } elseif ($dda->id == 2911) { // Position
                               $document_detail_attribute_value->attribute_value = $department ? $department->staff[0]->position->name_uz_latin : '';
                            } elseif ($dda->id == 2912) { // Department Code
                               $document_detail_attribute_value->attribute_value = $department ? $department->department_code : '';
                            } elseif ($dda->id == 2913) { // Department Name
                                $document_detail_attribute_value->attribute_value = $department ? $department->staff[0]->department->name_uz_latin : '';;
                            } elseif ($dda->id == 2914) { // Disciplinary Punishment
                               $document_detail_attribute_value->attribute_value = ' ';
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



///////////////////////////////////////////////
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
                //dd($value);
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


    public function DisciplinePunishmentDocumentsList()
    {
        // $documents = Document::where('document_template_id', 564)->where('status', 1)->with('documentSigners', 'documentDetails.documentDetailAttributeValues')->get();
        // return $documents;
        $documents = Document::select('documents.*')
            ->limit(10)
            ->where('documents.status', 3)
            ->whereIn('documents.document_template_id', [11])
            ->with('documentType')
            ->orderByDesc('created_at')
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

}

