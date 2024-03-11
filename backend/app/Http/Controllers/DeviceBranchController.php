<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\DeviceBranch;
use Illuminate\Http\Request;

class DeviceBranchController extends Controller
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
        $deviceBranch = DeviceBranch::select();

        if (isset($filter)) {
            $deviceBranch->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];

        if(count($sortBy)>0){
            if($sortBy[0]=="name"){
                $deviceBranch->orderBy('device_branches.name',$sortDesc[0] ? 'asc' : 'desc');
            }
        }
        else {
            $deviceBranch->orderBy('id', 'desc');
        }

        return $deviceBranch->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function all()
    {
        $branches = DeviceBranch::get();
        $my_branches = [];
        foreach ($branches as $key => $value) {
            if(Auth::user()->isAbleTo('orgtex-'.strtolower($value->name))){
                $my_branches[] = $value;
            }
        }
        return $my_branches;
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
     * @param  \App\Http\Models\DeviceBranch $DeviceBranch
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceBranch $DeviceBranch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DeviceBranch $DeviceBranch
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceBranch $DeviceBranch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DeviceBranch $DeviceBranch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceBranch $DeviceBranch)
    {
        $model = DeviceBranch::find($request->input('id'));
        if (!$model) {
            $model = new DeviceBranch();
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
    public function destroy(DeviceBranch $DeviceBranch, $id)
    {
        $model = DeviceBranch::find($id);
        $model->delete();
    }
}
