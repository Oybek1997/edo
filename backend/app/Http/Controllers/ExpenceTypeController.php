<?php

namespace App\Http\Controllers;

use App\Http\Models\ExpenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ExpenceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        //return ExpenceType::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $expenceType = ExpenceType::select();
        
        if (isset($search)) {
            $expenceType->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
                    
            });
        }
        return $expenceType->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
                
       
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
     * @param  \App\Http\Models\ExpenceType  $ExpenceType
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenceType $ExpenceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ExpenceType  $ExpenceType
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenceType $ExpenceType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ExpenceType  $ExpenceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenceType $ExpenceType)
    {
        //
        $model = ExpenceType::find($request->input('id'));
        if (!$model) {
            $model = new ExpenceType();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\ExpenceType  $ExpenceType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = ExpenceType::find($id);
        $model->delete();
    }
}
