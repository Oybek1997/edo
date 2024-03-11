<?php

namespace App\Http\Controllers;

use App\Http\Models\PositionType;
use App\Http\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //return PositionType::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $positionType = PositionType::select();
        
        if (isset($search)) {
            $positionType->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
                    
            });
        }
        return $positionType->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\PositionType  $PositionType
     * @return \Illuminate\Http\Response
     */
    public function show(PositionType $PositionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\PositionType  $PositionType
     * @return \Illuminate\Http\Response
     */
    public function edit(PositionType $PositionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\PositionType  $PositionType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PositionType $PositionType)
    {
        //
        $model = PositionType::find($request->input('id'));
        if (!$model) {
            $model = new PositionType();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->code = $request['code'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\PositionType  $PositionType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = PositionType::find($id);
        $model->delete();
    }

    public function gerStatPositionType()
    {
        $position_types = PositionType::get();
        foreach ($position_types as $key => $position_type) {
            $count = [];
            foreach (Branch::get() as $key => $branch) {
                $count[$branch->name] = DB::select("SELECT COUNT(*) as count FROM employee_staff
                WHERE staff_id in (SELECT id FROM staff
                WHERE position_id in (SELECT id FROM positions
                WHERE position_type_id = $position_type->id) and branch_id = $branch->id) and is_active = true");
            }
            $position_type->employee_count = $count;
        }

        return $position_types;
    }
}
