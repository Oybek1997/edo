<?php

namespace App\Http\Controllers;

use App\InventoryProductList;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class InventoryProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $products = InventoryProductList::with('warehouse')
        ->leftJoin('inventory_warehouse_lists', 'inventory_warehouse_lists.id', 'inventory_product_lists.warehouse_id');
        if (isset($filter['part_number'])) {
            $products->where(function (Builder $query) use ($filter) {
                return $query->where('part_number', 'like', $filter['part_number'] . "%");
            });
        }
        if (isset($filter['product_name'])) {
            $products->where(function (Builder $query) use ($filter) {
                return $query->where('product_name', 'like', "%" . $filter['product_name'] . "%");
            });
        }
        if (isset($filter['stock'])) {
            $products->where(function (Builder $query) use ($filter) {
                return $query->where('stock', 'like', $filter['stock'] . "%");
            });
        }
        if (isset($filter['unit_measure'])) {
            $products->where(function (Builder $query) use ($filter) {
                return $query->where('unit_measure', 'like', "%" . $filter['unit_measure'] . "%");
            });
        }
        if (isset($filter['warehouse_id'])) {
            $products->where('warehouse_id', $filter['warehouse_id']);
        }
        return json_encode([
        'products' => $products->select('inventory_product_lists.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['inventory_product_lists.id'], 'page name', $page),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = InventoryProductList::find($request->input('id'));

        if (!$model) {
            $model = new InventoryProductList();
        }
        $model->warehouse_id = $request->input('warehouse_id');
        $model->part_number = $request->input('part_number');
        $model->product_name = $request->input('product_name');
        $model->stock = $request->input('stock');
        $model->unit_measure = $request->input('unit_measure');
        $model->save();
        return 'Saved successfully!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = InventoryProductList::find($id);
        $model->delete();
        return 'Deleted successfully!';
    }
}
