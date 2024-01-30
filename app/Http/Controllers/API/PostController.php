<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Category, Comment, Post, Tag, User};
use Illuminate\Http\Request;
use App\Traits\{ApiResponse, FileUpload};
use Illuminate\Support\Facades\{Config, DB, Log, Validator};

class PostController extends Controller
{
    use ApiResponse, FileUpload;

    /**
     * Post Add
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function addPost(Request $request)
    {
        $validator_rules = [
            'user_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'tags' => 'sometimes',
            'image' => 'required|file|mimes:jpg,jpeg,png',
        ];

        $validator_check = Validator::make($request->all(), $validator_rules);
        if ($validator_check->fails()) {
            return $this->error(Config::get('constants.FIELDS_ERROR'), 200, $validator_check->errors()->all());
        }
        try {
            $user = User::findOrFail($request->user_id);
            $post = Post::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->content,
            ]);
            if($request->has('categories_id')) {
                $explode_id = array_map('intval', explode(',', $request->categories_id));
                $post->category()->attach($explode_id);
            }
            if ($request->has('tags')) {
                $tagNames = explode(',', $request->tags);
                $tagNames = array_map('trim', $tagNames);
                $tagNames = array_filter($tagNames);
                foreach($tagNames as $tag) {
                    Tag::create([
                        'post_id' => $post->id,
                        'name' => $tag
                    ]);
                }
            }
            $path = 'public/images';
            if($request->hasFile('image')) {
                $image_file = $this->uploadFile($request->file('image'), $path); 
            }
            $post->image()->create(['name' => $image_file]);
    
            return $this->success(Config::get('constants.POST.POST_ADD'), 200, $post);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PostController/addPost() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Post list
     *
     * @return \Illuminate\Http\Response
     */
    public function listPost(Request $request)
    {
        try {
            $posts = Post::with(['category']);
            if ($request->has('search') && $request['search'] != "") {
                $posts = $posts->where('title', 'LIKE', '%' . $request->input('search') . '%');
            }
            if($request->input('sort_column')){
                $posts = $posts->orderBy($request->input('sort_column'), $request->input('sort_order'));
            }   

            $posts = $posts->paginate($request->input('selected_show_entry'))->onEachSide(1);
            return $this->success(Config::get('constants.SUCCESS'), 200, $posts);
        } catch (\Exception $e) {
            Log::error('PostController/listPost() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * get categories
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoryList()
    {
        try {
            $categories = Category::select('id', 'name')->get();
            return $this->success(Config::get('constants.SUCCESS'), 200, $categories);
        } catch (\Exception $e) {
            Log::error('PostController/getCategoryList() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Post edit
     *
     * @return \Illuminate\Http\Response
     */
    public function editPost($slug)
    {
        try {
            $post = Post::with(['category', 'image'])->where('slug', $slug)->first();
            return $this->success(Config::get('constants.SUCCESS'), 200, $post);
        } catch(\Exception $e) {
            Log::error('PostController/editCourse() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Post Update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function updatePost(Request $request)
    {
        try {
            $validator_rules = [
                'id' => 'required',
                'title' => 'required',
                'content' => 'required',
            ];
            $validator_check = Validator::make($request->all(), $validator_rules);
            if ($validator_check->fails()) {
                return $this->error(Config::get('constants.FIELDS_ERROR'), 200, $validator_check->errors()->all());
            }
            $post = Post::findOrFail($request->id);
            $input_fields = $request->all();
            $input_fields['is_active'] = $request['is_active'] == "true" ? 1 : 0;
            $post->update($input_fields);
            return $this->success(Config::get('constants.POST.POST_UPDATE'), 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PostController/updateCourse() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * Post Delete
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function deletePost(Request $request)
    {
        try {
            $validator_rules = [
                'post_id' => 'required',
            ];
            $validator_check = Validator::make($request->all(), $validator_rules);
            if ($validator_check->fails()) {
                return $this->error(Config::get('constants.FIELDS_ERROR'), 200, $validator_check->errors()->all());
            }
            $post = Post::findOrFail($request->post_id);
            $post->delete(); 
            return $this->success(Config::get('constants.POST.POST_DELETE_SUCCESS'), 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PostController/deletePost() => '.$e->getMessage());
            return $this->error(Config::get('constants.POST.POST_DELETE_FAIL'), 200);
        }
    }

    /**
     * add comment on post
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request)
    {
        try {
            $validator_rules = [
                'comment' => 'required|string',
                'slug' => 'required'
            ];
            $validator_check = Validator::make($request->all(), $validator_rules);
            if ($validator_check->fails()) {
                return $this->error(Config::get('constants.FIELDS_ERROR'), 200, $validator_check->errors()->all());
            }
            
            $post = Post::where('slug', $request->slug)->first();
            $comment = Comment::create([
                'post_id' => $post->id,
                'content' => $request->comment
            ]);

            return $this->success(Config::get('constants.POST.COMMENT_ADDED_SUCCESS'), 200, $comment);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PostController/deletePost() => '.$e->getMessage());
            return $this->error(Config::get('constants.POST.POST_DELETE_FAIL'), 200);
        }
        
    }

}
