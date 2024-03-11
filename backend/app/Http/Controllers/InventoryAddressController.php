<?php

namespace App\Http\Controllers;

use App\InventoryAddress;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class InventoryAddressController extends Controller
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
        $addresses = InventoryAddress::with('warehouse')
        ->leftJoin('inventory_warehouse_lists', 'inventory_warehouse_lists.id', 'inventory_addresses.warehouse_id');
        if (isset($filter['address_name'])) {
            $addresses->where(function (Builder $query) use ($filter) {
                return $query->where('address_name', 'like', "%" . $filter['address_name'] . "%");
            });
        }
        if (isset($filter['warehouse_id'])) {
            $addresses->where('warehouse_id', $filter['warehouse_id']);
        }
        return json_encode([
        'addresses' => $addresses->select('inventory_addresses.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['inventory_addresses.id'], 'page name', $page),
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
        $model = InventoryAddress::find($request->input('id'));

        if (!$model) {
            $model = new InventoryAddress();
        }
        $model->address_name = $request->input('address_name');
        $model->warehouse_id = $request->input('warehouse_id');
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
        $model = InventoryAddress::find($id);
        $model->delete();
        return 'Deleted successfully!';
    }
}
