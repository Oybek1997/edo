<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Currency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CurrencyController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $Currencys = Currency::with([
                                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                                    ]
        );
        if (isset($search)) {
            $Currencys->where('name', 'ilike', "%" . $search . "%")
                ->orWhere('description', 'ilike', "%" . $search . "%");
        }
        return $Currencys->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Currency $Currency)
    {
        return Currency::get();
    }

    public function update(Request $request)
    {
        $model = Currency::find($request->input('id'));
        if (!$model) {
            $model = new Currency();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }

       
        $model->name = $request['name'];
        $model->description = $request['description'];
        $model->save();
    }

    public function destroy($id)
    {
        $model = Currency::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
