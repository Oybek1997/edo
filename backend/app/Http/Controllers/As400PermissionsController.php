<?php

namespace App\Http\Controllers;

use App\Http\Models\As400Permission;
use App\Http\Models\Employee;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class As400PermissionsController extends Controller
{
    public function findEmployees($id)
    {
        return Employee::where('tabel', '=', $id)->first();
    }
    public function attachEmployee($item, $employee){
        $permission = As400Permission::where('query_id',$item)->where('employee_id',$employee)->first();
        if($permission) {
            return false;
        }
        $permission = new As400Permission([
            'query_id' => $item,
            'employee_id' => $employee,
        ]);
        $permission->save();
        return As400Permission::with('employee')->find($permission->id);
    }
    public function destroy($id)
    {
        $model = As400Permission::find($id);
        $model->delete();
    }
}
