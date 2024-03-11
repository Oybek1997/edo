<?php

namespace App\Http\Controllers;


use App\Http\Models\Graphic;
use Illuminate\Http\Request;
use App\Http\Models\Document;

class GraphicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function graphicValidate(Request $request)
     {
        $requestgraphics = $request['graphics'];
        foreach($requestgraphics as $key=>$value){
            $depatrnet_id = isset($value['department_id']) ? $value['department_id'] : '';
            $year = isset($value['year']) ? $value['year'] : '';
            $month = isset($value['month']) ? $value['month'] : '';
            $day = isset($value['day']) ? $value['day'] : ''; // dam yoki ish kuni: 0 => ish kuni 1 => dam kuni
            if($value['day'] == 1){
                $graphic = Graphic::where('year',$year)
                ->where('month', $month)
                ->where('department_id', $depatrnet_id)
                ->where('day', 0)
                ->whereHas('document', function($q) {
                   $q->whereIn('status', [3,4,5]);
                })
                ->first();
                if(!($graphic)){
                    return ['status' => 500, 'message'=> [$year, $month, $depatrnet_id]];
                }
            }
        }
        return ['status' => 200, 'message'=> 'success']; 
     }
     
    public function index()
    {
        //
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
