<?php

namespace App\Http\Controllers;

use App\Http\Models\DocumentBlankAttribute;
use App\Http\Models\DocumentBlankTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentBlankTemplateController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(DocumentBlankTemplate $DocumentBlankTemplate)
    {
        //
    }

    public function edit($id)
    {
        return DocumentBlankTemplate::with('documentBlankAttribute.blankAttributeTemplate')
            ->with(['blankTemplate' => function($q){
                $q->select('id', 'blank_name');
            }])
            ->where('document_template_id', $id)->get();
    }

    public function update(Request $request)
    {
        $documentBlankTemplates = $request->all();
        foreach ($documentBlankTemplates as $key => $value) {
            $documentBlankTemplate = DocumentBlankTemplate::find($value['id']);
            if (!$documentBlankTemplate) {
                $documentBlankTemplate = new DocumentBlankTemplate();
                $documentBlankTemplate->created_by = Auth::id();
            } else {
                $documentBlankTemplate->updated_by = Auth::id();
            }
            $documentBlankTemplate->document_template_id = $value['document_template_id'];
            $documentBlankTemplate->blank_id = $value['blank_id'];
            if ($documentBlankTemplate->save()) {
                foreach ($value['document_blank_attribute'] as $key1 => $blank_attribute) {
                    $documentBlankAttribute = DocumentBlankAttribute::find($blank_attribute['id']);
                    if (!$documentBlankAttribute) {
                        $documentBlankAttribute = new DocumentBlankAttribute();
                        $documentBlankAttribute->created_by = Auth::id();
                    } else {
                        $documentBlankAttribute->updated_by = Auth::id();
                    }
                    $documentBlankAttribute->document_blank_id = $documentBlankTemplate->id;
                    $documentBlankAttribute->blank_attribute_id = $blank_attribute['blank_attribute_id'];
                    $documentBlankAttribute->relation_type = $blank_attribute['relation_type'];
                    $documentBlankAttribute->relation_attribute = $blank_attribute['relation_attribute'];
                    $documentBlankAttribute->date_format = isset($blank_attribute['date_format']) ? $blank_attribute['date_format'] : null;
                    $documentBlankAttribute->save();
                }
            }
        }

        return 'Successfully saved!';
    }

    public function destroy(DocumentBlankTemplate $DocumentBlankTemplate, $id)
    {
        $model = DocumentBlankTemplate::find($id);
        $model->delete();
        $documentBlankAttributes = DocumentBlankAttribute::where('document_blank_id', $id)->get();
        foreach ($documentBlankAttributes as $key => $value) {
            $value->delete();
        }
        return 'Successfully deleted!';
    }
}
