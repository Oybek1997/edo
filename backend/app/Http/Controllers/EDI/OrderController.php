<?php

namespace App\Http\Controllers\EDI;

use App\Http\Controllers\Controller;
use App\Http\Models\EDI\Order;
use App\Http\Models\EDI\OrderDetail;
use App\Http\Models\EDI\Contract;
use App\Http\Models\EDI\NumberCounter;
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
                'contract.businessPartner',
                'contract.currency',
                'contract.contractDetails.material',
                'contract.contractDetails.targetWarehouse',
                'asnDetails',
                'orderDetails.contractDetail.material'
            ]
        );
        if (isset($filter['contract_id']) && $filter['contract_id']) {
            $models->whereHas('contract', function ($q) use ($filter) {
                $q->where('contract_number', 'ilike', '%' . $filter['contract_id'] . '%');
            });
        }
        if (isset($filter['title']) && $filter['title']) {
            $models->where('title', 'ilike', '%' . $filter['title'] . '%');
        }
        if (isset($filter['order_number']) && $filter['order_number']) {
            $models->where('order_number', 'ilike', '%' . $filter['order_number'] . '%');
        }
        if (isset($filter['total_price']) && $filter['total_price']) {
            $models->where('total_price', 'ilike', '%' . $filter['total_price'] . '%');
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
            $model->order_number = NumberCounter::getNumber('B');
        } else {
            $model->updated_by = Auth::id();
        }

        if ($model && count($model->orderDetails) && $model->contract_id != $request->input('contract_id')) {
            return ['status' => 500, 'message' => "Shartnomani o'zgartirishdan oldin ushbu shartnomaga bog'langan buyurtma detalizatsiyalarini o'chirish lozim."];
        }

        $model->contract_id = $request->input('contract_id');
        $model->title = $request->input('title');
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
        $model->contract_detail_id = $request->input('contract_detail_id');
        $model->order_quantity = $request->input('order_quantity');
        $model->order_start_date = $request->input('order_start_date');
        $model->order_finish_date = $request->input('order_finish_date');
        try {
            $model->save();
            Order::reduceOrderQuantity($order->id);
        } catch (\Illuminate\Database\QueryException $ex) {
            return ['status' => 500, 'message' => $ex->getCode() == 23505 ? "Ushbu material bu buyurtmaga avval qo'shilgan." : "Ma'lumotlarni bazaga saqlashda hatolik yuz berdi.", $ex->errorInfo];
            // dd($ex->getCode(),$ex->errorInfo);
        }
        return ['status' => 200, 'message' => "Ma'lumotlar muvaffaqiyatli saqlandi.", $model];
    }

    public function getRef(Request $request)
    {
        return [
            'contracts' => Contract::select('id as value', 'contract_number as text', 'active_from', 'active_to')->get(),
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
