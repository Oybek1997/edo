<?php

namespace App\Http\Controllers\ME;

use App\Http\Models\ME\QualityControl;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class QualityControlController extends Controller 
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $qualityControl = QualityControl::with('createdBy');
        if (isset($search)) {
            $qualityControl->where('name', 'ilike', "%" . $search . "%");
        }
        return $qualityControl->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(QualityControl $QualityControl)
    {
        return QualityControl::get();
    }

    public function update(Request $request, QualityControl $QualityControl)
    {
        $model = QualityControl::find($request->input('id'));
        if (!$model) {
            $model = new QualityControl();
            $model->created_by= Auth::id();
        }
       
        $model->quality_control_name = $request['quality_control_name'];
        $model->save();
    }

    public function destroy(QualityControl $QualityControl, $id)
    {
        $model = QualityControl::find($id);
            $model->delete();
    }
}