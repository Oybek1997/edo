<?php

namespace App\Http\Controllers;

use App\Http\Models\ActionType;
use App\Http\Models\AttributeSignerStaff;
use App\Http\Models\Department;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailAttribute;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentType;
use App\Http\Models\DataType;
use App\Http\Models\DocumentFavorite;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailSignerAttribute;
use App\Http\Models\DocumentDetailTemplate;
use App\ImageTemp;
use App\Http\Models\SignerGroup;
use App\Http\Models\SignerGroupDetail;
use App\Http\Models\DocumentSignerTemplate;
use App\Http\Models\DocumentTemplateSignerGroup;
use App\Http\Models\Staff;
use App\User;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        //
        $filter = $request->input('filter');
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $document_templates = DocumentTemplate::with('documentType')
            ->with('department')->orderBy('id');
        if (isset($search)) {
            $document_templates->where(function ($query) use ($search) {
                return $query
                    ->orWhere('name_uz_latin', 'ilike', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'ilike', "%" . $search . "%")
                    ->orWhere('name_ru', 'ilike', "%" . $search . "%");
            });
        }

        return $document_templates->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef(Request $request)
    {
        $locale = $request->input('language');
        // $departments = Department::select('id as value', 'name_' . $locale . ' as text')->get();
        $documentTypes = DocumentType::select('id  as value', 'name_' . $locale . ' as text')->get();
        $dataTypes = DataType::select('id as value', 'name_' . $locale . ' as text')->get();
        $actionTypes = ActionType::select('id as value', 'name_' . $locale . ' as text', 'description')->get();

        $signerGroups = SignerGroup::with(['signerGroupDetails.staff' => function ($q) use ($locale) {
            $q->with('range:id,code')
                ->with(['department' => function ($query) use ($locale) {
                    $query->select(['id', 'department_code as code', 'name_' . $locale . ' as text']);
                }])
                ->with(['position' => function ($query) use ($locale) {
                    $query->select(['id', 'name_' . $locale . ' as text']);
                }])
                ->select(["id", "department_id", "position_id", "range_id", "rate_count"]);
        }])
            ->select(['id', 'name_' . $locale . ' as text'])->get();
        $staffs = Staff::with('range:id,code')
            ->with(['department' => function ($query) use ($locale) {
                $query->select(['id', 'department_code as code', 'name_' . $locale . ' as text']);
            }])
            ->with(['position' => function ($query) use ($locale) {
                $query->select(['id', 'name_' . $locale . ' as text']);
            }])
            ->select(["id", "department_id", "position_id", "range_id", "rate_count"])
            ->paginate(50);
        return [
            // 'departments' => $departments,
            'documentTypes' => $documentTypes,
            'signerGroups' => $signerGroups,
            'dataTypes' => $dataTypes,
            'staffs' => $staffs,
            'actionTypes' => $actionTypes
        ];
    }

    public function edit($id, $locale)
    {
        return DocumentTemplate::with(['documentSignerTemplates.staff' => function ($q) use ($locale) {
            $q->with('range:id,code')
                ->with(['department' => function ($query) use ($locale) {
                    $query->select(['id', 'department_code as code', 'name_' . $locale . ' as text']);
                }])
                ->with(['position' => function ($query) use ($locale) {
                    $query->select(['id', 'name_' . $locale . ' as text']);
                }])
                ->select(["id", "department_id", "position_id", "range_id", "rate_count"]);
        }])
            ->with('department')
            ->with('signerGroups')
            ->with(['documentDetailTemplates.documentDetailAttributes' => function ($q) {
                $q->where('is_active', 1)->with('dataType')->with('signerStaffIds');
            }])->where('id', $id)->first();
    }
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            //update or insert document template
            $document_template = $request->all();
            $model = DocumentTemplate::where('id', $document_template['id'])->first();

            $name = str_replace(' ', '_', $document_template['name_uz_latin']);
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
            $name = str_replace("«", "", $name);
            $name = str_replace("»", "", $name);
            $name = strtolower($name);

            if ($model) {
                //dd($model);
                $model->updated_by = Auth::id();
                $name_old = str_replace(' ', '_', $model['name_uz_latin']);
                $name_old = str_replace("'", "", $name_old);
                $name_old = str_replace(",", "", $name_old);
                $name_old = str_replace("?", "", $name_old);
                $name_old = str_replace("(", "", $name_old);
                $name_old = str_replace(")", "", $name_old);
                $name_old = str_replace("`", "", $name_old);
                $name_old = str_replace("!", "", $name_old);
                $name_old = str_replace('"', '', $name_old);
                $name_old = str_replace("\\", "", $name_old);
                $name_old = str_replace("/", "", $name_old);
                $name_old = str_replace("«", "", $name_old);
                $name_old = str_replace("»", "", $name_old);
                $name_old = strtolower($name_old);
                $permission = Permission::where('name', $name_old . '-create')->first();
                if (!$permission) {
                    $permission = new Permission();
                }
            } else {
                $model = new DocumentTemplate();
                $permission = Permission::where('name', $name . '-create')->first();
                if (!$permission) {
                    $permission = new Permission();
                }
                $model->created_by = Auth::id();
            }

            $model->department_id = $document_template['department_id'];
            $model->document_type_id = $document_template['document_type_id'];
            $model->numbering_order = $document_template['numbering_order'];
            $model->has_employee = $document_template['has_employee'] ? 1 : 0;
            $model->table_font_size = $document_template['table_font_size'];
            $model->change_staff = isset($document_template['change_staff']) ? $document_template['change_staff'] : null;
            $model->add_signer = $document_template['add_signer'] ? 1 : 0;
            $model->add_signer_employee = $document_template['add_signer_employee'] ? 1 : 0;
            $model->add_parent = $document_template['add_parent'] ? 1 : 0;
            $model->is_pdf_portrait = $document_template['is_pdf_portrait'] ? 1 : 0;
            $model->select_staff = isset($document_template['select_staff']) && $document_template['select_staff'] ? 1 : 0;
            $model->select_department = isset($document_template['select_department']) && $document_template['select_department'] ? 1 : 0;
            $model->is_list_vertical = $document_template['is_list_vertical'] ? 1 : 0;
            $model->is_from_to_department_show = $document_template['is_from_to_department_show'] ? 1 : 0;
            $model->is_content_visible = $document_template['is_content_visible'] ? 1 : 0;
            $model->is_confidential = isset($document_template['is_confidential']) && $document_template['is_confidential'] ? 1 : 0;
            $model->is_manual_file = isset($document_template['is_manual_file']) && $document_template['is_manual_file'] ? 1 : 0;
            $model->is_document_relation = $document_template['is_document_relation'] ? 1 : 0;
            $model->is_attribute_show = isset($document_template['is_attribute_show']) && $document_template['is_attribute_show'] ? 1 : 0;
            $model->numeration_type = isset($document_template['numeration_type']) ? $document_template['numeration_type'] : null;
            $model->template_code = isset($document_template['template_code']) ? $document_template['template_code'] : null;
            $model->folder_code = isset($document_template['folder_code']) ? $document_template['folder_code'] : null;
            $model->digital = isset($document_template['digital']) ? $document_template['digital'] : null;
            $model->pdf_download_with_eimzo = isset($document_template['pdf_download_with_eimzo']) ? $document_template['pdf_download_with_eimzo'] : null;
            //$model->signer_group_id = $document_template['signer_group_id'];
            $model->name_uz_latin = $document_template['name_uz_latin'];
            $model->name_uz_cyril = $document_template['name_uz_cyril'];
            $model->name_ru = $document_template['name_ru'];

            // $model->description_uz_latin = $document_template['description_uz_latin'];
            // $model->description_uz_cyril = $document_template['description_uz_cyril'];
            // $model->description_ru = $document_template['description_ru'];
            $model->save();
            $permission->name = $name . '-create';
            $permission->display_name = 'Create ' . $document_template['name_uz_latin'];
            $permission->description = $document_template['name_uz_latin'] . ' ' . $document_template['name_uz_cyril'] . ' ' . $document_template['name_ru'];
            $permission->save();
            //update or insert document signers template
            $document_signer_templates = $document_template['document_signer_templates'];

            foreach ($model->documentSignerTemplates as $key => $value) {
                $tmp = true;
                foreach ($document_signer_templates as $k => $v) {
                    if ($value->id == $v['id']) {
                        $tmp = false;
                    }
                }
                if ($tmp) {
                    $document_signer_template = DocumentSignerTemplate::where("id", $value->id)->first();
                    if ($document_signer_template) {
                        $document_signer_template->delete();
                    }
                }
            }

            foreach ($document_signer_templates as $key => $value) {
                $dsTemplate = DocumentSignerTemplate::where("id", $value['id'])->first();
                if ($dsTemplate) {
                    $dsTemplate->updated_by = Auth::id();
                } else {
                    $dsTemplate = new DocumentSignerTemplate();
                    $dsTemplate->created_by = Auth::id();
                }
                $dsTemplate->document_template_id = $model->id;
                $dsTemplate->staff_id = $value['staff_id'];
                $dsTemplate->due_day_count = $value['due_day_count'];
                $dsTemplate->action_type_id = $value['action_type_id'];
                $dsTemplate->sequence = $value['sequence'];
                $dsTemplate->sign_type = $value['sign_type'];
                $dsTemplate->is_registry = $value['is_registry'];
                $dsTemplate->save();
            }

            //update or insert document detail templates
            $document_detail_templates = $document_template['document_detail_templates'];
            // foreach ($model->documentDetailTemplates as $key => $value) {
            //     $tmp = true;
            //     foreach ($document_detail_templates as $k => $v) {
            //         return $value->id == $v['id'];
            //         if ($value->id == $v['id']) {
            //             $tmp = false;
            //         }
            //     }
            //     if ($tmp) {
            //         $document_detail_template = DocumentDetailTemplate::where("id", $value->id)->first();
            //         if ($document_detail_template) {
            //             $document_detail_template->delete();
            //         }
            //     }
            // }

            foreach ($document_detail_templates as $key => $value) {
                $document_detail_template = DocumentDetailTemplate::where("id", $value['id'])->first();
                if (!$document_detail_template) {
                    $document_detail_template = new DocumentDetailTemplate();
                }
                $document_detail_template->document_template_id = $model->id;
                $document_detail_template->content_ru = $value['content_ru'];
                $document_detail_template->content_uz_latin = $value['content_uz_latin'];
                $document_detail_template->content_uz_cyril = $value['content_uz_cyril'];
                $document_detail_template->save();


                //update or insert document detail attributes
                $document_detail_attributes = $value['document_detail_attributes'];
                foreach ($document_detail_attributes as $key1 => $attribute_value) {
                    $ddAttribute = DocumentDetailAttribute::where("id", $attribute_value['id'])->first();
                    if ($ddAttribute) {
                        $ddAttribute->updated_by = Auth::id();
                    } else {
                        $ddAttribute = new DocumentDetailAttribute();
                        $ddAttribute->created_by = Auth::id();
                    }
                    // $ddAttribute = DocumentDetailAttribute::create( $document_detail_template->id,);
                    $ddAttribute->document_detail_template_id = $document_detail_template->id;
                    $ddAttribute->data_type_id = $attribute_value['data_type_id'];
                    $ddAttribute->table_list_id = $attribute_value['table_list_id'];
                    $ddAttribute->required = $attribute_value['required'] ? 1 : 0;
                    $ddAttribute->unique = isset($attribute_value['unique']) && $attribute_value['unique'] ? 1 : 0;
                    $ddAttribute->is_summa = isset($attribute_value['is_summa']) && $attribute_value['is_summa'] ? 1 : 0;
                    $ddAttribute->replace_attribute = isset($attribute_value['replace_attribute']) && $attribute_value['replace_attribute'] ? $attribute_value['replace_attribute'] : '';
                    $ddAttribute->is_list_vertical = $attribute_value['is_list_vertical'] ? 1 : 0;
                    $ddAttribute->is_registry_show = $attribute_value['is_registry_show'] ? 1 : 0;
                    $ddAttribute->is_show = $attribute_value['is_show'] ? 1 : 0;
                    $ddAttribute->is_signer_staff = $attribute_value['signer_staff_ids'] ? 1 : 0;
                    $ddAttribute->sequence = $attribute_value['sequence'];
                    $ddAttribute->attribute_name_uz_latin = $attribute_value['attribute_name_uz_latin'];
                    $ddAttribute->attribute_name_uz_cyril = $attribute_value['attribute_name_uz_cyril'];
                    $ddAttribute->attribute_name_ru = $attribute_value['attribute_name_ru'];
                    $ddAttribute->value_min_length = $attribute_value['value_min_length'];
                    $ddAttribute->value_max_length = $attribute_value['value_max_length'];
                    $ddAttribute->description = $attribute_value['description'];
                    $ddAttribute->save();

                    AttributeSignerStaff::where('attribute_id', $ddAttribute->id)->delete();
                    if (count($attribute_value['signer_staff_ids'])) {
                        foreach ($attribute_value['signer_staff_ids'] as $key => $staff_id) {
                            $attrubute_signer_staff = new AttributeSignerStaff();
                            $attrubute_signer_staff->attribute_id = $ddAttribute->id;
                            $attrubute_signer_staff->staff_id = $staff_id;
                            $attrubute_signer_staff->save();
                        }
                    }
                }
            }
            DocumentTemplateSignerGroup::where('document_template_id', $model->id)->delete();
            foreach ($document_template['signer_group_ids'] as $key => $value) {
                $document_template_signer_group = new DocumentTemplateSignerGroup();
                $document_template_signer_group->document_template_id = $model->id;
                $document_template_signer_group->signer_group_id = $value;
                $document_template_signer_group->save();
            }
            DB::commit();
            // dd($model->documentDetailTemplates);
            return ['status' => 200];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy($id)
    {
        $model = DocumentTemplate::find($id);

        foreach ($model->documentDetailTemplates as $key => $value) {
            $value->delete();
        }
        $model->delete();
    }

    public function destroyDocumentSignerTemplate($id)
    {
        $model = DocumentSignerTemplate::find($id);
        $model->delete();
    }

    public function destroyDocumentDetailTemplate($id)
    {
        $model = DocumentDetailTemplate::find($id);
        $model->delete();
    }

    public function destroyDocumentDetailAttribute($id)
    {
        $model = DocumentDetailAttribute::find($id);
        $model->is_active = 0;
        $model->save();
    }

    public function getName(Request $request)

    {
        $locale = $request->input('language');
        $id = $request->input('document_type_id');
        $for_ides = $request->input('for_ides');
        $search = $request->input('search');
        $getName = DocumentTemplate::with('documentType:id,name_' . $locale)->withCount('favorites')->where('document_type_id', '=', $id)->orderBy('id');
        if ($for_ides) {
            $getName = DocumentTemplate::with('department')->whereIn('id', [305, 357]);
        }
        if (isset($search)) {
            $getName->where(function ($query) use ($search) {
                return $query
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
            });
        }
        // select('id', 'name_' .$locale . ' as text')->
        return  $getName->get();
    }

    public function getNameForUser(Request $request)
    {
        $search = $request->input('search');
        $getName = DocumentTemplate::with('department');
        if (isset($search)) {
            $getName->where(function ($query) use ($search) {
                return $query
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
            });
        }
        // select('id', 'name_' .$locale . ' as text')->
        return  $getName->get();
    }

    public function getNameIndex(Request $request)
    {
        $locale = $request->input('language');
        $id = $request->input('document_type_id');
        $getName = DocumentTemplate::select('id', 'name_' . $locale . ' as text')
            ->with('documentDetailTemplates.documentDetailAttributes.dataType')
            ->with('documentDetailTemplates.documentDetailAttributes.tableList')
            ->where('document_type_id', '=', $id)->get();
        return  $getName;
    }

    public function getAll(Request $request)
    {
        $locale = $request->input('language');
        $id = $request->input('document_template_id');
        $getAll = DocumentTemplate::with('documentType')
            ->with('department')
            // ->with('departmentStaffId')
            ->with('signerGroups.signerGroupDetails.staff.department')
            ->with('signerGroups.signerGroupDetails.staff.position')
            ->with('signerGroups.signerGroupDetails.staff.employees')
            ->with('documentSignerTemplates')
            ->with('documentSignerTemplates.actionType')
            ->with('documentSignerTemplates.staff')
            ->with('documentSignerTemplates.staff.employees')
            ->with('documentSignerTemplates.staff.position')
            ->with('documentSignerTemplates.staff.department')
            ->with(['documentDetailTemplates' => function ($query) {
                $query->with(['documentDetailAttributes' => function ($q) {
                    $q->with('tableList')
                        ->with('dataType')
                        ->where('is_active', 1)
                        ->where('is_signer_staff', 0)
                        ->orderBy('sequence', 'asc');
                }]);
            }])
            ->where('id', $id)->first();

        // foreach ($getAll->documentDetailTemplates[0]->documentDetailAttributes as $key => $value) {
        //     if($value->table_list_id){
        //         $column_name = $value->tableList->is_locale == 1 ? $value->tableList->column_name.'_'.$locale : $value->tableList->column_name;
        //         $getAll->documentDetailTemplates[0]->documentDetailAttributes[$key]->table_lists = DB::table($value->tableList->table_name)->get();
        //     }
        // }
        return  $getAll;
    }

    public function signedAttribute(Request $request)
    {
        $document_template_id = $request->input('document_template_id');
        $document_id = $request->input('document_id');

        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $doc_detail = DocumentDetail::where('document_id', $document_id)
            ->with('documentDetailSignerAttributes')->get();

        $edit_attributes = DocumentDetailAttribute::where('is_signer_staff', 1)
            ->where('document_detail_template_id', DocumentTemplate::find($document_template_id)->documentDetailTemplates[0]->id)
            ->whereHas('signerStaffIds', function ($q) use ($userStaffIds) {
                $q->whereIn('staff_id',  $userStaffIds);
            })
            ->get();
        $i = 0;
        foreach ($doc_detail as $key => $value) {
            foreach ($edit_attributes as $key2 => $edit_attribute) {
                $attribute = DocumentDetailSignerAttribute::where('d_d_attribute_id', $edit_attribute->id)
                    ->where('document_detail_id', $value->id)->first();
                if (!$attribute) {
                    $new_attribute = new DocumentDetailSignerAttribute();
                    $new_attribute->document_detail_id = $value->id;
                    $new_attribute->d_d_attribute_id  = $edit_attribute->id;
                    $new_attribute->staff_id  = $edit_attribute->signerStaffIds->whereIn('staff_id', $userStaffIds)->first()->staff_id;
                    $new_attribute->save();
                    $i++;
                }
            }
        }

        return $i;
    }

    public function getList(Request $request)
    {
        $locale = $request->input('language');

        return DocumentTemplate::select('id', 'name_' . $locale . ' as text', 'document_type_id')->get();
    }

    public function getDocumentTemplates($doc_type_id)
    {
        return DocumentTemplate::where('document_type_id', $doc_type_id)
            ->with(['documentDetailTemplates.documentDetailAttributes' => function ($q) {
                $q->where('is_active', 1)
                    ->with('tableList');
            }])->get();
    }

    public function usedVacationDays(Request $request)
    {
        $inputs = $request->input('filter');
        if (
            isset($inputs['tabel']) && !empty($inputs['tabel']) &&
            isset($inputs['month1']) && !empty($inputs['month1']) &&
            isset($inputs['month2']) && !empty($inputs['month2']) &&
            $inputs['month1'] != $inputs['month2'] && $inputs['month1'] < $inputs['month2']
        ) {
            $json_value = [
                "tabnos" => $inputs['tabel'],
                "month1" => $inputs['month1'],
                "month2" => $inputs['month2'],
            ];
            $curl = curl_init();
            $curlOptions = [
                CURLOPT_URL => "http://edo-db2.uzautomotors.com/api/person/vacation",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($json_value),
                CURLOPT_HTTPHEADER => [
                    "Authorization: BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ",
                    "Content-Type: application/json",
                    "token: BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ",
                ],
            ];
            curl_setopt_array($curl, $curlOptions);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            return ($response);
        }
        return 0;
    }

    public function getDocumentFavorites($id)
    {
        $user = Auth::id();
        $favorite = DocumentFavorite::where('user_id', $user)->where('document_template_id', $id)->first();
        if (!$favorite) {
            $favorite = new DocumentFavorite();
            $favorite->user_id = $user;
            $favorite->document_template_id = $id;
            $favorite->created_at = date('Y-m-d H:i:s');
            $favorite->save();
            return 'Tanlanganga saqlandi';
        } else {
            $favorite->delete();
            return 'Tugadi';
        }
    }

    public function getUserFavorites()
    {
        $favorites = DocumentTemplate::whereHas('favorites', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->with('department')
            ->withCount('favorites')
            ->get();
        return $favorites;
    }

    public function userTemplates(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $limit = $request->input('limit');
        $locale = $request->input('locale');
        $authId = Auth::id();
        $data = DB::select("SELECT df.id AS df_id, dt.id AS template_id, dt.name_" . $locale . " AS template, dtype.name_" . $locale . " AS type, count(d.id) AS soni FROM documents d
        INNER JOIN document_templates dt ON dt.id = d.document_template_id
        LEFT JOIN document_favorites df ON df.document_template_id = dt.id AND df.user_id = " . $authId . "
        INNER JOIN document_types dtype ON dtype.id = dt.document_type_id
        WHERE d.created_employee_id = " . $employee_id . "
        GROUP BY dt.id, df.id, dtype.name_" . $locale . "
        ORDER BY soni DESC, template ASC LIMIT " . $limit);
        return $data;
    }
    
}
