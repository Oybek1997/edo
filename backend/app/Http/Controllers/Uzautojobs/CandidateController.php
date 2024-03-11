<?php

namespace App\Http\Controllers\Uzautojobs;

use App\Http\Models\Uzautojobs\CandidateSubmitted;
use App\Http\Models\Uzautojobs\CandidateSelected;
use App\Http\Models\Uzautojobs\itemsMessage;
use App\Http\Models\Uzautojobs\SortByVacancie;
use App\Http\Models\Uzautojobs\Vacancies;
use App\Http\Models\Staff;
use App\Http\Models\Employee;
use App\Http\Models\Document;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\DocumentRelation;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Hidehalo\Nanoid\Client;


class CandidateController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function MatchingIndex(Request $request)
  {
    $language = $request->input('language');
    $page = $request->input('pagination')['page'];
    $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    $filter = $request->input('filter');
    $model = CandidateSubmitted::select('*')
    ->whereNotNull('protocol_number')
    ->with('choiceStatus')
    ->whereNotNull('order_number');
    return ['choice' => $model->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)];
  }
  public function CandidateIndex(Request $request)
  {
    $language = $request->input('language');
    $page = $request->input('pagination')['page'];
    $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    $filter = $request->input('filter');
    $model = CandidateSubmitted::select('*')
      ->whereHas('choiceStatus')
      ->with('choiceStatus')
    ;
    return ['choice' => $model->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)];
  }
  public function CandidatesDocumentsCreate(Request $request)
  {
    $selection = $request->input('id');
    DB::beginTransaction();
    try {
      // $documents = Document::whereIn('id', $request->input('ids'))->get();
      $documents = SortByVacancie::query()
        ->whereNotNull('status')
        ->with('vacancies')
        ->whereHas('choice', function ($q) use ($selection) {
          $q->where('tanlov_id', $selection);
        })
        ->with('choice')
        ->with('message')
        ->orderBy('status', 'DESC')
        ->orderBy('id')
        ->get();
      // dd($documents);
      $chois = str_pad($documents[0]->choice->tanlov->id, 6, "0", STR_PAD_LEFT);
      $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(663);
      //dd($documents);
      $model = new Document();
      $model->document_template_id = 663;
      $model->created_employee_id = Auth::user()->employee_id;
      $model->department_id = $document_template->department_id;
      $model->document_type_id = $document_template->document_type_id;
      $model->title = $chois . ' tanlov bo`yich bayonnoma';
      $model->locale = 'uz_latin';
      $model->document_date = date('Y-m-d H:i:s');
      $model->pdf_file_name = $this->generateNanoId();
      $model->save();


      $content = $document_template->documentDetailTemplates[0]['content_uz_latin'];

      $content_lavozm = $documents[0]->choice->staff->department->branch->name . ' ';
      $content_lavozm .= $documents[0]->choice->staff->department->functionalDepartment->name_uz_latin . ' ';
      $content_lavozm .= $documents[0]->choice->staff->department->name_uz_latin . ' ';
      $content_lavozm .= $documents[0]->choice->staff->position->name_uz_latin;
      $content = str_replace('@lavozim', $content_lavozm, $content);

      foreach ($documents as $key => $value) {
        $document_detail = new DocumentDetail();
        $document_detail->document_id = $model->id;

        if ($key == 0) {
          $document_detail->content = $content;
        } else {
          $document_detail->content = '';
        }
        $document_detail->save();

        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
        foreach ($ddas as $ddak => $dda) {

          $document_detail_attribute_value = new DocumentDetailAttributeValue();
          $document_detail_attribute_value->document_detail_id = $document_detail->id;
          $document_detail_attribute_value->d_d_attribute_id = $dda->id;
          if ($dda->id == 2934) { // Tabel
            $document_detail_attribute_value->attribute_value = $value->vacancies->lastname_uz_latin;
          } else if ($dda->id == 2935) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->firstname_uz_latin;
          } else if ($dda->id == 2936) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->middlename_uz_latin;
          } else if ($dda->id == 2937) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->passport_serial . $value->vacancies->passport_number;
          } else if ($dda->id == 2938) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->INPS;
          } else if ($dda->id == 2939) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->born_date;
          } else if ($dda->id == 2940) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->knowledge_name . ' ' . $value->vacancies->knowledge_direction . ' ' . $value->vacancies->knowledge_specialty;
          } else if ($dda->id == 2941) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->knowledge_type;
          } else if ($dda->id == 2942) {
            $document_detail_attribute_value->attribute_value = $value->status == true ? 'Ijobiy' : 'Salbiy';
          } else if ($dda->id == 2943) {
            $document_detail_attribute_value->attribute_value = $value->message ? $value->message->name : '';
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

      $document_signer = new DocumentSigner();
      $document_signer->document_id = $model->id;
      $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
      ;
      $document_signer->taken_datetime = date('Y-m-d H:i:s');
      $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
      $document_signer->action_type_id = 6;
      $document_signer->sequence = 100;
      $document_signer->signer_employee_id = Auth::user()->employee_id;
      ;
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
      $canSub = CandidateSubmitted::where('id', $selection)->first();
      $canSub->protocol_number = $model->pdf_file_name;
      // $canSub->order_number=$model->id;
      $canSub->save();
      DB::commit();
      return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
    } catch (\Throwable $th) {
      DB::rollBack();
      throw $th;
      return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
    }
  }
  public function CandidatesOrderCreate(Request $request)
  {
    $selection = $request->input('id');
    DB::beginTransaction();
    try {
      // $documents = Document::whereIn('id', $request->input('ids'))->get();
      $documents = SortByVacancie::query()
        ->whereNotNull('status')
        ->where('status', '!=', false)
        ->with('vacancies')
        ->whereHas('choice', function ($q) use ($selection) {
          $q->where('tanlov_id', $selection);
        })
        ->with('choice')
        ->with('message')
        ->orderBy('status', 'DESC')
        ->orderBy('id')
        ->get();
      // dd($documents);
      $diapazon = $documents[0]->choice->staff->range->code;
      $chois = str_pad($documents[0]->choice->tanlov->id, 6, "0", STR_PAD_LEFT);
      $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(664);
      //dd($documents);
      $model = new Document();
      $model->document_template_id = 664;
      $model->created_employee_id = Auth::user()->employee_id;
      $model->department_id = $document_template->department_id;
      $model->document_type_id = $document_template->document_type_id;
      $model->title = $chois . ' tanlov bo`yich buyruq';
      $model->locale = 'uz_latin';
      $model->document_date = date('Y-m-d H:i:s');
      $model->pdf_file_name = $this->generateNanoId();
      $model->save();

      $content = $document_template->documentDetailTemplates[0]['content_uz_latin'];

      $content_lavozm = $documents[0]->choice->staff->department->branch->name . ' ';
      $content_lavozm .= $documents[0]->choice->staff->department->functionalDepartment->name_uz_latin . ' ';
      $content_lavozm .= $documents[0]->choice->staff->department->name_uz_latin . ' ';
      $content_lavozm .= $documents[0]->choice->staff->position->name_uz_latin;
      $content = str_replace('@diapazon', $diapazon, $content);
      $content = str_replace('@lavozim', $content_lavozm, $content);
      $content = str_replace('@condidantCount', count($documents), $content);

      foreach ($documents as $key => $value) {
        $document_detail = new DocumentDetail();
        $document_detail->document_id = $model->id;

        if ($key == 0) {
          $document_detail->content = $content;
        } else {
          $document_detail->content = '';
        }
        $document_detail->save();

        $ddas = collect($document_template->documentDetailTemplates[0]->documentDetailAttributes)->where('is_active', 1);
        foreach ($ddas as $ddak => $dda) {
          $document_detail_attribute_value = new DocumentDetailAttributeValue();
          $document_detail_attribute_value->document_detail_id = $document_detail->id;
          $document_detail_attribute_value->d_d_attribute_id = $dda->id;
          $R = null;
          $S = null;
          $H = null;
          $K = null;
          foreach ($value->choice->staffCoefficients as $valueCoy) {
            switch ($valueCoy->coefficient->code) {
              case "R":
                $R = $valueCoy->coefficient->protsent;
                break;
              case "S":
                $S = $valueCoy->coefficient->protsent;
                break;
              case "H":
                $H = $valueCoy->coefficient->protsent;
                break;
              case "K":
                $K = $valueCoy->coefficient->protsent;
                break;
              default:
                // Agar coefficient->code "R", "S", "H" yoki "K" emas bo'lsa, hech narsa qilmaslik
                break;
            }
          }
          if ($dda->id == 2944) { // Tabel
            $document_detail_attribute_value->attribute_value = str_pad($value->choice->tanlov_id, 6, "0", STR_PAD_LEFT);
          } else if ($dda->id == 2945) {
            $document_detail_attribute_value->attribute_value = $value->tabelNumber;
          } else if ($dda->id == 2946) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->lastname_uz_latin;
          } else if ($dda->id == 2947) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->firstname_uz_latin;
          } else if ($dda->id == 2948) {
            $document_detail_attribute_value->attribute_value = $value->vacancies->middlename_uz_latin;
          } else if ($dda->id == 2949) {
            $document_detail_attribute_value->attribute_value = $value->choice->staff->department->name_uz_latin;
          } else if ($dda->id == 2950) {
            $document_detail_attribute_value->attribute_value = $value->choice->staff->department->department_code;
          } else if ($dda->id == 2951) {
            $document_detail_attribute_value->attribute_value = $value->choice->staff->position->name_uz_latin;
          } else if ($dda->id == 2952) {
            $document_detail_attribute_value->attribute_value = $value->categorie;
          } else if ($dda->id == 2953) {
            $document_detail_attribute_value->attribute_value = $R;
          } else if ($dda->id == 2954) {
            $document_detail_attribute_value->attribute_value = $S;
          } else if ($dda->id == 2955) {
            $document_detail_attribute_value->attribute_value = $H;
          } else if ($dda->id == 2956) {
            $document_detail_attribute_value->attribute_value = $K;
          } else if ($dda->id == 2957) {
            $document_detail_attribute_value->attribute_value = $value->coefficient;
          } else if ($dda->id == 2958) {
            $document_detail_attribute_value->attribute_value = $value->shift == 1 ? 'C' : ($value->shift == 2 ? 'A' : $value->shift == 3 ? 'B' : $value->shift == 4 ? 'D' : '*');
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

      $document_signer = new DocumentSigner();
      $document_signer->document_id = $model->id;
      $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
      ;
      $document_signer->taken_datetime = date('Y-m-d H:i:s');
      $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
      $document_signer->action_type_id = 6;
      $document_signer->sequence = 100;
      $document_signer->signer_employee_id = Auth::user()->employee_id;
      ;
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
      $canSub = CandidateSubmitted::where('id', $selection)->first();
      $canSub->order_number = $model->pdf_file_name;
      // $canSub->order_number=$model->id;
      $canSub->save();
      DB::commit();
      return response()->json(["message" => "Model status successfully updated!", "data" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
    } catch (\Throwable $th) {
      DB::rollBack();
      throw $th;
      return response()->json(["message" => "Model status not successfully updated!", "data" => $th], 500);
    }
  }
  public function CandidatesСontractCreate(Request $request)
  {
    $selection = $request->input('id');
    $contractDate = $request->input('contractDate');
    $employeeStaff = Staff::select('*')->where('id', 39)
      ->with([
        'employeeStaff' => function ($q) {
          $q->with([
            'employee' => function ($q1) {
              $q1->where('is_active', 1);
            }
          ]);
          $q->orderBy('is_main_staff', 'DESC');
        }
      ])
      ->first();
    $hrDirector1 = $employeeStaff->employeeStaff[0]->employee->lastname_uz_latin . ' ' . $employeeStaff->employeeStaff[0]->employee->firstname_uz_latin . ' ' . $employeeStaff->employeeStaff[0]->employee->middlename_uz_latin;
    $hrDirectorShortName = $employeeStaff->employeeStaff[0]->employee->fio;
    DB::beginTransaction();
    try {
      $documents = SortByVacancie::query()
        ->whereNotNull('status')
        ->where('status', '!=', false)
        ->with('vacancies')
        ->whereHas('choice', function ($q) use ($selection) {
          $q->where('tanlov_id', $selection);
        })
        ->with('choice')
        ->with('message')
        ->orderBy('status', 'DESC')
        ->orderBy('id')
        ->get();
      foreach ($documents as $key => $value) {
        if (!$value->contract_id) {
          $vacant_fam = $value->vacancies->lastname_uz_latin;
          $vacant_ism = $value->vacancies->firstname_uz_latin;
          $vacant_shar = $value->vacancies->middlename_uz_latin;
          $vacant_full = $vacant_fam . ' ' . $vacant_ism . ' ' . $vacant_shar;
          $vacant_pass = $value->vacancies->passport_serial . $value->vacancies->passport_number;
          $vacant_short = substr($vacant_ism, 0, 1) . '. ' . substr($vacant_shar, 0, 1) . '. ' . $vacant_fam;
          $document_template = DocumentTemplate::with('documentDetailTemplates.documentDetailAttributes', 'documentSignerTemplates')->find(666);
          $model = new Document();
          $model->document_template_id = 666;
          $model->created_employee_id = Auth::user()->employee_id;
          $model->department_id = $document_template->department_id;
          $model->document_type_id = $document_template->document_type_id;
          $chois = str_pad($value->choice->tanlov->id, 6, "0", STR_PAD_LEFT);
          $model->title = $vacant_short . ' (' . $vacant_pass . ') Mehnat shartnomasi. Tanlov №' . $chois;
          $model->status = 1;
          $model->locale = 'uz_latin';
          $model->document_date = date('Y-m-d H:i:s');
          $model->pdf_file_name = $this->generateNanoId();
          $model->save();
          $content = $document_template->documentDetailTemplates[0]['content_uz_latin'];
          $content = str_replace('@sana', date('Y-m-d'), $content);
          $content = str_replace('@nomzodShortName', $vacant_short, $content);
          $content = str_replace('@nomozdFullName', $vacant_full, $content);
          $content = str_replace('@dodate', $contractDate, $content);
          $content = str_replace('@nomzodFamiliya', $vacant_fam, $content);
          $content = str_replace('@nomzodIsm', $vacant_ism, $content);
          $content = str_replace('@nomzodSharif', $vacant_shar, $content);

          $vacant_pass = $value->vacancies->passport_serial . $value->vacancies->passport_number;
          $content = str_replace('@pasportSerial', $vacant_pass, $content);
          $vacant_pass_date = date('Y-m-d', strtotime($value->vacancies->passport_date));
          $content = str_replace('@pasportGiveDate', $vacant_pass_date, $content);
          $vacant_pass_give = $value->vacancies->passport_give;
          $content = str_replace('@pasportGivePlace', $vacant_pass_give, $content);
          $vacant_pass_address = $value->vacancies->passport_address;
          $content = str_replace('@pasportAddress', $vacant_pass_address, $content);
          $vacant_INPS = $value->vacancies->INPS;
          $content = str_replace('@pasportINPS', $vacant_INPS, $content);
          $vacant_INN = $value->vacancies->INN;
          $content = str_replace('@pasportINN', $vacant_INN, $content);
          $content = str_replace('@nomzodTel', $value->vacancies->tel_first, $content);
          $content = str_replace('@kategoriya', $value->categorie, $content);
          $content = str_replace('@tabel', $value->tabelNumber, $content);
          $content = str_replace('@hrDirector1', $hrDirector1, $content);
          $content = str_replace('@hrDirectorShortName', $hrDirectorShortName, $content);

          $content_lavozm = $documents[0]->choice->staff->department->branch->name . ' ';
          $content_lavozm .= $documents[0]->choice->staff->department->functionalDepartment->name_uz_latin . ' ';
          $content_lavozm .= $documents[0]->choice->staff->department->name_uz_latin . ' ';
          $content_lavozm .= $documents[0]->choice->staff->position->name_uz_latin;
          $content = str_replace('@lavozim', $content_lavozm, $content);

          $document_detail = new DocumentDetail();
          $document_detail->document_id = $model->id;
          $document_detail->content = $content;
          $document_detail->save();

          $canSub = SortByVacancie::where('id', $value->id)->first();
          $canSub->contract_id = $model->id;
          $canSub->save();

          $document_signer = new DocumentSigner();
          $document_signer->document_id = $model->id;
          $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
          ;
          $document_signer->taken_datetime = date('Y-m-d H:i:s');
          $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
          $document_signer->action_type_id = 6;
          $document_signer->sequence = 100;
          $document_signer->signer_employee_id = Auth::user()->employee_id;
          ;
          $document_signer->status = 1;
          $document_signer->department = Auth::user()->employee->mainStaff[0]->department['name_' . $model->locale];
          $document_signer->position = Auth::user()->employee->mainStaff[0]->position['name_' . $model->locale];
          $document_signer->fio = Auth::user()->employee->getShortName($model->locale);

          $document_signer->save();

          $document_signer_event = new DocumentSignerEvent();
          $document_signer_event->document_signer_id = $document_signer->id;
          $document_signer_event->action_type_id = 6;
          $document_signer_event->comment = 'created';
          $document_signer_event->status = 1;
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
          DocumentSigner::where('document_id', $model->id)
            ->where('action_type_id', '!=', 6)
            ->update(['taken_datetime' => date('Y-m-d H:i:s'), 'due_date' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'))])
          ;
        }

      }
      DB::commit();
      return 1;
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
  public function setInformation(Request $request)
  {

    $model = SortByVacancie::where('id', (int) $request['id'])->first();
    if ((int) $request['type'] === 1) {
      $tabel = Employee::where('tabel', $request['info'])->first();
      if (!$tabel) {
        $tabell = SortByVacancie::where('tabelNumber', $request['info'])->first();
        if (!$tabell) {
          $model->created_tab_by = Auth::user()->employee_id;
          $model->tabelNumber = $request['info'];
          $model->created_tab_at = date('Y-m-d H:i:s');
          $model->save();
          return 1;
        }
        return 3;
      }
      return 2;
    }
    if ((int) $request['type'] === 2) {
      $model->categorie = $request['info'];
      $model->created_kat_at = Auth::user()->employee_id;
      $model->created_kat_by = date('Y-m-d H:i:s');
      $model->save();
    }
    if ((int) $request['type'] === 3) {
      $model->coefficient = $request['info'];
      $model->created_koy_at = Auth::user()->employee_id;
      $model->created_koy_by = date('Y-m-d H:i:s');
      $model->save();
    }
    if ((int) $request['type'] === 4) {
      $shift = strtoupper($request['info']);
      ;
      if ($shift == "C") {
        $model->shift = 1;
      } elseif ($shift == "A") {
        $model->shift = 2;
      } elseif ($shift == "B") {
        $model->shift = 3;
      } elseif ($shift == "D") {
        $model->shift = 4;
      } else {
        $model->shift = 5; // Agar belgilangan harflar C, A, B, D emas bo'lsa
      }
      $model->save();
    }
    return 1;
  }
  
  public function rejectListAdd(Request $request)
  {
    $filter = $request->input('filter');
    $model = itemsMessage::where('name', $filter)->first();
    if (!$model) {
      $model = new itemsMessage();
    }
    $model->name = $filter;
    $model->status = 1;
    $model->type = 2;
    $model->save();
    return 1;

  }
  public function rejectCandidate(Request $request)
  {
    $model = SortByVacancie::where('id', $request['item'])->first();
    if ($request['sort'] == 1) {
      $model->one_sorting_status = $request['type'];
      $model->status = $request['type'] !== 1 ? false : null;
      $model->status_type = $request['type'] !== 1 ? $request['type'] : null;
      $model->one_sorting_created_at = now()->format('Y-m-d H:i:s');
      $model->one_sorting_created_by = Auth::id();
    }
    if ($request['sort'] == 2) {
      $model->two_sorting_status = $request['type'];
      $model->status = $request['type'] !== 1 ? $request['type'] !== 9991 ? false : null : null;
      $model->status_type = $request['type'] !== 1 ? $request['type'] !== 9991 ? $request['type'] : null : null;
      $model->two_sorting_created_at = now()->format('Y-m-d H:i:s');
      $model->two_sorting_created_by = Auth::id();
    }
    if ($request['sort'] == 3) {
      $model->three_sorting_status = $request['type'];
      $model->status = $request['type'] !== 1 ? $request['type'] !== 9991 ? false : null : null;
      $model->status_type = $request['type'] !== 1 ? $request['type'] !== 9991 ? $request['type'] : null : null;
      $model->three_sorting_created_at = now()->format('Y-m-d H:i:s');
      $model->three_sorting_created_by = Auth::id();
    }
    if ($request['sort'] == 4) {
      $model->four_sorting_status = $request['type'];
      $model->status = $request['type'] !== 1 ? false : null;
      $model->status_type = $request['type'] !== 1 ? $request['type'] : null;
      $model->four_sorting_created_at = now()->format('Y-m-d H:i:s');
      $model->four_sorting_created_by = Auth::id();
    }
    if ($request['sort'] == 5) {
      $model->five_sorting_status = $request['type'];
      $model->status = $request['type'] === 1 ? true : false;
      $model->status_type = $request['type'] !== 1 ? $request['type'] : $request['type'];
      $model->five_sorting_created_at = now()->format('Y-m-d H:i:s');
      $model->five_sorting_created_by = Auth::id();
    }
    $model->save();
    return 1;
  }
  public function setMatchingCandidate(Request $request)
  {
    $language = $request->input('language');
    $page = $request->input('pagination')['page'];
    $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    $filter = $request->input('filter');
    $selection = (int) ($filter['selectioID']);
    $sort_model = SortByVacancie::
      with('vacancies')
      ->with('choice')
      ->where('status',true)
      ->whereHas('choice', function ($q) use ($selection) {
        $q->where('tanlov_id', $selection);
      })
      ->orderBy('id', 'asc')
    ;
    // $vacancy = $sort_model->get()->pluck('choice.tanlov_id')->flatten()->unique();
    $value = "";
    if (isset($filter['active'])) {
      $value = (int) $filter['active'];
      $sort_model->whereHas('choice', function ($q) use ($value) {
        if ($value === 1) {
          $q->where('status', '!=', 200);
        } elseif ($value != 1) {
          $q->where('status', '=', 200);
        }
      });
    }
    if (isset($filter['tanlov_id']) && $filter['tanlov_id'] != []) {
      $value = $filter['tanlov_id'];
      $sort_model->whereHas('choice', function ($q) use ($value) {
        $q->whereIn('tanlov_id', $value);
      });
    }
    if (isset($filter['status'])) {
      $status = (int) $filter['status'];
      if ($status === 0) {
        $sort_model->where('status', '!=', '1');
      } elseif ($status === 9999) {
        $sort_model->whereNull('status');
      } elseif ($status != 0) {
        $sort_model->where('status', $status);
      }
    }
    if (isset($filter['status1'])) {
      $status = (int) $filter['status1'];
      if ($status === 0) {
        $sort_model->where('one_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('one_sorting_status', $status);
      }
    }
    if (isset($filter['status2'])) {
      $status = (int) $filter['status2'];
      if ($status === 0) {
        $sort_model->where('two_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('two_sorting_status', $status);
      }
    }
    if (isset($filter['status3'])) {
      $status = (int) $filter['status3'];
      if ($status === 0) {
        $sort_model->where('three_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('three_sorting_status', $status);
      }
    }
    if (isset($filter['status4'])) {
      $status = (int) $filter['status4'];
      if ($status === 0) {
        $sort_model->where('four_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('four_sorting_status', $status == 2);
      }
    }
    if (isset($filter['status5'])) {
      $status = (int) $filter['status5'];
      if ($status === 0) {
        $sort_model->where('five_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('five_sorting_status', $status == 2);
      }
    }

    return [
      'candidate' => $sort_model->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
      // 'vacancy' => $vacancy,
      // 'value' => $value,
      'messages' => itemsMessage::get(),
    ];
  }
  public function setCandidate(Request $request)
  {
    $language = $request->input('language');
    $page = $request->input('pagination')['page'];
    $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    $filter = $request->input('filter');
    $selection = (int) ($filter['selectioID']);
    $sort_model = SortByVacancie::
      with('vacancies')
      ->with('choice')
      ->whereHas('choice', function ($q) use ($selection) {
        $q->where('tanlov_id', $selection);
      })
      ->orderBy('id', 'asc')
    ;
    // $vacancy = $sort_model->get()->pluck('choice.tanlov_id')->flatten()->unique();
    $value = "";
    if (isset($filter['active'])) {
      $value = (int) $filter['active'];
      $sort_model->whereHas('choice', function ($q) use ($value) {
        if ($value === 1) {
          $q->where('status', '!=', 200);
        } elseif ($value != 1) {
          $q->where('status', '=', 200);
        }
      });
    }
    if (isset($filter['tanlov_id']) && $filter['tanlov_id'] != []) {
      $value = $filter['tanlov_id'];
      $sort_model->whereHas('choice', function ($q) use ($value) {
        $q->whereIn('tanlov_id', $value);
      });
    }
    if (isset($filter['status'])) {
      $status = (int) $filter['status'];
      if ($status === 0) {
        $sort_model->where('status', '!=', '1');
      } elseif ($status === 9999) {
        $sort_model->whereNull('status');
      } elseif ($status != 0) {
        $sort_model->where('status', $status);
      }
    }
    if (isset($filter['status1'])) {
      $status = (int) $filter['status1'];
      if ($status === 0) {
        $sort_model->where('one_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('one_sorting_status', $status);
      }
    }
    if (isset($filter['status2'])) {
      $status = (int) $filter['status2'];
      if ($status === 0) {
        $sort_model->where('two_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('two_sorting_status', $status);
      }
    }
    if (isset($filter['status3'])) {
      $status = (int) $filter['status3'];
      if ($status === 0) {
        $sort_model->where('three_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('three_sorting_status', $status);
      }
    }
    if (isset($filter['status4'])) {
      $status = (int) $filter['status4'];
      if ($status === 0) {
        $sort_model->where('four_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('four_sorting_status', $status == 2);
      }
    }
    if (isset($filter['status5'])) {
      $status = (int) $filter['status5'];
      if ($status === 0) {
        $sort_model->where('five_sorting_status', '!=', '1');
      } elseif ($status != 0) {
        $sort_model->where('five_sorting_status', $status == 2);
      }
    }

    return [
      'candidate' => $sort_model->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
      // 'vacancy' => $vacancy,
      // 'value' => $value,
      'messages' => itemsMessage::get(),
    ];
  }


}
