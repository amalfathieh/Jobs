<?php

namespace App\Http\Controllers;

use App\Http\Requests\postRequest;
use App\Models\Post;
use App\Models\User;
use App\services\FileService;
use App\services\PostService;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use responseTrait;
    protected $postService;
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function create(PostRequest $request ,FileService $fileService){

        $seeker = Auth::user()->seeker;

        $this->postService->store(
            $seeker->id, $request->title, $request->body, $request->file('file')
        );

        return $this->apiResponse(null, 'post create successfully', 201);
    }

    public function edit(Request $request , $post_id){
        $post = Post::find($post_id);
        $user = User::where('id', Auth::user()->id)->first();
        if (!is_null($post)) {
            if( $post['seeker_id'] == Auth::user() ) {
                $this->postService->edit($request, $post);

                return $this->apiResponse(null,'post edit successfully',200);
            }
            return $this->apiResponse(null,'You can not edit this post.',403);
        }
        return $this->apiResponse(null, 'Post not excite.', 404);
    }

    public function delete($id){
        $post = Post::find($id);
        $user = User::where('id', Auth::user()->id)->first();
        if (!is_null($post)) {
            if (($user->hasRole('job_seeker') && $post['seeker_id'] == $user->seeker->id) || $user->can('delete post')) {
                $post->delete();
                return $this->apiResponse(null, 'Post deleted successfully', 200);
            }
            return $this->apiResponse(null,'You do not have permission',403);
        }
        return $this->apiResponse(null, 'Post not found.', 404);
    }

    public function allPosts(){
         $posts =Post::query();
        $posts = $posts->latest()->get();
        return $this->apiResponse($posts,'all posts',200);
    }
}
