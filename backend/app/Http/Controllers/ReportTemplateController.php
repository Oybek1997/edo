<?php

namespace App\Http\Controllers;

use App\Http\Models\ReportColumnTemplate;
use App\Http\Models\ReportTemplate;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportTemplateController extends Controller
{
    public function index(Request $request)
    {
        //return Range::all();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $reportTemplate = ReportTemplate::select();

        if (isset($search)) {
            $reportTemplate->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");

            });
        }
        return $reportTemplate->orderBy('id')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ReportTemplate $reportTemplate)
    {
        //
    }

    public function edit($id)
    {
        return ReportTemplate::with('reportColumns')->find($id);
    }

    public function update(Request $request)
    {
        $reportTemplate = $request->all();
        $model = ReportTemplate::find($reportTemplate['id']);
        $permission = null;
        if(!$model){
            $model = new ReportTemplate();
            $model->created_by = Auth::id();
        }
        else{
            $model->updated_by = Auth::id();
            $permission = Permission::where('name', 'report_'.$model->permission)->first();
        }
        $model->name_uz_latin = $reportTemplate['name_uz_latin'];
        $model->name_uz_cyril = $reportTemplate['name_uz_cyril'];
        $model->name_ru = $reportTemplate['name_ru'];
        $model->permission = $reportTemplate['permission'];
        $model->document_type_id = $reportTemplate['document_type_id'] == 'all' ? 0 : $reportTemplate['document_type_id'];
        $model->document_template_id = isset($reportTemplate['document_template_id']) ? ($reportTemplate['document_template_id'] == 'all' ? 0 : $reportTemplate['document_template_id']) : null;
        if($model->save()){
            if($permission){
                $permission->name = 'report_'.$model->permission;
                $permission->display_name = $model->name_uz_latin;
                $permission->description = $model->name_uz_latin;
            }
            else{
                $permission = new Permission();
                $permission->name = 'report_'.$model->permission;
                $permission->display_name = $model->name_uz_latin;
                $permission->description = $model->name_uz_latin;
            }
            $permission->save();
            $abs_rc = ReportColumnTemplate::where('report_template_id', $model->id)->get();
            foreach ($abs_rc as $key => $val) {
                $tmp = true;
                foreach ($reportTemplate['report_columns'] as $k => $v) {
                    if ($val->id == $v['id']) {
                        $tmp = false;
                    }
                }
                if ($tmp) {
                    $reportColumn = ReportColumnTemplate::find($val->id);
                    if ($reportColumn) {
                        $reportColumn->delete();
                    }
                }
            }
            foreach ($reportTemplate['report_columns'] as $key => $reportColumn) {
                $report_column = ReportColumnTemplate::find($reportColumn['id']);
                if(!$report_column){
                    $report_column = new ReportColumnTemplate();
                    $report_column->created_by = Auth::id();
                }
                else{
                    $report_column->updated_by = Auth::id();
                }
                $report_column->report_template_id = $model->id;
                $report_column->name_uz_latin = $reportColumn['name_uz_latin'];
                $report_column->name_uz_cyril = $reportColumn['name_uz_cyril'];
                $report_column->name_ru = $reportColumn['name_ru'];
                $report_column->report_column_name = $reportColumn['report_column_name'];
                $report_column->report_column_table = $reportColumn['report_column_table'];
                $report_column->table_list_column_name = $reportColumn['table_list_column_name'];
                $report_column->tr = $reportColumn['tr'];
                $report_column->save();
            }
        }

        return 'SuccessFully saved!!!';
    }

    public function destroy($id)
    {
        $model = ReportTemplate::find($id);
        $model->delete();
        $reportColumnTemplates = ReportColumnTemplate::where('report_template_id', $id)->get();
        foreach ($reportColumnTemplates as $key => $reportColumnTemplate) {
            $reportColumnTemplate->delete();
        }
    }
}
