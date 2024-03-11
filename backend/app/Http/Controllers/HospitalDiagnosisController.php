<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\HospitalDiagnosis;
use App\User;
use App\Http\Models\DiagnosisCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalDiagnosisController extends Controller
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
        $hospital_created_at = $request->input('created_at');
        $hospitalDiagnosis = HospitalDiagnosis::with('createdBy')
                                                ->with('diagnosisCode');

        if (isset($filter['diagnosis_code_id'])) {
            $hospitalDiagnosis->where('hospital_diagnoses.diagnosis_code_id', $filter['diagnosis_code_id']);
        }
        if (isset($filter['name'])) {
            $hospitalDiagnosis->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if (isset($filter['mkbx'])) {
            $hospitalDiagnosis->where('mkbx', 'like', '%' . $filter['mkbx'] . '%');
        }
        if (isset($filter['created_at'])) {
            $hospitalDiagnosis->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        if ($created_by) {
            $hospitalDiagnosis->whereIn('created_by', User::where('username', 'like', '%'.$created_by.'%')->pluck('id')->toArray());
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];

        if(count($sortBy)>0){
            if($sortBy[0]=="diagnosis_code.name"){
                $hospitalDiagnosis->orderBy('hospital_diagnoses.diagnosis_code_id',$sortDesc[0] ? 'asc' : 'desc');
            }
            elseif($sortBy[0]=="created_by.username"){
                $hospitalDiagnosis->orderBy('hospital_diagnoses.created_by',$sortDesc[0] ? 'asc' : 'desc');
            }
            else{
                $hospitalDiagnosis->orderBy($sortBy[0],  $sortDesc[0] ? 'desc' : 'asc');
            }
        }

        return $hospitalDiagnosis->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef()
    {
        return DiagnosisCode::all();
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
     * @param  \App\Http\Models\HospitalDiagnosis $HospitalDiagnosis
     * @return \Illuminate\Http\Response
     */
    public function show(HospitalDiagnosis $HospitalDiagnosis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\HospitalDiagnosis $HospitalDiagnosis
     * @return \Illuminate\Http\Response
     */
    public function edit(HospitalDiagnosis $HospitalDiagnosis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HospitalDiagnosis $HospitalDiagnosis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HospitalDiagnosis $HospitalDiagnosis)
    {
        $model = HospitalDiagnosis::find($request->input('id'));
        if (!$model) {
            $model = new HospitalDiagnosis();
            $model->created_by= Auth::id();
        }
        $model->diagnosis_code_id = $request['diagnosis_code_id'];
        $model->name = $request['name'];
        $model->mkbx = $request['mkbx'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\HospitalDiagnosis $HospitalDiagnosis
     * @return \Illuminate\Http\Response
     */
    public function destroy(HospitalDiagnosis $HospitalDiagnosis, $id)
    {
        $model = HospitalDiagnosis::find($id);
        $model->delete();
    }
}
