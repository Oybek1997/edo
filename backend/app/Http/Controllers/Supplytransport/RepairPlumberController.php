<?php

namespace App\Http\Controllers\Supplytransport;

use App\Http\Models\Supplytransport\RepairPlumber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepairPlumberController extends Controller
{
    public function indexRepairPlumbers(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $repair_plumbers = RepairPlumber::select();
        if (isset($search)) {
            $repair_plumbers->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%");
            });
        }
        return  $repair_plumbers->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $table_number = $request->input('table_number');
        $dep_code = $request->input('dep_code');
        $surname = $request->input('surname');
        $name = $request->input('name');
        $description = $request->input('description');
        $status = $request->input('status');
        $model = RepairPlumber::find($request->input('id'));
    
        if (!$model) {
            $model = new RepairPlumber();
        }
    
        $model->table_number = $table_number;
        $model->dep_code = $dep_code;
        $model->surname = $surname;
        $model->name = $name;
        $model->description = $description;
        $model->status = $status;
        $model->save();
    
        return 'done';
    }
    public function destroy(Request $request)
    {
        $model = RepairPlumber::find($request->input('id'));
        $model->delete();
    }
}