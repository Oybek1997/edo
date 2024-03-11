<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\DiagnosisCode;
use Illuminate\Http\Request;

class DiagnosisCodeController extends Controller
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
        $diagnosisCode = DiagnosisCode::query();

        if (isset($filter)) {
            $diagnosisCode->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];

        if(count($sortBy)>0){
            $diagnosisCode->orderBy('diagnosis_codes.name',$sortDesc[0] ? 'desc' : 'asc');
        }
        else {
            $diagnosisCode->orderBy('id', 'asc');
        }
        return $diagnosisCode->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\DiagnosisCode $DiagnosisCode
     * @return \Illuminate\Http\Response
     */
    public function show(DiagnosisCode $DiagnosisCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DiagnosisCode $DiagnosisCode
     * @return \Illuminate\Http\Response
     */
    public function edit(DiagnosisCode $DiagnosisCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DiagnosisCode $DiagnosisCode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DiagnosisCode $DiagnosisCode)
    {
        $model = DiagnosisCode::find($request->input('id'));
        if (!$model) {
            $model = new DiagnosisCode();
        }
        $model->name = $request['name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DiagnosisCode $DiagnosisCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiagnosisCode $DiagnosisCode, $id)
    {
        $model = DiagnosisCode::find($id);
        $model->delete();
    }
}
