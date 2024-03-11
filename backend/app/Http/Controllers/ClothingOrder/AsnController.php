<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Asn;
use App\Http\Models\EDI\ShipmentType;
use App\Http\Models\EDI\Contract;
use App\Http\Models\EDI\NumberCounter;
use App\Http\Models\EDI\Order;
use App\Http\Models\EDI\AsnDetail;
use App\Http\Models\EDI\UserBusinessPartner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AsnController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $filter = $request->input('filter');
        $models = Asn::with(
            [
                'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                'ShipmentType',
                'asnDetails',
                'order.contract.businessPartner',
                'order.contract.currency',
                'order.contract.contractDetails.material',
                'order.contract.contractDetails.targetWarehouse',
                'order.orderDetails.contractDetail.material',
                'asnDetails.orderDetail.contractDetail.material',
            ]
        );
        if (!$user->hasPermission('edi-contract') && !$user->hasPermission('edi-order') && !$user->hasPermission('edi-admin')) {
            $userBusinessPartner = UserBusinessPartner::where('user_id', Auth::id())->get()->pluck('bp_id');
            return $userBusinessPartner;
            $models->where('created_by', $user->id);
        }
        if (isset($filter['asn_number']) && $filter['asn_number']) {
            $models->where('asn_number', 'ilike', '%' . $filter['asn_number'] . '%');
        }
        if (isset($filter['invoice']) && $filter['invoice']) {
            $models->where('invoice', 'ilike', '%' . $filter['invoice'] . '%');
        }
        if (isset($filter['container_number']) && $filter['container_number']) {
            $models->where('container_number', 'ilike', '%' . $filter['container_number'] . '%');
        }
        if (isset($filter['order_id']) && $filter['order_id']) {
            $models->whereHas('order', function ($q) use ($filter) {
                $q->where('order_number', 'ilike', '%' . $filter['order_id'] . '%');
            });
        }
        if (isset($filter['shipment_type_id']) && $filter['shipment_type_id']) {
            $models->whereHas('shipmentType', function ($q) use ($filter) {
                $q->where('name', 'ilike', '%' . $filter['shipment_type_id'] . '%');
            });
        }
        if (isset($filter['contract_id']) && $filter['contract_id']) {
            $models->whereHas('order', function ($q) use ($filter) {
                $q->whereHas('contract', function ($q1) use ($filter) {
                    $q1->where('contract_number', 'ilike', '%' . $filter['contract_id'] . '%');
                });
            });
        }
        if (isset($filter['shipment_date']) && $filter['shipment_date']) {
            $models->where('shipment_date', 'ilike', '%' . $filter['shipment_date'] . '%');
        }
        if (isset($search)) {
            $models->where(function ($query) use ($search) {
                return $query
                    ->where('asn_number', 'ilike', "%" . $search . "%")
                    ->orWhere('invoice', 'ilike', "%" . $search . "%")
                    ->orWhere('container_number', 'ilike', "%" . $search . "%");
            });
        }
        if (isset($filter['from_date']) && $filter['from_date']) {
            $models->where('created_at', '>=', $filter['from_date'] . ' 00:00:00');
        }
        if (isset($filter['to_date']) && $filter['to_date']) {
            $models->where('created_at', '<=', $filter['to_date'] . ' 23:59:59');
        }
        if (isset($filter['created_at']) && $filter['created_at']) {
            $models->where('created_at', 'ilike', '%' . $filter['created_at'] . '%');
        }
        return $models->orderBY('asn_number', 'desc')->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function update(Request $request, Asn $asn)
    {
        $model = Asn::find($request->input('id'));
        if (!$model) {
            $model = new Asn();
            $model->created_by = Auth::id();
            $model->asn_number = NumberCounter::getNumber('A');
        } else {
            $model->updated_by = Auth::id();
        }

        if ($model && count($model->asnDetails) && $model->order_id != $request->input('order_id')) {
            return ['status' => 500, 'message' => "Buyurtmani o'zgartirishdan oldin ushbu buyurtmaga bog'langan ASN detalizatsiyalarini o'chirish lozim."];
        }

        $model->order_id = $request->input('order_id');
        $model->container_number = $request->input('container_number');
        $model->invoice = $request->input('invoice');
        $model->shipment_date = $request->input('shipment_date');
        $model->shipment_type_id = $request->input('shipment_type_id');
        $model->save();
        return ['status' => 200, 'message' => "Muvaffaqiyatli saqalndi."];
    }

    public function updateDetail(Request $request)
    {
        $asn = Asn::find($request->input('asn_id'));
        if (!$asn) {
            return ['status' => 404, 'message' => "Shartnoma topilmadi."];
        }
        $model = AsnDetail::find($request->input('id'));
        if (!$model) {
            $model = new AsnDetail();
            $model->created_by = Auth::id();
        }
        $model->asn_id = $request->input('asn_id');
        $model->order_detail_id = $request->input('order_detail_id');
        $model->shipment_quantity = $request->input('shipment_quantity');
        try {
            $model->save();
            Order::reduceShipmentQuantity($asn->order_id);
        } catch (\Illuminate\Database\QueryException $ex) {
            return ['status' => 500, 'message' => $ex->getCode() == 23505 ? "Ushbu material bu buyurtmaga avval qo'shilgan." : "Ma'lumotlarni bazaga saqlashda hatolik yuz berdi.", $ex->errorInfo];
            // dd($ex->getCode(),$ex->errorInfo);
        }

        return ['status' => 200, 'message' => "Ma'lumotlar muvaffaqiyatli saqlandi.", $model];
    }

    public function getRef(Request $request)
    {
        return [
            'shipmentTypes' => ShipmentType::select('id as value', 'name as text')->get(),
            'contracts' => Contract::select('id as value', 'contract_number as text')->get(),
            'orders' => Order::select('id as value', 'order_number as text')->get(),
        ];
    }

    public function destroy($id)
    {
        //return $id;
        $model = Asn::find($id);
        // return $model;

        $model->delete();
    }

    public function destroyDetail($id)
    {
        $model = AsnDetail::find($id);
        if ($model) {
            $model->delete();
        }
    }

    public function print($id)
    {
        $asn = Asn::find($id);

        $bp = $asn->order->contract->businessPartner;

        $content = "<style>table tr td {vertical-align:top; padding:5px;}</style>";

        foreach ($asn->asnDetails as $key => $value) {
            $content .= "<table border='1' style='border-collapse:collapse;width:100%;margin-top:70px;'>";
            $content .= "<tr>";
            $content .= "<td style='height:190px;width:30%;'>";
            $content .= "From:<br>";
            $content .= $bp->name;
            $content .= "<br>";
            $content .= "<br>";
            $content .= "<br>";
            $content .= "<br>";
            $content .= "Address:<br><span style='font-size:16pt;'>";
            $content .= $bp->address;
            $content .= "</span></td>";
            $content .= "<td style='width:30%;'>";
            $content .= "To:<br>";
            $content .= "73, Bobur street, Andijan city,<br>170011 Enterprice code 2838100<br>";
            $content .= "<br>";
            $content .= "<br>";
            $content .= "PLANT / DOCK :<br><span style='font-size:16pt;'>";
            $content .= $value->orderDetail->contractDetail->targetWarehouse->warehouse_number;
            $content .= "</span></td>";
            $content .= "<td style='width:30%;padding: 40px;'>";
            $asn_text = "Asn";
            $content .= '<img width="100" height="100" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($asn->asn_number)) . '"/>';
            $content .= "</td>";
            $content .= "</tr>";

            $content .= "<tr>";
            $content .= "<td style='height:70px;width:30%;'>";
            $content .= "Quantity:";
            $content .= "<br><span style='font-size:30pt;'>";
            $content .= $value->shipment_quantity;
            $content .= "</span></td>";
            $content .= "<td style='width:30%;'>";
            $content .= "MATERIAL HANDLING CODE:";
            $content .= "</td>";
            $content .= "<td style='width:30%;'>";
            $content .= "REFERENCE:<br><span style='font-size:22pt;'>";
            $content .= $asn->order->contract->contract_number;
            $content .= "</span></td>";
            $content .= "</tr>";

            $content .= "<tr>";
            $content .= "<td style='height:70px;' colspan='3'>";
            $content .= "PART NUMBER:";
            $content .= "<br><span style='font-size:30pt; margin-left:50px;'>";
            $content .= $value->orderDetail->contractDetail->material->material_number;
            $content .= "</span></td>";
            $content .= "</tr>";

            $content .= "<tr>";
            $content .= "<td style='height:150px;' colspan='2'>";
            $content .= "LICENCE PLATE(1J):";
            $content .= "<br>";
            $un_number = "UN " . $asn->order->contract->businessPartner->duns_number . " " . str_pad($asn->id, 5, 0, STR_PAD_RIGHT) . "" . str_pad($key + 1, 4, 0, STR_PAD_LEFT);
            $content .= '<img width="100" height="100" style="margin: 20px 100px 5px;" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($un_number)) . '"/>';
            $content .= "<br><span style='margin-left:50px;'>";
            $content .= $un_number;
            $content .= "</span></td>";
            $content .= "<td style=''>";
            $content .= "SHIPMENT DATE:";
            $content .= "<br><span style='font-size:25pt; margin-left:5px;'>";
            $content .= substr($value->asn->created_at, 0, 10);
            $content .= "</span><br>";
            $content .= "CONTAINER TYPE:<br><span style='font-size:25pt; margin-left:5px;'>CNT</span>";
            $content .= "<br>";
            $content .= "GROSS WEIGHT KG:";
            $content .= $value->orderDetail->contractDetail->net_weight;
            $content .= "</td>";
            $content .= "</tr>";

            $content .= "<tr>";
            $content .= "<td style='' colspan='2'>";
            $content .= "<td style=''>";
            $content .= "DELIVERYNOTE or PUS or INVOICE NUMBER:";
            $content .= $asn->invoice;
            $content .= "</td>";
            $content .= "</tr>";

            $content .= "</table>";
        }

        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->setOption('images', true)
            // ->setOrientation('landscape')
            ->setOption('footer-right', '[page] / [topage]')
            ->setOption('footer-font-name', 'times')
            ->setOption('footer-font-size', '10')
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            return $pdf->inline();
            // $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }
}
