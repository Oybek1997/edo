<?php

namespace App\Http\Controllers;

use App\Http\Models\TariffScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TariffScaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //return TariffScale::get();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $tariffScale = TariffScale::select();
        
        if (isset($search)) {
            $tariffScale->where(function ($query) use ($search) {
                return $query
                    ->where('category', 'like', "%" . $search . "%")
                    ->orWhere('salary', 'like', "%" . $search . "%")
                    ->orWhere('hourly_salary', 'like', "%" . $search . "%")
                    ->orWhere('description', 'like', "%" . $search . "%");
            });
        }
        return $tariffScale->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\http\models\TariffScale  $TariffScale
     * @return \Illuminate\Http\Response
     */
    public function show(TariffScale $TariffScale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\TariffScale  $TariffScale
     * @return \Illuminate\Http\Response
     */
    public function edit(TariffScale $TariffScale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\TariffScale  $TariffScale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TariffScale $TariffScale)
    {
        //
        $model = TariffScale::find($request->input('id'));
        if (!$model) {
            $model = new TariffScale();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->category = $request['category'];
        $model->salary = $request['salary'];
        $model->order_date = $request['order_date'];
        $model->order_number = $request['order_number'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\TariffScale  $TariffScale
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = TariffScale::find($id);
        $model->delete();
    }
    public function getTariffScales()
    {
        return TariffScale::select(['id','description','category'])->get();
    }
}
