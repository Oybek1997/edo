<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Staff;
use Illuminate\Support\Facades\DB;

class DocumentReportController extends Controller
{
    public function index($menu, $template_id)
    {
        $locale = 'uz_latin';
        $staff_ids = $this->getStaffIds(918);
        if (count($staff_ids) == 0) {
            return ['Shtatlar topiladi'];
        }

        $document = Document::query();
        $document->select([
            'documents.id',
            'documents.created_employee_id',
            'documents.department_id',
            DB::raw('case when document_number_reg then document_number_reg else document_number end as number'),
            DB::raw('substring(document_date,1,10) as date'),
            'title',
            'document_templates.name_' . $locale . ' as template',
            'document_types.name_' . $locale . ' as type'
        ]);
        $document->join('document_types', 'documents.document_type_id', '=', 'document_types.id');
        $document->join('document_templates', 'documents.document_template_id', '=', 'document_templates.id');
        $document->whereNotIn('documents.status', [0, 6]);
        $document->orderBy('id', 'desc');

        $document->limit(200);
        $documents = $document->get();
        foreach ($documents as $key => $value) {
            $value->author = $this->getAuthor($locale, $value->id);
            $value->sender = $this->getSender($locale, $value->id);
            // dd($value->created_employee_id, $value);
            // [
            //     $value->from_department,
            //     $value->from_manager,
            //     $value->from_position,
            //     $value->to_department,
            //     $value->to_manager,
            //     $value->to_position,
            // ] = Document::getDocumentDepartmentInfo($value->created_employee_id, $value->department_id, $value->locale);
        }

        return $documents;
    }

    public function getSender()
    {
        # code...
    }

    public function getAuthor($locale, $document_id)
    {
        $author = DocumentSigner::select(
            [
                DB::raw("concat(lastname_" . $locale . ", ' ', substring(firstname_" . $locale . ",1,1),'.',substring(middlename_" . $locale . ",1,1),'.') as author")
            ]
        )
            ->where('document_id', $document_id)
            ->join('employee_staff', 'employee_staff.staff_id', '=', 'document_signers.staff_id')
            ->join('employees', 'employee_staff.employee_id', '=', 'employees.id')
            ->where('employee_staff.is_active', 1)
            ->where('action_type_id', 6)
            ->first();
        return $author ? $author->author : '';
    }

    public function getStaffIds($employee_id)
    {
        return  DB::select("
            select s.id from staff s
            inner join employee_staff es on es.staff_id = s.id
            where es.employee_id = " . $employee_id . " and es.is_active=1
            order by is_main_staff desc
        ");
    }
}
