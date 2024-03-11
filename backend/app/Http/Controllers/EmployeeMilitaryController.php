<?php

namespace App\Http\Controllers;

use App\Http\Models\MaterialResponsiblePeople;
use App\Http\Models\EDI\BusinessPartnerType;
use App\Http\Models\Employee;
use App\Http\Models\DocumentDetailEmployee;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\Document;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentRelation;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\EmployeeMilitary;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;


class EmployeeMilitaryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $filters = $request->input('filters'); // Corrected this line

        $employeeMilitary = EmployeeMilitary::with([
            'employee'
        ]);
        //dd($filters['tabno']);

        if (isset($filters['tabno']) && $filters['tabno']) {
            //dd($filters['tabno']);
            $employeeId=Employee::where('tabel','=',$filters['tabno'])->first()->id;

            //dd($employeeId);
            $employeeMilitary->where('employee_id', 'ilike', '%' . $employeeId . '%');
        }
        if (isset($filters['service_duration']) && $filters['service_duration']) {
            $employeeMilitary->where('service_duration', 'ilike', '%' . $filters['service_duration'] . '%');
        }
        if (isset($filters['is_mandatory']) && $filters['is_mandatory']) {
            //dd($filters['is_mandatory']);
            if($filters['is_mandatory']== 'ha' || $filters['is_mandatory']== 'Ha'|| $filters['is_mandatory']== 'HA'){
                $isMandatory='true';
            }elseif($filters['is_mandatory']== 'yo`q' || $filters['is_mandatory']== "Yo'q" || $filters['is_mandatory']== "YO`Q" || $filters['is_mandatory']== "yo'q" || $filters['is_mandatory']== "yo`q"){
                $isMandatory='false';
            }
            $employeeMilitary->where('is_mandatory', 'ilike', '%' . $isMandatory . '%');
        }
        if (isset($filters['in_military_account']) && $filters['in_military_account']) {
            //dd($filters['in_military_account']);
            if($filters['in_military_account']== 'ha' || $filters['in_military_account']== 'Ha'|| $filters['in_military_account']== 'HA'){
                $inMilitaryAccount='true';
            }elseif($filters['in_military_account']== 'yo`q' || $filters['in_military_account']== "Yo'q" || $filters['in_military_account']== "YO`Q" || $filters['in_military_account']== "yo'q" || $filters['in_military_account']== "yo`q"){
                $inMilitaryAccount='false';
            }
            $employeeMilitary->where('is_mandatory', 'ilike', '%' . $inMilitaryAccount . '%');
        }
        if (isset($filters['ticket_serial_number']) && $filters['ticket_serial_number']) {
            $employeeMilitary->where('ticket_serial_number', 'ilike', '%' . $filters['ticket_serial_number'] . '%');
        }
        if (isset($filters['ticket_issuer']) && $filters['ticket_issuer']) {
            $employeeMilitary->where('ticket_issuer', 'ilike', '%' . $filters['ticket_issuer'] . '%');
        }
        if (isset($filters['ticket_given_date']) && $filters['ticket_given_date']) {
            $employeeMilitary->where('ticket_given_date', 'ilike', '%' . $filters['ticket_given_date'] . '%');
        }
        if (isset($filters['special_certificate_number']) && $filters['special_certificate_number']) {
            $employeeMilitary->where('special_certificate_number', 'ilike', '%' . $filters['special_certificate_number'] . '%');
        }
        if (isset($filters['team_number']) && $filters['team_number']) {
            $employeeMilitary->where('team_number', 'ilike', '%' . $filters['team_number'] . '%');
        }

        // ... (your other conditions)

        return $employeeMilitary->orderBy('id', 'desc')->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page', $page); // Corrected 'page name' to 'page'
    }


    public function update(Request $request, EmployeeMilitary $mployeeMilitary)
    {
        //return $request->input('id');
//        dd($request->input('id'));
        

//        $model = EmployeeMilitary::where('employee_id','=',$request->input('employee_id'))->first();
        $model = EmployeeMilitary::where('id','=',$request->input('id'))->first();
        //dd($model);
        $employeeObject=Employee::where('id','=',$request->input('employee_id'))->first();
//        dd($request->input('employee_id'));

        if (!$model) {
            //dd('Hello');
            $model = new EmployeeMilitary();
//            $model->created_by = Auth::id();
            $model->created_at = date('Y-m-d H:i:s');


            //$firstName=$employeeObject->firstname_uz_latin;
            //dd($employeeObject);

            $model->employee_id = $employeeObject->id;
            $model->service_duration = $request->input('service_duration');
            $model->is_mandatory = $request->input('is_mandatory');
            $model->in_military_account = $request->input('in_military_account');
            $model->ticket_serial_number = $request->input('ticket_serial_number');
            $model->ticket_issuer = $request->input('ticket_issuer');
            $model->ticket_given_date = $request->input('ticket_given_date');
            $model->special_certificate_number = $request->input('special_certificate_number');
            $model->team_number = $request->input('team_number');
            $model->save();
            return $model;

        } else {
//            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
            $model->employee_id = $employeeObject->id;
            $model->service_duration = $request->input('service_duration');
            $model->is_mandatory = $request->input('is_mandatory');
            $model->in_military_account = $request->input('in_military_account');
            $model->ticket_serial_number = $request->input('ticket_serial_number');
            $model->ticket_issuer = $request->input('ticket_issuer');
            $model->ticket_given_date = $request->input('ticket_given_date');
            $model->special_certificate_number = $request->input('special_certificate_number');
            $model->team_number = $request->input('team_number');
            $model->save();


        }



    }


    public function getRef(Request $request)
    {
        return [
            'businessPartnerTypes' => BusinessPartnerType::select('id as value', 'name as text')->get()
        ];
    }

    public function destroy($id)
    {
        //return $id;
        $model = EmployeeMilitary::find($id);
        // return $model;

        $model->delete();
    }
}

