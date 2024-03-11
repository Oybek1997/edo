<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\HospitalDiagnosis;
use App\Http\Models\RegistrationPatient;
use App\Http\Models\MedicalTreatment;
use App\Http\Models\Employee;
use App\Http\Models\DiagnosisCode;
use App\Http\Models\AmbulanceCall;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationPatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $created_by = $filter['username'];
        $employee_id = $filter['lastname_uz_cyril'];
        $tabel = $filter['tabel'];
        $registrationPatient = RegistrationPatient::with('employee')
            ->with('createdBy')
            ->with('ambulanceCalls')
            ->with('medicalTreatments')
            ->with(['medicalTreatments' => function ($q) {
                $q->with(['medicineCosts' => function ($v){
                    $v->with('medicines');
                }]);
            }])
            ->with('hospitalDiagnosis.diagnosisCode');

            
            if (isset($filter['department_code'])) {
                $registrationPatient->where('department_code', 'like', '%' . $filter['department_code'] . '%');
            }
            if (isset($filter['type'])) {
                $registrationPatient->where('type', 'like', '%' . $filter['type'] . '%');
            }
            if (isset($filter['employee_department'])) {
                $registrationPatient->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
            }
           
            if (isset($filter['diagnosis'])) {
                $registrationPatient->where('diagnosis', 'like', '%' . $filter['diagnosis'] . '%');
            }
            if (isset($filter['description'])) {
                $registrationPatient->where('description', 'like', '%' . $filter['description'] . '%');
            }
            if (isset($filter['created_at'])) {
                $registrationPatient->where('created_at', 'like', '%' . $filter['created_at'] . '%');

            }
            if (isset($filter['hospital_diagnosis_id'])) {
                $registrationPatient->where('hospital_diagnosis_id', 'like', '%' . $filter['hospital_diagnosis_id'] . '%');
            }
            if ($created_by) {
                $registrationPatient->whereIn('created_by', User::where('username', 'like', '%' . $created_by . '%')->pluck('id')->toArray());
            }
            if ($employee_id) {
                $registrationPatient->whereIn('employee_id', Employee::where('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
            }
            if ($tabel) {
                $registrationPatient->whereIn('employee_id', Employee::where('tabel', 'like', '%'.$tabel.'%')->pluck('id')->toArray());
            }

            return $registrationPatient->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
    }


    public function updateAmbulanceCall(Request $request, RegistrationPatient $RegistrationPatient)
    {
        $model = AmbulanceCall::find($request->input('id'));
        if (!$model) {
            $model = new AmbulanceCall();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->registration_patient_id = $request['registration_patient_id'];
        $model->ambulance_call_type = $request['ambulance_call_type'];
        $model->call_time = $request['call_time'];
        $model->description = $request['description'];
        $model->save();
        return AmbulanceCall::find($model->id);
    }

    public function updateMedicalTreatment(Request $request, RegistrationPatient $RegistrationPatient)
    {
        $model = MedicalTreatment::find($request->input('id'));
        if (!$model) {
            $model = new MedicalTreatment();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->registration_patient_id = $request['registration_patient_id'];
        $model->treatment_type = $request['treatment_type'];
        $model->description = $request['description'];
        $model->save();
        return MedicalTreatment::find($model->id);
    }

    public function report($month)
    {

        //$medReports =RegistrationPatient::select('*')->count();
        //dd( $medReports);
        $medReports = DB::connection('workflow_medpunkt')->table('registration_patients')->count();
        $medReportsThisMonth = DB::connection('workflow_medpunkt')->table('registration_patients')->where('created_at','like',$month.'%')->count();

        $permissions = DB::connection('workflow_medpunkt')->table('registration_patients')->where('type','=',1)->count();
        $permissionsThisMonth = DB::connection('workflow_medpunkt')->table('registration_patients')->where('created_at','like',$month.'%')->where('type','=',1)->count();

        $referrals = DB::connection('workflow_medpunkt')->table('registration_patients')->where('type','=',2)->count();
        $referralsThisMonth = DB::connection('workflow_medpunkt')->table('registration_patients')->where('created_at','like',$month.'%')->where('type','=',2)->count();

        $treatment = MedicalTreatment::where('created_at','like',$month.'%')->where('treatment_type','=',1)->get();
        $connection = MedicalTreatment::where('created_at','like',$month.'%')->where('treatment_type','=',2)->get();
        $physiotherapy = MedicalTreatment::where('created_at','like',$month.'%')->where('treatment_type','=',3)->get();
        
        $call_all = DB::connection('workflow_medpunkt')->table('ambulance_calls')->count();
        $call_allThisMonth = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('created_at','like',$month.'%')->count();

        $call_type1 = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',1)->count();
        $call_type1ThisMonth = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',1)->where('created_at','like',$month.'%')->count();

        $call_type2 = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',2)->count();
        $call_type2ThisMonth = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',2)->where('created_at','like',$month.'%')->count();

        $call_type3 = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',3)->count();
        $call_type3ThisMonth = DB::connection('workflow_medpunkt')->table('ambulance_calls')->where('ambulance_call_type','=',3)->where('created_at','like',$month.'%')->count();
        
        // return $call_type1ThisMonth;

        // $call_type2 = AmbulanceCall::where('created_at','like',$month.'%')->where('ambulance_call_type','=',2)->get();
        // $call_type3 = AmbulanceCall::where('created_at','like',$month.'%')->where('ambulance_call_type','=',3)->get();

        // $call_all = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls group by sanasi');
        // $call_type1 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 1 group by sanasi');
        // $call_type2 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 2 group by sanasi');
        // $call_type3 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 3 group by sanasi');

        return [
            'medReports' => $medReports, 
            'medReportsThisMonth' => $medReportsThisMonth,
            'permissions' => $permissions, 
            'permissionsThisMonth' => $permissionsThisMonth,
            'referrals' => $referrals, 
            'referralsThisMonth' => $referralsThisMonth,
            'call_all' => $call_all, 
            'call_allThisMonth' => $call_allThisMonth,
            'call_type1' => $call_type1,
            'call_type1ThisMonth' => $call_type1ThisMonth,
            'call_type2' => $call_type2,
            'call_type2ThisMonth' => $call_type2ThisMonth,
            'call_type3' => $call_type3,
            'call_type3ThisMonth' => $call_type3ThisMonth, 
            'treatment' => $treatment,
            'connection' => $connection,
            'call_all' => $call_all, 
            'call_type1' => $call_type1, 
            'call_type2' => $call_type2, 
            'call_type3' => $call_type3];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\RegistrationPatient $RegistrationPatient
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationPatient $RegistrationPatient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\RegistrationPatient $RegistrationPatient
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationPatient $RegistrationPatient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\RegistrationPatient $RegistrationPatient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegistrationPatient $RegistrationPatient)
    {
        $model = RegistrationPatient::find($request->input('id'));
        if (!$model) {
            $model = new RegistrationPatient();
            $model->created_by = Auth::id();
        } 
        $model->hospital_diagnosis_id = $request->input('hospital_diagnosis_id');
        $model->employee_id = $request['employee_id'];
        $model->department_code = $request['department_code'];
        $model->employee_department = $request['employee_department'];
        $model->type = $request['type'];
        $model->diagnosis = $request['diagnosis'];
        $model->description = $request['description'];
        return $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\RegistrationPatient $RegistrationPatient
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationPatient $RegistrationPatient, $id)
    {
        $model = RegistrationPatient::find($id);
        $model->delete();
    }
}
