<?php

namespace App\Http\Controllers;

use App\Http\Models\Directory;
use App\Http\Models\DirectoryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination') ? $request->input('pagination')['page'] : '';
        $itemsPerPage = $request->input('pagination') ? $request->input('pagination')['itemsPerPage'] : '';
        $search = $request->input('search');
        $filter = $request->input('filter');
        $directories = Directory::select();

        if (isset($search)) {
            $directories->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%")
                    ->orWhere('code', 'like', "%" . $search . "%");
            });
        }
        if (isset($filter) && is_array($filter)) {

            // if(Auth::id() == 2785){
            //     return $filter;
            // }
            $directories->where(function ($query) use ($filter) {
                return $query
                    ->whereIn('directory_type_id', $filter);
            });
        }
        if (isset($filter) && !is_array($filter)) {
            $directories->where(function ($query) use ($filter) {
                return $query
                    ->where('directory_type_id', $filter);
            });
        }
        // if (isset($page) || isset($itemsPerPage) ) {
            // return ['directories' => $directories->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            return ['directories' => $directories->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'directory_types' => DirectoryType::get()];
        // }
        // else {
        //     return $directories->orderBy('id');
        // }
        
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
     * @param  \App\Http\Models\Directory  $directory
     * @return \Illuminate\Http\Response
     */
    public function show(Directory $directory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Directory  $directory
     * @return \Illuminate\Http\Response
     */
    public function edit(Directory $directory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Directory  $directory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = Directory::find($request->input('id'));

        if (!$model) {
            $model = new Directory();
            $model->created_by = Auth::id();
        }
        $model->directory_type_id = $request->input('directory_type_id');
        $model->code = $request->input('code');
        $model->name_uz_latin = $request->input('name_uz_latin');
        $model->name_uz_cyril = $request->input('name_uz_cyril');
        $model->name_ru = $request->input('name_ru');
        $model->updated_by = Auth::id();
        $model->save();
        return 'Saved successfully!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Directory  $directory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Directory::find($id);
        $model->delete();
        return 'Deleted successfully!';
    }

    public function addType(Request $request)
    {
        $model = new DirectoryType();
        $model->name_uz_latin = $request->input('name_uz_latin');
        $model->name_uz_cyril = $request->input('name_uz_cyril');
        $model->name_ru = $request->input('name_ru');
        $model->created_by=Auth::id();
        // $model->created_at=date('Y-m-d H:i:s');
        $model->save();
        return $model;
    }

    public function barn(){
        return 
        $barn = Directory::where('directory_type_id', 41)->get();
    }
    public function yearmonth(){
        return 
        $barn = Directory::where('directory_type_id', 37)->get();
    }
}
