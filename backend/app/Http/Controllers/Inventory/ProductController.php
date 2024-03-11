<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\Product;
use App\Http\Models\Inventory\EoProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function eoProduct(Request $request)
    {
        return 
        $quarter = EoProduct::get();
    }
    // public function quarter(Request $request)
    // {
    //     return 
    //     $quarter = Quarter::get();
    // }
    public function index(Request $request)
    {       
        // return 5; 
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter =$request['filter'];
        $id = isset($filter['id']) ? $filter['id'] : null;
        
        $name = isset($filter['name']) ? $filter['name'] : null;
        $part_number = isset($filter['part_number']) ? $filter['part_number'] : null;
        $unit_measure = isset($filter['unit_measure']) ? $filter['unit_measure'] : null;
        $year = isset($filter['year']) ? $filter['year'] : null;
        $quarter = isset($filter['quarter']) ? $filter['quarter'] : null;
        $product = Product::with('quarter');
        
        if (isset($id)) {
            $product->where('id', $id);
        }
        if (isset($part_number)) {
            $product->where('part_number', 'like', "%" . $part_number . "%" );
        }
        if (isset($name)) {
            $product->where('name', 'like', "%" . $name . "%" );
        }
        if (isset($unit_measure)) {
            $product->where('unit_measure', 'like', "%" . $unit_measure . "%" );
        }
        if (isset($year)) {
            $product->whereHas('quarter', function($q) use($year){
                $q->where('year', 'like', "%". $year . "%");
            });
        }
        if (isset($quarter)) {
            $product->whereHas('quarter', function($q) use($quarter){
                $q->where('quarter', 'like', "%". $quarter . "%");
            });
        }
        
        return $product->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        // return 
        // $request->input('id');
        $model = Product::find($request->input('id'));
        if (!$model) {
            $model = new Product();
        }


        // $model->quarter = intval($request['quarter']);
        $model->name = $request['name'];
        $model->part_number = $request['part_number'];
        $model->quarter_id = $request['quarter_id'];
        $model->unit_measure = $request['unit_measure'];
        // $model->status = isset($request['status']) ? $request['status'] : 0;
        
        if($model->save()){
            return 'success';
        }else{
            return 'no save';
        }
    }

    public function destroy($id)
    {
        $model = Product::find($id);
        if ($model) {
            $model->delete();
        }
    }
    
}
