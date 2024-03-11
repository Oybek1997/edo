<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\AccessType;

class AccessTypeController extends Controller
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
        $accessType = AccessType::select();

        if (isset($search)) {
            $accessType->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
            });
        }
        return $accessType->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\AccessType  $accessType
     * @return \Illuminate\Http\Response
     */
    public function show(AccessType $accessType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AccessType  $accessType
     * @return \Illuminate\Http\Response
     */
    public function edit(AccessType $accessType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccessType  $accessType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccessType $accessType)
    {
        $model = AccessType::find($request->input('id'));
        if (!$model) {
            $model = new AccessType();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccessType  $accessType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccessType $accessType, $id)
    {
        $model = AccessType::find($id);
        $model->delete();
    }
}
