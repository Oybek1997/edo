<?php

namespace App\Http\Controllers\EDI;

use App\Http\Models\EDI\Material;
use App\Http\Models\EDI\UnitMeasure;
use App\Http\Models\EDI\MaterialType;
use App\Http\Models\EDI\MaterialGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;



class MaterialController extends Controller
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
        $Materials = Material::with([
                                    'createdBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                    'updatedBy.employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin',
                                    'unitMeasure',
                                    'materialGroup',
                                    'materialType'    
                                    ]
        );
        if (isset($search)) {
            $Materials->where('material_number', 'like', "%" . $search . "%")
                        ->orWhere('description', 'like', "%" . $search . "%");
        }
        return $Materials->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\http\models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function show(Material $Material)
    {
        return Material::get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function edit(Material $Material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\http\models\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = Material::find($request->input('id'));
        if (!$model) {
            $model = new Material();
            $model->created_by = Auth::id();
            $model->created_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }  else {
            $model->updated_by = Auth::id();
            $model->updated_at = Carbon::now('Asia/Tashkent')->format('Y-m-d H:i:s');
        }


        $model->material_number = $request->input('material_number');
        $model->description = $request->input('description');
        $model->active_from = $request->input('active_from');
        $model->active_to = $request->input('active_to');
        $model->status = $request->input('status');
        $model->unit_measure_id = $request->input('unit_measure_id');
        $model->material_type_id = $request->input('material_type_id');
        $model->material_group_id = $request->input('material_group_id');
        $model->save();
        return $model;
    }

    public function getRef(Request $request)
    {
        return [
            'material_types' => MaterialType::select('id as value', 'name as text')->get(),
            'material_groups' => MaterialGroup::select('id as value', 'name as text')->get(),
            'unit_measures' => UnitMeasure::select('id as value', DB::raw("concat(name,'(',value,')') as text"))->get(),
        ];
    }

    public function fileUpload(Request $request, $id)
    {
        $material = Material::find($id);
        if (!$material) return [
            'status' => 404,
            'message' => 'Material topilmadi.'
        ];
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if (!in_array(strtolower($image->extension()), ['jpg', 'png']))
                return [
                    'status' => 404,
                    'message' => 'JPG yoki PNG formatdagi rasm tanlang.',
                    'ext' => $image->extension()
                ];
            $filename = (microtime(true) * 10000) . '.' . $image->extension();
            if (Storage::exists('edi/materials/' . $material->picture)) {
                Storage::delete('edi/materials/' . $material->picture);
            }
            Storage::putFileAs(
                'edi/materials',
                $image,
                $filename
            );
            $material->picture = $filename;
            $material->save();
            return $material;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\models\EDI\Material  $Material
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Material::find($id);
        if ($model) {
            $model->delete();
        }
    }
}
