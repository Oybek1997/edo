<?php
/**
 * Created by PhpStorm.
 * User: IMB727
 * Date: 23.02.2021
 * Time: 7:41
 */

namespace App\Http\Controllers;

use App\Http\Models\File;
use App\Http\Models\Tag;
use App\Http\Models\Timeline;
use App\Http\Models\TimelineTag;
use App\Http\Models\TimelineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TimelineController extends Controller
{
    public function getTimeline(Request $request)
    {
        $user_id = Auth::id();
        $page = $request->page;
        $timeline = Timeline::select()
            ->whereNull('parent')
            ->with('comments.createdBy.employee')
            ->with('createdBy.employee')
            ->with('files')
            ->leftJoinSub('SELECT timeline_id, COUNT(timeline_id) as like_count FROM timeline_user WHERE likes = true GROUP BY timeline_id', 'likes', 'likes.timeline_id', 'timeline.id')
            ->leftJoinSub('SELECT timeline_id, COUNT(timeline_id) as dislike_count FROM timeline_user WHERE dislikes = true GROUP BY timeline_id', 'dislikes', 'dislikes.timeline_id', 'timeline.id')
            ->with('likers.whoLike.employee')
            ->with('dislikers.whoLike.employee')
            ->with('timelineTags.tag')
            ->orderBy('created_at', 'Desc');

        if (isset($request->search)) {
            $timeline = $timeline->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        if ($request->search_tag && count($request->search_tag) > 0) {
            $t = $request->search_tag;
            $a = TimelineTag::whereIn('tag_id', $t)->pluck('timeline_id');
            $timeline = $timeline->whereIn('timeline.id', $a);
        }

        $timeline = $timeline->paginate($page * 10, ['*'], 'page name', 1);

        $timeline_user = TimelineUser::select('timeline_id')->where('user_id', $user_id)->get();

        foreach ($timeline as $key => $value) {
            $new_t = $timeline_user->where('timeline_id', $value->id)->first();
            if (!$new_t) {
                $model = new TimelineUser();
                $model->user_id = $user_id;
                $model->timeline_id = $value->id;
                $model->save();
            }
        }
        return ['timeline' => $timeline];
    }

    public function getRef()
    {
        $tags = Tag::get();
        return $tags;
    }

    public function update(Request $request)
    {
        $req = $request->all();
        $file = $request->file('file');
        $tags = json_decode($req['tags']);

        DB::beginTransaction();
        try {
            $model = Timeline::find($request->input('id'));
            if (!$model) {
                $model = new Timeline();
                $model->created_by = Auth::id();
            }
            $model->title = $req['title'] ? $req['title'] : '';
            $model->name = $req['name'];
            $model->is_active = $req['is_active'];
            if ($req['parent'] > 0)
                $model->parent = $req['parent'];
            if ($req['parent_comment'] > 0)
                $model->parent_comment = $req['parent_comment'];
            $model->updated_by = Auth::id();
            $model->save();
            if ($tags) {
                TimelineTag::where('timeline_id', $model->id)->delete();
                foreach ($tags as $tag) {
                    $m = new TimelineTag();
                    $m->timeline_id = $model->id;
                    $m->tag_id = $tag;
                    $m->save();
                }
            }
            if ($file) {
                foreach ($file as $item) {
                    $fileModel = new File();
                    $fileName = $item->getClientOriginalName();
                    $physicalName = time() . rand();
                    $object_type = 9;
                    $object_id = $model->id;
                    $fileModel->object_type_id = $object_type;
                    $fileModel->file_name = $fileName;
                    $fileModel->physical_name = $physicalName;
                    $fileModel->object_id = $object_id;
                    $fileModel->created_by = Auth::id();

                    $fileModel->save();
                    Storage::putFileAs('timeline', $item, $fileName);
                }
            }
            DB::commit();
            return 'Saved successfully';
        } catch
        (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function deleteFile(Request $request)
    {
        $model = $request->input('file');
        Storage::delete('timeline/' . $model['file_name']);
        $model = File::find($model['id']);
        $model->delete();
        return 'OK';
    }

    public function destroy($id)
    {
        $model = Timeline::find($id);
        $child = Timeline::where('parent', $model->id)->get();
        $files = File::where('object_type_id', 9)->where('object_id', $model->id)->get();
        $timeline_user = TimelineUser::where('timeline_id', $id)->get();

        foreach ($timeline_user as $value) {
            $value->delete();
        }
        foreach ($child as $value) {
            $value->where('timeline_id', $model->id)->delete();
        }
        foreach ($files as $value) {
            Storage::delete('timeline/' . $value->file_name);
            $value->delete();
        }
        $model->delete();
    }

    public function getCount()
    {
        $count = Timeline::whereNull('parent')
            ->leftJoinSub('select * from timeline_user WHERE timeline_user.user_id = ' . Auth::id(), 'tu', 'tu.timeline_id', '=', 'timeline.id')
            ->whereNull('tu.timeline_id')->get()->count();
        return $count;
    }

    public function likeIt($id, Request $request)
    {
        if ($request->type) {
            $model = TimelineUser::where('user_id', Auth::id())->where('timeline_id', $id)->first();
            $model->likes = !$model->likes;
            $model->dislikes = false;
            $model->save();
            return 'Likes';
        } else {
            $model = TimelineUser::where('user_id', Auth::id())->where('timeline_id', $id)->first();
            $model->dislikes = !$model->dislikes;
            $model->likes = false;
            $model->save();
            return 'Dislikes';
        }
    }

    public function getFile($id)
    {
        $file = File::where('id', $id)->first();
        if ($file) {
            try {
                Storage::get('timeline/' . $file->file_name);
                $path = Storage::path('timeline/' . $file->file_name);
                return response()->file($path);
            } catch (FileNotFoundException $error) {
                return "<h1 style='text-align: center; font-style: italic'>Fayl topilmadi :(</h1>";
            }


        } else
            return "Bazada fayl topilmadi";
    }

    public function insertTag(Request $request)
    {
        $model = new Tag();
        $model->tag_name = $request->tag_name;
        $model->save();
        return 'Inserted';
    }
}
