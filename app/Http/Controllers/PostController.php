<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Notifications\SendNotification;
use App\services\FileService;
use App\services\PostService;
use App\services\UserService;
use App\Traits\NotificationTrait;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function PHPUnit\Framework\isEmpty;

class PostController extends Controller
{
    use responseTrait,NotificationTrait;
    protected $postService;
public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function create(PostRequest $request){
        $user = User::find(Auth::user()->id);
        $seeker = $user->seeker;

        $post = $this->postService->store(
            $seeker->id, $request->body, $request->file('file'),$request->type
        );

        $followers = $user->followers;
        if ($followers) {
            $tokens = [];
            foreach($followers as $follower){
                $tokens = array_merge($tokens , $follower->routeNotificationForFcm());
            }
            $data =[
                'obj_id'=>$post->id,
                'title'=>'New Post',
                'body'=>'New post has been published by: '.$seeker->first_name.'.',
            ];

            Notification::send($followers,new SendNotification($data));
//            $this->sendPushNotification($data['title'],$data['body'],$tokens);
        }
        return $this->apiResponse(null, 'post create successfully', 201);
    }

    public function edit(Request $request, $post_id){
        $post = Post::find($post_id);
        $user = User::where('id', Auth::user()->id)->first();
        if (!is_null($post)) {
            if( $post['seeker_id'] == $user->seeker->id ) {
                $post = $this->postService->edit($request, $post);

                return $this->apiResponse($post,__('strings.updated_successfully'),200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function delete($id){
        $post = Post::find($id);
        $user = User::where('id', Auth::user()->id)->first();
        if ($post) {
            if (($user->hasRole('job_seeker') && $post['seeker_id'] == $user->seeker->id) || (($user->hasRole('employee') || $user->hasRole('owner')) && $user->can('post delete'))) {
                $post->delete();
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function allPosts(){
        $userId = Auth::user()->id;
        $posts = Post::select('posts.*')->addSelect(DB::raw("EXISTS(SELECT 1 FROM followers WHERE followers.follower_id = posts.seeker_id AND followers.followee_id = $userId) AS is_followed"))
            ->orderByDesc('is_followed')
            ->latest()
            ->get();

        return $this->apiResponse(PostResource::collection($posts),'all posts',200);
    }
}
