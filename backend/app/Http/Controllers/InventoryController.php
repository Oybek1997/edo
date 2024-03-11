<?php

namespace App\Http\Controllers;

use App\InventoryAddress;
use App\InventoryBlankList;
use App\InventoryCommission;
use App\CommissionWarehouse;
use App\InventoryProductList;
use App\InventoryUniqueProduct;
use App\InventoryWarehouseList;
use App\InventoryCommissionBlank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $inventoryBlankList = [];
        if ($user->hasRole('inventory_controller') || $user->hasRole('inventory_report') || $user->hasRole('inventory_blank_status_change')) {
            $inventoryBlankList = InventoryBlankList::query()
                ->select('inventory_blank_lists.*')
                ->with('warehouse')
                ->with('address')
                ->with('product')
                ->with('commission')
                ->with('product')
                ->orderBy('id', 'desc')
                ->distinct();
        } elseif ($user->hasRole('inventory_operator')) {
            $inventoryBlankList = InventoryBlankList::query()
                ->with('warehouse')
                ->with('address')
                ->with('commission')
                ->where('created_by', $user->id)
                ->orderBy('id', 'desc')
                ->select('inventory_blank_lists.*')
                ->distinct();
        } else {
            return 0;
        }
        if ($request->input('type') == 2) {
            $inventoryBlankList->where('blank_status', 1);
            $inventoryBlankList->whereNotNull('checked_at');
        }
        if (isset($filter['warehouse_id'])) {
            $inventoryBlankList->where('warehouse_id', $filter['warehouse_id']);
        }
        if (isset($filter['commission_id'])) {
            $inventoryBlankList->where('inventory_commission_id', $filter['commission_id']);
        }
        if (isset($filter['blank_date'])) {
            $inventoryBlankList->where('blank_date', $filter['blank_date']);
        }
        if (isset($filter['blank_number'])) {
            $inventoryBlankList->where('blank_number', 'like', '%' . $filter['blank_number'] . '%');
        }
        if (isset($filter['part_number'])) {
            $inventoryBlankList->where('part_number', 'like', '%' . $filter['part_number'] . '%');
        }
        if (isset($filter['real_stock'])) {
            $inventoryBlankList->where('real_stock', 'like', '%' . $filter['real_stock'] . '%');
        }
        if (isset($filter['inventory_address_id'])) {
            $inventoryBlankList->where('inventory_address_id', $filter['inventory_address_id']);
        }
        $inventoryBlankList->where('year', 2024);
        return $inventoryBlankList->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }


    public function attach(Request $request)
    {
        $user = Auth::user();

        $commission = $request["commission"];
        $letter = $request["letter"];

        if ($letter == Null) {
            $inventoryCommission = InventoryCommission::where('commission_number', $commission)->first();
            if (!$inventoryCommission) {
                $invCommissionObj = new InventoryCommission();
                $invCommissionObj->year = 2024;
                $invCommissionObj->commission_number = $commission;
                $invCommissionObj->warehouse_id = 16;
                $invCommissionObj->models = null;
                $invCommissionObj->uchastka = null;
                $invCommissionObj->responsible_person = null;
                $invCommissionObj->chairman = null;
                $invCommissionObj->member1 = null;
                $invCommissionObj->member2 = null;
                $invCommissionObj->member3 = null;
                $invCommissionObj->member4 = null;
                $invCommissionObj->member5 = null;
                $invCommissionObj->inspector = null;
                $invCommissionObj->shtab_member1 = null;
                $invCommissionObj->shtab_member2 = null;
                $invCommissionObj->control_group = null;
                $invCommissionObj->status = null;
                $invCommissionObj->save();
                $commissionId = InventoryCommission::where('commission_number', $commission)->first()->id;
                $warehouseId = InventoryCommission::where('commission_number', $commission)->first()->warehouse_id;
                $commissionWarehouseObj=new CommissionWarehouse();
                $commissionWarehouseObj->commission_id=$commissionId;
                $commissionWarehouseObj->warehouse_id=$warehouseId;
                $commissionWarehouseObj->save();

                return ["commissionStatus" => 1];
            } else {
                return ["commissionStatus" => 0];
            }
        }

        $resultCommission = $request["commission"] . $request["letter"];
        $inventoryCommission = InventoryCommission::where('commission_number', $resultCommission)->first();

        if (!$inventoryCommission) {

            $commissionObj = InventoryCommission::where('commission_number', $commission)->first();
            //return $commissionObj;
            if(!$commissionObj){
                return  ["commissionStatus" => 500];
            }

            $commissionId=$commissionObj->id;
            $warehouseId = CommissionWarehouse::where('commission_id', $commissionId)->first()->warehouse_id;



            $invCommissionObj = new InventoryCommission();
            $invCommissionObj->year = 2024;
            $invCommissionObj->commission_number = $resultCommission;
            $invCommissionObj->warehouse_id = null;
            $invCommissionObj->models = null;
            $invCommissionObj->uchastka = null;
            $invCommissionObj->responsible_person = null;
            $invCommissionObj->chairman = null;
            $invCommissionObj->member1 = null;
            $invCommissionObj->member2 = null;
            $invCommissionObj->member3 = null;
            $invCommissionObj->member4 = null;
            $invCommissionObj->member5 = null;
            $invCommissionObj->inspector = null;
            $invCommissionObj->shtab_member1 = null;
            $invCommissionObj->shtab_member2 = null;
            $invCommissionObj->control_group = null;
            $invCommissionObj->status = null;
            $invCommissionObj->save();

            //return "Successfully";

            $newCommissionId = InventoryCommission::where('commission_number', $resultCommission)->first()->id;

            $commissionWarehouseObj=new CommissionWarehouse();
            $commissionWarehouseObj->commission_id=$newCommissionId;
            $commissionWarehouseObj->warehouse_id=$warehouseId;
            $commissionWarehouseObj->save();

            return ["commissionStatus" => 1];
        } else {
            return ["commissionStatus" => 0];
        }
    }

    public function report11(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $inventoryBlankList = [];

        $inventoryUniqueProducts = InventoryUniqueProduct::with('warehouse');
        if (isset($filter['warehouse_id'])) {
            $inventoryUniqueProducts->where('warehouse_id', $filter['warehouse_id']);
        }
        if (isset($filter['part_number'])) {
            $inventoryUniqueProducts->where('part_number', $filter['part_number']);
        }
        if (isset($filter['part_name'])) {
            $inventoryUniqueProducts->where('product_name', 'like', '%' . $filter['part_name'] . '%');
        }
        // $inventoryUniqueProducts->get();
        $inventoryUniqueProducts = $inventoryUniqueProducts->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
        foreach ($inventoryUniqueProducts as $key => $inventoryUniqueProduct) {
            $inventoryBlankList = InventoryBlankList::where('warehouse_id', $inventoryUniqueProduct->warehouse_id)
                ->where('part_number', $inventoryUniqueProduct->part_number)->sum('real_stock');
            $inventoryUniqueProduct->real_stock = $inventoryBlankList;
            // $inventoryUniqueProduct->real_stock = $inventoryBlankList;
        }
        return $inventoryUniqueProducts;
    }

    public function report1(Request $request)
    {
        $report_type = $request->input('report_type');
        $search = $request->input('search');
        if ($report_type == 1) {
            return DB::connection('workflow_log')
                ->select('SELECT w.name, sum(b.real_stock) sum , b1.blank_count FROM `inventory_blank_lists` b inner join inventory_warehouse_lists w on w.id = b.warehouse_id inner join (select count(id) blank_count, warehouse_id from inventory_blank_lists group by warehouse_id) b1 on w.id = b1.warehouse_id WHERE b.blank_status = 1 GROUP by w.name, b1.blank_count');
        } else if ($report_type == 2) {
            if ($search != '') {
                return DB::connection('workflow_log')
                    ->select("SELECT p.part_number, p.product_name name,b.stock_sum sum, p2.stock_sum
                    FROM inventory_product_lists p
                    inner join (select part_number, sum(real_stock) stock_sum from inventory_blank_lists group by part_number ) b on b.part_number = p.part_number
                    inner join (select part_number, sum(stock) stock_sum from inventory_product_lists group by part_number ) p2 on p2.part_number = p.part_number
                    where p.part_number like '%" . $search . "%'
                    GROUP by p.part_number, p.product_name,b.stock_sum, p2.stock_sum");
            } else {
                return DB::connection('workflow_log')
                    ->select("SELECT p.part_number, p.product_name name,b.stock_sum sum, p2.stock_sum
                    FROM inventory_product_lists p
                    inner join (select part_number, sum(real_stock) stock_sum from inventory_blank_lists group by part_number ) b on b.part_number = p.part_number
                    inner join (select part_number, sum(stock) stock_sum from inventory_product_lists group by part_number ) p2 on p2.part_number = p.part_number

                    GROUP by p.part_number, p.product_name,b.stock_sum, p2.stock_sum limit 100");
            }
        }
    }

    public function report2(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $status = $request->input('status');
        $inventoryBlankList = [];

        $uniqueProducts = InventoryUniqueProduct::with('inventoryBlankLists');
        $uniqueProducts->select('part_number', DB::raw('sum(stock_1c) as stock_1c'));
        $uniqueProducts->where('year', 2024);
        // if($status == 1){
        //     $uniqueProducts->where('status',1);
        // }
        // else if($status == 2){
        //     $uniqueProducts->where('status',0);
        // }
        $uniqueProducts->groupBy('part_number');

        $up = InventoryUniqueProduct::query();
        $up->select(DB::raw('sum(stock_1c) as stock_1c'));
        $up->where('year', 2024);
        $up->where('status', 1);

        $up1 = InventoryUniqueProduct::query();
        $up1->select(DB::raw('sum(stock_1c) as stock_1c'));
        $up1->where('year', 2024);
        $up1->where('status', 0);

        $ib = InventoryBlankList::query();
        $ib->select(DB::raw('sum(real_stock) as real_stock'));
        $ib->where('year', 2024);
        $ib->whereHas('product', function (Builder $query) {
            $query->where('status', 1)
                ->where('year', 2024);
        });
        $ib->where('blank_status', 1);

        $ib1 = InventoryBlankList::query();
        $ib1->select(DB::raw('sum(real_stock) as real_stock'));
        $ib1->where('year', 2024);
        $ib1->whereHas('product', function (Builder $query) {
            $query->where('status', 0)
                ->where('year', 2024);
        });
        $ib1->where('blank_status', 1);

        $ibc = InventoryBlankList::query();
        $ibc->select(DB::raw('sum(real_stock) as real_stock'));
        $ibc->where('year', 2024);
        $ibc->where('blank_status', 1);
        $ibc->whereHas('product', function (Builder $query) {
            $query->where('status', 1);
        });
        $ibc->where('blank_status', 1);
        $ibc->whereNotNull('checked_at');

        $ibc1 = InventoryBlankList::query();
        $ibc1->select(DB::raw('sum(real_stock) as real_stock'));
        $ibc1->where('year', 2024);
        $ibc1->where('blank_status', 1);
        $ibc1->whereHas('product', function (Builder $query) {
            $query->where('status', 0);
        });
        $ibc1->where('blank_status', 1);
        $ibc1->whereNotNull('checked_at');

        $uniqueProducts = $uniqueProducts->paginate($itemsPerPage == '-1' ? 100000 : $itemsPerPage, ['*'], 'page name', $page);
        return [$uniqueProducts, $up->first()->stock_1c, $ib->first()->real_stock * 1, $ibc->first()->real_stock * 1, $up1->first()->stock_1c, $ib1->first()->real_stock * 1, $ibc1->first()->real_stock * 1];
    }

    public function blank(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $offset = ($page - 1) * $itemsPerPage;
        $total = DB::connection('workflow_log')
            ->select('SELECT COUNT(num2.blank_number) AS TOTAL FROM inventory_blank_lists
              RIGHT JOIN (SELECT DISTINCT inventory_commission_blanks.blank_number FROM inventory_commission_blanks) num2
              ON inventory_blank_lists.blank_number = num2.blank_number WHERE inventory_blank_lists.blank_number IS null ');

        $inventoryBlank = DB::connection('workflow_log')
            ->select('SELECT DISTINCT inventory_blank_lists.blank_number as num1, num2.blank_number FROM inventory_blank_lists 
              RIGHT JOIN (SELECT DISTINCT inventory_commission_blanks.blank_number FROM inventory_commission_blanks) num2 
              ON inventory_blank_lists.blank_number = num2.blank_number WHERE inventory_blank_lists.blank_number IS null limit ' . $offset . ',' . $itemsPerPage);

        if (isset($filter['commission_id'])) {
            $inventoryBlank->where('c1.commission_number', $filter['commission_id']);
        }

        if (isset($filter['blank_number'])) {
            $inventoryBlank->where(function (Builder $query) use ($filter) {
                return $query->where('num2.blank_number', 'like', "%" . $filter['blank_number'] . "%");
            });
        }
        return ['total' => $total[0]->TOTAL, 'items' => $inventoryBlank];
    }

    public function blankReport()
    {
        $inventoryBlank = DB::connection('workflow_log')
            ->select('SELECT DISTINCT  num2.blank_number FROM inventory_blank_lists
              RIGHT JOIN (SELECT DISTINCT inventory_commission_blanks.blank_number FROM inventory_commission_blanks) num2
              ON inventory_blank_lists.blank_number = num2.blank_number WHERE inventory_blank_lists.blank_number IS null ');


        return $inventoryBlank;
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

    public function changeStatus(Request $request)
    {
        $blank = InventoryBlankList::find($request->input('id'));
        if ($blank) {
            $blank->blank_status = 0;
            $blank->updated_by = Auth::id();
            $blank->save();
            return 200;
        } else {
            return 404;
        }
    }

    public function changeStatusold(Request $request)
    {
        $blanks = InventoryBlankList::where('blank_number', $request['blank_number'])->get();
        if ($blanks) {
            foreach ($blanks as $key => $blank) {
                $blank->blank_status = 0;
                $blank->updated_by = Auth::id();
                $blank->save();
            }
            return 200;
        } else {
            return 404;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function getRef()
    {
        return [
            'commissions' => InventoryCommission::where('year', 2024)->with('warehouses')->get(),
            'warehouses' => InventoryWarehouseList::where('year', 2024)->get(),
            'addresses' => InventoryAddress::where('year', 2024)->get(),
        ];
    }

    public function getRef1(Request $request)
    {
        $status = $request->input('status');
        $statuses = [];
        switch ($status) {
            case 1:
                $statuses = [1, 3];
                break;
            case 2:
                $statuses = [2, 3];
                break;
            default:
                $statuses = [];
                break;
        }
        return [
            'commissions' => InventoryCommission::where('year', 2024)->with('warehouses')->get(),
            // 'commissions' => InventoryCommission::whereIn('status',$statuses)->where('year', 2024)->with('warehouses')->get(),
            'warehouses' => InventoryWarehouseList::where('year', 2024)->get(),
            'addresses' => InventoryAddress::where('year', 2024)->get(),
        ];
    }

    public function getComission(Request $request)
    {
        $status = $request->input('status');
        $statuses = [];
        switch ($status) {
            case 1:
                $statuses = [1, 3];
                break;
            case 2:
                $statuses = [2, 3];
                break;
            default:
                $statuses = [];
                break;
        }
        return [
            'commissions' => InventoryCommission::get(),
            'warehouses' => InventoryWarehouseList::get(),
            'addresses' => InventoryAddress::get(),
        ];
    }


    public function getPart($part_number, $warehouse_id)
    {
        $address_ids = InventoryProductList::where('part_number', $part_number)
            ->where('year', 2024)
            ->get()
            ->pluck('address_id');

        $p = InventoryProductList::where('part_number', $part_number)
            ->where('year', 2024)
            ->first();

        if (!$p) {
            $p = new InventoryProductList();
            $p->part_number = $part_number;
            $p->product_name = $part_number;
            $p->stock = 0;
            $p->year = 2024;
            $p->address_id = 20834;
            $p->unit_measure = '-';
            $p->with_error = 1;
            $p->save();
            $address_ids = [20834];
        }

        $addr = InventoryAddress::where('year', 2024)
            ->select('id as value', 'address_name as text')
            ->whereIn('id', $address_ids)
            ->get();
        $p->addresses = $addr;
        return $p;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $products = $request->input('products');
        $errors = '';

        $product = InventoryProductList::where('part_number', $request->input('part_number'))->first();

        if (!$product) {
            $product = new InventoryProductList();
            $product->warehouse_id = $request->input('warehouse_id');
            $product->part_number = $request->input('part_number');
            $product->product_name = $request->input('part_number');
            $product->stock = 0;
            $product->year = 2024;
            $product->with_error = 1;
            // $product->unit_measure = $request->input('unit_measure');
            $product->save();
        }

        foreach ($products as $key => $value) {
            if ($value['real_stock']) {
                $model = InventoryBlankList::find($request->input('id'));
                if ($model) {
                    $model->updated_by = Auth::id();
                } else {
                    if (InventoryBlankList::where('blank_number', $request->input('blank_number'))->where('inventory_address_id', $value['inventory_address_id'])->first() && $value['inventory_address_id'] != 27779 && $value['inventory_address_id'] != 20834) {
                        $address = InventoryAddress::find($value['inventory_address_id']);
                        $errors .= 'Ushbu Blanka: "' . $request->input('blank_number') . '" va Adres: ' . $address->address_name . ' avval kiritilgan. ';
                    }
                    // if (InventoryBlankList::where('blank_number', $request->input('blank_number'))->where('blank_status', 0)->first()) {
                    //     $errors .= 'Ushbu Blanka: "' . $request->input('blank_number') . '" yaroqsiz deb belgilangan.';
                    // }
                    $model = new InventoryBlankList();
                    $model->created_by = Auth::id();
                }
                $model->blank_number = $request->input('blank_number');
                $model->warehouse_id = $request->input('warehouse_id');
                $model->blank_date = $request->input('blank_date');
                $model->part_number = $request->input('part_number');
                $model->inventory_commission_id = $request->input('inventory_commission_id');
                $model->unit_measure = $request->input('unit_measure');
                $model->real_stock = $value['real_stock'];
                $model->inventory_address_id = isset($value['inventory_address_id']) ? $value['inventory_address_id'] : 13242;
                $model->manual_address = isset($value['manual_address']) ? $value['manual_address'] : '';
                $model->year = 2024;
                try {
                    $model->save();
                } catch (\Throwable $th) {
                    // return ['code' => 100, 'message' => 'Siz kiritilgan blankani kiritdingiz.'];
                    throw $th;
                }
            }
        }
        if ($errors != '') {
            return ['code' => 100, 'message' => $errors];
        }
        return ['code' => 200, 'message' => "Siz jo'natgan ma'lumotlar muvaffaqiyatli saqlandi."];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function check($id)
    {
        $model = InventoryBlankList::with('warehouse')->with('address')->with('commission')->find($id);
        $model->checked_at = date('Y-m-d H:i:s');
        $model->checked_by = Auth::id();
        $model->save();
        return $model;
    }

    public function getAddress($id)
    {
        return InventoryAddress::where('warehouse_id', $id)->get();
    }

    public function getWarehouses($id)
    {
        return InventoryWarehouseList::query()
            // ->join('inventory_commissions', 'inventory_commissions.warehouse_id', '=', 'inventory_warehouse_lists.id')
            // ->where('inventory_commissions.id', $id)
            // ->select('inventory_warehouse_lists.*')
            ->get();
    }

    public function checkedReport()
    {
        $brak = DB::connection('workflow_log')->select("select count(blank_number) c from inventory_blank_lists where blank_status = 0");
        $all = DB::connection('workflow_log')->select("select count(blank_number) c from inventory_blank_lists where blank_status = 1");
        $checked = DB::connection('workflow_log')->select("select count(blank_number) c from inventory_blank_lists where blank_status = 1 and checked_at is not null");
        return [number_format($checked[0]->c / $all[0]->c, 2), $brak[0]->c];
    }

    public function report(Request $request)
    {
        $result = DB::connection('workflow_log')->select("SELECT
        whid,
        NAME whname,
        qoldiq,
        topildi,
        blanka_soni,
        100 * topildi / qoldiq bajarildi
    FROM
        (
        SELECT
            wls.id whid,
            wls.name,
            (select SUM(pls.stock) from inventory_product_lists pls where wls.id = pls.warehouse_id)  Qoldiq, SUM(j.topildi) topildi, SUM(j.blanka_soni) blanka_soni
        FROM
            inventory_warehouse_lists wls
        LEFT OUTER JOIN(
            SELECT
                bl.warehouse_id,
                bl.part_number,
                SUM(bl.real_stock) Topildi,
                COUNT(bl.id) blanka_soni
            FROM
                inventory_blank_lists bl
                where bl.blank_status = 1
            GROUP BY
                bl.warehouse_id,
                bl.part_number
        ) j
    ON
        wls.id = j.warehouse_id
    GROUP BY
        wls.id,
        wls.name
    ) t");
        return $result;
    }


    public function attaching(Request $request)
    {
        $user = Auth::user();
        echo "helloo";
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $inventoryBlankList = [];
        if ($user->hasRole('inventory_controller') || $user->hasRole('inventory_report')) {
            $inventoryBlankList = InventoryBlankList::query()
                ->select('inventory_blank_lists.*')
                ->with('warehouse')
                ->with('address')
                ->with('product')
                ->with('commission')
                ->with('product')
                ->orderBy('id', 'desc')
                ->distinct();
        } elseif ($user->hasRole('inventory_operator')) {
            $inventoryBlankList = InventoryBlankList::query()
                ->with('warehouse')
                ->with('address')
                ->with('commission')
                ->where('created_by', $user->id)
                ->orderBy('id', 'desc')
                ->select('inventory_blank_lists.*')
                ->distinct();
        } else {
            return 0;
        }
        if ($request->input('type') == 2) {
            $inventoryBlankList->where('blank_status', 1);
            $inventoryBlankList->whereNotNull('checked_at');
        }
        if (isset($filter['warehouse_id'])) {
            $inventoryBlankList->where('warehouse_id', $filter['warehouse_id']);
        }
        if (isset($filter['commission_id'])) {
            $inventoryBlankList->where('inventory_commission_id', $filter['commission_id']);
        }
        if (isset($filter['blank_date'])) {
            $inventoryBlankList->where('blank_date', $filter['blank_date']);
        }
        if (isset($filter['blank_number'])) {
            $inventoryBlankList->where('blank_number', 'like', '%' . $filter['blank_number'] . '%');
        }
        if (isset($filter['part_number'])) {
            $inventoryBlankList->where('part_number', 'like', '%' . $filter['part_number'] . '%');
        }
        if (isset($filter['real_stock'])) {
            $inventoryBlankList->where('real_stock', 'like', '%' . $filter['real_stock'] . '%');
        }
        if (isset($filter['inventory_address_id'])) {
            $inventoryBlankList->where('inventory_address_id', $filter['inventory_address_id']);
        }
        $inventoryBlankList->where('year', 2022);
        return $inventoryBlankList->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }
}
