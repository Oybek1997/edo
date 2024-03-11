<?php

namespace App\Http\Controllers;

use App\Http\Models\PurchaseCatalog;
use App\Http\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PurchaseCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $purchaseCatalog = PurchaseCatalog::with('measure')
            ->leftJoin('unit_measures', 'unit_measures.id', '=', 'purchase_catalogs.unit_measure_id')
            ->orderByRaw('code ASC');

        if (isset($filter['code'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('code', 'like', $filter['code'] . "%");
            });
        }
        if (isset($filter['part_number'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('part_number', 'like', $filter['part_number'] . "%");
            });
        }
        if (isset($filter['name_eng'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('name_eng', 'like', "%" . $filter['name_eng'] . "%");
            });
        }
        if (isset($filter['name_ru'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('name_ru', 'like', "%" . $filter['name_ru'] . "%");
            });
        }
        if (isset($filter['specification'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('specification', 'like', "%" . $filter['specification'] . "%");
            });
        }
        if (isset($filter['manufacturer'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('manufacturer', 'like', "%" . $filter['manufacturer'] . "%");
            });
        }
        if (isset($filter['unit_measure'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('unit_measures.short_name', 'like', "%" . $filter['unit_measure_name'] . "%");
            });
        }
        if (isset($filter['description'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('description', 'like', "%" . $filter['description'] . "%");
            });
        }
        if (isset($filter['using_purpose'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('using_purpose', 'like', "%" . $filter['using_purpose'] . "%");
            });
        }
        if (isset($filter['material_content'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('material_content', 'like', "%" . $filter['material_content'] . "%");
            });
        }
        if (isset($filter['using_location'])) {
            $purchaseCatalog->where(function (Builder $query) use ($filter) {
                return $query->where('using_location', 'like', "%" . $filter['using_location'] . "%");
            });
        }
        return  json_encode($purchaseCatalog->select('purchase_catalogs.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page));
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
     * @param  \App\PurchaseCatalog  $purchaseCatalog
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseCatalog $purchaseCatalog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseCatalog  $purchaseCatalog
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseCatalog $purchaseCatalog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseCatalog  $purchaseCatalog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseCatalog $purchaseCatalog)
    {
        //

        $model = PurchaseCatalog::find($request->input('id'));
        if (!$model) {
            $model = new PurchaseCatalog();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->code = $request['code'];
        $model->part_number = $request['part_number'];
        $model->name_eng = $request['name_eng'];
        $model->name_ru = $request['name_ru'];
        $model->specification = $request['specification'];
        $model->manufacturer = $request['manufacturer'];
        $model->unit_measure_id = $request['unit_measure_id'];
        $model->description = $request['description'];
        $model->using_purpose = $request['using_purpose'];
        $model->material_content = $request['material_content'];
        $model->using_location = $request['using_location'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseCatalog  $purchaseCatalog
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseCatalog $purchaseCatalog, $id)
    {
        $model = PurchaseCatalog::find($id);
        $model->delete();
    }
}
