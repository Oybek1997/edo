<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Models\CompetenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetenceTypeController extends Controller
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
        $competenceType = CompetenceType::select();

        if (isset($filter)) {
            $competenceType->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }
        if (isset($filter)) {
            $competenceType->where(function ($query) use ($filter) {
                return $query
                    ->where('type', 'like', "%" . $filter['type'] . "%");
            });
        }
        // if (isset($filter['type'])) {
        //     $deviceHistory->where('type', 'like', '%' . $filter['type'] . '%');
        // }

        // $sortBy=$request->input('pagination')['sortBy'];
        // $sortDesc=$request->input('pagination')['sortDesc'];
        

        return $competenceType->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef()
    {
        return CompetenceType::all();
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
     * @param  \App\Http\Models\CompetenceType $CompetenceType
     * @return \Illuminate\Http\Response
     */
    public function show(CompetenceType $CompetenceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\CompetenceType $CompetenceType
     * @return \Illuminate\Http\Response
     */
    public function edit(CompetenceType $CompetenceType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\CompetenceType $CompetenceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompetenceType $CompetenceType)
    {
        $model = CompetenceType::find($request->input('id'));
        if (!$model) {
            $model = new CompetenceType();
            // $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->name = $request['name'];
        $model->type = $request['type'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\CompetenceType $CompetenceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompetenceType $CompetenceType, $id)
    {
        $model = CompetenceType::find($id);
        $model->delete();
    }
}
