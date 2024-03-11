<?php

namespace App\Http\Controllers\Linestop;

use App\Http\Models\Linestop\Line;
use App\Http\Models\Linestop\Plcdata;
use App\Http\Models\Linestop\Reason;
use App\Http\Models\Linestop\Category;
use App\Http\Models\Linestop\Ticket;
use App\Http\Models\Linestop\Provider;
use App\Http\Models\Linestop\Productmodel;
use App\Http\Models\Linestop\TicketDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LineStopController extends Controller
{
    public function websocketApi(Request $request)
    {
        $status = $request->input('status');
        return $status;
    }
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

        $linestops = Plcdata::with(['line.shop']);

        $linestops->selectRaw('*, EXTRACT(SECOND FROM (startdt - stopdt)) AS duration');
        $linestops->orderBy('id', 'desc');

        $search = $request->input('search');
        if (!empty($search['shop'])) {
            $shopId = $search['shop'];
            $linestops->where('sector', '=', $shopId);
        }
        $line = $request->input('line');
        if (!empty($line)) {
            $linestops->where('lineid', '=', $line);
        }

        return [
            'linestops' => $linestops->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page),
        ];
    }
    public function getlines()
    {
        $lines = Line::all();
        return $lines;
    }
    public function ticketdeparments()
    {
        $categories = TicketDepartment::all();
        return $categories;
    }
    public function getreasons()
    {
        $lines = Reason::all();
        return $lines;
    }
    public function getproviders()
    {
        $providers = Provider::all();
        return $providers;
    }
    public function getproductmodels()
    {
        $productmodels = Productmodel::all();
        return $productmodels;
    }
    public function storeTicket(Request $request)
    {
        $SelectedLine = $request->input('SelectedLine');
        $SelectedSector = $request->input('SelectedSector');
        $stopLinedate = $request->input('stopLinedate');
        $startLinedate = $request->input('startLinedate');
        $OperatorTabel = $request->input('OperatorTabel');

        $newPlcdata = new Plcdata();
        $newPlcdata->lineid = $SelectedLine;
        $newPlcdata->sector = $SelectedSector;
        $newPlcdata->stopdt = $stopLinedate;
        $newPlcdata->startdt = $startLinedate;
        $newPlcdata->status = '1';
        $newPlcdata->created_by = $OperatorTabel;
        $newPlcdata->save();

        $plcdataId = $newPlcdata->id;

        $newTicket = new Ticket();
        $newTicket->plcdata_id = $plcdataId;
        $newTicket->status = '0';
        $newTicket->save();

        return 'Saved';
    }


    public function indexReasons(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

        $reasons = Reason::with('category');

        if (isset($search)) {
            $reasons->where(function ($query) use ($search) {
                return $query
                    ->where('title', 'like', "%" . $search . "%")
                    ->orWhereHas('category', function ($query) use ($search) {
                        return $query->where('name', 'like', "%" . $search . "%");
                    });
            });
        }
        return $reasons->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);
    }

    public function saveupdate(Request $request)
    {
        $model = Reason::find($request->input('id'));
        if (!$model) {
            $model = new Reason();
        }
        $model->title = $request->input('title');
        $model->category_id = $request->input('category_id');
        $model->save();
        return 'done';
    }
    public function destroy($id)
    {
        $model = Reason::find($id);
        $model->delete();
    }
    public function indexDepartments(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $reasons = TicketDepartment::select();

        if (isset($search)) {
            $reasons->where(function ($query) use ($search) {
                return $query
                    ->Where('name', 'like', "%" . $search . "%");
            });
        }
        return $reasons->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdatedepartment(Request $request)
    {
        $model = TicketDepartment::find($request->input('id'));
        if (!$model) {
            $model = new TicketDepartment();
        }
        $model->name = $request->input('name');
        $model->save();
        return 'done';
    }
    public function destroydepartment(Request $request)
    {
        $model = TicketDepartment::find($request->input('id'));
        $model->delete();
    }
    public function indexProviders(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $reasons = Provider::select();

        if (isset($search)) {
            $reasons->where(function ($query) use ($search) {
                return $query
                    ->Where('name', 'like', "%" . $search . "%");
            });
        }
        return $reasons->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdateprovider(Request $request)
    {
        $model = Provider::find($request->input('id'));
        if (!$model) {
            $model = new Provider();
        }
        $model->name = $request->input('name');
        $model->sap_code = $request->input('sap_code');
        $model->region = $request->input('region');
        $model->address = $request->input('address');
        $model->save();
        return 'done';
    }
    public function destroyprovider($id)
    {
        $model = Provider::find($id);
        $model->delete();
    }

    public function indexProductmodels(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $reasons = Productmodel::select();

        if (isset($search)) {
            $reasons->where(function ($query) use ($search) {
                return $query
                    ->Where('name', 'like', "%" . $search . "%");
            });
        }
        return $reasons->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function saveupdateproductmodel(Request $request)
    {
        $model = Productmodel::find($request->input('id'));

        if (!$model) {
            $model = new Productmodel();
        }

        $model->name = $request->input('name');
        $model->save();

        return 'done';
    }

    public function destroyproductmodel($id)
    {
        $model = Productmodel::find($id);
        $model->delete();
    }

    public function getLastMonthData(Request $request)
    {
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $lastMonthData = Plcdata::with('line')
            ->select('lineid', DB::raw('COUNT(*) AS lineid_count'), DB::raw('SUM(startdt - stopdt) AS total_duration'))
            ->whereBetween('stopdt', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('lineid')
            ->get();

        return response()->json([
            'data' => $lastMonthData,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);
    }
    public function getCategories()
    {
        $categories = Category::all();
        return $categories;
    }

    // Main page uchun hisobot qismi boshlandi
    public function getDataMainpage()
    {
        $mainpagedata = Line::withCount('linestop')->get();

        return $mainpagedata;
    }
    // Main page uchun hisobot qismi tugadi

    // linya bo'yicha vaqt orlagida hisobot qismi boshlandi
    public function getLineStatistics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = "
        SELECT l.id AS line_id, l.line AS line_name, l.comment AS line_comment, COUNT(t.id) AS total_tickets, SUM(EXTRACT(EPOCH FROM (p.startdt - p.stopdt)) / 60) AS total_duration_minutes
        FROM
        line l
        LEFT JOIN
        plcdata p ON l.id = p.lineid
        LEFT JOIN
        ticket t ON p.id = t.plcdata_id
        WHERE
        t.created_at BETWEEN '$startDate' AND '$endDate'
        GROUP BY
        l.id, l.line, l.comment";

        $lenistatistics = DB::connection('linestop')->select($query);
        return $lenistatistics;
    }

    // linya bo'yicha vaqt orlagida hisobot qismi tugadi
    // Sabablar bo'yicha vaqt orlagida hisobot qismi boshlandi
    public function getReasonStatistics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = "
        SELECT
        r.title AS reason_title,
        COUNT(t.id) AS ticket_count,
        SUM(EXTRACT(EPOCH FROM (p.startdt - p.stopdt)) / 60) AS total_duration_minutes
        FROM
            reason r
        JOIN
            ticket t ON r.id = t.reason_id
        JOIN
            plcdata p ON t.plcdata_id = p.id
        WHERE
            t.created_at BETWEEN '$startDate' AND '$endDate'
        GROUP BY
            r.title
        ORDER BY
            r.title;
        ";
        $reasonstatistics = DB::connection('linestop')->select($query);
        return $reasonstatistics;
    }
    // Sabablar bo'yicha vaqt orlagida hisobot qismi tugadi
}
