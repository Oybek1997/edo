<?php

namespace App\Http\Controllers;

use App\Http\Models\SelectedTemplates;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\AttributePermission;
use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelectedTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SelectedTemplates::with('documentTemplate')->get();
    }



    public function alltemplate()
    {
        return DocumentTemplate::get();
    }
    public function centrumTemplate()
    {
        $templates = [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543];
        $centrum = DocumentTemplate::whereIn('id', $templates)->get();
        return $centrum;
    }
    public function selectedReportTemplate()
    {
        // $templates = [242];
        $user = Auth::id();
        // $user = '2785';
        $templates = AttributePermission::where('user_id', $user)->pluck('document_template_id')->toArray();
        // return $templates;
        $centrum = DocumentTemplate::whereIn('id', $templates)->get();
        return $centrum;
    }
    public function aviaReportTemplate()
    {
        $templates = [242];
        $centrum = DocumentTemplate::whereIn('id', $templates)->get();
        return $centrum;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = SelectedTemplates::find($request->input('id'));
        if (!$model) {
            $model = new SelectedTemplates();
        }
        $model->document_template_id = $request->input('template_id');
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = SelectedTemplates::find($id);
        $model->delete();
    }
}
