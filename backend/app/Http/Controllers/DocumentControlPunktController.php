<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\DocumentControlPunkt;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentControlPunktController extends Controller
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
     * @param  \App\DocumentControlPunkt  $documentControlPunkt
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentControlPunkt $documentControlPunkt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentControlPunkt  $documentControlPunkt
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentControlPunkt $documentControlPunkt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentControlPunkt  $documentControlPunkt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentControlPunkt $documentControlPunkt)
    {
        DB::beginTransaction();
        try {
            $model = DocumentControlPunkt::find($request['id']);
            if(!$model){
                $model = new DocumentControlPunkt();
                $model->created_by = Auth::user()->id;
            }
            else{
                $model->updated_by = Auth::user()->id;
            }
            $model->document_id = $request['document_id'];
            $model->department_id = $request['department_id'];
            $model->content = $request['content'];
            $model->journal_id = isset($request['journal_id']) ? $request['journal_id'] : null;
            $model->priority = $request['priority'];
            $model->punkt_type = $request['punkt_type'];
            $model->save();

            $controller = DocumentSigner::where('control_punkt_id', $model->id)->where('staff_id', $request['controller']['staff_id'])->first();
            if(!$controller) {
                $controller = new DocumentSigner();
                $controller->taken_datetime = date('Y-m-d h:i:s');
                $controller->staff_id = $request['controller']['staff_id'];
                $controller->control_punkt_id = $model->id;
            }
            $controller->document_id = $model->document_id;
            $controller->action_type_id = 11;
            // $controller->due_date = $request['controller']['staff_id'];
            $controller->sequence = 0;
            $controller->department = Staff::find($request['controller']['staff_id'])->department['name_'.Document::find($model->document_id)->locale];
            $controller->position = Staff::find($request['controller']['staff_id'])->position['name_'.Document::find($model->document_id)->locale];
            $controller->fio = Staff::find($request['controller']['staff_id'])->employees[0]->getShortname(Document::find($model->document_id)->locale);
            $controller->save();

            $model->controller_id = $controller->id;
            $model->save();

            foreach ($request['document_signers'] as $key => $document_signer) {
                $docSigner = DocumentSigner::find($document_signer['id']);
                if(!$docSigner){
                    $docSigner = new DocumentSigner();
                    $docSigner->taken_datetime = date('Y-m-d h:i:s');
                    $docSigner->staff_id = $document_signer['staff_id'];
                    $docSigner->control_punkt_id = $model->id;
                    $docSigner->document_id = $model->document_id;
                    $docSigner->action_type_id = 4;
                    $docSigner->sequence = 0;
                }
                $docSigner->department = Staff::find($document_signer['staff_id'])->department['name_'.Document::find($model->document_id)->locale];
                $docSigner->position = Staff::find($document_signer['staff_id'])->position['name_'.Document::find($model->document_id)->locale];
                $docSigner->due_date = isset($document_signer['due_date']) ? $document_signer['due_date'] : null;
                $docSigner->save();
            }
            DB::commit();
            return "Successfully saved!";
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentControlPunkt  $documentControlPunkt
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentControlPunkt $documentControlPunkt)
    {
        //
    }
}
