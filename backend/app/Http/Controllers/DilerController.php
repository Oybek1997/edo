<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\DocumentTemplate;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;
use App\Http\Models\DocumentRelation;



class DilerController extends Controller
{
    public function dilerRegistry($locale)
    {
        $document = Document::query();
        $document->where('document_template_id', 479);
        // $document->where('created_at','>', '2023-04-26 00:00:00');
        $document->with('documentDetails');
        $document->with('documentDetails.documentDetailContents');


        return $document->paginate(20);
    }

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }

    public function dilerRegistryCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $documents = Document::with('documentDetails.documentDetailEmployees')
                ->with('documentDetails.documentDetailAttributeValues')
                ->whereIn('id', $request->input('ids'))->get();
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(561);
            $model = new Document();
            $model->document_template_id = 561;
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
                $employee = $value->employee;
                $department = $value->employee->staff[0]->department;
                foreach ($value->documentDetails as $dk => $dd) {
                    foreach ($dd->documentDetailEmployees as $ddek => $dde) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;

                        if ($key == 0) {
                            $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
                        } else {
                            $document_detail->content = '';
                        }
                        $document_detail->save();

                        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
                        foreach ($ddas as $ddak => $dda) {
                            // 2401    Diler kodi
                            // 2402    Diler nomi
                            // 2403    To'lov amalga oshiriladi (Xisob raqami)
                            // 2404    To'lov amalga oshiriladi (Bank nomi)
                            // 2405    To'lov amalga oshiriladi (Bank MFO)
                            // 2406    Mijoz (JSh yoki Yuridik shaxs nomi)
                            // 2407    JShShIR/STIR
                            // 2408    Shartnoma raqami
                            // 2409    Shartnoma sanasi
                            // 2410    Qaytariladigan to'lov miqdori
                            // 2411    Oluvchining nomi
                            // 2412    Oluvchi bank nomi
                            // 2413    Oluvchi bank STIRi
                            // 2414    MFO
                            // 2415    Hisob raqami
                            // 2416    Plastik karta raqami
                            // 2417    Asos
                            // 2418    To'lov maqsadi
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $dda->id;
                            $shartnoma_raqami = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1559);
                            $shartnoma_sanasi = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1560);
                            $oluvchi = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1566);
                            $jshshir = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1562);
                            $plastik = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1571);

if ($dda->id == 2401) {$department ? $department->department_code : '';}
if ($dda->id == 2402) {$department ? $department->name_uz_latin : '';}
if ($dda->id == 2403) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 2191);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2404) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 2192);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2405) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 2193);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2406) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1561);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2407) {$document_detail_attribute_value->attribute_value = $jshshir->attribute_value;}
if ($dda->id == 2408) {$document_detail_attribute_value->attribute_value = $shartnoma_raqami->attribute_value;}
if ($dda->id == 2409) {$document_detail_attribute_value->attribute_value = $shartnoma_sanasi->attribute_value;}
if ($dda->id == 2410) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1564);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2411) {$document_detail_attribute_value->attribute_value = $oluvchi->attribute_value;}
if ($dda->id == 2412) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1567);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2413) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1568);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2414) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1569);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2415) {$attr = collect($dd->documentDetailAttributeValues)->firstWhere('d_d_attribute_id', 1570);$document_detail_attribute_value->attribute_value = $attr->attribute_value;}
if ($dda->id == 2416) {$document_detail_attribute_value->attribute_value = $plastik->attribute_value;}

$date = $value->document_date_reg ? $value->document_date_reg : $value->document_date;
$number = $value->document_number_reg ? $value->document_number_reg : $value->document_number;
if ($dda->id == 2417) {$document_detail_attribute_value->attribute_value = $date.' sanadagi '.$number.' sonli hat';}
$maqsad="00501- ".$shartnoma_sanasi->attribute_value." danadagi ".$shartnoma_raqami->attribute_value." sonli shartnomaga asosan ".$oluvchi->attribute_value." (JShShIR ".$jshshir->attribute_value." Plastik karta raqami:".$plastik->attribute_value.")  farq summasi qaytarilmoqda";
if ($dda->id == 2418) {$document_detail_attribute_value->attribute_value = $maqsad;}
// 2023 yil 27 fevraldagi 23DX-D061-00115 sonli xat	
// 00501- 2022 yil 10 dekabrdagi  1-261-2022-23407UA sonli shartnomaga asosan  
// BAXRIDINOVA SHAXNOZA AKBAR QIZI (JShShIR 42201942380094 Plastik karta raqami:8600130452060546)  farq summasi qaytarilmoqda


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

    public function dilerRegistryDelete($id)
    {
        $document = Document::find($id);
        if ($document && $document->status == 3) {
            $document->status = 4;
            $document->save();
        }
    }
}
