<?php

namespace App\Http\Controllers;

use App\Http\Models\HrUniversity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrUniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->input('search');
        $content = isset($filter) ? $filter : 0;

        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $hrUniversity = HrUniversity::whereNull('deleted_at')->orderBy('name_uz_cyril', 'ASC');

        if ($content) {
            $hrUniversity->where(function ($q) use ($content) {
                $q->where('name_uz_latin', 'like', '%' . $content . '%')
                    ->orWhere('name_uz_cyril', 'like', '%' . $content . '%')
                    ->orWhere('name_ru', 'like', '%' . $content . '%');
            });
        }

        return $hrUniversity->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function show(HrUniversity $HrUniversity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HrUniversity $HrUniversity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Http\Models\HrUniversity $HrUniversity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrUniversity $HrUniversity)
    {
        //
        $model = HrUniversity::find($request->input('id'));

        if(!$model){
            $model = new HrUniversity();
            $model->created_by=Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }

        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        // $model->updated_by = Auth::id();
        $model->save();

        return HrUniversity::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\models\HrUniversity $HrUniversity
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrUniversity $HrUniversity, $id)
    {
        //
        $model = HrUniversity::find($id);
        $model->delete();
    }
}
