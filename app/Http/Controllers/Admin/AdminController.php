<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\responseTrait;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser(Request $request) {
        if (Gate::allows('isAdmin')) {
            $user = User::where('id', $request->id)->first();
            if ($user) {
                $user->delete();
                return $this->apiResponse(null, 'User removed successfull', 200);
            }
            return $this->apiResponse(null, 'User not found', 404);
        }
        return $this->apiResponse(null, 'You are not allowed to remove user', 403);
    }

    public function removePost(Request $request) {
        if (Gate::allows('isAdmin')) {
            $post = Post::where('id', $request->id)->first();
            if ($post) {
                $post->delete();
                return $this->apiResponse(null, 'Post removed successfull', 200);
            }
            return $this->apiResponse(null, 'Post not found', 404);
        }
        return $this->apiResponse(null, 'You are not allowed to remove post', 403);
    }
}
