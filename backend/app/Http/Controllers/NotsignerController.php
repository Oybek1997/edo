<?php

namespace App\Http\Controllers;

use App\Http\Models\Notsigner;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\SignerGroupDetail;
use App\Http\Models\Staff;
use App\Http\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotsignerController extends Controller
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

        $notsigner = Notsigner::with('staff.employees')->with('staff.position')->with('documentTemplate')->with('documentType');
        // ->where('name_uz_cyril','like','%'.$search.'%')
        // ->orWhere('name_ru','like','%'.$search.'%')
        // ->orWhere('name_uz_latin','like','%'.$search.'%')
        // ->with('signerGroupDetails.signerGroup')
        // ->with('signerGroupDetails.staff.position')
        // ->with('signerGroupDetails.staff.department')
        // ->with('signerGroupDetails.actionType');

        return  $notsigner->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }


    public function update(Request $request)
    {
        $staff_id = $request->input('staff_id');
        $documentTemplates = $request->input('document_template_id');
        $documentTypes = $request->input('document_type_id');
        // return $request;
        if (isset($documentTemplates)) {
            foreach ($documentTemplates as $key => $template) {
                $model = Notsigner::where('staff_id', $staff_id)->where('document_template_id', $template)->first();
                if (!$model) {
                    $model = new Notsigner();
                    $model->staff_id = $staff_id;
                    $model->document_template_id = $template;
                    $model->save();
                }
            }
        }
        if (isset($documentTypes)) {
            foreach ($documentTypes as $key => $type) {
                $model = Notsigner::where('staff_id', $staff_id)->where('document_type_id', $type)->first();
                if (!$model) {
                    $model = new Notsigner();
                    $model->staff_id = $staff_id;
                    $model->document_type_id = $type;
                    $model->save();
                }
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\SignerGroup  $signerGroup
     * @return \Illuminate\Http\Response
     */
    public function getForTemplate($id)
    {
        $dt = DocumentTemplate::where('id', $id)->first();
        return Notsigner::where('document_template_id', $id)
            ->orWhere('document_type_id', $dt->document_type_id)
            ->get()
            ->pluck('staff_id');
    }
    public function getNotSigner()
    {
        $staff = Notsigner::get();
        return $staff;
    }
    public function destroy($id)
    {
        $department = Notsigner::find($id);
        $department->delete();
    }
}
