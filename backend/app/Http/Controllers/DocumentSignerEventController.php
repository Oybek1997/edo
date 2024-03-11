<?php

namespace App\Http\Controllers;

use App\Http\Models\DocumentSignerEvent;
use Illuminate\Http\Request;

class DocumentSignerEventController extends Controller
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
        $filter = $request->input('filter');
        $doc_id = $filter['doc_id'];
        $doc_num = $filter['doc_num'];
        $fio = $filter['doc_signer'];

        $events = DocumentSignerEvent::with(['documentSigner' => function ($q) {
            $q->select('id', 'fio', 'document_id')->with(['documents' => function ($qu) {
                $qu->select('id', 'document_number', 'pdf_file_name');
            }]);
        }])->orderByDesc('id');

        if (isset($filter['comment'])) {
            $events->where('comment', 'like', '%' . $filter['comment'] . '%');
        }
        if (isset($filter['status'])) {
            $events->where('status', $filter['status']);
        }
        if (isset($filter['startDate']) && isset($filter['endDate'])) {
            $events->whereBetween('created_at', [$filter['startDate'], $filter['endDate']]);
        }
        if (isset($doc_id)) {
            $events->whereHas('documentSigner', function ($q) use ($doc_id) {
                $q->where('document_id', $doc_id);
            });
        }
        if (isset($doc_num)) {
            $events->whereHas('documentSigner', function ($q) use ($doc_num) {
                $q->whereHas('documents', function ($query) use ($doc_num) {
                    // $query->where('document_number', 'like', '%' . $doc_num . '%');
                    $query->where('document_number', $doc_num);
                });
            });
        }
        if (isset($fio)) {
            $events->whereHas('documentSigner', function ($q) use ($fio) {
                $q->where('fio', 'like', '%' . $fio . '%');
            });
        }

        // ->where('id', 14416637)
        return $events->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);;
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
     * @param  \App\DocumentSignerEvent  $documentSignerEvent
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentSignerEvent $documentSignerEvent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentSignerEvent  $documentSignerEvent
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentSignerEvent $documentSignerEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentSignerEvent  $documentSignerEvent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $comment = $request->input('comment');
        // return $comment;
        $event = DocumentSignerEvent::where('id', $id)->first();
        if ($event) {
            $event->comment = $comment;
            $event->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentSignerEvent  $documentSignerEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = DocumentSignerEvent::find($id);
        $model->delete();
    }
}
