<?php

namespace App\Http\Controllers;

use App\Http\Models\Company;
use App\Http\Models\Position;
use App\Http\Models\PositionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class PositionController extends Controller
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

	   $position = Position::with('company')
                ->with('positionType');
                
        if (isset($search)) {
            $position->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('name_ru', 'like', "%" . $search . "%");
                    
            });
        }
        return [
            'positions' =>$position->orderBY('code', 'ASC')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page),
            'companies' => Company::get(),
            'position_types'=>PositionType::get()
            ];
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
     * @param  \App\Http\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        //
        $model = Position::find($request->input('id'));
        if (!$model) {
            $model = new Position();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->company_id = $request['company_id'];
        $model->position_type_id = $request['position_type_id'];
        $model->code = $request['code'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->is_active = $request->input('is_active') ? 1 : 0;
        $model->name_ru = $request['name_ru'];
        $model->save();

        $data = [
            'pos_code' => $model->code,
            'pos_name' => preg_replace('/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu', '', $model->name_uz_latin),
        ];
        $response = Http::post('http://edo-db2.uzautomotors.com/api/new-position', $data);
        dd($response->body());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = Position::find($id);
        $model->delete();
    }
}
