<?php

namespace App\Http\Controllers;

use App\Http\Models\Coefficient;
use App\Http\Models\StaffCoefficient;
use App\Http\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoefficientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shift(Request $request)
    // public function staffCoefficient(Request $request)
    {
        return Shift::get();
        // return StaffCoefficient::with('staff')->with('coefficient')->get();
    }
    public function indexStaffCoefficient(Request $request)
    // public function staffCoefficient(Request $request)
    {
        return Coefficient::get();
        // return StaffCoefficient::with('staff')->with('coefficient')->get();
    }
    public function index(Request $request)
    {
        //
        //return Coefficient::get();
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $language = $request->input('language');
        $coefficient = Coefficient::select();
        
        if (isset($search)) {
            $coefficient->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('description', 'like', "%" . $search . "%")
                    ->orWhere('order_date', 'like', "%" . $search . "%")
                    ->orWhere('order_number', 'like', "%" . $search . "%");
            });
        }
        return $coefficient->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\http\models\Coefficient  $Coefficient
     * @return \Illuminate\Http\Response
     */
    public function show(Coefficient $Coefficient)
    {
        return Coefficient::get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\Coefficient  $Coefficient
     * @return \Illuminate\Http\Response
     */
    public function edit(Coefficient $Coefficient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\Coefficient  $Coefficient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coefficient $Coefficient)
    {
        //
        $model = Coefficient::find($request->input('id'));
        if (!$model) {
            $model = new Coefficient();
           $model->created_by= Auth::id();
        } else {
	         $model->updated_by = Auth::id();
        }
        $model->code = $request['code'];
        //$model->percent = $request['percent'];
        $model->description = $request['description'];
        $model->order_date = $request['order_date'];
        $model->order_number = $request['order_number'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\Coefficient  $Coefficient
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Coefficient::find($id);
        $model->delete();
    }
}
