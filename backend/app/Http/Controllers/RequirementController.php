<?php

namespace App\Http\Controllers;

use App\Http\Models\Requirement;
use App\Http\Models\RequirementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $search = $request->input('search');
	    $itemsPerPage = $request->input('pagination')['itemsPerPage'];

	    $requirement = Requirement::with('requirementType');

        if (isset($search)) {
            $requirement->where(function (Builder $query) use ($search) {
                return $query                            
                            ->where('name_uz_latin', 'like', "%".$search."%")
                            ->orWhere('name_uz_cyril', 'like', "%".$search."%")
                            ->orWhere('name_ru', 'like', "%".$search."%");
            });
        }
        return [
            'requirements' =>$requirement->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'requirement_types'=>RequirementType::get()
            ];
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
     * @param  \App\Http\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function show(Requirement $requirement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function edit(Requirement $requirement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Requirement $requirement)
    {
        $model = Requirement::find($request->input('id'));
        if (!$model) {
            $model = new Requirement();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->requirement_type_id = $request['requirement_type_id'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Requirement::find($id);
        $model->delete();
    }
}
