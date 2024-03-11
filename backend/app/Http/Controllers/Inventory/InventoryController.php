<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Inventory\Item;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\CommissionUser;
use App\Http\Models\Employee;
use App\User;
use App\Http\Models\Inventory\Product;
use App\Http\Models\Inventory\Quarter;
use App\Http\Models\Inventory\Address;
use App\Http\Models\Inventory\Eo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{

    public function getExcel(Request $request)
    {
        $page = $request->input('pagination.page');
        $filter = $request->input('filter');
        $perPage = $request->input('pagination.itemsPerPage');
        $from_date = $filter['menu1'] . ' ' . "00:00:00";
        $to_date = $filter['menu2'] . ' ' . "23:59:59";
        $query = Item::select()
            ->whereBetween('created_at', [$from_date, $to_date])
            ->with('createdby')
            ->with('updatedby')
            ->with('address')
            ->with('product')
            ->with('quarter')
            ->with('eo');
        $query->where(function ($q) {
            $q
                ->whereNull('tmp')
                ->orWhere('tmp', 1);
        });
        if (isset($filter['to_delete'])) {
            $query->where('to_delete', $filter['to_delete']);
        }
        if (isset($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (isset($filter['smesh'])) {
            $task = $filter['smesh'];
            $query->where('is_smesh', $task);
        }
        if (isset($filter['dubl'])) {
            $task = $filter['dubl'];
            $query->where('is_duplicate', $task);
        }
        if (isset($filter['partNumer'])) {
            $task = $filter['partNumer'];
            $query->whereHas('product', function ($q) use ($task) {
                $q->where('part_number', 'ilike', '%' . $task . '%');
            });
        }
        if (isset($filter['edoNumber'])) {
            $task = $filter['edoNumber'];
            $query->whereHas('eo', function ($q) use ($task) {
                $q->where('eo_number', 'ilike', '%' . $task . '%');
            });
        }
        if (isset($filter['whNumner'])) {
            $task = $filter['whNumner'];
            $query->whereHas('address', function ($q) use ($task) {
                $q->where('warehouse_id', 'ilike', $task . '%');
            });
        }
        if (isset($filter['addresName'])) {
            $task = $filter['addresName'];
            $query->whereHas('address', function ($q) use ($task) {
                $q->where('address_name', 'ilike', '%' . $task . '%');
            });
        }
        if (isset($filter['quarterYear'])) {
            $task = $filter['quarterYear'];
            $query->whereHas('quarter', function ($q) use ($task) {
                $q->where('year', $task);
            });
        }
        $name = isset($filter['name']) ? $filter['name'] : null;
        if (isset($name)) {
            $task = $filter['name'];
            $query->whereHas('createdby', function ($q) use ($task) {
                $q->where('firstname', 'ilike', $task . '%')
                    ->orWhere('lastname', 'ilike',  $task . '%')
                    ->orWhere('tab', 'ilike',  $task . '%');
            });
        }
        if (isset($filter['camNum'])) {
            $task = $filter['camNum'];
            $query->whereHas('createdby', function ($q) use ($task) {
                $q->where('commission_number', 'ilike',  $task . '%');
            });
        }
        $excel = [];
        $products = $query->paginate($perPage, ['*'], 'page name', $page);
        foreach ($products as $key => $value) {
            array_push($excel, (object) [
                "№" => $key + 1 + $page * $perPage - $perPage,
                "PartNumber" => $value->product->part_number,
                "PartName" => $value->product->name,
                "EOName" => $value->eo->eo_number,
                "WHID" => $value->address->warehouse ? $value->address->warehouse->wh_number : '',
                "AddressName" => $value->address->address_name,
                "ScanerFact" => $value->quantity,
                "ManualFact" => $value->fact,
                "ScanDate" => $value->scan_date,
                "CreatedAT" => $value->created_at,
                "UniqNumber" => $value->un_number,
                "FullQRCode" => $value->full_qr,
                "Duplicat" => !$value->is_duplicate ? 'No' : 'Yes',
                "status" => $value->status ? 'Active' : 'Deleted',
                "to_delete" => !$value->to_delete ? 'No' : 'Yes',
                "Smesh" => !$value->is_smesh ? 'No' : 'Yes',
                "CreatedBy" => $value->createdby->firstname . " " . $value->createdby->lastname,
                "Commissions" => $value->createdby->commission_number,
                "Quater" => $value->quarter ? $value->quarter->year . "/" . $value->quarter->quarter : null,
            ]);
        }
        return $excel;
    }
    public function getInfo(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $from_date = $filter['menu1'] . ' ' . "00:00:00";
        $to_date = $filter['menu2'] . ' ' . "23:59:59";
        $query = Item::select()
            ->whereBetween('created_at', [$from_date, $to_date])
            ->with('createdby')
            ->with('updatedby')
            ->with('address')
            ->with('product')
            ->with('quarter')
            ->with('eo');

        // $query->whereNull('tmp');
        $query->where(function ($q) {
            $q
                ->whereNull('tmp')
                ->orWhere('tmp', 1);
        });

        if (isset($filter['to_delete'])) {
            $query->where('to_delete', $filter['to_delete']);
        }
        if (isset($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (isset($filter['smesh'])) {
            $task = $filter['smesh'];
            $query->where('is_smesh', $task);
        }
        if (isset($filter['unNumber'])) {
            $task = $filter['unNumber'];
            $query->where('un_number', 'ilike', $task . '%');
        }
        if (isset($filter['dubl'])) {
            $task = $filter['dubl'];
            $query->where('is_duplicate', $task);
        }
        if (isset($filter['partNumer'])) {
            $task = $filter['partNumer'];
            $query->whereHas('product', function ($q) use ($task) {
                $q->where('part_number', 'ilike', '%' . $task . '%');
            });
        }
        if (isset($filter['edoNumber'])) {
            $task = $filter['edoNumber'];
            $query->whereHas('eo', function ($q) use ($task) {
                $q->where('eo_number', 'ilike', '%' . $task . '%');
            });
        }
        if (isset($filter['addresName'])) {
            $task = $filter['addresName'];
            $query->whereHas('address', function ($q) use ($task) {
                $q->where('address_name', 'ilike',  '%' . $task . '%');
            });
        }
        if (isset($filter['whNumner'])) {
            $task = $filter['whNumner'];
            $query->whereHas('address', function ($q) use ($task) {
                $q->whereHas('warehouse', function ($qw) use ($task) {
                    $qw->where('wh_number', 'ilike', $task . '%');
                });
            });
        }
        if (isset($filter['quarterYear'])) {
            $task = $filter['quarterYear'];
            $query->whereHas('quarter', function ($q) use ($task) {
                $q->where('year', $task);
            });
        }
        $name = isset($filter['name']) ? $filter['name'] : null;
        if (isset($name)) {
            $task = $filter['name'];
            $query->whereHas('createdby', function ($q) use ($task) {
                $q->where('firstname', 'ilike', $task . '%')
                    ->orWhere('lastname', 'ilike',  $task . '%')
                    ->orWhere('tab', 'ilike',  $task . '%');
            });
        }
        if (isset($filter['camNum'])) {
            $task = $filter['camNum'];
            $query->whereHas('createdby', function ($q) use ($task) {
                $q->where('commission_number', 'ilike',  $task . '%');
            });
        }
        return $query->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function readFromMobile(Request $request)
    {
        $data = $request->all();
        Log::channel('inventory')->info($data);
        // return [
        //     'message' => 'Internal server error',
        //     'code' => '500'
        // ];
        // DB::beginTransaction();
        try {
            $data = $request->all();
            Log::channel('inventory')->info($data);
            $tmp = [];
            if (gettype($data) == 'array') {
                foreach ($data as $key => $value) {
                    $item = new Item;
                    $item->address_id = $this->getAddressID($value['address']);
                    $item->eo_id = $this->getEoID($value['eo']);
                    $quarter_id = $this->getQuarterID($value['year'], $value['quarter']);
                    $item->product_id = $this->getProductID($value['part_number'], $quarter_id);
                    $item->quantity = $value['quantity'];
                    $item->fact = $value['fact'];
                    $item->full_qr = $value['full_qr'];
                    $item->un_number = $value['un_number'];
                    $item->is_smesh = $value['is_smesh'];
                    $item->scan_date = $value['scan_date'];
                    $item->quarter_id = $quarter_id;
                    $item->created_by = $value['created_by'];
                    $item->is_duplicate = $value['is_duplicate'];
                    if (!$item->is_duplicate && $item->un_number) {
                        $itm = Item::where('un_number', $item->un_number)->first();
                        if ($itm) {
                            $item->to_delete = true;
                            $item->save();
                        }
                    }
                    $item->save();
                    $tmp[] = $item;
                }
                // DB::commit();
                return [
                    'message' => 'Successfully saved',
                    'code' => '200',
                ];
            }
        } catch (\Throwable $th) {
            // DB::rollBack();
            Log::info($request->all());
            Log::error($th->getMessage());
            foreach ($tmp as $key => $value) {
                $value->tmp = 1;
                $value->save();
            }
            return [
                'message' => 'Internal server error',
                'code' => '500',
                'data' => $th->getMessage(),
            ];
        }
    }

    public function getAddressID($address)
    {
        return Address::firstOrCreate(['address_name' => $address])->id;
    }

    public function getEoID($eo_number)
    {
        return Eo::firstOrCreate(['eo_number' => $eo_number])->id;
    }

    public function getQuarterID($year, $quarter)
    {
        return Quarter::firstOrCreate(['year' => $year, 'quarter' => $quarter])->id;
    }

    public function getProductID($part_number, $quarter_id)
    {
        $quarter_id = 1;
        return Product::firstOrCreate(['part_number' => $part_number, 'quarter_id' => $quarter_id])->id;
    }
    public function testControl(Request $request)
    {
        $chekItem = Warehouse::where('wh_number', '9592')->first();
        if ($chekItem) {
            $model = new Item();
            $model->address_id = 1;
            $model->eo_id = 1;
            $model->product_id = $chekItem->id;
            $model->quantity = '10.00';
            $model->fact = '10.00';
            $model->full_qr = 'sdsdffsdfsdfsdfdassdasdasda';
            $model->un_number = 'dsfwe54fd54fw5f';
            $model->un_number = true;
            $model->is_smesh = false;
            $model->scan_date = '2022-06-02 10:32:16';
            $model->created_at = '2022-06-02 10:32:16';
            $model->updated_at = '2022-06-02 10:32:16';
            $model->created_by = 1;
            $model->updated_by = 1;
            $model->quarter_id = 1;
            $model->quarter_id = 1;
            $model->deleted_at = null;
            $model->save();
            return '11';
        } else {
            // $model=new warehouses();
            return 1;
        }
        return '$items';
        // id, address_id, eo_id, product_id, quantity, fact, full_qr, un_number, is_duplicate, is_smesh, scan_date, created_at, updated_at, created_by, updated_by, quarter_id)
    }
}
