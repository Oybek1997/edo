<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
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
        $medicines = Medicine::query();

        if (isset($filter)) {
            $medicines->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];

        if(count($sortBy)>0){
            $medicines->orderBy('medicines.name',$sortDesc[0] ? 'desc' : 'asc');
        }
        else {
            $medicines->orderBy('id', 'asc');
        }
        return $medicines->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    
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
     * @param  \App\Http\Models\Medicine $Medicine
     * @return \Illuminate\Http\Response
     */
    public function show(Medicine $Medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Medicine $Medicine
     * @return \Illuminate\Http\Response
     */
    public function edit(Medicine $Medicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Medicine $Medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medicine $Medicine)
    {
        $model = Medicine::find($request->input('id'));
        if (!$model) {
            $model = new Medicine();
        }
        $model->name = $request['name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Medicine $Medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medicine $Medicine, $id)
    {
        $model = Medicine::find($id);
        $model->delete();
    }
}
