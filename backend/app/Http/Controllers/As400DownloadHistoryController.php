<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\As400DownloadHistory;
use App\Http\Models\As400Query;
use Illuminate\Support\Facades\Auth;

class As400DownloadHistoryController extends Controller
{
    public function index()
    {
        $data = As400DownloadHistory::query()
                                    ->with('As400Query')
                                    ->with('queryBy.employee')
                                    ->where('user_id', '=', Auth::id())
                                    ->get();
        return $data;
    }

    public function update(Request $request)
    {
        $model = As400DownloadHistory::find($request->input('id'));
        if (!$model) {
            $model = new As400DownloadHistory();
            $model->user_id = Auth::id();
        }

        $model->as400_query_id = $request->input('query_name');
        $model->query = $request->input('query');
        $model->use_date_time = date('Y-m-d H:i:s');
        $model->save();
    }
    // public function getExcel(Request $request)
    // {
    //     $staffCriticals = StaffCritical::with('employee')
    //     ->with('staff.position')
    //     ->with('staff.department')
    //     ->get();
    //     $excel = [];
    //     $department = '';
    //     $position = '';
    //     $begin_date = '';
    //     $end_date = '';
    //     $description = '';
    //     foreach ($staffCriticals as $key => $value) {
    //         // return $value;
    //                 $department = $value->staff->department ? $value->staff->department['name_' . $_locale] : '';
    //                 $position = $value->staff->position ? $value->staff->position['name_' . $_locale] : "";
    //                 $begin_date = $value->begin_date ? $value->begin_date : '';
    //                 $end_date = $value->end_date ? $value->end_date : '';
    //                 $description = $value->description ? $value->description : '';

    //         array_push($excel, (object)[
    //             "№" => $key + 1 + $page * $perPage - $perPage,
    //             "Сотрудник" => $value->employee['firstname_' . $locale] . ' ' . $value->employee['lastname_' . $locale] . ' ' . $value->employee['middlename_' . $locale],
    //             "Подразделения" => $department,
    //             "Должность" => $position,
    //             "Дата начала" => $begin_date,
    //             "Дата окончания" => $end_date,
    //             "Описание" => $description,
    //         ]);
    //     }
    //     return $excel;
    // }
}
