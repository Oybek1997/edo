<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdressController extends Controller
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
        $address_name = isset($filter['address_name']) ? $filter['address_name'] : null;
        $wh_name = isset($filter['wh_name']) ? $filter['wh_name'] : null;
        $wh_number = isset($filter['wh_number']) ? $filter['wh_number'] : null;
        $year = isset($filter['year']) ? $filter['year'] : null;
        $quarter = isset($filter['quarter']) ? $filter['quarter'] : null;
        $language = $request->input('language');
        $adress = Address::with('warehouse');
       
        if (isset($id)) {
            $adress->where('id', $id);
        }
        if (isset($address_name)) {
            $adress->where('address_name', 'ilike', "%". $address_name . "%");
        }
        // if (isset($wh_number)) {
        //     $warehouse->where('wh_number', 'like', "%". $wh_number . "%");
        // }
        if (isset($wh_name)) {
            $adress->whereHas('warehouse', function($q) use($wh_name){
                $q->where('wh_name', 'ilike', "%". $wh_name . "%");
            });
        }
        if (isset($wh_number)) {
            $adress->whereHas('warehouse', function($q) use($wh_number){
                $q->where('wh_number', 'ilike', "%". $wh_number . "%");
            });
        }
        return $adress->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        // return $request;
        $model = Address::find($request->input('id'));
        if (!$model) {
            $model = new Address();
            // $model->created_by = Auth::id();
            // $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
            // } else {
            //     $model->updated_by = Auth::id();
            //     $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $model->address_name = $request['address_name'];
        // $model->wh_number = $request['wh_number'];
        $model->warehouse_id = $request['warehouse_id'];
        
        if($model->save()){
            return 'success';
        }else{
            return 'no save';
        }
    }

    public function destroy($id)
    {
        $model = Address::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
