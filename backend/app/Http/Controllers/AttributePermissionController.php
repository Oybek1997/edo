<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\Employee;
use App\Http\Models\AttributePermission;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttributePermissionController extends Controller
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
        $partners = AttributePermission::with("user")->with("DocumentTemplate");

        if (isset($search)) {
            $partners->where(function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%" . $search . "%")
                    ->orWhere('adress', 'like', "%" . $search . "%");
            });
        }
        return  $partners->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
        $model = AttributePermission::find($request['id']);
        if (!$model) {
            $model = new AttributePermission();
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
        $user = AttributePermission::find($id);
        $user->delete();
    }



    public function getMaindata()
    {
        $documenttemplates = DocumentTemplate::get();
        $users = User::get();
       //  return $users;
        return ['documenttemplates' => $documenttemplates, 'users' => $users];
    }


    public function add(Request $request)
    {
        //return $request;
        $userId = Auth::user()->id;
        $documenttemplates = $request['documenttemplates'];
        $user = $request['users'];
        // return $user;

        foreach ($documenttemplates as $key => $documenttemplate) {
            $userTemplate = AttributePermission::where('document_template_id', $documenttemplate)->where('user_id', $user)->first();

            if (!$userTemplate) {
                $userTemplate = new AttributePermission();
                $userTemplate->document_template_id = $documenttemplate;
                $userTemplate->user_id = $user;
                $userTemplate->created_at = Carbon::now();
                $userTemplate->created_by = $userId;
                $userTemplate->save();
            }
        }

        return $userTemplate;
    }
}

