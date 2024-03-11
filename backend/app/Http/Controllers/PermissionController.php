<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Permission::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $search = $request->input('search');
        $permission = Permission::select();

        if (isset($search)) {
            $permission->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%")
                    ->orWhere('display_name', 'like', "%" . $search . "%")
                    ->orWhere('description', 'like', "%" . $search . "%");
            });
        }
        return $permission->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function update(Request $request, Permission $permission)
    {
        //
        $model = Permission::find($request->input('id'));
        if (!$model) {
            $model = new Permission();
            //$model->created_by= Auth::id();
        } else {
            //$model->updated_by = Auth::id();
        }
        $model->name = $request['name'];
        $model->display_name = $request['display_name'];
        $model->description = $request['description'];
        $model->save();
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
        $model = Permission::find($id);
        $model->delete();
    }

    public function checkPermission(Request $request)
    {
        $permission = Permission::where('name', 'report_'.$request['permission'])->first();
        if ($permission) {
            return 1;
        } else {
            return 0;
        }
    }
}
