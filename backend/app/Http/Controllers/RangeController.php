<?php

namespace App\Http\Controllers;

use App\http\models\Coefficient;
use App\Http\Models\Range;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Range::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $range = Range::select();
        
        if (isset($search)) {
            $range->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
                    
            });
        }
        return $range->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
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
     * @param  \App\http\models\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function show(Range $range)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function edit(Range $range)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Range $range)
    {
        $model = Range::find($request->input('id'));
        if (!$model) {
            $model = new Range();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->code = $request['code'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->minfond = $request['minfond'];
        $model->maxfond = $request['maxfond'];
        $model->order_date = $request['order_date'];
        $model->order_number = $request['order_number'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Range::find($id);
        $model->delete();
    }
}
