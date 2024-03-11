<?php

namespace App\Http\Controllers\SapIntegration;

use GuzzleHttp\Client;
use SimpleXMLElement;
use Illuminate\Http\Request;
use App\Http\Models\SapIntegration\WarehouseResponsible;
use App\Http\Models\SapIntegration\Warehouse;
use App\Http\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WarehouseResponsibleController extends Controller
{
    public function index(Request $request)
    {
        $materialResponsibles = WarehouseResponsible::with('employeeResponsible')
                                    ->with('accountantResponsible')
                                    ->with('warehouse');
                                    
        // $page = $request->input('pagination')['page'];
        // $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');

        // $materialResponsibles = WarehouseResponsible::with([
        //     'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
        //     'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
        //     'employee'
        // ]);
        if($search){

            $materialResponsibles->whereIn('responsible_employee_id', Employee::where('lastname_uz_latin', 'ilike', '%'.$search.'%')
            ->orWhere('firstname_uz_latin', 'ilike', '%'.$search.'%')
            ->orWhere('tabel', 'ilike', '%'.$search.'%')->pluck('id')->toArray())
            ->orWhereIn('responsible_accountant_id', Employee::where('lastname_uz_latin', 'ilike', '%'.$search.'%')
            ->orWhere('firstname_uz_latin', 'ilike', '%'.$search.'%')
            ->orWhere('tabel', 'ilike', '%'.$search.'%')->pluck('id')->toArray())
            ->orWhereIn('warehouse_id', Warehouse::where('code', 'ilike', '%'.$search.'%')->pluck('id')->toArray());
        }

        return $materialResponsibles->get();
    }

    public function update(Request $request)
    {
        // return $request;
        //return $request->input('first_name');
        $employee_id = $request->input('responsible_employee_id');
        $accountant_id = $request->input('responsible_accountant_id');
        $warehouse = $request->input('warehouse');
        $model = WarehouseResponsible::where('warehouse_id', $warehouse)
                                     ->where('responsible_employee_id', $employee_id)->first();
        if (!$model) {
            $model = new WarehouseResponsible();
            $model->warehouse_id = $warehouse;
            $model->responsible_employee_id = $employee_id;
            $model->responsible_accountant_id = $accountant_id;
        } else {
            return 'Bu hodim avval qo`shilgan';
        }
        
        $model->save();
        return $model;
    }


    public function getRef(Request $request)
    {
        return [
            'businessPartnerTypes' => WarehouseResponsible::select('id as value', 'name as text')->get()
        ];
    }

    public function destroy($id)
    {
        //return $id;
        $model = WarehouseResponsible::find($id);
        // return $model;

        $model->delete();
    }
}
