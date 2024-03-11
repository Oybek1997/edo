<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\Quarter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class QuarterController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function quarter(Request $request)
    {
        return 
        $quarter = Quarter::get();
    }
    public function index(Request $request)
    {        
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter =$request['filter'];
        $id = isset($filter['id']) ? $filter['id'] : null;
        
        $year = isset($filter['year']) ? $filter['year'] : null;
        $quarter = isset($filter['quarter']) ? $filter['quarter'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $quarterValue = Quarter::query();
        
        if (isset($id)) {
            $quarterValue->where('id', $id);
        }
        if (isset($year)) {
            $quarterValue->where('year', $year );
        }
        if (isset($quarter)) {
            $quarterValue->where('quarter',  $quarter);
        }
        if (isset($status)) {
            $quarterValue->where('status',  $status);
        }
        return $quarterValue->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        // return 
        // $request->input('id');
        $model = Quarter::find($request->input('id'));
        if (!$model) {
            $model = new Quarter();
        }


        // $model->quarter = intval($request['quarter']);
        $model->quarter = $request['quarter'];
        $model->year = $request['year'];
        $model->status = isset($request['status']) ? $request['status'] : 0;
        
        if($model->save()){
            return 'success';
        }else{
            return 'no save';
        }
    }

    public function destroy($id)
    {
        $model = Quarter::find($id);
        if ($model) {
            $model->delete();
        }
    }
    
}
