<?php

namespace App\Http\Controllers;

use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailAttribute;
use App\Http\Models\DocumentDetailFakt;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\Employee;
use App\Http\Models\KpiFactComission;
use App\Http\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\KpiObject;
use App\Http\Models\KpiComment;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Offset;

class DocumentDetailAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\DocumentDetailAttribute  $documentDetailAttribute
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentDetailAttribute $documentDetailAttribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DocumentDetailAttribute  $documentDetailAttribute
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentDetailAttribute $documentDetailAttribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DocumentDetailAttribute  $DocumentDetailAttribute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentDetailAttribute $DocumentDetailAttribute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DocumentDetailAttribute  $DocumentDetailAttribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentDetailAttribute $DocumentDetailAttribute)
    {
        //
    }


    public function getAttributes($id)
    {
        return DocumentDetailAttribute::whereHas('documentDetailTemplate', function ($q) use ($id) {
            $q->where('document_template_id', $id);
        })->get();
    }

    public function templateReport(Request $request)
    {
        $template = $request->input('template');
        $reaction = $request->input('reaction');
        $menu = $request->input('menu_item');
        $employee = $request->input('employee');
        $tabno = $request->input('tabno');
        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');

        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['per_page'];
        $offset = ($page * $itemsPerPage) - $itemsPerPage;
        $user = Auth::user()->employee_id;
        // $user = 12562;
        if (in_array(3, $reaction)) {
            array_push($reaction, 4, 5);
        }
        if (in_array(2, $reaction)) {
            array_push($reaction, 1);
        }
        // $employeeTemplate = DocumentTemplate::where('has_employee', 1)->pluck('id')->toArray();
        if (isset($tabno)) {
            $employee_id = Employee::where('tabel', $tabno)->first();
            $employee_id = $employee_id->id;
        }
        if ($employee == 1) {
            if ($menu == 'all') {
                if (isset($tabno)) {
                    $result = DB::select("select d.pdf_file_name, d.document_number, d.status,  d.document_date, d.id, dde.employee_fio, e.tabel, dd.id as dd_id, ddc.attribute_name, ddc.value 
                    from documents d
                    inner join document_details dd on dd.document_id = d.id
                    inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                    inner join document_detail_employees dde on dde.document_detail_id = dd.id
                    inner join employees e on e.id = dde.employee_id
                    where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                    and dde.employee_id=" . $employee_id . " order by id DESC
                    ");
                } else {

                    $result = DB::select("select d.pdf_file_name, d.document_number, d.status,  d.document_date,  d.id, dde.employee_fio, e.tabel, dd.id as dd_id, ddc.attribute_name, ddc.value 
                    from documents d
                    inner join document_details dd on dd.document_id = d.id
                    inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                    inner join document_detail_employees dde on dde.document_detail_id = dd.id
                    inner join employees e on e.id = dde.employee_id
                    where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                     order by id DESC
                    ");
                }
            }
            // if ($menu == 'selected') {
            //     if (isset($tabno)) {
            //         $result = DB::select("select d.pdf_file_name, d.document_number, d.status, d.document_date, dde.employee_fio, e.tabel, dd.id as dd_id, ddc.attribute_name, ddc.value 
            //         from documents d
            //         inner join document_details dd on dd.document_id = d.id
            //         inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            //         inner join document_detail_employees dde on dde.document_detail_id = dd.id
            //         inner join employees e on e.id = dde.employee_id
            //         where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
            //         and dde.employee_id=" . $employee_id . " order by dd_id DESC
            //         ");
            //     } else {
            //         $result = DB::select("select d.pdf_file_name, d.document_number, d.status, d.document_date, dde.employee_fio, e.tabel, dd.id as dd_id, ddc.attribute_name, ddc.value 
            //         from documents d
            //         inner join document_details dd on dd.document_id = d.id
            //         inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            //         inner join document_detail_employees dde on dde.document_detail_id = dd.id
            //         inner join employees e on e.id = dde.employee_id
            //         where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
            //          order by dd_id DESC
            //         ");
            //     }
            // }
            if ($menu == 'my') {
                $result = DB::select("select d.pdf_file_name, d.document_number, d.status, d.document_date, dde.employee_fio, e.tabel, dd.id as dd_id, ddc.attribute_name, ddc.value 
                from documents d
                inner join document_details dd on dd.document_id = d.id
                inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                inner join document_detail_employees dde on dde.document_detail_id = dd.id
                inner join employees e on e.id = dde.employee_id
                where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                and d.created_employee_id=" . $user . " order by dd_id DESC
                ");
            }
            if ($menu == 'my-inbox') {
                $result = DB::select("select d.pdf_file_name, d.id, d.document_number, d.status, d.document_date, dd.id as dd_id, ddc.attribute_name, ddc.value 
                from documents d
                inner join document_details dd on dd.document_id = d.id
                inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                inner join document_signers ds on ds.document_id = d.id
                where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                and ds.signer_employee_id=" . $user . " order by dd_id DESC
                ");
            }
        } else 
        {
            if ($menu == 'all' || $menu == 'selected') {
                $result = DB::select("select d.pdf_file_name, d.document_number, d.document_date,  dd.id as dd_id, ddc.attribute_name, ddc.value 
                from documents d
                inner join document_details dd on dd.document_id = d.id
                inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                order by dd_id DESC
                ");
            }
            if ($menu == 'my-inbox') {
                $result = DB::select("select d.pdf_file_name, d.id, d.status, d.document_number, d.document_date, dd.id as dd_id, ddc.attribute_name, ddc.value 
                from documents d
                inner join document_details dd on dd.document_id = d.id
                inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                inner join document_signers ds on ds.document_id = d.id
                where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                and ds.signer_employee_id=" . $user . " order by dd_id DESC
                ");
            } else {
                $result = DB::select("select d.pdf_file_name, d.document_number, d.status, d.document_date, dd.id as dd_id, ddc.attribute_name, ddc.value 
                from documents d
                inner join document_details dd on dd.document_id = d.id
                inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
                where d.document_template_id = " . $template . " and  d.status in (" . implode(',', $reaction) . ")
                and d.created_employee_id=" . $user . " order by dd_id DESC
                ");
            }
        }
        // LIMIT $itemsPerPage OFFSET $offset");
        $group_data = collect($result);
        if ($startDate && $endDate) {

            $group_data = $group_data->whereBetween('document_date', [$startDate, $endDate]);
        }
        // $group_data = collect($result)->whereBetween('document_date', ['2022-01-01', '2023-01-01']);
        $group_data = $group_data->groupBy('dd_id');
        // return $group_data;
        $total = count($group_data);
        // return count($group_data);
        if ($itemsPerPage != 0) {

            $group_data = $group_data->skip($offset)->take($itemsPerPage);
        }
        if (count($group_data) > 0) {
            // $key = array_keys( $group_data);
            // return $key;
            $header = collect($group_data->first())->map(function ($item, $key) {
                return $item->attribute_name;
            })->all();
            return [$header, $group_data, $total, $offset];
        }

        // $data = collect($result)->map(function ($item, $key) {
        //     return [
        //         'document_number' => $item->document_number,
        //         'value' => $item->value,
        //         'employee_fio' => $item->employee_fio,
        //     ];
        // })->groupBy('document_number')->all();
    }
    public function aviaTemplateReport(Request $request)
    {
        $template = $request->input('template');
        $search = $request->input('search');
        // $reaction = $request->input('reaction');
        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['per_page'];
        $offset = ($page * $itemsPerPage) - $itemsPerPage;


        // $limit = 20;
        // $offset = 1;

        $result = DB::select("select d.document_template_id, d.pdf_file_name,  d.document_date, d.document_number, dd.id as dd_id, ddc.attribute_name, ddc.value 
        from documents d
        inner join document_details dd on dd.document_id = d.id
        inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
        where d.document_template_id = $template and  d.status in (3,4,5)
        order by dd_id DESC
        ");
        $result = Document::hydrate($result);

        if ($startDate && $endDate) {

            $result = $result->whereBetween('document_date', [$startDate, $endDate])->pluck('dd_id')->toArray();
        } else {

            $result =  collect($result)->pluck('dd_id')->toArray();
        }
        // return $result;
        $result1 = DB::select("select d.pdf_file_name, d.status, d.document_number,  d.document_date, dd.id as dd_id, ddc.attribute_name, ddc.value 
        from documents d
        inner join document_details dd on dd.document_id = d.id
        inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
        where dd.id in (" . implode(',', $result) . ")  order by dd_id DESC
        ");

        $group_data = collect($result1)->groupBy('dd_id');
        // return $group_data;
        $total = count($group_data);
        // return count($group_data);
        $group_data = $group_data->skip($offset)->take($itemsPerPage);
        if (count($group_data) > 0) {
            // $key = array_keys( $group_data);
            // return $key;
            $header = collect($group_data->first())->map(function ($item, $key) {
                return $item->attribute_name;
            })->all();
            return [$header, $group_data, $total, $offset];
        }
    }
    public function LSPTemplateReport(Request $request)
    {
        // $template = $request->input('template');
        $dateType = $request->input('dateType');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        if (Auth::id() == 1) {
            $template = [117, 118, 189, 230, 444, 604, 605, 579, 580, 589, 590, 616, 621, 209, 633];
        } else {
            $template = [117, 118, 189, 230, 444, 604, 605, 579, 580, 589, 590, 616, 621, 209, 633];
        }
        $documents = Document::select('documents.*')
            // ->limit(10)
            // ->whereIn('documents.document_template_id', [592])
            // ->whereIn('documents.status', [1])
            ->whereIn('documents.status', [3])
            ->whereIn('documents.document_template_id', $template)
            ->with('documentDetails.documentDetailContents')
            ->with('documentDetails.documentDetailSignerAttributes')
            ->with('documentChildren')
            // ->whereBetween('documents.document_date', [$fromDate, $toDate])
            // ->whereHas('documentDetails.documentDetailSignerAttributes', function($query) use($fromDate, $toDate){
            //     $query->whereIn('d_d_attribute_id', [2679,2680,2681,2682,2683,2684,2685,2686,2688,2689, 2726, 2728])
            //     ->whereBetween('value', [$fromDate, $toDate]);
            // })
            ->orderByDesc('id');
        if ($dateType == 1) {
            $documents->whereBetween('documents.document_date', [$fromDate, $toDate]);
        } else if ($dateType == 2) {
            $documents->whereHas('documentDetails.documentDetailSignerAttributes', function ($query) use ($fromDate, $toDate) {
                $query->whereIn('d_d_attribute_id', [2679, 2680, 2681, 2682, 2683, 2684, 2685, 2686, 2688, 2689, 2726, 2728, 2762, 2866])
                    ->whereBetween('value', [$fromDate, $toDate]);
            });
        }
        // $documents = $documents->limit(10)->get()->makeVisible(['base64', 'pdf']);
        $documents = $documents->get()->makeVisible(['base64', 'pdf']);
        return $documents;
    }
}
