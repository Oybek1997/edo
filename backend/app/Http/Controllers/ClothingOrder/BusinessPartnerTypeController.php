<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\BusinessPartnerType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;



class BusinessPartnerTypeController extends Controller
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

        $materialGroups = BusinessPartnerType::with([
                                                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                    ]
        );

        if (isset($search)) {
            $materialGroups->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%");
            });
        }
        return $materialGroups->orderBY('name', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Http\Models\BusinessPartnerType $businessPartnerType
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessPartnerType $businessPartnerType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\BusinessPartnerType $businessPartnerType
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessPartnerType $businessPartnerType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\BusinessPartnerType $businessPartnerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessPartnerType $businessPartnerType)
    {
        $model = BusinessPartnerType::find($request->input('id'));
        if (!$model) {
            $model = new BusinessPartnerType();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        // if (!$model) {
        //     $model = new BusinessPartner();
        //     $model->created_by = Auth::id();
        //     // $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        // }else {
        //     $model->updated_by = Auth::id();
        //     $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        // }

        $model->name = $request['name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\BusinessPartnerType $businessPartnerType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //return $id;
        $model = BusinessPartnerType::find($id);
        // return $model;

        $model->delete();
    }
}
