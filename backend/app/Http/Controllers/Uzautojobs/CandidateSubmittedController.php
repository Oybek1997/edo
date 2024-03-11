<?php

namespace App\Http\Controllers\Uzautojobs;

use App\Http\Models\Uzautojobs\CandidateSubmitted;
use App\Http\Models\Uzautojobs\CandidateSelected;
use App\Http\Models\Uzautojobs\SelectionStatus;
use App\Http\Models\Uzautojobs\SortByVacancie;
use App\Http\Models\Staff;
use App\Http\Models\Uzautojobs\Vacancies;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CandidateSubmittedController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function setvacancies(Request $request)
  {
    $model = Vacancies::where('INPS', $request['INPS'])->first();
    if (!$model) {
      $model = new Vacancies();
    }
    $model->firstname_uz_latin = $request['firstname_uz_latin'];
    $model->lastname_uz_latin = $request['lastname_uz_latin'];
    $model->middlename_uz_latin = $request['middlename_uz_latin'];
    $model->born_date = $request['born_date'];
    $model->gender = (int)$request['gender'];
    $model->INN = (int)$request['INN'];
    $model->INPS = (int)$request['INPS'];
    $model->email = $request['email'];
    $model->experience = (int)$request['experience'];
    $model->passport_serial = $request['passport_serial'];
    $model->passport_number = (int)$request['passport_number'];
    $model->passport_region = $request['passport_region'];
    $model->passport_town = $request['passport_town'];
    $model->passport_address = $request['passport_address'];
    $model->nationality = $request['nationality'];
    $model->knowledge_name = $request['knowledge_name'];
    $model->knowledge_direction = $request['knowledge_direction'];
    $model->knowledge_specialty = $request['knowledge_specialty'];
    $model->knowledge_year = $request['knowledge_year'];
    $model->knowledge_type = $request['knowledge_type'];
    $model->knowledge_serial = $request['knowledge_serial'];
    $model->knowledge_number = (int)$request['knowledge_number'];
    $model->tel_first = $request['tel_first'];
    $model->tel_second = $request['tel_second'];
    $model->language_skills_first = $request['language_skills_first'];
    $model->language_skills_second = $request['language_skills_second'];
    $model->status = $request['status'];
    $model->uzJobPersonID = $request['uzJobPersonID'];
    $model->created_at =now();
    if($model->save())
    {
      $model_sortByVac=new SortByVacancie();
      $model_sortByVac->uzautoJobs_id=$request['jobID'];
      $model_sortByVac->vacancies_id = $model->id;
      $model_sortByVac->created_at =now();
      $model_sortByVac->save();
      return [
        'message' => 'Successfully sending',
        'dateTime' => now()->format('Y-m-d H:i:s'),
        'code' => '200',
      ];
    };
    return 0;

  }
  public function updateCandidate(Request $request)
  {    
    $model_old = SelectionStatus::where('tanlov_id', $request['tanlov_id'])
    ->orderBy('id', 'DESC')
    ->first();
    $model = new SelectionStatus();
    $model->uzautoJobs_id = $request['job_id'];
    $model->status = $request['job_status'];
    $model->tanlov_id = $model_old['tanlov_id'];
    $model->staff_id = $model_old['staff_id'];
    $model->created_at = now();
    $model->save();
    return [
      'message' => 'Successfully sending',
      'dateTime' => now()->format('Y-m-d H:i:s'),
      'code' => '200',
    ];
  }
  public function setCandidate(Request $request)
  {
    $inputs = $request->input('data');
    $staffID = $inputs['staff_id'];
    $json_value = [
      "data" => $inputs,
    ];
    $curl = curl_init();
    $curlOptions = [
      CURLOPT_URL => "https://testjobs.uzautomotors.com/api/candidate/set-submitted",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($json_value),
      CURLOPT_HTTPHEADER => [
        "Authorization: BraveLion%TgHNSY(AZZAny*7cx3yV(3jp7dhV3Afu*Q%RE2XGerzhJsmM",
        "Content-Type: application/json",
        "token: BraveLion%TgHNSY(AZZAny*7cx3yV(3jp7dhV3Afu*Q%RE2XGerzhJsmM",
      ],
    ];
    curl_setopt_array($curl, $curlOptions);
    $response = json_decode(curl_exec($curl));
    $err = curl_error($curl);
    curl_close($curl);
    if ($response->status == 201) {
      $model = new CandidateSubmitted();
      $model->company_id = $inputs['company_id'];
      $model->depatament_name = $inputs['depatament_name'];
      $model->depatament_code = $inputs['depatament_code'];
      $model->position_name = $inputs['position_name'];
      $model->position_code = $inputs['position_code'];
      $model->staf_min_req = $inputs['staf_min_req'];
      $model->send_count = (int) $inputs['send_count'];
      $model->staff_id = (int) $staffID;
      if ($model->save()) {
        $model_status = new SelectionStatus();
        $model_status->tanlov_id = $model->id;
        $model_status->status = 201;
        $model_status->created_by = Auth::id();
        $model_status->created_at = now();
        $model_status->uzautoJobs_id = $response->jobs_id;
        $model_status->staff_id = (int) $staffID;
        $model_status->save();
      }
      if ($model_status->save()) {
        $modelStaff = Staff::find((int) $staffID);
        $modelStaff->rate_count_critical = 0;
        $modelStaff->rate_count_sv += (int) $inputs['send_count'];
        $modelStaff->save();
      }
    }
    return [
      'message' => 'Successfully sending',
      'dateTime' => now()->format('Y-m-d H:i:s'),
      'code' => '200',
    ];
  }


}
