<?php

namespace App\Http\Controllers\Supplytransport;

use App\Http\Models\Supplytransport\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function indexVehicles(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $spare_parts = Vehicle::select()->with('transport_type');
        if (isset($search)) {
            $spare_parts->where(function ($query) use ($search) {
                return $query
                    ->where('exploitation_date', 'ilike', "%" . $search . "%")
                    ->orWhere('inventory_number', 'ilike', "%" . $search . "%");
            });
        }
        return  $spare_parts->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $transport_id = $request->input('transport_id');
        $exploitation_date = $request->input('exploitation_date');
        $inventory_number = $request->input('inventory_number');
        $belong_dep = $request->input('belong_dep');
        $dep_shop = $request->input('dep_shop');
        $description = $request->input('description');

        $id = $request->input('id');
        $model = Vehicle::find($id);
    
        if (!$model) {
            $model = new Vehicle();
        } else {
            $model->transport_id = $transport_id;
            $model->exploitation_date = $exploitation_date;
            $model->inventory_number = $inventory_number;
            $model->belong_dep = $belong_dep;
            $model->dep_shop = $dep_shop;
            $model->description = $description;
            $model->save();
            return 'updated';
        }
        $model->transport_id = $transport_id;
        $model->exploitation_date = $exploitation_date;
        $model->inventory_number = $inventory_number;
        $model->belong_dep = $belong_dep;
        $model->dep_shop = $dep_shop;
        $model->description = $description;
        $model->save();
        return 'created';
    }
    

    public function destroy(Request $request)
    {
        $model = Vehicle::find($request->input('id'));
        $model->delete();
    }
}