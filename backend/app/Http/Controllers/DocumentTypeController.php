<?php

namespace App\Http\Controllers;

use App\Http\Models\DocumentType;
use App\Http\Models\DocumentTemplate;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $documentType = DocumentType::whereNull('deleted_at');

        return $documentType->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page);

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
     * @param  \App\Http\Models\DocumentType  $DocumentType
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentType $DocumentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DocumentType  $DocumentType
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentType $DocumentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DocumentType  $DocumentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentType $DocumentType)
    {
        //
        $model = DocumentType::find($request->input('id'));
        if (!$model) {
            $model = new DocumentType();
            $permission = new Permission();
            $name = str_replace(' ', '_', $request['name_uz_latin']);
            $name = str_replace("'", "", $name);
            $name = str_replace(",", "", $name);
            $name = str_replace("?", "", $name);
            $name = str_replace("(", "", $name);
            $name = str_replace(")", "", $name);
            $name = str_replace("`", "", $name);
            $name = str_replace("!", "", $name);
            $name = str_replace('"', '', $name);
            $name = str_replace("\\", "", $name);
            $name = str_replace("/", "", $name);
            $name = mb_strtolower($name);
            $permission->name = $name.'-create';
            $permission->display_name = 'Create '.$request['name_uz_latin'];
            $permission->description = $request['name_uz_latin'].' '.$request['name_uz_cyril'].' '.$request['name_ru'];
            $permission->save();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->code = strtoupper($request['code']);
        $model->year = date('Y');
        $model->counter = 0;
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DocumentType  $DocumentType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = DocumentType::find($id);
        $model->delete();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getName(Request $request)
    {
        $locale = $request->input('language');
        $id = $request->input('id');
        $getId = DocumentType::select('name_' .$locale)->where('id', '=', $id)->get();
        return ['Id' =>$getId];
    }

    public function getDocumentTypes()
    {
        $doc_type = DocumentType::orderBy('name_uz_latin')->get();
        $data = [];
        foreach ($doc_type as $key => $value) {
            $count = DocumentTemplate::where('document_type_id', $value->id)->get();

            // $doc_type[$key]->count = $count->count();
            $data[$key]['count'] = count($count);
            $permission = [];
            foreach ($count as $key_temp => $value_temp) {
                $name = str_replace(' ', '_', $value_temp->name_uz_latin);
                $name = str_replace("'", "", $name);
                $name = str_replace(",", "", $name);
                $name = str_replace("?", "", $name);
                $name = str_replace("(", "", $name);
                $name = str_replace(")", "", $name);
                $name = str_replace("`", "", $name);
                $name = str_replace("!", "", $name);
                $name = str_replace('"', '', $name);
                $name = str_replace("\\", "", $name);
                $name = str_replace("/", "", $name);
                $name = strtolower($name);
                $permission[] = $name.'-create';
            }
            $data[$key]['id'] = $value->id;
            $data[$key]['permissions'] = $permission;
            $data[$key]['name_uz_latin'] = $value->name_uz_latin;
            $data[$key]['name_uz_cyril'] = $value->name_uz_cyril;
            $data[$key]['name_ru'] = $value->name_ru;
        }
        // dd($data);
        return $data;
    }
}
