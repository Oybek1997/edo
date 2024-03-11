<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\CompetenceType;
use App\Http\Models\SpecificSkill;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FunctionalKnowledgeController extends Controller
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
        $employee_id = $filter['lastname_uz_latin'];
        // $tabel = $filter['tabel'];
        $specificSkill = SpecificSkill::with('employee')->with('competenceType')->where('competenceType.type', '==', '2');

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
            $competenceType->where('competenceType.competence_type_id', $filter['competence_type_id']);
        }
        if ($employee_id) {
            $specificSkill->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }

        return $specificSkill->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
