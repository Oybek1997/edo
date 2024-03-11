<?php

namespace App\Http\Controllers;

use App\Http\Models\AboutCompany;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutCompanyController extends Controller
{
    public function aboutCompany(Request $request)
    {
        $latestAboutCompany = AboutCompany::orderBy('id', 'desc')->get();
        return response()->json(['data' => $latestAboutCompany]);
    }
    


    public function saveupdate(Request $request)
    {
        $model = aboutCompany::find($request->input('id'));
        if (!$model) {
            $model = new aboutCompany();
        }
        $model->business_name = $request->input('business_name');
        $model->adress = $request->input('adress');
        $model->main_activity = $request->input('main_activity');
        $model->supervisor = $request->input('supervisor');
        $model->founder = $request->input('founder');
        $model->save();
        return 'done';
    }
    public function destroy(Request $request)
    {
        $model = AboutCompany::find($request->input('id'));
        $model->delete();
    }
}