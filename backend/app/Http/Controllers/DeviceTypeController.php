<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\DeviceType;
use App\Http\Models\DeviceBranch;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
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
        $device_branch_id = $request->input('device_branch_id');
        $deviceType = DeviceType::with('deviceBranch')
            ->where('device_branch_id', $device_branch_id)
            ;

        if (isset($filter)) {
            $deviceType->where(function ($query) use ($filter) {
                return $query
                    ->where('type', 'like', "%" . $filter['type'] . "%");
            });
        }
        if (isset($filter['device_branch_id'])) {
            $deviceType->where('device_types.device_branch_id', $filter['device_branch_id']);
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];
        //dd($sortBy[0]);
        if(count($sortBy)>0){
            if($sortBy[0]=="name"){
            $deviceType->orderBy('device_types.type',$sortDesc[0] ? 'asc' : 'desc');
            }elseif($sortBy[0]=="name"){
            $deviceType->orderBy('device_types.device_branch_id',$sortDesc[0] ? 'asc' : 'desc');
            }
        }
        else {
            $deviceType->orderBy('id', 'desc');
        }
        return $deviceType->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRefBranch()
    {
        return DeviceBranch::select()->get();
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
     * @param  \App\Http\Models\DeviceType $DeviceType
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceType $DeviceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DeviceType $DeviceType
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceType $DeviceType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DeviceType $DeviceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceType $DeviceType)
    {
        $model = DeviceType::find($request->input('id'));
        if (!$model) {
            $model = new DeviceType();
            // $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->device_branch_id = $request['device_branch_id'];
        $model->type = $request['type'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DeviceType $DeviceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeviceType $DeviceType, $id)
    {
        $model = DeviceType::find($id);
        $model->delete();
    }
}
