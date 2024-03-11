<?php

namespace App\Http\Controllers\ClothingOrder;

use App\Http\Models\ClothingOrder\Size;
use App\Http\Models\ClothingOrder\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return $request->search;
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $sizes = Size::orderBy('id')
            ->with(
                [
                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                ]
            )
          ->with('product');
          
          if ($request->search) {
            $sizes->where('size', '=', $request->search);
        }
         
        /*if ($request->search) {
            $sizes->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%");
            });
        }*/
        
        
        if ($search) {
           $sizes->whereHas('product', function ($query) use ($search) {
           $query->where('name', 'ilike', "%" . $search . "%");
        });
    }
         
        return $sizes->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Size $sizes)
    {
        return Size::get();
    }

    public function update(Request $request)
    {
         //return $request['id'];
        $model = Size::find($request->input('id'));
        if (!$model) {
            $model = new Size();
            //$model->created_by = Auth::id();
        } else {
            //$model->updated_by = Auth::id();
        }

        $model->size = $request['size'];
        $model->product_id = $request['product_id'];
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
        $model = Size::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
