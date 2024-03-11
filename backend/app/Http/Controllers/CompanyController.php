<?php

namespace App\Http\Controllers;

use App\Http\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Company::all();
    }


    public function update(Request $request)
    {
        $model = Company::find($request->input('id'));
        if (!$model) {
            $model = new Company();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }

        $model->name = $request['name'];
        $model->email = $request['email'];
        $model->phone = $request['phone'];
        $model->save();
    }

    public function destroy($id)
    {
        $model = Company::find($id);
        $model->delete();
    }
}
