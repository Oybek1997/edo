<?php

namespace App\Http\Controllers\ClothingOrder;

use App\Http\Models\ClothingOrder\Product;
use App\Http\Controllers\Controller;
use App\Http\Models\ClothingOrder\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return 111;
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $products = Product::orderBy('id')
            ->with(
                [
                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                    'sizes'
                ]
            );
        // if (isset($search)) {
        //     $products->where(function ($query) use ($search) {
        //         return $query
        //             ->where('warehouse_number', 'ilike', "%" . $search . "%")
        //             ->orWhere('description', 'ilike', "%" . $search . "%");
        //     });
        // }
        return $products->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Product $products)
    {
        return Product::get();
    }

    public function saveSize(Request $request)
    {
        $product = Product::find($request->input('product_id'));
        if (!$product) {
            return ['message' => "Product topilmadi."];
        }
        $size = new Size;
        $size->product_id = $request->input('product_id');
        $size->size = $request->input('size');
        $size->save();
        return ['message' => "Successfully saved."];
    }

    public function deleteSize($id)
    {
        $size = Size::find($id);
        if (!$size) {
            return ['message' => "Size topilmadi."];
        }
        $size->delete();
        return ['message' => "Successfully deleted."];
    }

    public function update(Request $request)
    {
        $model = Product::find($request->input('id'));
        if (!$model) {
            $model = new Product();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        $model->name = $request['name'];
        $model->save();
        return 'Successfully saved';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\ClothingOrder\Product  $Product
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $model = Product::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
