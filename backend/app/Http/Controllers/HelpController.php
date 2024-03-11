<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Help;
use App\Http\Models\File;
use App\Http\Models\OtgulDate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HelpController extends Controller
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
        $language = $request->input('language');
        $filter = $request->input('filter');
        $help = Help::with('files')
                    ->with('createdBy.employee');

        if (isset($filter)) {
            $help->where(function ($query) use ($filter) {
                return $query
                    ->where('title', 'like', "%" . $filter['title'] . "%");
            });
        }
        if (isset($filter)) {
            $help->where(function ($query) use ($filter) {
                return $query
                    ->where('name', 'like', "%" . $filter['name'] . "%");
            });
        }
        if (isset($filter)) {
            $help->where(function ($query) use ($filter) {
                return $query
                    ->where('src', 'like', "%" . $filter['src'] . "%");
            });
        }
        if (isset($filter)) {
            $help->where(function ($query) use ($filter) {
                return $query
                    ->where('is_active', 'like', "%" . $filter['is_active'] . "%");
            });
        }
        return $help->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
    
    public function helpIndex(Request $request)
    {
        $filter = $request->input('filter');

        $help = Help::with('files')
                    ->with('createdBy.employee');
        if ($filter) {
            $help->where(function ($query) use ($filter) {
                return $query
                    ->where('src', 'like', "%" . $filter . "%");
            });
        }

        return $help->get();
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
     * @param  \App\Http\Models\Help $Help
     * @return \Illuminate\Http\Response
     */
    public function show(Help $Help)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Help $Help
     * @return \Illuminate\Http\Response
     */
    public function edit(Help $Help)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Help $Help
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Help $Help)
    {
        DB::beginTransaction();
        try{
            $model = Help::find($request->input('id'));
    
            if (!$model) {
                $model = new Help;
                $model->created_by = Auth::id();
            }
            $model->title = $request->input('title');
            $model->name = $request->input('name');
            $model->src = $request->input('src');
            $model->is_active = $request->input('is_active');
            $model->updated_by = Auth::id();
            $model->save();
            DB::commit();
            $this->updateFile($request,$model->id);
            return ['message' => 'Successfully saved!', 'id' => $model->id];
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function updateFile(Request $request, $id)
    {
        $files = $request->file('files');
        if ($files) {
            try {
                $object_type_id = 12;
                $object_id = $id;

                foreach ($files as $key => $value) {
                    $filename = time() . rand();
                    Storage::putFileAs(
                        'documents',
                        $value,
                        $filename
                    );
                    $file = new File();
                    $file->object_type_id = $object_type_id;
                    $file->file_name = $value->getClientOriginalName();
                    $file->physical_name = $filename;
                    $file->object_id = $object_id;
                    $file->created_by = Auth::id();
                    $file->save();
                }
                return ['message' => 'Successfully saved!', 'document_id' => $object_id];
            } catch (\Throwable $th) {
                dd($th);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Help $Help
     * @return \Illuminate\Http\Response
     */
    public function destroy(Help $Help, $id)
    {
        $model = Help::find($id);
        $model->delete();
    }
    public function otguldateadd(){
        $otguls = OtgulDate::where('document_detail_employee_id', 853395)->get();
        $sana = '';
        foreach ($otguls as $key => $value) {
            $sana .= $value->start_date;
            $sana .= ',';
            $sana .= $value->end_date;
            $sana .= ';';
        }
        return $sana;
    }
}
