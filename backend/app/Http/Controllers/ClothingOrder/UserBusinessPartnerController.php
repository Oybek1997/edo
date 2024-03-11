<?php

namespace App\Http\Controllers\EDI;

use App\User;
use App\Http\Models\EDI\UserBusinessPartner;
use App\Http\Models\EDI\BusinessPartner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;



class UserBusinessPartnerController extends Controller
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
        $language = $request->input('language');
        $UserBusinessPartners = UserBusinessPartner::with([
                                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                    'user',
                                    'business_partner' 
                                    ]
        );
        // if (isset($search)) {
        //     $UserBusinessPartners->where('material_number', 'like', "%" . $search . "%")
        //                 ->orWhere('description', 'like', "%" . $search . "%");
        // }
        return $UserBusinessPartners->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\http\models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function show(UserBusinessPartner $UserBusinessPartner)
    {
        return UserBusinessPartner::get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function edit(UserBusinessPartner $UserBusinessPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\UserBusinessPartner $UserBusinessPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = UserBusinessPartner::find($request->input('id'));
        // return $request->input('business_partner_id');

        if (!$model) {
            $model = new UserBusinessPartner();
       
           $model->created_by = Auth::id();
            // $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
         }  else {
             $model->updated_by = Auth::id();
        //     $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
         }


        $model->user_id = $request->input('user_id');
        $model->bp_id = $request->input('business_partner_id');
        $model->save();
        return $model;
    }

    public function getRef(Request $request)
    {
        return [
            'users' => User::select('id as value', 'username as text')->get(),
            'business_partners' => BusinessPartner::select('id as value', 'name as text')->get(),
        ];
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\EDI\UserBusinessPartner $UserBusinessPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = UserBusinessPartner::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
