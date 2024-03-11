<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Contract;
use App\Http\Models\EDI\Currency;
use App\Http\Models\EDI\Material;
use App\Http\Models\EDI\Warehouse;
use App\Http\Models\EDI\BusinessPartner;
use App\Http\Controllers\Controller;
use App\Http\Models\EDI\ContractDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $filter = $request->input('filter');
        $language = $request->input('language');
        $models = Contract::with(
            [
                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'businessPartner',
                'currency',
                'contractDetails.material',
                'contractDetails.targetWarehouse'
            ]
        );

        // if (!$user->hasPermission('edi-contract') && !$user->hasPermission('edi-order') && !$user->hasPermission('edi-admin')) {
        //     $models->where('created_by', $user->id);
        // }
        if (isset($filter['contract_number']) && $filter['contract_number']) {
            $models->where('contract_number', 'ilike', '%' . $filter['contract_number'] . '%');
        }
        if (isset($filter['contract_date']) && $filter['contract_date']) {
            $models->where('contract_date', 'ilike', '%' . $filter['contract_date'] . '%');
        }
        if (isset($filter['title']) && $filter['title']) {
            $models->where('title', 'ilike', '%' . $filter['title'] . '%');
        }
        if (isset($filter['bp_id']) && $filter['bp_id']) {
            $models->where('bp_id', 'ilike', '%' . $filter['bp_id'] . '%');
        }
        if (isset($filter['active_from']) && $filter['active_from']) {
            $models->where('active_from', 'ilike', '%' . $filter['active_from'] . '%');
        }
        if (isset($filter['active_to']) && $filter['active_to']) {
            $models->where('active_to', 'ilike', '%' . $filter['active_to'] . '%');
        }
        if (isset($filter['currency_id']) && $filter['currency_id']) {
            $models->where('currency_id', 'ilike', '%' . $filter['currency_id'] . '%');
        }
        if (isset($filter['total_amount']) && $filter['total_amount']) {
            $models->where('total_amount', 'ilike', '%' . $filter['total_amount'] . '%');
        }
        if (isset($filter['total_price']) && $filter['total_price']) {
            $models->where('total_price', 'ilike', '%' . $filter['total_price'] . '%');
        }
        if (isset($filter['from_date']) && $filter['from_date']) {
            $models->where('contract_date', '>=', $filter['from_date']);
        }
        if (isset($filter['to_date']) && $filter['to_date']) {
            $models->where('contract_date', '<=', $filter['to_date']);
        }
        if (isset($filter['status']) && $filter['status'] == 1) {
            $models->where('active_from', '<=', date('Y-m-d'))
                ->where('active_to', '>=', date('Y-m-d'));
        } else if (isset($filter['status']) && $filter['status'] == 0) {
            $models->whereRaw("'" . date('Y-m-d') . "' not between active_from and active_to");
        }

        return $models->orderBy('id', 'desc')->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Contract  $contract)
    {
        return StockParameter::get();
    }

    public function update(Request $request)
    {
        $model = Contract::find($request->input('id'));
        if (!$model) {
            $model = new Contract();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        if ($request->input('active_from') >= $request->input('active_to')) {
            return [
                'status' => 500,
                'message' => "Aktiv datalarni tog'ri ketma ketlikda kiriting."
            ];
        }

        $model->active_from = $request->input('active_from');
        $model->active_to = $request->input('active_to');
        $model->bp_id = $request->input('bp_id');
        $model->contract_number = $request->input('contract_number');
        $model->contract_date = $request->input('contract_date');
        $model->currency_id =  $request->input('currency_id');
        $model->title = $request->input('title');
        $model->total_amount = $request->input('total_amount');
        $model->save();

        return [
            'status' => 200,
            'message' => "Saved"
        ];
    }

    public function updateDetail(Request $request)
    {
        $contract = Contract::find($request->input('contract_id'));
        if (!$contract) {
            return ['status' => 404, 'message' => "Shartnoma topilmadi."];
        }
        $lastDetail = ContractDetail::where('contract_id', $contract->id)->orderBy('id', 'desc')->first();
        $model = ContractDetail::find($request->input('id'));
        if (!$model) {
            $model = new ContractDetail();
            $model->position = $lastDetail ? $lastDetail->position + 1 : 1;
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->contract_id = $request->input('contract_id');
        $model->material_id = $request->input('material_id');
        $model->quantity = $request->input('quantity');
        $model->price = $request->input('price');
        $model->status = $request->input('status');
        $model->tranzit_time = $request->input('tranzit_time');
        $model->frozen_period = $request->input('frozen_period');
        $model->forecast_period = $request->input('forecast_period');
        $model->net_weight = $request->input('net_weight');
        $model->brutto_weight = $request->input('brutto_weight');
        $model->target_warehouse_id = $request->input('target_warehouse_id');
        $model->moq = $request->input('moq');
        $model->ruq = $request->input('ruq');
        $model->save();

        $total_price = 0;
        foreach ($contract->contractDetails as $key => $value) {
            $total_price += $value->quantity * $value->price;
        }
        $contract->total_price = $total_price;
        $contract->save();
        return ['status' => 200, 'message' => "Ma'lumotlar muvaffaqiyatli saqlandi."];
    }

    public function getRef(Request $request)
    {
        return [
            'businessPartners' => BusinessPartner::select('id as value', 'name as text')->get(),
            'currencies' => Currency::select('id as value', 'name as text')->get(),
            'materials' => Material::select('id as value', 'material_number as text')->get(),
            'warehouses' => Warehouse::select('id as value', 'warehouse_number as text')->get()
        ];
    }

    public function destroy($id)
    {
        $model = Contract::find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function destroyDetail($id)
    {
        $model = ContractDetail::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
