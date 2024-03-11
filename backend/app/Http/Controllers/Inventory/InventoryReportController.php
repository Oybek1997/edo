<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Inventory\Item;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\commissionUsers;
use App\Http\Models\Employee;
use App\User;
use App\Http\Models\Inventory\Product;
use App\Http\Models\Inventory\Quarter;
use App\Http\Models\Inventory\Address;
use App\Http\Models\Inventory\Eo;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{

    public function getStatus()
    {
        $result = DB::connection('pg_inventory')->select("
        select w.wh_number, sum(stock) stock, sum(fact) fact from (select a.warehouse_id wh_id, 
        (select sum(stock) from address_products where address_id=a.id) stock,
        (select sum(fact) from items where (tmp is null or tmp = 1) and address_id=a.id) fact
        from addresses a) t
        left join warehouses w on w.id=t.wh_id
        group by w.wh_number
        ");
        // select w.wh_number, 
        // (select sum(stock) from address_products where address_id=a.id) stock,
        // (select sum(fact) from items where address_id=a.id) fact
        // from addresses a
        // inner join warehouses w on w.id=a.warehouse_id
        return $result;
    }
    public function getReportBlanka()
    {
        $result = DB::connection('workflow_log')->select("
        SELECT w.code,
            (select sum(stock_1c) from inventory_unique_products where warehouse_id=w.id) stock,
            (select sum(real_stock) from inventory_blank_lists where warehouse_id=w.id) fact
            from inventory_warehouse_lists w
        ");
        return $result;
    }
    public function getLocationReport(Request $request)
    {
        $filter = $request->input('filter');
        $excel = $request->input('excel');
        $address = $filter ? $filter['address'] : null;
        // $excel = $filter ? $filter['excel']: null;
        // return $excel;
        $result = DB::connection('pg_inventory')->select("

            select a.address_name, 
            (select sum(stock) from address_products where address_id=a.id) stock,
            (select sum(fact) from items where address_id=a.id) fact
            from addresses a
            order by fact desc

            ");
        // select ap.id,
        // case when substring(w.wh_number,1,2) = 'W1' then 'Asaka' else 'Xorazm' end wh_name, 
        // a.address_name, sum(ap.stock) stock, sum(i.fact) fact from address_products ap
        // left join items i on i.address_id=ap.address_id
        // inner join addresses a on a.id=ap.address_id
        // inner join warehouses w on w.id=a.warehouse_id
        // inner join products p on p.id=ap.product_id
        // group by substring(w.wh_number,1,2), a.address_name, ap.id
        // order by fact desc

        if (isset($address)) {
            $result = DB::connection('pg_inventory')->select("
            select a.address_name, 
            (select sum(stock) from address_products where address_id=a.id) stock,
            (select sum(fact) from items where address_id=a.id) fact
            from addresses a
            where a.address_name like '%$address%'
            order by fact desc

            ");
            // select ap.id,
            // case when substring(w.wh_number,1,2) = 'W1' then 'Asaka' else 'Xorazm' end wh_name, 
            // a.address_name, sum(ap.stock) stock, sum(i.fact) fact from address_products ap
            // left join items i on i.address_id=ap.address_id
            // inner join addresses a on a.id=ap.address_id
            // inner join warehouses w on w.id=a.warehouse_id
            // inner join products p on p.id=ap.product_id
            // where a.address_name like '%$address%'
            // group by substring(w.wh_number,1,2), a.address_name, ap.id
            // order by fact desc
        } else if ($excel == 1) {
            return $result;
        } else {
            $result = collect($result)->take(100);
        }
        return $result;
    }
    public function getPartnumberReport(Request $request)
    {
        $filter = $request->input('filter');
        $excel = $request->input('excel');
        $address = $filter ? $filter['address'] : null;

        $result = DB::connection('pg_inventory')->select("
            select p.part_number, 
            (select sum(stock) from address_products where product_id=p.id) stock,
            (select sum(fact) from items where product_id=p.id) fact
            from products p
            order by fact desc
        ");
        if (isset($address)) {
            $result = DB::connection('pg_inventory')->select("
            select p.part_number, 
            (select sum(stock) from address_products where product_id=p.id) stock,
            (select sum(fact) from items where product_id=p.id) fact
            from products p
            where p.part_number like '%$address%'
            order by fact desc
            ");
            // select 
            // case when substring(w.wh_number,1,2) = 'W1' then 'Asaka' else 'Xorazm' end wh_name, 
            // p.part_number, sum(ap.stock) stock, sum(i.fact) fact from address_products ap
            // left join items i on i.address_id=ap.address_id
            // inner join addresses a on a.id=ap.address_id
            // inner join warehouses w on w.id=a.warehouse_id
            // inner join products p on p.id=ap.product_id
            // where p.part_number like '%$address%'
            // group by substring(w.wh_number,1,2), p.part_number
            // order by fact desc
            // return [];
        } else if ($excel == 1) {
            return $result;
        } else {
            $result = collect($result)->take(100);
        }
        return $result;
    }

    public function changeStatus(Request $request)
    {
        // return 0;
        $item = Item::find($request->input('id'));
        if ($item) {
            $item->status = false;
            $item->save();
            return $item;
        }
    }
}
