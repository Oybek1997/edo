<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;



class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $warehouses = Warehouse::with([
                                        'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                        'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                                      ]
        );
        if (isset($search)) {
            $warehouses->where(function ($query) use ($search) {
                return $query
                    ->where('warehouse_number', 'ilike', "%" . $search . "%")
                    ->orWhere('description', 'ilike', "%" . $search . "%");
            });
        }
        return $warehouses->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Warehouse $Warehouse)
    {
        return Warehouse::get();
    }

    public function update(Request $request)
    {
        $model = Warehouse::find($request->input('id'));
        if (!$model) {
            $model = new Warehouse();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }

        $model->warehouse_number = $request['warehouse_number'];
        $model->description = $request['description'];
        $model->type = $request['type'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\EDI\Warehouse  $Warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Warehouse::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
