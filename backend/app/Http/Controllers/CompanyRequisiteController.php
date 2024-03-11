<?php

namespace App\Http\Controllers;

use App\Http\Models\CompanyRequisite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyRequisiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $requisites = CompanyRequisite::select();

        if (isset($search)) {
            $requisites->where(function ($query) use ($search) {
                return $query
                    ->where('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%")
                    ->orWhere('address_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('address_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('address_ru', 'like', "%" . $search . "%")
                    ->orWhere('inn', 'like', "%" . $search . "%")
                    ->orWhere('account', 'like', "%" . $search . "%")
                    ->orWhere('swift', 'like', "%" . $search . "%")
                    ->orWhere('oknh', 'like', "%" . $search . "%")
                    ->orWhere('mfo', 'like', "%" . $search . "%");
            });
        }
        return $requisites->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\CompanyRequisite  $companyRequisite
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyRequisite $companyRequisite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyRequisite  $companyRequisite
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyRequisite $companyRequisite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyRequisite  $companyRequisite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyRequisite $companyRequisite)
    {
        $model = CompanyRequisite::find($request->input('id'));
        if(!$model){
            $model = new CompanyRequisite();
            $model->created_by = Auth::id();
        }
        $model->name_uz_latin = $request->input('name_uz_latin');
        $model->name_uz_cyril = $request->input('name_uz_cyril');
        $model->name_ru = $request->input('name_ru');
        $model->address_uz_latin = $request->input('address_uz_latin');
        $model->address_uz_cyril = $request->input('address_uz_cyril');
        $model->address_ru = $request->input('address_ru');
        $model->inn = $request->input('inn');
        $model->account = $request->input('account');
        $model->swift = $request->input('swift');
        $model->oknh = $request->input('oknh');
        $model->mfo = $request->input('mfo');
        $model->updated_by = Auth::id();
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyRequisite  $companyRequisite
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyRequisite $companyRequisite, $id)
    {
        $model = CompanyRequisite::find($id);
        $model->delete();
    }
}
