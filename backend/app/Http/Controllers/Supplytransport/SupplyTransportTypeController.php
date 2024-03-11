<?php

namespace App\Http\Controllers\Supplytransport;

use App\Http\Models\Supplytransport\TransportType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplyTransportTypeController extends Controller
{
    public function indexTransportsType(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $transports = TransportType::select();
        if (isset($search)) {
            $transports->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%");
            });
        }
        return  $transports->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $model = TransportType::find($request->input('id'));
    
        if (!$model) {
            $model = new TransportType();
        }
    
        $model->name = $request->input('name');
        $model->save();
    
        return 'done';
    }
    public function destroy(Request $request)
    {
        $model = TransportType::find($request->input('id'));
        $model->delete();
    }
}