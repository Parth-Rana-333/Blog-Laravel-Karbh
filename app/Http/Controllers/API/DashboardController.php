<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Config, Log, Validator};

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Get Auth User
     * 
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        try {
            $user = Auth::user();
            if($user) {
                return $this->success(Config::get('constants.SUCCESS'), 200, $user);
            }
            return $this->error(Config::get('constants.ERROR'), 401);
        } catch(\Exception $e) {
            Log::error('DashboardController/getUser() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    /**
     * get all blogs
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAllBlogList(Request $request)
    {
        try {
            $posts_all = Post::with(['category', 'image']);
            if ($request->has('search') && $request['search'] != "") {
                $posts_all = $posts_all->where('title', 'LIKE', '%' . $request->input('search') . '%');
            }
            $posts_all = $posts_all->get();

            return $this->error(Config::get('constants.ERROR'), 200, $posts_all); 
        } catch(\Exception $e) {
            Log::error('DashboardController/getAllBlogList() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }

    public function getPostByID($slug)
    {
        try {
            $post = Post::with(['category', 'image', 'comments'])->where('slug', $slug)->first();
            return $this->error(Config::get('constants.ERROR'), 200, $post); 
        } catch(\Exception $e) {
            Log::error('DashboardController/getPostByID() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }
}
