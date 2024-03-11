<?php

namespace App\Http\Controllers;

use App\InventoryCommission;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class InventoryCommissionController extends Controller
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
        $commissions = InventoryCommission::with('warehouse')
        ->with('responsible')
        ->with('chairman')
        ->with('member1')
        ->with('member2')
        ->with('member3')
        ->with('member4')
        ->with('member5')
        ->with('shtab_member1')
        ->with('shtab_member2')
        ->with('control_group')
        ->leftJoin('inventory_warehouse_lists', 'inventory_warehouse_lists.id', 'inventory_commissions.warehouse_id');
        if (isset($filter['part_number'])) {
            $commissions->where(function (Builder $query) use ($filter) {
                return $query->where('part_number', 'like', $filter['part_number'] . "%");
            });
        }
        if (isset($filter['product_name'])) {
            $commissions->where(function (Builder $query) use ($filter) {
                return $query->where('product_name', 'like', "%" . $filter['product_name'] . "%");
            });
        }
        if (isset($filter['stock'])) {
            $commissions->where(function (Builder $query) use ($filter) {
                return $query->where('stock', 'like', $filter['stock'] . "%");
            });
        }
        if (isset($filter['unit_measure'])) {
            $commissions->where(function (Builder $query) use ($filter) {
                return $query->where('unit_measure', 'like', "%" . $filter['unit_measure'] . "%");
            });
        }
        if (isset($filter['warehouse_id'])) {
            $commissions->where('warehouse_id', $filter['warehouse_id']);
        }
        return json_encode([
        'commissions' => $commissions->select('inventory_commissions.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['inventory_product_lists.id'], 'page name', $page),
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
        $model = InventoryCommission::find($request->input('id'));

        if (!$model) {
            $model = new InventoryCommission();
        }
        $model->commission_number = $request->input('commission_number');
        $model->warehouse_id = $request->input('warehouse_id');
        $model->models = $request->input('models');
        $model->uchastka = $request->input('uchastka');
        $model->responsible_person = $request->input('responsible_person');
        $model->chairman = $request->input('chairman');
        $model->member1 = $request->input('member1');
        $model->member2 = $request->input('member2');
        $model->member3 = $request->input('member3');
        $model->member4 = $request->input('member4');
        $model->member5 = $request->input('member5');
        $model->inspector = $request->input('inspector');
        $model->shtab_member1 = $request->input('shtab_member1');
        $model->shtab_member2 = $request->input('shtab_member2');
        $model->control_group = $request->input('control_group');
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
        $model = InventoryCommission::find($id);
        $model->delete();
        return 'Deleted successfully!';
    }
}
