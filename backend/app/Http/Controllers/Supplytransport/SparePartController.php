<?php

namespace App\Http\Controllers\Supplytransport;

use App\Http\Models\Supplytransport\SparePart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function indexSpareParts(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $spare_parts = SparePart::select()->with('transport_type');
        if (isset($search)) {
            $spare_parts->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%")
                    ->orWhere('sap_number', 'ilike', "%" . $search . "%");
            });
        }
        return  $spare_parts->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $transport_id = $request->input('transport_id');
        $sapnumber = $request->input('sap_number');
        $sparename = $request->input('name');
        $standard_indicator = $request->input('standard_indicator');
        $estimated_month = $request->input('estimated_month');
        $description = $request->input('description');
    
        $id = $request->input('id');
        $model = SparePart::find($id);
    
        if (!$model) {
            $model = new SparePart();
        } else {
            $model->transport_id = $transport_id;
            $model->sap_number = $sapnumber;
            $model->name = $sparename;
            $model->standard_indicator = $standard_indicator;
            $model->estimated_month = $estimated_month;
            $model->description = $description;
            $model->save();
            return 'updated';
        }
        $model->transport_id = $transport_id;
        $model->sap_number = $sapnumber;
        $model->name = $sparename;
        $model->standard_indicator = $standard_indicator;
        $model->estimated_month = $estimated_month;
        $model->description = $description;
        $model->save();
        return 'created';
    }
    

    public function destroy(Request $request)
    {
        $model = SparePart::find($request->input('id'));
        $model->delete();
    }
}