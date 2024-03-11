<?php

namespace App\Http\Controllers\ClothingOrder;

use App\Http\Controllers\Controller;
use App\NumberCounter;
use App\Http\Models\ClothingOrder\Order;
use App\Http\Models\ClothingOrder\Product;
use App\Http\Models\ClothingOrder\Size;
use App\Http\Models\ClothingOrder\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $filter = $request->input('filter');
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $models = Order::with(
            [
                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'orderDetails.employee',
                'orderDetails.product',
                'orderDetails.size',
            ]
        );
        if (isset($filter['title']) && $filter['title']) {
            $models->where('title', 'ilike', '%' . $filter['title'] . '%');
        }
        if (isset($filter['order_number']) && $filter['order_number']) {
            $models->where('order_number', 'ilike', '%' . $filter['order_number'] . '%');
        }
        if (isset($filter['created_at']) && $filter['created_at']) {
            $models->where(DB::raw('SUBSTRING(created_at::varchar,1,10)'), 'ilike', '%' . $filter['created_at'] . '%');
        }
        if (isset($filter['from_date']) && $filter['from_date']) {
            $models->where('created_at', '>=', $filter['from_date'] . ' 00:00:00');
        }
        if (isset($filter['to_date']) && $filter['to_date']) {
            $models->where('created_at', '<=', $filter['to_date'] . ' 23:59:59');
        }
        return $models->orderBy('order_number', 'desc')->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function create()
    {
        //
    }

    public function show(Order $Order)
    {
        return Order::get();
    }

    public function update(Request $request)
    {
        $model = Order::find($request->input('id'));

        if (!$model) {
            $model = new Order();
            $model->created_by = Auth::id();
            $model->responsible_id = Auth::id();
            $model->order_number = NumberCounter::getNumber('CO');
            $model->status = 0;
        } else {
            $model->updated_by = Auth::id();
        }

        $model->title = $request->input('title');
        $model->responsible_id = $request->input('responsible_id') ? $request->input('responsible_id') : 0;
        $model->executer_id = $request->input('executer_id');
        $model->save();
        return ['status' => 200, 'message' => "Buyurtma saqlandi."];
    }

    public function updateDetail(Request $request)
    {
        $order = Order::find($request->input('order_id'));
        if (!$order) {
            return ['status' => 404, 'message' => "Buyurtma topilmadi."];
        }

        $model = OrderDetail::find($request->input('id'));
        if (!$model) {
            $model = new OrderDetail();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->order_id = $request->input('order_id');
        $model->size_id = $request->input('size_id');
        $model->product_id = $request->input('product_id');
        $model->employee_id = $request->input('employee_id');
        try {
            $model->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            return ['status' => 500, 'message' => $ex->getCode() == 23505 ? "Ushbu material bu buyurtmaga avval qo'shilgan." : "Ma'lumotlarni bazaga saqlashda hatolik yuz berdi.", $ex->errorInfo];
            // dd($ex->getCode(),$ex->errorInfo);
        }
        return ['status' => 200, 'message' => "Ma'lumotlar muvaffaqiyatli saqlandi.", $model];
    }

    public function getRef(Request $request)
    {
        return [
            'products' => Product::select('id as value', 'name as text')->get(),
            'sizes' => Size::select('id as value', 'size as text', 'product_id')->get(),
        ];
    }

    public function destroy($id)
    {
        $model = Order::find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function destroyDetail($id)
    {
        $model = OrderDetail::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
