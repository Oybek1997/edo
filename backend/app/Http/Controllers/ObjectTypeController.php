<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\ObjectType;
use Illuminate\Http\Request;

class ObjectTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $search = $request->input('search');
        $objectType = ObjectType::select();
        
        if (isset($search)) {
            $objectType->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%")
                    ->orWhere('controller', 'like', "%" . $search . "%");
            });
        }
        return $objectType->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\ObjectType  $objectType
     * @return \Illuminate\Http\Response
     */
    public function show(ObjectType $objectType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\ObjectType  $objectType
     * @return \Illuminate\Http\Response
     */
    public function edit(ObjectType $objectType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\ObjectType  $objectType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectType $objectType)
    {
        //

        $model = ObjectType::find($request->input('id'));
        if (!$model) {
            $model = new ObjectType();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->controller = $request['controller'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\ObjectType  $objectType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectType $objectType, $id)
    {
        //
        $model = ObjectType::find($id);
        $model->delete();
    }
}
