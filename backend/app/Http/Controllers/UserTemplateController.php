<?php

namespace App\Http\Controllers;

use App\Http\Models\Employee;
use App\Http\Models\UserTemplate;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserTemplateController extends Controller
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
        // $users = UserTemplate::with(['user.employee' => function ($query) {
        //     $query->withTrashed();
        // }])->with('documentTemplate');
        $users = UserTemplate::with(['user' => function ($query) {
            $query->withTrashed();
            $query->with('employee');
        }])->with('documentTemplate');

        if (isset($filter['username'])) {
            $users->whereHas('user', function ($q) use ($filter) {
                $q->where('username', 'like', "%" . $filter['username'] . "%");
            });
        }
        if (isset($filter['employee'])) {
            $users->whereHas('user.employee', function ($q) use ($filter) {
                $q->where('lastname_uz_latin', 'like', "%" . $filter['employee'] . "%")
                ->orWhere('firstname_uz_latin', 'like', "%" . $filter['employee'] . "%")
                ->orWhere('lastname_uz_cyril', 'like', "%" . $filter['employee'] . "%")
                ->orWhere('firstname_uz_cyril', 'like', "%" . $filter['employee'] . "%");
            });
        }

        if (isset($filter['document_template_id'])) {
            $users->whereHas('documentTemplate', function ($q) use ($filter) {
                $q->where('name_uz_latin', 'like', "%" . $filter['document_template_id'] . "%");
            });
        }

        return ['users' => $users->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)];
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
     * @param  \App\UserTemplate $UserTemplate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserTemplate  $UserTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(UserTemplate $UserTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserTemplate $UserTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return Auth::id();
        $model = UserTemplate::find($request['id']);
        if (!$model) {
            $model = new UserTemplate();
            $model->created_by = Auth::id();
        }
        $model->user_id = $request['user_id'];
        $model->document_template_id = $request['document_template_id'];
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
        $user = UserTemplate::find($id);
        $user->delete();
    }
}
