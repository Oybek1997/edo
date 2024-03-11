<?php

namespace App\Http\Controllers\ClothingOrder;

use App\Http\Models\ClothingOrder\EmployeeClothe;
use App\Http\Models\ClothingOrder\Product;
use App\Http\Models\ClothingOrder\Size;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EmployeeClotheController extends Controller
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
        $employeeClothes = EmployeeClothe::orderBy('id')
            ->with('employee')
            ->with('products')
            ->with('sizes')
            ->with(
                [
                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                ]
            );
          
          if ($request->search) {
            $employeeClothes->where('size', '=', $request->search);
        }
         
        /*if ($request->search) {
            $sizes->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'ilike', "%" . $search . "%");
            });
        }*/
        
        
        if ($search) {
           $employeeClothes->whereHas('product', function ($query) use ($search) {
           $query->where('name', 'ilike', "%" . $search . "%");
        });
    }
         
        return $employeeClothes->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function getRef(Request $request)
    {
        return [
            'products' => Product::select('id as value', 'name as text')->get(),
            'sizes' => Size::select('id as value', 'size as text', 'product_id')->get(),
        ];
    }

    public function show(EmployeeClothe $EmployeeClothe)
    {
        return EmployeeClothe::get();
    }

    public function update(Request $request)
    {
         //return $request['id'];
        $model = EmployeeClothe::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeClothe();
            //$model->created_by = Auth::id();
        } else {
            //$model->updated_by = Auth::id();
        }
        $model->created_by = Auth::id();
        $model->size_id = $request->input('size_id');
        $model->product_id = $request->input('product_id');
        $model->employee_id = $request->input('employee_id');
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
        $model = EmployeeClothe::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
