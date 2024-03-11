<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\HospitalDiagnosis;
use App\Http\Models\RegistrationPeriodIllness;
use App\Http\Models\Employee;
use App\Http\Models\District;
use App\Http\Models\DiagnosisCode;
use Carbon\Carbon;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationPeriodIllnessController extends Controller
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
        $registrationPeriodIllness = RegistrationPeriodIllness::with('employee')
            ->with('createdBy')
            ->with('district.region')
            ->with('hospitalDiagnosis.diagnosisCode');


            
            if (isset($filter['department_code'])) {
                $registrationPeriodIllness->where('department_code', 'like', '%' . $filter['department_code'] . '%');
            }
            if (isset($filter['employee_department'])) {
                $registrationPeriodIllness->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
            }
            if (isset($filter['illness_list_serieses'])) {
                $registrationPeriodIllness->where('illness_list_serieses', 'like', '%' . $filter['illness_list_serieses'] . '%');
            }
            if (isset($filter['illness_list_numbers'])) {
                $registrationPeriodIllness->where('illness_list_numbers', 'like', '%' . $filter['illness_list_numbers'] . '%');
            }
            if (isset($filter['address'])) {
                $registrationPeriodIllness->where('address', 'like', '%' . $filter['address'] . '%');
            }
            if (isset($filter['begin_date'])) {
                $registrationPeriodIllness->where('begin_date', 'like', '%' . $filter['begin_date'] . '%');
            }
            if (isset($filter['end_date'])) {
                $registrationPeriodIllness->where('end_date', 'like', '%' . $filter['end_date'] . '%');
            }
            if (isset($filter['description'])) {
                $registrationPeriodIllness->where('description', 'like', '%' . $filter['description'] . '%');
            }
            if (isset($filter['created_at'])) {
                $registrationPeriodIllness->where('created_at', 'like', '%' . $filter['created_at'] . '%');
            }
            if (isset($filter['district_id'])) {
                $registrationPeriodIllness->where('district_id', 'like', '%' . $filter['district_id'] . '%');
            }
            if (isset($filter['hospital_diagnosis_id'])) {
                $registrationPeriodIllness->where('hospital_diagnosis_id', 'like', '%' . $filter['hospital_diagnosis_id'] . '%');
            }
            if ($created_by) {
                $registrationPeriodIllness->whereIn('created_by', User::where('username', 'like', '%' . $created_by . '%')->pluck('id')->toArray());
            }
            if (isset($filter['diagnosis_code_id'])) {
                $registrationPeriodIllness->whereHas('hospitalDiagnosis', function ($query) use ($filter) {
                    $query->where('diagnosis_code_id', $filter['diagnosis_code_id']);
                });
            }
            if ($employee_id) {
                $registrationPeriodIllness->whereIn('employee_id', Employee::where('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
            }
            if ($tabel) {
                $registrationPeriodIllness->whereIn('employee_id', Employee::where('tabel', 'like', '%'.$tabel.'%')->pluck('id')->toArray());
            }

            return $registrationPeriodIllness->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef($locale)
    {
        return Employee::select(
            'id',
            'tabel',
            $locale == 'uz_latin' ? 'firstname_uz_cyril' : 'firstname_uz_cyril',
            $locale == 'uz_latin' ? 'middlename_uz_cyril' : 'middlename_uz_cyril',
            $locale == 'uz_latin' ? 'lastname_uz_cyril' : 'lastname_uz_cyril'
        )->where('is_active', 1)->get();
    }

    public function getRefHospitalDiagnoses()
    {
        return HospitalDiagnosis::with('diagnosisCode')->get();
    }

    public function getRefDistricts()
    {
        return District::all();
    }

    public function report()
    {
        $medReports = DB::table('workflow_medpunkt.registration_period_illnesses')->count();
        // $medReportsThisMonth = DB::table('workflow_medpunkt.registration_patients')->where('created_at','like',$month.'%')->count();
        
        // return $call_type1ThisMonth;

        // $call_type2 = AmbulanceCall::where('created_at','like',$month.'%')->where('ambulance_call_type','=',2)->get();
        // $call_type3 = AmbulanceCall::where('created_at','like',$month.'%')->where('ambulance_call_type','=',3)->get();

        // $call_all = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls group by sanasi');
        // $call_type1 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 1 group by sanasi');
        // $call_type2 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 2 group by sanasi');
        // $call_type3 = DB::select('SELECT SUBSTRING(created_at,6,5) sanasi, count(id) soni FROM workflow_medpunkt.ambulance_calls WHERE ambulance_call_type = 3 group by sanasi');

        return [
            'medReports' => $medReports 
        ];
    }
    public function reportIllness()
    {
        // return 'API ishladi';
        
        //$illness =RegistrationPeriodIllness::select 
        // $illness = DB::select('SELECT hd.name, COUNT(rpi.id) Soni, SUM(rpi.day_difference) Summa  FROM workflow_medpunkt.registration_period_illnesses  rpi
        // inner join workflow_medpunkt.hospital_diagnoses hd
        // on hd.id = rpi.hospital_diagnosis_id
        // group by hospital_diagnosis_id');

        $illness = DB::connection('workflow_medpunkt')->select('
        SELECT hd.name, COUNT(rpi.id) AS Soni, SUM(rpi.day_difference) AS Summa
        FROM registration_period_illnesses rpi
        INNER JOIN hospital_diagnoses hd ON hd.id = rpi.hospital_diagnosis_id
        GROUP BY hd.name, rpi.hospital_diagnosis_id
       ');
    
         
        return [
            'illness' => $illness 
        ];
    }

    public function reportMothIllness($month)
    {
        // return 'API ishladi';

        // $illnessMonth = DB::connection('workflow_medpunkt')->table('registration_period_illnesses as rpi')
        //     ->select('hd.name', DB::raw('COUNT(rpi.id) as Soni'), DB::raw('SUM(rpi.day_difference) as Summa'))
        //     ->join('hospital_diagnoses as hd', 'hd.id', '=', 'rpi.hospital_diagnosis_id')
        //     ->where('rpi.created_at', 'like', $month.'%')
        //     ->groupBy('rpi.hospital_diagnosis_id')
        //     ->get();

        $illnessMonth = DB::connection('workflow_medpunkt')->table('registration_period_illnesses as rpi')
            ->select('hd.name', DB::raw('COUNT(rpi.id) as Soni'), DB::raw('SUM(rpi.day_difference) as Summa'))
            ->join('hospital_diagnoses as hd', 'hd.id', '=', 'rpi.hospital_diagnosis_id')
            ->where('rpi.created_at', 'like', $month.'%')
            ->groupBy('hd.name', 'rpi.hospital_diagnosis_id') // Include 'hd.name' in the GROUP BY clause
            ->get();

        return [
            'illnessMonth' => $illnessMonth 
        ];
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
     * @param  \App\Http\Models\RegistrationPeriodIllness $RegistrationPeriodIllness
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationPeriodIllness $RegistrationPeriodIllness)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\RegistrationPeriodIllness $RegistrationPeriodIllness
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationPeriodIllness $RegistrationPeriodIllness)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\RegistrationPeriodIllness $RegistrationPeriodIllness
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegistrationPeriodIllness $RegistrationPeriodIllness)
{
    $model = RegistrationPeriodIllness::find($request->input('id'));
    try {
        if (!$model) {
            $model = new RegistrationPeriodIllness();
            $model->created_by = Auth::id();
        } 

        // Mavjud maydonlarni yangilash
        $model->hospital_diagnosis_id = $request->input('hospital_diagnosis_id');
        $model->district_id = $request['district_id'];
        $model->employee_id = $request['employee_id'];
        $model->department_code = $request['department_code'];
        $model->employee_department = $request['employee_department'];
        $model->illness_list_serieses = $request['illness_list_serieses'];
        $model->illness_list_numbers = $request['illness_list_numbers'];
        $model->address = $request['address'];
        $model->begin_date = $request['begin_date'];
        $model->end_date = $request['end_date'];
        $model->description = $request['description'];

        // Sanalar orasidagi farqni hisoblash
        $beginDate = Carbon::parse($request['begin_date']);
        $endDate = Carbon::parse($request['end_date']);
        $model->day_difference = $endDate->diffInDays($beginDate);

        // Modelni saqlash
        $model->save();

        return response()->json($model, 200);
    }
    catch(\Throwable $th){
        return $th;
        return response()->json(['error' => 'Xato yuz berdi'], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\RegistrationPeriodIllness $RegistrationPeriodIllness
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationPeriodIllness $RegistrationPeriodIllness, $id)
    {
        $model = RegistrationPeriodIllness::find($id);
        $model->delete();
    }
}
