<?php

namespace App\Http\Controllers\ClothingOrder;

use App\Http\Models\ClothingOrder\DepartmentResponsible;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DepartmentResponsibleController extends Controller
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
        $warehouses = DepartmentResponsible::orderBy('id')
            ->with(
                [
                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'
                ]
            )
            ->with('responsible.employee')
            ->with('department');
        // if (isset($search)) {
        //     $warehouses->where(function ($query) use ($search) {
        //         return $query
        //             ->where('warehouse_number', 'ilike', "%" . $search . "%")
        //             ->orWhere('description', 'ilike', "%" . $search . "%");
        //     });
        // }
        return $warehouses->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    public function show(Warehouse $Warehouse)
    {
        return Warehouse::get();
    }

    public function update(Request $request)
    {
        $model = DepartmentResponsible::find($request->input('id'));
        if (!$model) {
            $model = new DepartmentResponsible();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }

        $model->responsible_id = $request['responsible_id'];
        $model->department_id = $request['department_id'];
        $model->save();
        return 'Successfully saved';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\EDI\Warehouse  $Warehouse
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
        $model = DepartmentResponsible::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
