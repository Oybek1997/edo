<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Shipmenttype;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ShipmentTypeController extends Controller
{
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

        $unitMeasures = Shipmenttype::select('*');

        if (isset($search)) {
            $unitMeasures->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%");
                    //->orWhere('value', 'like', "%" . $search . "%");
            });
        }
        return $unitMeasures->orderBY('name', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\Unitmeasure $unitMeasure
     * @return \Illuminate\Http\Response
     */
    public function show(Shipmenttype $shipmentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Shipmenttype $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function edit(Shipmenttype $shipmentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Shipmenttype $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipmenttype $shipmentType)
    {
        //return $request;
        //
        $model = Shipmenttype::find($request->input('id'));
        if (!$model) {
            $model = new Shipmenttype();
        } else {
        }
        $model->name = $request['name'];
        $model->value = $request['value'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Shipmenttype $shipmentType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //return $id;
        $model = Shipmenttype::find($id);
        // return $model;

        $model->delete();
    }
}
