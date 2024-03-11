<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
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
    public function getwh(Request $request)
    {
        return $warehouse = Warehouse::get();
    }        
    public function index(Request $request)
    {        
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        // return $request['filter'];
        $filter =$request['filter'];
        // return
        $id = isset($filter['id']) ? $filter['id'] : null;
        $wh_name = isset($filter['wh_name']) ? $filter['wh_name'] : null;
        $wh_number = isset($filter['wh_number']) ? $filter['wh_number'] : null;
        $wh_number = isset($filter['wh_number']) ? $filter['wh_number'] : null;
        $year = isset($filter['year']) ? $filter['year'] : null;
        $quarter = isset($filter['quarter']) ? $filter['quarter'] : null;
        $language = $request->input('language');
        $warehouse = Warehouse::with('quarter');
       
        if (isset($id)) {
            $warehouse->where('id', $id);
        }
        if (isset($wh_name)) {
            $warehouse->where('wh_name', 'ilike', "%". $wh_name . "%");
        }
        if (isset($wh_number)) {
            $warehouse->where('wh_number', 'ilike', "%". $wh_number . "%");
        }
        if (isset($year)) {
            $warehouse->whereHas('quarter', function($q) use($year){
                $q->where('year', 'ilike', "%". $year . "%");
            });
        }
        if (isset($quarter)) {
            $warehouse->whereHas('quarter', function($q) use($quarter){
                $q->where('quarter', 'ilike', "%". $quarter . "%");
            });
        }
        return $warehouse->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        $model = Warehouse::find($request->input('id'));
        if (!$model) {
            $model = new Warehouse();
            // $model->created_by = Auth::id();
            // $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
            // } else {
            //     $model->updated_by = Auth::id();
            //     $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $model->wh_name = $request['wh_name'];
        $model->wh_number = $request['wh_number'];
        $model->quarter_id = $request['quarter_id'];
        
        if($model->save()){
            return 'success';
        }else{
            return 'no save';
        }
    }

    public function destroy($id)
    {
        $model = Warehouse::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
