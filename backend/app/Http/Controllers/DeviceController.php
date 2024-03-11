<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\Device;
use App\User;
use App\Http\Models\DeviceType;
use App\Http\Models\DeviceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
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
        $created_by = $filter['username'];
        $device_branch_id = $request->input('device_branch_id');
        $device_created_at = $request->input('created_at');
        $device = Device::with('createdBy')
                        ->with('history')
                        ->with('deviceType')
                        ->where('devices.device_branch_id', $device_branch_id)
                        ;

        //if (isset($filter)) {
         //   $device->where(function ($query) use ($filter) {
          //      return $query
                    //->where('model', 'like', "%" . $filter['model'] . "%")
                    //->where('serial_number', 'like', "%" . $filter['serial_number'] . "%")
                    //->where('inventory_number', 'like', "%" . $filter['inventory_number'] . "%")
                    //->where('created_at', 'like', "%" . $filter['created_at'] . "%")
                    //->where('created_at', 'like', "%" . $filter['created_at'] . "%")
                    //->where('first_use_date', 'like', "%" . $filter['first_use_date'] . "%");
           // });
       // }
        if (isset($filter['device_type_id'])) {
            $device->where('devices.device_type_id', $filter['device_type_id']);
        }
        if (isset($filter['model'])) {
            $device->where('model', 'like', '%' . $filter['model'] . '%');
        }
        if (isset($filter['serial_number'])) {
            $device->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
        }
        if (isset($filter['inventory_number'])) {
            $device->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
        }
        if (isset($filter['created_at'])) {
            $device->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        if (isset($filter['first_use_date'])) {
            $device->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
        }
        if ($created_by) {
            $device->whereIn('created_by', User::where('username', 'like', '%'.$created_by.'%')->pluck('id')->toArray());
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];
        //dd($sortDesc);

        if(count($sortBy)>0){
            if($sortBy[0]=="device_type.type"){
                $device->orderBy('devices.device_type_id',$sortDesc[0] ? 'asc' : 'desc');
            }
            elseif($sortBy[0]=="created_by.username"){
                $device->orderBy('devices.created_by',$sortDesc[0] ? 'asc' : 'desc');
            }
            else{
                $device->orderBy($sortBy[0],  $sortDesc[0] ? 'desc' : 'asc');
            }
        }

        return $device->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function myDevices(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $created_by = $filter['username'];
        $device_branch_id = $request->input('device_branch_id');
        $device = Device::with('createdBy')
                        ->with('lastHistory')
                        ->where('device_branch_id', $device_branch_id)
                        ;

        //if (isset($filter)) {
          //  $device->where(function ($query) use ($filter) {
            //    return $query
                    //->where('model', 'like', "%" . $filter['model'] . "%")
                    //->where('serial_number', 'like', "%" . $filter['serial_number'] . "%")
                    //->where('inventory_number', 'like', "%" . $filter['inventory_number'] . "%")
                   // ->where('created_at', 'like', "%" . $filter['created_at'] . "%")
                    //->where('created_at', 'like', "%" . $filter['created_at'] . "%")
                    //->where('first_use_date', 'like', "%" . $filter['first_use_date'] . "%");
           // });
       // }
        if (isset($filter['model'])) {
            $device->where('model', 'like', '%' . $filter['model'] . '%');
        }
        if (isset($filter['serial_number'])) {
            $device->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
        }
        if (isset($filter['inventory_number'])) {
            $device->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
        }
        if (isset($filter['created_at'])) {
            $device->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        if (isset($filter['first_use_date'])) {
            $device->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
        }
        if (isset($filter['device_type_id'])) {
            $device->where('devices.device_type_id', $filter['device_type_id']);
        }
        if ($created_by) {
            $device->whereIn('created_by', User::where('username', 'like', '%'.$created_by.'%')->pluck('id')->toArray());
        }
        return $device->orderBy('id')->with('deviceType')->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function status(Request $request)
    {
       // dd($request);
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $created_by = $filter['username'];
        $device_branch_id = $request->input('device_branch_id');
        return 1;
        return $device = DeviceHistory::select(DB::raw('device_histories.*, count(id) as total_users'))
                        ->with('createdBy')
                        ->with('device')
                        ->groupBy('device_id')
                        ->paginate();

       // if (isset($filter)) {
           // $device->where(function ($query) use ($filter) {
              //  return $query
                    //->where('model', 'like', "%" . $filter['model'] . "%")
                    //->where('serial_number', 'like', "%" . $filter['serial_number'] . "%")
                    //->where('inventory_number', 'like', "%" . $filter['inventory_number'] . "%")
                    //->where('created_at', 'like', "%" . $filter['created_at'] . "%")
                    //->where('first_use_date', 'like', "%" . $filter['first_use_date'] . "%");
           // });
       // }

        return $device->orderBy('created_at', 'desc')->with('deviceType')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    // public function status(Request $request)
    // {
    //    // dd($request);
    //     $page = $request->input('pagination')['page'];
    //     $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    //     $language = $request->input('language');
    //     $filter = $request->input('filter');
    //     $created_by = $filter['username'];
    //     $device_branch_id = $request->input('device_branch_id');
    //     $device = Device::with('createdBy')
    //                     ->with('lastHistory.employee')
    //                     ->where('device_branch_id', $device_branch_id)
    //                     ;

    //    // if (isset($filter)) {
    //        // $device->where(function ($query) use ($filter) {
    //           //  return $query
    //                 //->where('model', 'like', "%" . $filter['model'] . "%")
    //                 //->where('serial_number', 'like', "%" . $filter['serial_number'] . "%")
    //                 //->where('inventory_number', 'like', "%" . $filter['inventory_number'] . "%")
    //                 //->where('created_at', 'like', "%" . $filter['created_at'] . "%")
    //                 //->where('first_use_date', 'like', "%" . $filter['first_use_date'] . "%");
    //        // });
    //    // }
    //     if (isset($filter['status'])) {
    //         $device->where('lastHistory.', function ($query) use ($filter) {
    //             $query->select(DB::raw('max(id)'), 'status')->where('status', $filter['status']);
    //         });
    //     }
    //     if (isset($filter['model'])) {
    //         $device->where('model', 'like', '%' . $filter['model'] . '%');
    //     }
    //     if (isset($filter['serial_number'])) {
    //         $device->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
    //     }
    //     if (isset($filter['inventory_number'])) {
    //         $device->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
    //     }
    //     if (isset($filter['created_at'])) {
    //         $device->where('created_at', 'like', '%' . $filter['created_at'] . '%');
    //     }
    //     if (isset($filter['first_use_date'])) {
    //         $device->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
    //     }
    //     if (isset($filter['device_type_id'])) {
    //         $device->where('devices.device_type_id', $filter['device_type_id']);
    //     }
    //     if ($created_by) {
    //         $device->whereIn('created_by', User::where('username', 'like', '%'.$created_by.'%')->pluck('id')->toArray());
    //     }





    //     return $device->orderBy('created_at', 'desc')->with('deviceType')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    // }

    public function model()
    {
        $model =  Device::Select('model')->groupBy('model')->get();
        return $model;
    }

    public function getRef($device_branch_id)
    {
        if ($device_branch_id) {
            return DeviceType::where('device_branch_id', $device_branch_id)->get();
        }
        return [];
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
     * @param  \App\Http\Models\Device $Device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $Device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Device $Device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $Device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Device $Device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $Device)
    {
        $model = Device::find($request->input('id'));
        if (!$model) {
            $model = new Device();
            $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->device_branch_id = $request['device_branch_id'];
        $model->device_type_id = $request['device_type_id'];
        $model->model = $request['model'];
        $model->serial_number = $request['serial_number'];
        $model->inventory_number = $request['inventory_number'];
        $model->first_use_date = $request['first_use_date'];
        $model->device_type_id = $request['device_type_id'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Device $Device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $Device, $id)
    {
        $model = Device::find($id);
        $model->delete();
    }
}
