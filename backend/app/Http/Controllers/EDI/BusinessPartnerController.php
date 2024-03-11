<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\BusinessPartner;
use App\Http\Models\EDI\BusinessPartnerType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


class BusinessPartnerController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');

        $businessPartners = BusinessPartner::with([ 
                                                   'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                   'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',             
                                                   'BusinessPartnerType'
                                                  ]
        );

        if (isset($search)) {
            $businessPartners->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%")
                    ->orWhere('type', 'ilike', "%" . $search . "%")
                    ->orWhere('status', 'ilike', "%" . $search . "%");
            });
        }
        return $businessPartners->orderBY('name', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function update(Request $request, BusinessPartner $businessPartner)
    {
        $model = BusinessPartner::find($request->input('id'));
        if (!$model) {
            $model = new BusinessPartner();
            $model->created_by = Auth::id();
            $model->created_at = date('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }

        $model->name = $request->input('name');
        $model->business_partner_type_id = $request->input('business_partner_type_id');
        $model->status = $request->input('status');
        $model->address = $request->input('address');
        $model->country_code = $request->input('country_code');
        $model->save();
        return $model;
    }


    public function getRef(Request $request)
    {
        return [
            'businessPartnerTypes' => BusinessPartnerType::select('id as value', 'name as text')->get()
        ];
    }

    public function destroy($id)
    {
        //return $id;
        $model = BusinessPartner::find($id);
        // return $model;

        $model->delete();
    }
}
