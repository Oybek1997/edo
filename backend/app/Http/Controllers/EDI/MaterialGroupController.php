<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\MaterialGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;



class MaterialGroupController extends Controller
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

        $MaterialGroups = MaterialGroup::with([
                                                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                                                ]
                                            );

        if (isset($search)) {
            $MaterialGroups->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%")
                    ->orWhere('description', 'ilike', "%" . $search . "%");
            });
        }
        return $MaterialGroups->orderBY('name', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\MaterialGroup $MaterialGroup
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialGroup $MaterialGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\MaterialGroup $MaterialGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialGroup $MaterialGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\MaterialGroup $MaterialGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialGroup $MaterialGroup)
    {
        //return $request;
        //
        $model = MaterialGroup::find($request->input('id'));

        if (!$model) {
            $model = new MaterialGroup();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }

        $model->name = $request['name'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\MaterialGroup $MaterialGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //return $id;
        $model = MaterialGroup::find($id);
        // return $model;

        $model->delete();
    }
}
