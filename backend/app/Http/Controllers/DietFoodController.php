<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\HospitalDiagnosis;
use App\Http\Models\DietFood;
use App\Http\Models\Employee;
use App\Http\Models\DiagnosisCode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DietFoodController extends Controller
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
        $dietFood = DietFood::with('employee')
            ->with('createdBy')
            ->with('hospitalDiagnosis.diagnosisCode');


            
            if (isset($filter['department_code'])) {
                $dietFood->where('department_code', 'like', '%' . $filter['department_code'] . '%');
            }
            if (isset($filter['employee_department'])) {
                $dietFood->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
            }
            if (isset($filter['begin_date'])) {
                $dietFood->where('begin_date', 'like', '%' . $filter['begin_date'] . '%');
            }
            if (isset($filter['end_date'])) {
                $dietFood->where('end_date', 'like', '%' . $filter['end_date'] . '%');
            }
            if (isset($filter['description'])) {
                $dietFood->where('description', 'like', '%' . $filter['description'] . '%');
            }
            if (isset($filter['created_at'])) {
                $dietFood->where('created_at', 'like', '%' . $filter['created_at'] . '%');
            }
            if (isset($filter['hospital_diagnosis_id'])) {
                $dietFood->where('hospital_diagnosis_id', 'like', '%' . $filter['hospital_diagnosis_id'] . '%');
            }
            if ($created_by) {
                $dietFood->whereIn('created_by', User::where('username', 'like', '%' . $created_by . '%')->pluck('id')->toArray());
            }
            if (isset($filter['diagnosis_code_id'])) {
                $dietFood->whereHas('hospitalDiagnosis', function ($query) use ($filter) {
                    $query->where('diagnosis_code_id', $filter['diagnosis_code_id']);
                });
            }
            if ($employee_id) {
                $dietFood->whereIn('employee_id', Employee::where('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
            }
            if ($tabel) {
                $dietFood->whereIn('employee_id', Employee::where('tabel', 'like', '%'.$tabel.'%')->pluck('id')->toArray());
            }

            return $dietFood->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\DietFood $DietFood
     * @return \Illuminate\Http\Response
     */
    public function show(DietFood $DietFood)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DietFood $DietFood
     * @return \Illuminate\Http\Response
     */
    public function edit(DietFood $DietFood)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DietFood $DietFood
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DietFood $DietFood)
    {
        $model = DietFood::find($request->input('id'));
        try{

            if (!$model) {
                $model = new DietFood();
                $model->created_by = Auth::id();
            } 
            $model->hospital_diagnosis_id = $request->input('hospital_diagnosis_id');
            $model->employee_id = $request['employee_id'];
            $model->department_code = $request['department_code'];
            $model->employee_department = $request['employee_department'];
            $model->begin_date = $request['begin_date'];
            $model->end_date = $request['end_date'];
            $model->description = $request['description'];
            return $model->save();
        }
        catch(\Throwable $th){
            return response()->json('xato', 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DietFood $DietFood
     * @return \Illuminate\Http\Response
     */
    public function destroy(DietFood $DietFood, $id)
    {
        $model = DietFood::find($id);
        $model->delete();
    }
}
