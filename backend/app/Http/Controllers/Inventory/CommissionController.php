<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\Commission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
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
        // return $request['filter'];
        $filter =$request['filter'];
        // return
        $id = isset($filter['id']) ? $filter['id'] : null;
        $name = isset($filter['commission_number']) ? $filter['commission_number'] : null;
        // $wh_name = isset($filter['wh_name']) ? $filter['wh_name'] : null;
        // $wh_number = isset($filter['wh_number']) ? $filter['wh_number'] : null;
        // $year = isset($filter['year']) ? $filter['year'] : null;
        // $quarter = isset($filter['quarter']) ? $filter['quarter'] : null;
        // $language = $request->input('language');
        $comission = Commission::query();
       
        if (isset($id)) {
            $comission->where('id', $id);
        }
        if (isset($name)) {
            $comission->where('commission_number', 'ilike', "%". $name . "%");
        }
        // if (isset($wh_number)) {
        //     $warehouse->where('wh_number', 'like', "%". $wh_number . "%");
        // }
        // if (isset($wh_name)) {
        //     $adress->whereHas('warehouse', function($q) use($wh_name){
        //         $q->where('wh_name', 'ilike', "%". $wh_name . "%");
        //     });
        // }
        // if (isset($wh_number)) {
        //     $adress->whereHas('warehouse', function($q) use($wh_number){
        //         $q->where('wh_number', 'ilike', "%". $wh_number . "%");
        //     });
        // }
        return $comission->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        // return $request;
        $model = Commission::find($request->input('id'));
        if (!$model) {
            $model = new Commission();
            // $model->created_by = Auth::id();
            // $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
            // } else {
            //     $model->updated_by = Auth::id();
            //     $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $model->commission_number = $request['commission_number'];
        // $model->wh_number = $request['wh_number'];
        // $model->warehouse_id = $request['warehouse_id'];
        
        if($model->save()){
            return 'success';
        }else{
            return 'no save';
        }
    }

    public function destroy($id)
    {
        $model = Commission::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
