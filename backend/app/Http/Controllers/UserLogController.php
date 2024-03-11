<?php

namespace App\Http\Controllers;

use App\Http\Models\UserLog;
use App\Http\Models\UserLog\IpAddress;
use App\Http\Models\UserLog\Action;
use App\Http\Models\ControllerModel;
use App\User;
use Illuminate\Http\Request;

class UserLogController extends Controller
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
        $filter = $request->input('filters');
        $language = $request->input('language');
        $documentDownloadLogs = UserLog::with([
                'user',
                'ipadress',
                'controller',
                'action'
            ]
        );
       // dd($documentDownloadLogs);
        if (isset($filter['username']) && $filter['username']) {
            $userId = User::where('username','=', $filter['username'])->first()->id;

            $documentDownloadLogs->where('user_id', 'ilike', '%' . $userId . '%');

        }
        if (isset($filter['eimzo_name']) && $filter['eimzo_name']) {
            $userId = User::where('eimzo_name','=', $filter['eimzo_name'])->first()->id;
            $documentDownloadLogs->where('user_id', 'ilike', '%' . $userId . '%');
        }
        if (isset($filter['ipadress']) && $filter['ipadress']) {
            $ipAdressId = IpAddress::where('name','=', $filter['ipadress'])->first()->id;
            $documentDownloadLogs->where('ip_address_id', 'ilike', '%' . $ipAdressId . '%');
        }
        if (isset($filter['action']) && $filter['action']) {
            $actionId = Action::where('name','=', $filter['action'])->first()->id;
            $documentDownloadLogs->where('action_id', 'ilike', '%' . $actionId . '%');
        }
        if (isset($filter['created_at']) && $filter['created_at']) {
//            $actionId = Action::where('name','=', $filter['action'])->first()->id;
            $documentDownloadLogs->where('created_at', 'ilike', '%' . $filter['created_at'] . '%');
        }



        return $documentDownloadLogs->paginate($itemsPerPage == '-1' ? 1000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function show(UserLog $userLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLog $userLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserLog $userLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLog $userLog)
    {
        //
    }
}
