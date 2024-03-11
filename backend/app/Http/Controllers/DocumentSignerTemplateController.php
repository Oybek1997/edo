<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\DocumentSignerTemplate;
use Illuminate\Http\Request;

class DocumentSignerTemplateController extends Controller
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
     * @param  \App\Http\Models\DocumentSignerTemplate  $documentSignerTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentSignerTemplate $documentSignerTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DocumentSignerTemplate  $documentSignerTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentSignerTemplate $documentSignerTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DocumentSignerTemplate  $documentSignerTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentSignerTemplate $documentSignerTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DocumentSignerTemplate  $documentSignerTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentSignerTemplate $documentSignerTemplate)
    {
        //
    }

    public function getSigners($id)
    {
        return DocumentSignerTemplate::where('document_template_id', $id)
            ->with('staff.department')
            ->with('staff.position')
            ->with('actionType')
            ->with('staff.employees')->get();
    }
}
