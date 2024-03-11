<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\File;
use App\Http\Models\DocumentDownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
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
     * @param  \App\Http\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }

    public function stampuploadfile($id)
    {
        return
            Document::stampUploaddedPdf($id);
        // 'sasas';
    }

    // public function sardor(){
    //     $files = File::where('object_id',2450805)
    //     ->where('object_type_id', 5)
    //     // ->where('type', 0)
    //     ->where('file_name', 'like', '%.pdf')
    //     ->orderBy('id','asc')
    //     ->first();
    //     $path = Storage::path('documents_new\\' . $files->physical_name);
    //     return response()->download(storage_path('app/documents_new//' . $files->physical_name), $files->file_name);
    //     // return response()->file($path);
    // }

    public function documentDownloadLog(Request $request)
    {
        $document_id = $request->input('document_id');
        $eimzo_base64 = $request->input('eimzo_base64');

        $documentDownloadLog = new DocumentDownloadLog();
        $documentDownloadLog->document_id = $request->input('document_id');
        $documentDownloadLog->eimzo_base64 = $request->input('eimzo_base64');
        $documentDownloadLog->user_id = Auth::id();
        $documentDownloadLog->created_at = date('Y-m-d H:i:s');
        $documentDownloadLog->save();
        return 'Successfully saved';
    }

    public function imtq()
    {
        $path = public_path('/imtq/imtq2023.pdf');
        return response()->file($path);
        // /var/www/workflow/backend/public/imtq/imtq2023.pdf
    }
}
