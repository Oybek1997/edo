<?php

namespace App\Http\Controllers\Supplytransport;

use App\Http\Models\Supplytransport\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function indexDepartments(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $departments = Department::select();
        if (isset($search)) {
            $departments->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%");
            });
        }
        return  $departments->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $model = Department::find($request->input('id'));
        if (!$model) {
            $model = new Department();
        }
        $model->name = $request->input('name');
        $model->save();
        return 'done';
    }
    public function destroy(Request $request)
    {
        $model = Department::find($request->input('id'));
        $model->delete();
    }
}