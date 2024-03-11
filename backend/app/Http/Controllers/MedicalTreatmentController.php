<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\Medicine;
use App\Http\Models\MedicineCost;
use App\Http\Models\MedicalTreatment;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class MedicalTreatmentController extends Controller
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
        $medicalTreatments = MedicalTreatment::with('medicineCosts')
        ->with('createdBy');

        return $medicalTreatments->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
    }

    public function updateMedicineCost(Request $request, MedicalTreatment $MedicalTreatment)
    {
        $model = MedicineCost::find($request->input('id'));
        if (!$model) {
            $model = new MedicineCost();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->medical_treatment_id = $request['medical_treatment_id'];
        $model->medicine_id = $request['medicine_id'];
        $model->amount = $request['amount'];
        $model->unit_measure = $request['unit_measure'];
        $model->medicine_owner = $request['medicine_owner'];
        $model->description = $request['description'];
        $model->save();
        return MedicineCost::find($model->id);
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
     * @param  \App\Http\Models\MedicalTreatment $MedicalTreatment
     * @return \Illuminate\Http\Response
     */
    public function show(MedicalTreatment $MedicalTreatment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\MedicalTreatment $MedicalTreatment
     * @return \Illuminate\Http\Response
     */
    public function edit(MedicalTreatment $MedicalTreatment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\MedicalTreatment $MedicalTreatment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicalTreatment $MedicalTreatment)
    {
        $model = MedicalTreatment::find($request->input('id'));
        if (!$model) {
            $model = new MedicalTreatment();
            $model->created_by= Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }
        $model->registration_patient_id = $request['registration_patient_id'];
        $model->treatment_type = $request['treatment_type'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\MedicalTreatment $MedicalTreatment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalTreatment $MedicalTreatment, $id)
    {
        $model = MedicalTreatment::find($id);
        $model->delete();
    }
}
