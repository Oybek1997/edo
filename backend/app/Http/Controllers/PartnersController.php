<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\PartnersModel;
use Illuminate\Support\Facades\Auth;

class PartnersController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $partners = PartnersModel::select();

        if (isset($search)) {
            $partners->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%")
                    ->orWhere('adress', 'like', "%" . $search . "%")
                    ->orWhere('bank_name', 'like', "%" . $search . "%")
                    ->orWhere('bank_adress', 'like', "%" . $search . "%")
                    ->orWhere('account', 'like', "%" . $search . "%")
                    ->orWhere('swift_code', 'like', "%" . $search . "%")
                    ->orWhere('inn', 'like', "%" . $search . "%")
                    ->orWhere('mfo', 'like', "%" . $search . "%");
            });
        }
        return  $partners->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
        $partnersModel = PartnersModel::create($request->all());
        return response()->json(PartnersModel::get(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partners = PartnersModel::find($id);
        if (is_null($partners)) {
            return response()->json(["message" => "Ma`lumot topilmadi"], 404);
        }

        return response()->json(PartnersModel::find($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PartnersModel $partnersModel)
    {
        //
        $model = PartnersModel::find($request->input('id'));
        if (!$model) {
            $model = new PartnersModel();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->name = $request['name'];
        $model->adress = $request['adress'];
        $model->bank_name = $request['bank_name'];
        $model->bank_adress = $request['bank_adress'];
        $model->account = $request['account'];
        $model->swift_code = $request['swift_code'];
        $model->inn = $request['inn'];
        $model->mfo = $request['mfo'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartnersModel $partnersModel, $id)
    {
        $model = PartnersModel::find($id);
        $model->delete();
        // return response()->json(null, 204);
    }
}
