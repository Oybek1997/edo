<?php

namespace App\Http\Controllers;

use App\Http\Models\As400Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AS400QueriesController extends Controller
{
    public function index()
    {
        $data = As400Query::query()
                    ->with('As400Permissions.employee')
                    ->with('createdBy.employee')
                    ->get();
        return $data;
    }
    public function getQueryList()
    {
        $employee_id = Auth::user()->employee_id;
        $data = As400Query::query()
            ->whereHas('As400Permissions', function($query) use($employee_id){
                $query->where('employee_id',$employee_id);
            })
            ->get();
        return $data;
    }
    public function getValues(Request $request)
    {
        // $query = "SELECT * FROM zarlib.z101ptpf where Z101KEYTN = @tabel' and Z101YY = @yil' limit @limited'";
        $first_char = "@";
        $last_char = "@";
        $result = [];
        $query_id = $request->input('query_id');
        $query = As400Query::select("query")->find($query_id);
        $query = substr($query, 9);
        foreach (explode($first_char, $query) as $key => $value) {
            if (strpos($value, $last_char) !== FALSE) {
                $result[] = substr($value, 0, strpos($value, $last_char));
            }
        }
        return $result;
        // array_splice($result, 0, 1);
    }

    public function update(Request $request)
    {
        $model = As400Query::find($request->input('id'));
        if (!$model) {
            $model = new As400Query();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        $model->query_name = $request['query_name'];
        $model->query = $request['query'];
        $model->save();
    }

    public function destroy($id)
    {
        $model = As400Query::find($id);
        $model->delete();
    }

}
