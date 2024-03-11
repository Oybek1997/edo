<?php

namespace App\Http\Controllers\Linestop;

use App\Http\Models\Linestop\Shop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LineStopShopController extends Controller
{
    public function indexShops(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $shops = Shop::select();
    
        if (isset($search)) {
            $shops->where(function ($query) use ($search) {
                return $query
                    ->where('line', 'like', "%" . $search . "%")
                    ->orWhere('comment', 'like', "%" . $search . "%");
            });
        }
        return  $shops->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $model = Shop::find($request->input('id'));
        if (!$model) {
            $model = new Shop();
        }
        $model->name = $request->input('name');
        $model->comment = $request->input('comment');
        $model->save();
        return 'done';
    }
    public function destroy($id)
    {
        $model = Shop::find($id);
        $model->delete();
    }
}