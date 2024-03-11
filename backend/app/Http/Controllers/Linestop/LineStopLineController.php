<?php

namespace App\Http\Controllers\Linestop;

use App\Http\Models\Linestop\Line;
use App\Http\Models\Linestop\Shop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LineStopLineController extends Controller
{
    public function indexShops(Request $request)
    {
        $shops = Shop::all();
        return $shops;
    }
    public function indexLines(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $lines = Line::select()->with('shop');
    
        if (isset($search)) {
            $lines->where(function ($query) use ($search) {
                return $query
                    ->where('line', 'like', "%" . $search . "%")
                    ->orWhere('comment', 'like', "%" . $search . "%");
            });
        }
        return $lines->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    public function saveupdate(Request $request)
    {
        $shopId = $request->input('shopid');
        $shopExists = Shop::find($shopId);
        if (!$shopExists) {
            return response()->json(['error' => 'Invalid shopid'], 400);
        }
        $model = Line::find($request->input('id'));
        if (!$model) {
            $model = new Line();
        }
        $model->shopid = $shopId;
        $model->line = $request->input('line');
        $model->comment = $request->input('comment');
        $model->save();
    }
    public function destroy($id)
    {
        $model = Line::find($id);
        $model->delete();
    }
}