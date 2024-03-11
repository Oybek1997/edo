<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Models\EDI\Currency;
use App\Http\Models\Inventory\MainItem;
use App\Http\Models\Inventory\MainProduct;
use App\Http\Models\Inventory\MainWarehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function setItemIndex(Request $request)
    {
        if (isset($request['stock_id'])) {
            $model = new MainItem;
            $model->product_id = $request->input('product_id');
            $model->warehouse_id = $request->input('warehouse_id');
            $model->scan_by = $request->input('scan_by');
            $model->scan_date = $request->input('scan_date');
            $model->other_address = $request->input('other_address');
            $model->status = $request->input('status');
            $model->latitude = $request->input('latitude');
            $model->longitude = $request->input('longitude');
            $model->save();

            return [
                'message' => 'Successfully saved',
                'dateTime' => date('Y-m-d H:i:s'),
                'code' => '200'
            ];
        }
        return [
            'message' => 'Error',
            'code' => '500'
        ];
    }

    public function mainInventorySync(Request $request)
    {
        $user_id = $request->header('user-id');
        $data = $request->all();


        $this->read($request);

        $warehouses = MainWarehouse::select('id', 'warehouse_name')
            ->with(['mainProducts' => function ($q) {
                $q
                    ->select('main_products.id', 'main_products.inventory_number', 'product_name', 'main_products.warehouse_id', 'main_items.created_at as upload_date')
                    ->leftJoin('main_items', 'main_items.product_id', '=', 'main_products.id');
            }])
            ->whereHas('mainUserWarehouse', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->get();
        return [
            'code' => '200',
            'message' => 'Successfully',
            'data' => $warehouses,
            'date_time' => date('Y-m-d H:i:s'),
            "request_data" => $request->all(),
        ];
    }

    public function read($request)
    {
        try {
            $data = $request->all();
            // Log::channel('inventory')->info($data);
            // $tmp = [];
            if (gettype($data) == 'array') {
                
                foreach ($data as $key => $value) {
                    $product_id = $this->getProductID($value['inventory_number']);
                    $item = MainItem::where('product_id', $product_id)->first();
                    if (!$item) {
                        $item = new MainItem();
                        $item->warehouse_id = $value['warehouse_id'] ? $value['warehouse_id'] : null;
                        $item->product_id = $product_id;
                        $item->scan_by = $request->header('user-id');
                        Log::channel('inventory')->info([$value, $value['scan_date']]);
                        $item->scan_date = $value['scan_date'];
                        $item->latitude = $value['latitude'];
                        $item->longitude = $value['longitude'];
                        $item->created_at = date('Y-m-d H:i:s');
                        $item->save();
                    }
                    // $tmp[] = $item;
                }
                return [
                    'message' => 'Successfully saved',
                    'code' => '200',
                ];
            }
        } catch (\Throwable $th) {
            Log::channel('inventory')->info($th->getMessage());

            // Log::info($request->all());
            Log::error($th->getMessage());
            // foreach ($tmp as $key => $value) {
            //     $value->tmp = 1;
            //     $value->save();
            // }
            return [
                'message' => 'Internal server error',
                'code' => '500',
                'data' => $th->getMessage(),
            ];
        }
    }

    public function getProductID($inventory_number)
    {
        $quarter_id = 1;
        return MainProduct::firstOrCreate(['inventory_number' => $inventory_number])->id;
    }
}
