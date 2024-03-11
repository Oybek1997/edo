<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\StockParameter;
use App\Http\Models\EDI\Material;
use App\Http\Models\EDI\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class StockParameterController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $StockParameters = StockParameter::with([
                                                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                'material',
                                                'warehouse'                   
                                                ]
        );

        if (isset($search)) {
            $StockParameters->whereHas('material', function ($q) use ($search) {
                $q->where('material_number', 'ilike', "%" . $search . "%");
            })
                ->orWhereHas('warehouse', function ($q) use ($search) {
                    $q->where('warehouse_number', 'ilike', "%" . $search . "%");
                });
        }
        return $StockParameters->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(StockParameter $StockParameter)
    {
        return StockParameter::get();
    }

    public function update(Request $request)
    {
        $model = StockParameter::find($request->input('id'));
        if (!$model) {
            $model = new StockParameter(); 
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }

        $model->material_id = $request->input('material_id');
        $model->warehouse_id = $request->input('warehouse_id');
        $model->minimum = $request->input('minimum');
        $model->maximum = $request->input('maximum');
        $model->safety_stock = $request->input('safety_stock');
        $model->save();
        return $model;
    }

    public function getRef(Request $request)
    {
        return [
            'materials' => Material::select('id as value', 'material_number as text')->get(),
            'warehouses' => Warehouse::select('id as value', 'warehouse_number as text')->get()
        ];
    }

    public function destroy($id)
    {
        //
        $model = StockParameter::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
