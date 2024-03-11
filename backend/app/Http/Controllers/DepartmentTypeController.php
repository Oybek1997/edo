<?php

namespace App\Http\Controllers;

use App\Http\Models\Country;
use App\Http\Models\Department;
use App\Http\Models\DepartmentType;
use App\Http\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // return DepartmentType::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $departmentType = DepartmentType::select();

        if (isset($search)) {
            $departmentType->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
            });
        }
        return $departmentType->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\DepartmentType  $departmentType
     * @return \Illuminate\Http\Response
     */
    public function show(DepartmentType $departmentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DepartmentType  $departmentType
     * @return \Illuminate\Http\Response
     */
    public function edit(DepartmentType $departmentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DepartmentType  $departmentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DepartmentType $departmentType)
    {
        //
        $model = DepartmentType::find($request->input('id'));
        if (!$model) {
            $model = new DepartmentType();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->sequence = $request['sequence'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DepartmentType  $departmentType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = DepartmentType::find($id);
        $model->delete();
    }

    public function departmentTypeJointVenture()
    {
        return [
            'department_type' => DepartmentType::whereNotNull('type')->get(),
            'positions' => Position::where('company_id',124)->get(),
            'countries' => Country::select('id', 'name_uz_latin', 'name_uz_cyril', 'name_ru')
                ->with(['regions' => function ($q) {
                    $q->select('id', 'country_id', 'name_uz_latin', 'name_uz_cyril', 'name_ru');
                }])->get()
        ];
    }
}
