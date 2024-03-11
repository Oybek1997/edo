<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\CompetenceType;
use App\Http\Models\SpecificSkill;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecificSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type =  $request['id'];
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $employee_id = $filter['lastname_uz_latin'];
        $competence_type_id = $filter['name'];
        // $tabel = $filter['tabel'];
        $specificSkill = SpecificSkill::with('employee')->with('competenceType')->join('competence_types', 'competence_types.id', '=', 'specific_skills.competence_type_id')->where('competence_types.type', '=', $type);

        if (isset($filter['name'])) {
            $specificSkill->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if (isset($filter['evaluation_self'])) {
            $specificSkill->where('evaluation_self', 'like', '%' . $filter['evaluation_self'] . '%');
        }
        if (isset($filter['evaluation_chief'])) {
            $specificSkill->where('evaluation_chief', 'like', '%' . $filter['evaluation_chief'] . '%');
        }
        if (isset($filter['evaluation_hr'])) {
            $specificSkill->where('evaluation_hr', 'like', '%' . $filter['evaluation_hr'] . '%');
        }
        if (isset($filter['competence_type_id'])) {
            $specificSkill->where('competence_type_id', 'like', '%' . $filter['competence_type_id'] . '%');
        }
        if ($employee_id) {
            $specificSkill->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }

        return $specificSkill->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    // public function getRef($locale)
    // {
    //     return Employee::select(
    //         'id',
    //         'tabel',
    //         $locale == 'uz_latin' ? 'firstname_uz_latin' : 'firstname_uz_cyril',
    //         $locale == 'uz_latin' ? 'middlename_uz_latin' : 'middlename_uz_cyril',
    //         $locale == 'uz_latin' ? 'lastname_uz_latin' : 'lastname_uz_cyril'
    //     )->where('is_active', 1)->get();
    // }

    
    

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
    public function show(SpecificSkill $SpecificSkill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecificSkill $SpecificSkill)
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
    public function update(Request $request, SpecificSkill $SpecificSkill)
    {
        $model = SpecificSkill::find($request->input('id'));
        if (!$model) {
            $model = new SpecificSkill();
            $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->competence_type_id = $request['competence_type_id'];
        $model->employee_id = $request['employee_id'];
        $model->name = $request['name'];
        $model->evaluation_self = $request['evaluation_self'];
        $model->evaluation_chief = $request['evaluation_chief'];
        $model->evaluation_hr = $request['evaluation_hr'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpecificSkill $SpecificSkill, $id)
    {
        $model = SpecificSkill::find($id);
        $model->delete();
    }
}
