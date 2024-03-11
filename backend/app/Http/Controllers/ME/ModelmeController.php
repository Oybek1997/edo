<?php

namespace App\Http\Controllers\ME;

use App\Http\Models\ME\Modelme;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ModelmeController extends Controller 
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $modelme = Modelme::with('createdBy');
        if (isset($search)) {
            $modelme->where('name', 'ilike', "%" . $search . "%");
        }
        return $modelme->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Modelme $Modelme)
    {
        return Modelme::get();
    }

    public function update(Request $request, Modelme $Modelme)
    {
        $model = Modelme::find($request->input('id'));
        if (!$model) {
            $model = new Modelme();
            $model->created_by= Auth::id();
        }
       
        $model->name = $request['name'];
        $model->save();
    }

    public function destroy(Modelme $Modelme, $id)
    {
        $model = Modelme::find($id);
            $model->delete();
    }
}