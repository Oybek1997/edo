<?php

namespace App\Http\Controllers;

use App\Http\Models\DocumentDownloadLog;
use App\Http\Models\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DocumentDownloadLogController extends Controller
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
        $search = $request->input('search');
        $language = $request->input('language');
        $documentDownloadLogs = DocumentDownloadLog::with([
                'document'
            ]
        );

        if (isset($filter['document_id']) && $filter['document_id']) {
            $documentDownloadLogs = Document::where('id', $filter['document_id']);
        }
        if (isset($filter['created_at'])) {
            $documentDownloadLogs->where('created_at', 'ilike', '%' . $filter['created_at'] . '%');

        }
        return $documentDownloadLogs->orderBy('created_at', 'desc')->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
    }







}
