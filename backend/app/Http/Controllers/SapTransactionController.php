<?php

namespace App\Http\Controllers;

use App\Http\Models\SapTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SapTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $filter = $request->input('search');
        $content = isset($filter) ? $filter : 0;

        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $sapTransaction = SapTransaction::select();

        if ($content) {
            $sapTransaction->where(function ($q) use ($content) {
                $q->where('extended_name', 'like', '%' . $content . '%');
            });
        }

        return $sapTransaction->paginate($itemsPerPage == '-1' ? 10000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function show(SapTransaction $SapTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SapTransaction $SapTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\SapTransaction $SapTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SapTransaction $SapTransaction)
    {
        //
        $model = SapTransaction::find($request->input('id'));

        if(!$model){
            $model = new SapTransaction();
            $model->created_by=Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }

        $model->extended_name = $request['extended_name'];
        // $model->updated_by = Auth::id();
        $model->save();

        return SapTransaction::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\SapTransaction $SapTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapTransaction $SapTransaction, $id)
    {
        //
        $model = SapTransaction::find($id);
        $model->delete();
    }
}
