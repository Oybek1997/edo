<?php

namespace App\Http\Controllers\ME;

use App\Http\Models\ME\MethodQualityControl;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MethodQualityControlController extends Controller 
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $methodQualityControl = MethodQualityControl::with('createdBy');
        if (isset($search)) {
            $methodQualityControl->where('name', 'ilike', "%" . $search . "%");
        }
        return $methodQualityControl->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(MethodQualityControl $MethodQualityControl)
    {
        return MethodQualityControl::get();
    }

    public function update(Request $request, MethodQualityControl $MethodQualityControl)
    {
        $model = MethodQualityControl::find($request->input('id'));
        if (!$model) {
            $model = new MethodQualityControl();
            $model->created_by= Auth::id();
        }
       
        $model->method_quality_control_name = $request['method_quality_control_name'];
        $model->save();
    }

    public function destroy(MethodQualityControl $MethodQualityControl, $id)
    {
        $model = MethodQualityControl::find($id);
            $model->delete();
    }
}