<?php

namespace App\Http\Controllers;

use App\Http\Models\Employee;
use App\Http\Models\UnblockedUser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UnblockedUserController extends Controller
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
        $filter = $request->input('filter');
        $search = $request->input('search');
        // $users = UnblockedUser::with('user.employee')
        // ->with('employeeStaff.staff.department')
        // ->with('employeeStaff.staff.position');

        $users = UnblockedUser::with(['user.employee' => function ($query){
            $query->withTrashed();
            $query->with('employeeStaff.staff.department');
            $query->with('employeeStaff.staff.position');
        }]);
        
        return ['users' => $users->
        paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)];
        
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
     * @param  \App\UnblockedUser $UnblockedUser
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UnblockedUser  $UnblockedUser
     * @return \Illuminate\Http\Response
     */
    public function edit(UnblockedUser $UnblockedUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UnblockedUser $UnblockedUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return Auth::id();
        $model = UnblockedUser::find($request['id']);
        if (!$model) {
            $model = new UnblockedUser();
            $model->created_by = Auth::id();
        }
        $model->user_id = $request['user_id'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = UnblockedUser::find($id);
        $user->delete();
    }
}
