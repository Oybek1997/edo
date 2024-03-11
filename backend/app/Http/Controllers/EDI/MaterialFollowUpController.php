<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\MaterialFollowUp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;



class MaterialFollowUpController extends Controller
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
        $MaterialFollowUps = MaterialFollowUp::with([
                                                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin'   
                                                    ]
        );
        if (isset($search)) {
            $MaterialFollowUps->where('description', 'ilike', "%" . $search . "%");
        }
        return $MaterialFollowUps->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\http\models\MaterialFollowUp  $MaterialFollowUp
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialFollowUp $MaterialFollowUp)
    {
        return MaterialFollowUp::get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\MaterialFollowUp  $MaterialFollowUp
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialFollowUp $MaterialFollowUp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\MaterialFollowUp  $MaterialFollowUp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = MaterialFollowUp::find($request->input('id'));
        if (!$model) {
            $model = new MaterialFollowUp();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        } else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\EDI\MaterialFollowUp  $MaterialFollowUp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = MaterialFollowUp::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
