<?php

namespace App\Http\Controllers;

use App\Http\Models\Device;
use App\Http\Models\Insert;
use App\Http\Models\InsertHistory;
use App\Http\Models\DeviceHistory;
use App\Http\Models\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TemporaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        //////////////////////////Inserting To Devices//////////////////////////////////

        $inserts = Insert::get();

        //dd($inserts);
        foreach( $inserts as  $insert){
            $deviceObject=new Device;
            $type=null;
            if($insert->regcom_types=='PC' || $insert->regcom_types=='Pc' || $insert->regcom_types=='pc'){
                $type=26;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='PRINTER' || $insert->regcom_types=='Printer' || $insert->regcom_types=='printer'){
                $type=27;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='SCANER' || $insert->regcom_types=='Scaner' || $insert->regcom_types=='scaner'){
                $type=28;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='NOTEBOOK' || $insert->regcom_types=='Notebook' || $insert->regcom_types=='notebook'){
                $type=29;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='PROJECTOR' || $insert->regcom_types=='Projector' || $insert->regcom_types=='projector'){
                $type=30;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='Fotoapparat' ||$insert->regcom_types=='fotoapparat'){
                $type=31;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='USB flesh'){
                $type=32;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }elseif($insert->regcom_types=='MONITOR' || $insert->regcom_types=='Monitor' || $insert->regcom_types=='monitor'){
                $type=33;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }
            elseif($insert->regcom_types=='MFU' || $insert->regcom_types=='Mfu' || $insert->regcom_types=='mfu'){
                $type=34;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }
            elseif($insert->regcom_types=='Temp_PC' || $insert->regcom_types=='temp_PC' || $insert->regcom_types=='temp_pc'){
                $type=35;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }
            elseif($insert->regcom_types=='PLOTTER' || $insert->regcom_types=='Plotter' || $insert->regcom_types=='plotter'){
                $type=36;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }
            elseif($insert->regcom_types=='TEMP4_NOTEBOOK' || $insert->regcom_types=='TEMP4_NOTEBOOK'){
                $type=37;
                $deviceObject->device_type_id=$type;
                $deviceObject->device_branch_id=1;
                $deviceObject->model=$insert->regcom_type_model;
                $deviceObject->serial_number=$insert->regcom_sevis_sn;
                $deviceObject->inventory_number=$insert->regcom_inven;
                $deviceObject->created_by= 8107;
                $deviceObject->first_use_date=$insert->regcom_reg_date;
                $deviceObject->save();
            }else{
                return $insert->regcom_types;
            }
        }


// dd("Hellooooo");
        /////////////Inserting To Device histories//////////////////////////////////
        $insertHistory= InsertHistory::get();
        foreach($insertHistory as $oneHistory){
            $historyModel=new DeviceHistory();
            $rc = Insert::where('regcom_id',$oneHistory->reg_com_id)->first();
            $dev = Device::where('inventory_number',$rc->regcom_inven)->first();
            $historyModel->device_id=$dev->id;

            $employeeObj=Employee::where('tabel', $oneHistory->hist_tabno)->first();
            if($employeeObj){
                $id=$employeeObj->id;
            }else{
                $id=897;
            }

            $historyModel->employee_id=$id;

            $historyModel->department_code=$oneHistory->hist_div_code;
            $historyModel->employee_department=$oneHistory->hist_div_name;
            $historyModel->employee_position=$oneHistory->hist_status;
            if($oneHistory->hist_status_working=="Принимать"){
                $historyModel->status=1;
            }elseif($oneHistory->hist_status_working=="Отдавать"){
                $historyModel->status=2;
            }elseif($oneHistory->hist_status_working=="Ремонт"){
                $historyModel->status=3;
            }elseif($oneHistory->hist_status_working=="Уничтожения"){
                $historyModel->status=4;
            }
            //dd($oneHistory->hist_status_working);
            //$historyModel->status=$oneHistory->hist_status_working;
            $historyModel->description=$oneHistory->hist_comment;

            // $historyModel->act_number=NULL;

            $historyModel->first_use_date= Carbon::now();  ;
            $historyModel->created_by=8107;
            $historyModel->updated_by=8107;
            $historyModel->device_branch_id=1;
            $historyModel->save();
        }
        //dd("Hello");




        echo "Device and DeviceHistory Table Ready";
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
     * @param  \App\Http\Models\StaffFile  $staffFile
     * @return \Illuminate\Http\Response
     */
    public function show(StaffFile $staffFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\StaffFile  $staffFile
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffFile $staffFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\StaffFile  $staffFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StaffFile $staffFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\StaffFile  $staffFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(StaffFile $staffFile)
    {
        //
    }
}
