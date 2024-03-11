<?php

namespace App\Http\Controllers;

use App\Http\Models\ComplaensCencelDocument;
use App\Http\Models\Directory;
use App\Http\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentRelation;
use App\Http\Models\DocumentDetailContent;
use Hidehalo\Nanoid\Client;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;


class ComplaensCencelDocumentController extends Controller
{

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }

    public function makeDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $complaens_cencel_document = $request->input('complaens_cencel_document');
            $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(258);
            $model = new Document();
            $model->document_template_id = 258;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = 'uz_cyril';
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();

            // foreach ($documents as $key => $value)
            {
                $dr = new DocumentRelation();
                $dr->document_id = $model->id;
                $dr->parent_document_id = $request->input('id');
                $dr->save();
            }
            $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
            // return $ddas;
            // document_date: "2021-06-15"
            // document_number: "21AXX-2500-0045"
            // document_template: "Хизмат ҳати"
            // document_type: "Хизмат хати"
            // id: 78787
            // pdf_file_name: "okvdRITNjHrayOl6KYcpk"
            // status: 6


            // amount_sum: 1
            // detailed_reason: "2"
            // directory_id: 33
            // document_id: 78787
            // id: 3
            // identified_risks: null
            // identified_risks_text: null
            // risk_item: null
            // risk_item_text: null
            // short_reason: "Иқтисод қилиш."
            $document_detail = new DocumentDetail();
            $document_detail->document_id = $model->id;

            // return $document_template->documentDetailTemplates[0]['content_'.$model->locale];
            $document_detail->content = $document_template->documentDetailTemplates[0]['content_' . $model->locale];
            // if ($ddak == 0) {
            // } else {
            //     $document_detail->content = '';
            // }
            $document_detail->save();
            foreach ($ddas as $ddak => $dda) {

                $document_detail_attribute_value = new DocumentDetailAttributeValue();
                $document_detail_attribute_value->document_detail_id = $document_detail->id;
                $document_detail_attribute_value->d_d_attribute_id = $dda->id;

                if ($dda->id == 951) { // docno
                    $document_detail_attribute_value->attribute_value = $request->input('document_number');
                } elseif ($dda->id == 952) { // rad qilish sababi
                    $document_detail_attribute_value->attribute_value = $complaens_cencel_document['short_reason'];
                } elseif ($dda->id == 953) { // batafsil
                    $document_detail_attribute_value->attribute_value = $complaens_cencel_document['detailed_reason'];
                } elseif ($dda->id == 954) { // ekonom summa
                    $document_detail_attribute_value->attribute_value = $complaens_cencel_document['amount_sum'];
                } elseif ($dda->id == 955) { // xavf
                    $document_detail_attribute_value->attribute_value = $complaens_cencel_document['identified_risks_text'];
                } elseif ($dda->id == 956) { // havf bandlari
                    $document_detail_attribute_value->attribute_value = $complaens_cencel_document['risk_item_text'];
                } elseif ($dda->id == 957) { // xujjat turi
                    $document_detail_attribute_value->attribute_value = $request->input('document_type');
                } elseif ($dda->id == 958) { // xujjat sanasi
                    $document_detail_attribute_value->attribute_value = substr($request->input('document_date'),0,10);
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

            if(in_array($complaens_cencel_document['directory_id'],[33,34]))
                $signerGroup = $document_template->signerGroups[0];
            else{
                $signerGroup = $document_template->signerGroups[1];
            }

            foreach ($signerGroup->signerGroupDetails as $key => $value) {
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

            $complaensCencelDocument = ComplaensCencelDocument::find($complaens_cencel_document['id']);
            $complaensCencelDocument->reason_document_id = $model->id;
            $complaensCencelDocument->save();

            DB::commit();
            return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

            return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ComplaensCencelDocument $complaensCencelDocument)
    {
        //
    }

    public function edit(ComplaensCencelDocument $complaensCencelDocument)
    {
        //
    }

    public function update(Request $request)
    {
        $model = ComplaensCencelDocument::where('document_id', $request['id'])->first();
        if (!$model) {
            $model = new ComplaensCencelDocument();
            $model->created_by = Auth::id();
            $model->document_id = $request['id'];
        } else {
            $model->updated_by = Auth::id();
        }

        $model->short_reason = null;
        $model->detailed_reason = null;
        $model->amount_sum = null;
        $model->identified_risks = null;
        $model->identified_risks_text = null;
        $model->risk_item = null;
        $model->risk_item_text = null;

        // return $request['complaens_cencel_document']['directory_id'];
        $model->directory_id = $request['complaens_cencel_document']['directory_id'];
        $model->short_reason = Directory::find($request['complaens_cencel_document']['directory_id'])->name_uz_cyril;
        $model->detailed_reason = isset($request['complaens_cencel_document']['detailed_reason']) ? $request['complaens_cencel_document']['detailed_reason'] : null;
        // if (Directory::whereIn('code', ['231', '232'])->find($request['complaens_cencel_document']['directory_id']))
        
        if(in_array($model->directory_id,[33,34])){
            $model->amount_sum = $request['complaens_cencel_document']['amount_sum'];
        }
        else if(in_array($model->directory_id,[39])){
            // $model->amount_sum = null;
            $model->identified_risks = isset($request['complaens_cencel_document']['identified_risks']) ? $request['complaens_cencel_document']['identified_risks'] : null;
            $model->identified_risks_text = isset($request['complaens_cencel_document']['identified_risks']) ? Directory::find($request['complaens_cencel_document']['identified_risks'])->name_uz_cyril : null;
            $model->risk_item = isset($request['complaens_cencel_document']['risk_item'])  ? $request['complaens_cencel_document']['risk_item'] : null;
            $model->risk_item_text = isset($request['complaens_cencel_document']['risk_item'])  ? Directory::find($request['complaens_cencel_document']['risk_item'])->name_uz_cyril : null;
        }
        if(in_array($model->directory_id,[38,39])){
            $model->reason_document_id = null;
        }
        if ($model->save()) {
            return "Successfully Saved!";
        } else {
            return 0;
        }
    }

    public function destroy(ComplaensCencelDocument $complaensCencelDocument)
    {
        //
    }

    public function getList(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $document = Document::select('id', 'pdf_file_name', 'document_date', 'document_number', 'document_template', 'document_type', 'status')
            ->with('complaensCencelDocument')
            ->with('complaensCencelDocument.reasonDocument')
            ->whereHas('documentSigners', function ($q) {
                $q
                    ->where('staff_id', 5)
                    ->where('status','>', 0);
            });
            if($search){
                $document->where('document_number','like','%'.$search.'%');
                $document->orWhere('id','like','%'.$search.'%');
            }
        $document->orderBy('document_date', 'DESC');
        // return $this->updateDocument();
        return $document->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function getRef()
    {
        $short_reasons = Directory::where('directory_type_id', 3)->get();
        $_237 = Directory::where('directory_type_id', 4)->get();
        $c024 = Directory::where('directory_type_id', 5)->get();
        return [
            'short_reasons' => $short_reasons,
            '_237' => $_237,
            'c024' => $c024,
        ];
    }
}
