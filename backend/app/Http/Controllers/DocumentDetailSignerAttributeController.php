<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\DocumentDetailSignerAttribute;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\TariffScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentDetailSignerAttributeController extends Controller
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
     * @param  \App\DocumentDetailSignerAttribute  $documentDetailSignerAttribute
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentDetailSignerAttribute $documentDetailSignerAttribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentDetailSignerAttribute  $documentDetailSignerAttribute
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $document_details = $request->input('edit_attributes');
        $document_id = $request->input('document_id');
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $locale = Document::find($document_id)->locale ? Document::find($document_id)->locale : 'uz_latin';
        $assignment = '';
        foreach ($document_details as $key => $document_detail) {
            foreach ($document_detail['document_detail_signer_attributes'] as $key_signer => $value) {
                $document_detail_signer_attributes = DocumentDetailSignerAttribute::find($value['id']);
                if ($document_detail_signer_attributes) {
                    $document_detail_signer_attributes->emloyee_id = Auth::user()->employee_id;
                    if($value['document_detail_attributes']['data_type_id']==3){
                        $datefor =  date("Y-m-d", strtotime($value['value']));
                        $document_detail_signer_attributes->value = $datefor;
                    }
                    else if($value['d_d_attribute_id']==1289){
                        $tariff = TariffScale::find($value['value']);
                        if($tariff){
                            $document_detail_signer_attributes->value = $tariff->category;
                        }
                    }
                    else{
                        $document_detail_signer_attributes->value = $value['value'];

                    }
                    $assignment .= $key+1 .'-'. $value['document_detail_attributes']['attribute_name_'.$locale];
                    $assignment .= ': '.$value['value'].', ';
                    $document_detail_signer_attributes->save();
                }
            }
        }

        $signer = DocumentSigner::whereIn('staff_id', $userStaffIds)->where('document_id', $document_id)->first();
        $documentSignerEvent = new DocumentSignerEvent();
        $documentSignerEvent->document_signer_id = $signer->id;
        $documentSignerEvent->action_type_id = $signer->action_type_id;
        $documentSignerEvent->comment = $assignment;
        $documentSignerEvent->status = 15;
        $documentSignerEvent->signer_employee_id = Auth::user()->employee_id;
        $documentSignerEvent->fio = Auth::user()->employee->getShortname($locale);
        $documentSignerEvent->save();
        Document::savePdf($document_id);
        return 'Succesfully saved!';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentDetailSignerAttribute  $documentDetailSignerAttribute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentDetailSignerAttribute $documentDetailSignerAttribute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentDetailSignerAttribute  $documentDetailSignerAttribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentDetailSignerAttribute $documentDetailSignerAttribute)
    {
        //
    }
}
