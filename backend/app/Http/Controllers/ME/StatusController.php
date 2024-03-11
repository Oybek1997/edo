<?php

namespace App\Http\Controllers\ME;

use App\Http\Models\ME\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StatusController extends Controller 
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $status = Status::with('createdBy');
        if (isset($search)) {
            $status->where('name', 'ilike', "%" . $search . "%");
        }
        return $status->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Status $Status)
    {
        return Status::get();
    }

    public function update(Request $request, Status $Status)
    {
        $model = Status::find($request->input('id'));
        if (!$model) {
            $model = new Status();
            $model->created_by= Auth::id();
        }
       
        $model->name = $request['name'];
        $model->save();
    }

    public function destroy(Status $Status, $id)
    {
        $model = Status::find($id);
            $model->delete();
    }
}