<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\AmbulanceCall;
use Illuminate\Http\Request;

class AmbulanceCallController extends Controller
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
        $ambulanceCalls = AmbulanceCall::query();

        return $ambulanceCalls->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
    
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
     * @param  \App\Http\Models\AmbulanceCall $AmbulanceCall
     * @return \Illuminate\Http\Response
     */
    public function show(AmbulanceCall $AmbulanceCall)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\AmbulanceCall $AmbulanceCall
     * @return \Illuminate\Http\Response
     */
    public function edit(AmbulanceCall $AmbulanceCall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\AmbulanceCall $AmbulanceCall
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AmbulanceCall $AmbulanceCall)
    {
        $model = AmbulanceCall::find($request->input('id'));
        if (!$model) {
            $model = new AmbulanceCall();
            $model->created_by= Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }
        $model->registration_patient_id = $request['registration_patient_id'];
        $model->ambulance_call_type = $request['ambulance_call_type'];
        $model->call_time = $request['call_time'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\AmbulanceCall $AmbulanceCall
     * @return \Illuminate\Http\Response
     */
    public function destroy(AmbulanceCall $AmbulanceCall, $id)
    {
        $model = AmbulanceCall::find($id);
        $model->delete();
    }
}
