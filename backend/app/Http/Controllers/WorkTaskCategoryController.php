<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\WorkTaskCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkTaskCategoryController extends Controller
{
    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $category = new WorkTaskCategory();
            $category->name = $request->input('name');
            $category->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function getAll()
    {
        $category = WorkTaskCategory::get();
        return $category;
    }
    public function edit(Request $request)
    {
        DB::beginTransaction();
        try {
            $category = WorkTaskCategory::find($request->input('id'));
            $category->name = $request->input('name');
            $category->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function destroy($id)
    {
        WorkTaskCategory::find($id)->delete();
    }
}
