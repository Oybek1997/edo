<?php

namespace App\Http\Controllers;

use App\Http\Models\UnitMeasure;
use Illuminate\Http\Request;

class UnitMeasureController extends Controller
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
        $search = $request->input('search');
        $language = $request->input('language');
        $unitMeasure = UnitMeasure::select();
        
        if (isset($search)) {
            $unitMeasure->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%")
                    ->orWhere('short_name', 'like', "%" . $search . "%");
                    
            });
        }
        return $unitMeasure->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getMeasure()
    {
        return UnitMeasure::get();
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
     * @param  \App\Models\UnitMeasure  $unitMeasure
     * @return \Illuminate\Http\Response
     */
    public function show(UnitMeasure $unitMeasure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UnitMeasure  $unitMeasure
     * @return \Illuminate\Http\Response
     */
    public function edit(UnitMeasure $unitMeasure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UnitMeasure  $unitMeasure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UnitMeasure $unitMeasure)
    {
        $model = UnitMeasure::find($request->input('id'));
        if (!$model) {
            $model = new UnitMeasure();
	        // $model->created_by= Auth::id();
        } else {
	        // $model->updated_by = Auth::id();
        }
        $model->name = $request['name'];
        $model->short_name = $request['short_name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UnitMeasure  $unitMeasure
     * @return \Illuminate\Http\Response
     */
    public function destroy(UnitMeasure $unitMeasure, $id)
    {
        $model = UnitMeasure::find($id);
        $model->delete();
    }
}
