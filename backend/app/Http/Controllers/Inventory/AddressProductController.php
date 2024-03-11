<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\Address;
use App\Http\Models\Inventory\AddressProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AddressProductController extends Controller
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
        $product_name = isset($filter['product_name']) ? $filter['product_name'] : null;
        $warehouse = isset($filter['warehouse']) ? $filter['warehouse'] : null;
        $stock = isset($filter['stock']) ? $filter['stock'] : null;

        $adress = AddressProduct::with('address.warehouse')->with('product')->orderByDesc('stock');
       
        if (isset($id)) {
            $adress->where('id', $id);
        }
        if (isset($address_name)) {
            $adress->whereHas('address', function($q) use($address_name){
                $q->where('address_name', 'ilike', "%". $address_name . "%");
            });
        }
        // if (isset($stock)) {
        //     $warehouse->where('stock', 'like', "%". $stock . "%");
        // }
        if (isset($warehouse)) {
            $adress->whereHas('address', function($q) use($warehouse){
                $q->whereHas('warehouse', function ($q2) use ($warehouse) {
                    $q2->where('wh_number', 'ilike', '%' . $warehouse . '%');
                });
            });
        }
        if (isset($product_name)) {
            $adress->whereHas('product', function($q) use($product_name){
                $q->where('part_number', 'ilike', "%". $product_name . "%");
            });
        }
        if (isset($stock)) {
            $adress->where('stock', 'ilike', "%". $stock . "%");
        }
        // $adress->orderByDesc('stock');
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
