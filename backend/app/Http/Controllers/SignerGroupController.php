<?php

namespace App\Http\Controllers;

use App\Http\Models\SignerGroup;
use App\Http\Models\SignerGroupDetail;
use App\Http\Models\Staff;
use App\Http\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SignerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $search = $request->input('search');
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $actionTypes = DB::table('action_types')
                                    ->select('name_'.$request->language.' as name', 'id')
                                    ->get();

        $signersGroup = SignerGroup::with('signerGroupDetails')
                            ->where('name_uz_cyril','ilike','%'.$search.'%')
                            ->orWhere('name_ru','ilike','%'.$search.'%')
                            ->orWhere('name_uz_latin','ilike','%'.$search.'%')
                            ->with('signerGroupDetails.signerGroup')
                            ->with('signerGroupDetails.staff.position')
                            ->with('signerGroupDetails.staff.department')
                            ->with('signerGroupDetails.actionType');

        return [
            'signer_groups' => $signersGroup->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'action_types' => $actionTypes
        ];
    }

    public function getRef($locale)
    {
        $staff = Staff::query()
        // with(['department' => function ($query) use ($locale) {
        //     $query->select(['id', 'department_code', 'name_' . $locale]);
        // }])
        ->select(["id", "department_id", "position_id", "range_id", "rate_count"])
        ->with(['position' => function ($query) use ($locale) {
            $query->select(['id', 'name_' . $locale . ' as text']);
        }])
        ->with('range:id,code')
        ->where('is_active', 1)
        ->get();
        $staffs = [];
        foreach ($staff as $key => $value) {
            $dep = Department::select(['id', 'department_code as code', 'name_' . $locale . ' as text'])->find($value->department_id);
            $staffs[$key] = $value;
            $staffs[$key]['department'] = $dep;
        }

        
        // $staffs = Staff::with('range:id,code')
        //     ->with(['department' => function ($query) use ($locale) {
        //         $query->select(['id', 'department_code as code', 'name_' . $locale . ' as text']);
        //     }])
        //     ->with(['position' => function ($query) use ($locale) {
        //         $query->select(['id', 'name_' . $locale . ' as text']);
        //     }])
        //     ->select(["id", "department_id", "position_id", "range_id", "rate_count"])
        //     ->get();



        return [
            'staffs' => $staffs,
        ];
    }

    public function update(Request $request)
    {
        //
        $model = SignerGroup::find($request->input('id'));
        if (!$model) {
            $model = new SignerGroup();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    public function updateSignerGroupDetail(Request $request)
    {
        $model = SignerGroupDetail::find($request->input('id'));
        if (!$model) {
            $model = new SignerGroupDetail();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->signer_group_id = $request['signer_group_id'];
        $model->staff_id = $request['staff_id'];
        $model->action_type_id = $request['action_type_id'];
        $model->sequence = $request['sequence'];
        $model->due_day_count = $request['due_day_count'];
        $model->sign_type = $request['sign_type'];
        $model->is_registry = $request['is_registry'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\SignerGroup  $signerGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = SignerGroup::find($id);
        $department->delete();
    }

    public function destroySignerGroup($id)
    {
        $department = SignerGroupDetail::find($id);
        $department->delete();
    }
}
