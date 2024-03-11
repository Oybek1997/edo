<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\MedicineCost;
use App\Http\Models\Medicine;
use Illuminate\Http\Request;

class MedicineCostController extends Controller
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
        $medicineCosts = MedicineCost::query();

        return $medicineCosts->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
    }
    public function getRefMedicines()
    {
        return Medicine::all();
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
     * @param  \App\Http\Models\MedicineCost $MedicineCost
     * @return \Illuminate\Http\Response
     */
    public function show(MedicineCost $MedicineCost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\MedicineCost $MedicineCost
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\MedicineCost $MedicineCost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicineCost $MedicineCost)
    {
        $model = MedicineCost::find($request->input('id'));
        if (!$model) {
            $model = new MedicineCost();
            $model->created_by= Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }
        $model->medical_treatment_id = $request['medical_treatment_id'];
        $model->medicine_id = $request['medicine_id'];
        $model->amount = $request['amount'];
        $model->unit_measure = $request['unit_measure'];
        $model->medicine_owner = $request['medicine_owner'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\MedicineCost $MedicineCost
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicineCost $MedicineCost, $id)
    {
        $model = MedicineCost::find($id);
        $model->delete();
    }
}