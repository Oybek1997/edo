<?php

namespace App\Http\Controllers;

use App\Http\Models\TableList;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TableList::get();
    }

    public function tableList(Request $request)
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
     * @param  \App\Http\Models\TableList  $tableList
     * @return \Illuminate\Http\Response
     */
    public function show(TableList $tableList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\TableList  $tableList
     * @return \Illuminate\Http\Response
     */
    public function edit(TableList $tableList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\TableList  $tableList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TableList $tableList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\TableList  $tableList
     * @return \Illuminate\Http\Response
     */
    public function destroy(TableList $tableList)
    {
        //
    }

    public function getTableList(Request $request)
    {
        $tableListId = $request->input('table_list_id');
        $searchText = $request->input('search');
        $search = explode(' ', $searchText);

        $tableList = TableList::find($tableListId);

        $columns = explode(', ', $tableList->column_name);

        if ($tableList->conditions) {
            $select = 'select * from workflow.' . $tableList->table_name . ' where '.$tableList->conditions;
            // $tableListValue = DB::select('select * from `'.$tableList->table_name.'` where '.$tableList->conditions);   
            // $q =  
        } else {
            $select = 'select * from workflow.' . $tableList->table_name . ' where true';
            // $tableListValue = DB::table($tableList->table_name);
        }

        $select_tmp = '';
        foreach ($search as $search_key => $search_value) {
            if ($search_value) {
                foreach ($columns as $key => $column) {
                    if (strpos($column, "locale")) {
                        $column_l = str_replace('locale', 'ru', $column);
                        $select_tmp .= $column_l . " ilike '%" . $search_value . "%' or ";
                        // $tableListValue->orWhere($column_l, 'like', '%'.$search_value.'%');
                        $column_l = str_replace('locale', 'uz_latin', $column);
                        $select_tmp .= $column_l . " ilike '%" . $search_value . "%' or ";
                        // $tableListValue->orWhere($column_l, 'like', '%'.$search_value.'%');
                        $column_l = str_replace('locale', 'uz_cyril', $column);
                        $select_tmp .= $column_l . " ilike '%" . $search_value . "%' or ";
                        // $tableListValue->orWhere($column_l, 'like', '%'.$search_value.'%');
                    } else {
                        $select_tmp .= $column . " ilike '%" . $search_value . "%' or ";
                        // $tableListValue->orWhere($column, 'like', '%'.$search_value.'%');
                    }
                }
            }
            if($select_tmp){
                $select .= " and ( ".$select_tmp." false ) ";
            }
        }
        $select .= ' limit 100';

        // return $select;
        return ['table_list' => DB::select($select), 'columns' => $columns];
    }

    public function table_list_employee_staff(Request $request)
    {
        $tabel = $request->input('search');
        return
            $employee = Employee::where('tabel', $tabel)
            ->with(['mainStaff' => function ($q) {
                $q->with('position');
                $q->with('department');
                // $q->with(['department' => function ($q) {
                //     $q->with('departmentType');
                // }]);
            }])->first();
    }
}
