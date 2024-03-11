<?php

namespace App\Http\Controllers;

use App\Http\Models\MaterialResponsiblePeople;
use App\Http\Models\EDI\BusinessPartnerType;
use App\Http\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


class MaterialResponsiblePeopleController extends Controller
{
   public function index(Request $request)
{
    $page = $request->input('pagination')['page'];
    $itemsPerPage = $request->input('pagination')['itemsPerPage'];
    $search = $request->input('search');

    $materialResponsibles = MaterialResponsiblePeople::with([
        'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
        'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
        'employee'
    ]);

    $materialResponsibles->when($search, function ($query) use ($search) {
        return $query->whereHas('employee', function ($employeeQuery) use ($search) {
            $employeeQuery->where(function ($query) use ($search) {
                $query->where('tabel', 'ilike', "%" . $search . "%")
                      ->orWhere('employees.firstname_uz_latin', 'ilike', "%" . $search . "%")
                      ->orWhere('employees.lastname_uz_latin', 'ilike', "%" . $search . "%");
            });
        });
    });

    return $materialResponsibles->orderBy('employee_id', 'ASC')
        ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
}




    public function update(Request $request, MaterialResponsiblePeople $materialResponsible)
    {
        //return $request->input('first_name');
        //return $request->input('id');     
        $model = MaterialResponsiblePeople::find($request->input('id'));
        if (!$model) {
            $model = new MaterialResponsiblePeople();
            $model->created_by = Auth::id();
            $model->created_at = date('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }
        
          
        $employeeObject=Employee::where('tabel','=',$request->input('tabno'))->first();
        $firstName=$employeeObject->firstname_uz_latin;
        //return $firstName;
        
        $model->employee_id = $employeeObject->id;
        
        $model->save();
        return $model;
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
        $model = MaterialResponsiblePeople::find($id);
        // return $model;

        $model->delete();
    }
}
