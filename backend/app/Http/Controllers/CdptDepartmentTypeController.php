<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Models\CdptDepartmentType;
use Illuminate\Support\Facades\DB;

class CdptDepartmentTypeController extends Controller
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
        $departmentType = CdptDepartmentType::select();

        if (isset($filter)) {
            $departmentType->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }

        return $departmentType->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef()
    {
        return CdptDepartmentType::all();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CdptDepartmentType $CdptDepartmentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CdptDepartmentType $CdptDepartmentType)
    {
        $model = CdptDepartmentType::find($request->input('id'));
        if (!$model) {
            $model = new CdptDepartmentType();
            // $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->name = $request['name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CdptDepartmentType $CdptDepartmentType, $id)
    {
        $model = CdptDepartmentType::find($id);
        $model->delete();
    }
}
