<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    use responseTrait;

    public function followUser($user_id)
    {
        $user = User::find($user_id);

        $user->followers()->attach(auth()->user()->id);
        return $this->apiResponse(null, 'Successfully followed the user.',200);
    }

    public function unFollowUser($user_id)
    {
        $user = User::find($user_id);

        $user->followers()->detach(auth()->user()->id);
        return $this->apiResponse(null, 'Successfully unfollowed the user.',200);
    }


    public function showFollowers($userId)
    {
        $user = User::find($userId);
        $followers  = $user->followers;
        return response()->json(FollowerResource::collection($followers));

    }

    public function showFollowings($userId)
    {
        $user = User::find($userId);
        $followings  = $user->followings;
        return response()->json(FollowerResource::collection($followings));
    }

}
