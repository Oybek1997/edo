<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\Device;
use App\Http\Models\DeviceHistory;
use App\Http\Models\ActNumberCounter;
use App\Http\Models\Employee;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceHistoryController extends Controller
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
        $employee_id = $filter['lastname_uz_latin'];
        $tabel = $filter['tabel'];
        $device_branch_id = $request->input('device_branch_id');
        $deviceHistory = DeviceHistory::with('employee')
            ->with('createdBy')
            ->with('device.deviceType')
            ->where('device_histories.device_branch_id', $device_branch_id)
            ;

        // if (isset($filter)) {
        //     $deviceHistory->where(function ($query) use ($filter) {
        //         return $query
        //             ->where('status', 'like', "%" . $filter['status'] . "%")
        //             ->where('act_number', 'like', "%" . $filter['act_number'] . "%")
        //             ->where('description', 'like', "%" . $filter['description'] . "%")
        //             ->where('created_at', 'like', "%" . $filter['created_at'] . "%")
        //             ->where('department_code', 'like', "%" . $filter['department_code'] . "%")
        //             ->where('employee_department', 'like', "%" . $filter['employee_department'] . "%")
        //             ->where('employee_position', 'like', "%" . $filter['employee_position'] . "%")
        //             ->where('first_use_date', 'like', "%" . $filter['first_use_date'] . "%");
        //     });
        // }
        if (isset($filter['status'])) {
            $deviceHistory->where('status', 'like', '%' . $filter['status'] . '%');
        }
        if (isset($filter['act_number'])) {
            $deviceHistory->where('act_number', 'like', '%' . $filter['act_number'] . '%');
        }
        if (isset($filter['description'])) {
            $deviceHistory->where('description', 'like', '%' . $filter['description'] . '%');
        }
        if (isset($filter['address'])) {
            $deviceHistory->where('address', 'like', '%' . $filter['address'] . '%');
        }
        if (isset($filter['description'])) {
            $deviceHistory->where('description', 'like', '%' . $filter['description'] . '%');
        }
        if (isset($filter['created_at'])) {
            $deviceHistory->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        if (isset($filter['department_code'])) {
            $deviceHistory->where('department_code', 'like', '%' . $filter['department_code'] . '%');
        }
        if (isset($filter['employee_department'])) {
            $deviceHistory->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
        }
        if (isset($filter['employee_position'])) {
            $deviceHistory->where('employee_position', 'like', '%' . $filter['employee_position'] . '%');
        }
        // if (isset($filter['first_use_date'])) {
        //     $deviceHistory->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
        // }
        if (isset($filter['first_use_date'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
            });
        }
        if (isset($filter['inventory_number'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
            });
        }
        if (isset($filter['model'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('model', 'like', '%' . $filter['model'] . '%');
            });
        }
        if (isset($filter['serial_number'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
            });
        }
        if (isset($filter['device_type_id'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('device_type_id', 'like', '%' . $filter['device_type_id'] . '%');
            });
        }
        if ($created_by) {
            $deviceHistory->whereIn('created_by', User::where('username', 'like', '%' . $created_by . '%')->pluck('id')->toArray());
        }
        if ($employee_id) {
            $deviceHistory->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
            ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
            ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
            ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }
        if ($tabel) {
            $deviceHistory->whereIn('employee_id', Employee::where('tabel', 'like', '%'.$tabel.'%')->pluck('id')->toArray());
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];
        //dd($sortBy[0]);

        if(count($sortBy)>0){
            $deviceHistory->join('devices','devices.id','=','device_histories.device_id');
            if($sortBy[0]=="device.device_type.type"){
                $deviceHistory->orderBy('devices.device_type_id',$sortDesc[0] ? 'asc' : 'desc');
            }
           elseif($sortBy[0]=="employee.tabel"){
            $deviceHistory->select('workflow.employees.*', 'device_histories.*')->join('workflow.employees','workflow.employees.id','=','device_histories.employee_id')->orderBy('workflow.employees.tabel',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="employee"){
               //dd($sortBy[0]);
               $deviceHistory->select('workflow.employees.*', 'device_histories.*')->join('workflow.employees','workflow.employees.id','=','device_histories.employee_id')->orderBy('workflow.employees.firstname_uz_latin',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="device.model"){
            $deviceHistory->orderBy('devices.model',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="device.serial_number"){
               $deviceHistory->orderBy('devices.serial_number',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="device.inventory_number"){
            //    $deviceHistory->orderBy('devices.inventory_number',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="device.first_use_date"){
               $deviceHistory->orderBy('devices.first_use_date',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="devices.created_by"){
               $deviceHistory->orderBy('device_histories.created_by',$sortDesc[0] ? 'asc' : 'desc');
           }
            elseif($sortBy[0]=="created_by.username"){
                $deviceHistory->orderBy('device_histories.created_by',$sortDesc[0] ? 'asc' : 'desc');
            }
           else {
            $deviceHistory->orderBy($sortBy[0],  $sortDesc[0] ? 'asc' : 'desc');
           }
        }else{
        	 $deviceHistory->orderBy('id', 'desc');
        }
        return $deviceHistory->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function status(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $created_by = $filter['username'];
        $employee_id = $filter['lastname_uz_latin'];
        $tabel = $filter['tabel'];
        // return $tabel;        
        $device_branch_id = $request->input('device_branch_id');
        $devices = Device::query();
        $devices->with('history.employee');
        $devices->with('history.createdBy');
        $devices->with('deviceType');
        $devices->where('device_branch_id',$device_branch_id)->orderBy('id', 'desc');

        if (isset($filter['device_type_id'])) {
            $devices->where('device_type_id', $filter['device_type_id']);
        }
        if (isset($filter['model'])) {
            $devices->where('model', 'like', '%' . $filter['model'] . '%');
        }
        if (isset($filter['serial_number'])) {
            $devices->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
        }
        if (isset($filter['inventory_number'])) {
            $devices->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
        }
        if (isset($filter['created_at'])) {
            $devices->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        // if (isset($filter['first_use_date'])) {
        //     $devices->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
        // }
        if (isset($filter['first_use_date'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
            });
        }
        if ($created_by) {
            $devices->whereIn('created_by', User::where('username', 'like', '%'.$created_by.'%')->pluck('id')->toArray());
        }
        if (isset($filter['status'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('status', $filter['status']);
            });
        }
        if (isset($tabel)) {
            $devices->whereHas('histories', function ($query) use ($tabel) {
                $query->whereIn('employee_id', Employee::where('tabel', $tabel)->pluck('id'));
            });
        }
        if ($employee_id) {
            $devices->whereHas('histories', function($q) use($employee_id){
		$q->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
            	->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
            	->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
            	->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
	    });
        }
        
        if (isset($filter['department_code'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('department_code', 'like', '%' . $filter['department_code'] . '%');
            });
        }
        if (isset($filter['employee_department'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
            });
        }
        if (isset($filter['employee_position'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('employee_position', 'like', '%' . $filter['employee_position'] . '%');
            });
        }
        if (isset($filter['act_number'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('act_number', 'like', '%' . $filter['act_number'] . '%');
            });
        }
        if (isset($filter['first_use_date'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
            });
        }
        if (isset($filter['created_at'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('created_at', 'like', '%' . $filter['created_at'] . '%');
            });
        }
        if (isset($filter['address'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('address', 'like', '%' . $filter['address'] . '%');
            });
        }
        if (isset($filter['description'])) {
            $devices->whereHas('histories', function ($query) use ($filter) {
                $query->where('description', 'like', '%' . $filter['description'] . '%');
            });
        }

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];
        //dd($sortBy[0]);

        if(count($sortBy)>100000){
            // $devices->join('devices','devices.id','=','device_histories.device_id');
            if($sortBy[0]=="device_type.type"){
                $devices->orderBy('device_type_id',$sortDesc[0] ? 'asc' : 'desc');
            }
           elseif($sortBy[0]=="employee.tabel"){
            $devices->select('workflow.employees.*', 'device_histories.*')->join('workflow.employees','workflow.employees.id','=','device_histories.employee_id')->orderBy('workflow.employees.tabel',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="employee"){
               //dd($sortBy[0]);
               $devices->select('workflow.employees.*', 'device_histories.*')->join('workflow.employees','workflow.employees.id','=','device_histories.employee_id')->orderBy('workflow.employees.firstname_uz_latin',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="model"){
            $devices->orderBy('model',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="serial_number"){
               $devices->orderBy('serial_number',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="inventory_number"){
               $devices->orderBy('inventory_number',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="first_use_date"){
               $devices->orderBy('first_use_date',$sortDesc[0] ? 'asc' : 'desc');
           }
           elseif($sortBy[0]=="created_by"){
               $devices->orderBy('device_histories.created_by',$sortDesc[0] ? 'asc' : 'desc');
           }
            elseif($sortBy[0]=="created_by.username"){
                $devices->orderBy('device_histories.created_by',$sortDesc[0] ? 'asc' : 'desc');
            }
           else {
            $devices->orderBy($sortBy[0],  $sortDesc[0] ? 'asc' : 'desc');
           }
        }else{
        	 $devices->orderBy('id', 'desc');
        }

                
        return $devices->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function myDevices(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $created_by = $filter['username'];
        $employee_id = $filter['lastname_uz_latin'];
        $tabel = $filter['tabel'];
        $device_branch_id = $request->input('device_branch_id');
        $deviceHistory = DeviceHistory::with('employee')
            ->with('createdBy')
            ->with('device.deviceType')
            // ->where('device_branch_id', $device_branch_id)
            ->where('employee_id', Auth::user()->employee_id)
            ->where('status', 1)
            ;
        if (isset($filter['status'])) {
            $deviceHistory->where('status', 'like', '%' . $filter['status'] . '%');
        }
        if (isset($filter['act_number'])) {
            $deviceHistory->where('act_number', 'like', '%' . $filter['act_number'] . '%');
        }
        if (isset($filter['description'])) {
            $deviceHistory->where('description', 'like', '%' . $filter['description'] . '%');
        }
        if (isset($filter['description'])) {
            $deviceHistory->where('description', 'like', '%' . $filter['description'] . '%');
        }
        if (isset($filter['created_at'])) {
            $deviceHistory->where('created_at', 'like', '%' . $filter['created_at'] . '%');
        }
        if (isset($filter['department_code'])) {
            $deviceHistory->where('department_code', 'like', '%' . $filter['department_code'] . '%');
        }
        if (isset($filter['employee_department'])) {
            $deviceHistory->where('employee_department', 'like', '%' . $filter['employee_department'] . '%');
        }
        if (isset($filter['employee_position'])) {
            $deviceHistory->where('employee_position', 'like', '%' . $filter['employee_position'] . '%');
        }
        if (isset($filter['first_use_date'])) {
            $deviceHistory->where('first_use_date', 'like', '%' . $filter['first_use_date'] . '%');
        }
        if (isset($filter['inventory_number'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('inventory_number', 'like', '%' . $filter['inventory_number'] . '%');
            });
        }
        if (isset($filter['model'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('model', 'like', '%' . $filter['model'] . '%');
            });
        }
        if (isset($filter['serial_number'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('serial_number', 'like', '%' . $filter['serial_number'] . '%');
            });
        }
        if (isset($filter['device_type_id'])) {
            $deviceHistory->whereHas('device', function ($query) use ($filter) {
                $query->where('device_type_id', 'like', '%' . $filter['device_type_id'] . '%');
            });
        }
        if ($created_by) {
            $deviceHistory->whereIn('created_by', User::where('username', 'like', '%' . $created_by . '%')->pluck('id')->toArray());
        }
        if ($employee_id) {
            $deviceHistory->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
            ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
            ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
            ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }
        if ($tabel) {
            $deviceHistory->whereIn('employee_id', Employee::where('tabel', 'like', '%'.$tabel.'%')->pluck('id')->toArray());
        }

        $ids = collect(DB::connection('mysql_workflow_org_texnika')->select("select max(id) id, device_id from device_histories group by device_id"))->pluck('id');

        // $ids = collect(DB::connection('mysql_workflow_org_texnika')->select("select all_his.id from device_histories as all_his,
        // (select * from (select h2.device_id, max(h2.first_use_date) as first_use_date from device_histories as h2 group by h2.device_id) as grouped) as group_his
        // where all_his.device_id = group_his.device_id and all_his.first_use_date = group_his.first_use_date"))->pluck('id');
       // $deviceHistory->whereIn('id', $ids);

        $sortBy=$request->input('pagination')['sortBy'];
        $sortDesc=$request->input('pagination')['sortDesc'];
        //dd($sortBy);
        if(count($sortBy)>0){
            if($sortBy[0]=="device.device_type.type"){
                $deviceHistory->join('devices','devices.id','=','device_histories.device_id')->orderBy('devices.device_type_id',$sortDesc[0] ? 'asc' : 'desc');
            }
            elseif($sortBy[0]=="device.model"){
                $deviceHistory->join('devices','devices.id','=','device_histories.device_id')->orderBy('devices.model',$sortDesc[0] ? 'asc' : 'desc');
            }
            elseif($sortBy[0]=="created_by.username"){
                $deviceHistory->orderBy('device_histories.created_by',$sortDesc[0] ? 'asc' : 'desc');
            }
            elseif($sortBy[0]=="device.serial_number"){
                $deviceHistory->join('devices','devices.id','=','device_histories.device_id' )->orderBy('devices.serial_number',$sortDesc[0] ? 'asc' : 'desc');
            }
            else{
                $deviceHistory->orderBy('id', 'desc');
            }


        }
        return $deviceHistory->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef($locale)
    {
        return Employee::select(
            'id',
            'tabel',
            $locale == 'uz_latin' ? 'firstname_uz_latin' : 'firstname_uz_cyril',
            $locale == 'uz_latin' ? 'middlename_uz_latin' : 'middlename_uz_cyril',
            $locale == 'uz_latin' ? 'lastname_uz_latin' : 'lastname_uz_cyril'
        )->where('is_active', 1)->get();
    }

    public function getRefDevice($device_branch_id)
    {
        return Device::where('device_branch_id', $device_branch_id)->get();
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
     * @param  \App\Http\Models\DeviceHistory $DeviceHistory
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceHistory $DeviceHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DeviceHistory $DeviceHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceHistory $DeviceHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DeviceHistory $DeviceHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceHistory $DeviceHistory)
    {
        $model = DeviceHistory::find($request->input('id'));
        if (!$model) {
            $model = new DeviceHistory();
            $model->created_by = Auth::id();
            if ($request['status'] == 1) {
                // $counter = ActNumberCounter::where('year', date('Y'))->first();
                // if ($counter) {
                //     $counter->count = $counter->count + 1;
                //     $counter->save();
                // } else {
                //     $counter = new ActNumberCounter();
                //     $counter->year = date('Y');
                //     $counter->count = 1;
                //     $counter->save();
                // }
                // $model->act_number = 'AT' . date('Y') .'/'. str_pad($counter->count, 5, "0", STR_PAD_LEFT);
            }
        } else {
            $model->updated_by = Auth::id();
        }
        $model->act_number = $request->input('act_number');
        $model->device_branch_id = $request['device_branch_id'];
        $model->device_id = $request['device_id'];
        $model->employee_id = $request['employee_id'];
        $model->department_code = $request['department_code'];
        $model->employee_department = $request['employee_department'];
        $model->employee_position = $request['employee_position'];
        $model->status = $request['status'];
        // $model->first_use_date = $request['first_use_date'];
        $model->address = $request['address'];
        $model->description = $request['description'];
        return $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DeviceHistory $DeviceHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeviceHistory $DeviceHistory, $id)
    {
        $model = DeviceHistory::find($id);
        $model->delete();
    }
}
