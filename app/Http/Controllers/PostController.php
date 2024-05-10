<?php

namespace App\Http\Controllers;

use App\Http\Requests\postRequest;
use App\Models\Post;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use responseTrait;

    public function create(PostRequest $request){
        try {
            $this->authorize('isCompany');
            $id = Auth::user()->id;
            // here processing file

            Post::create([
                'seeker_id'=>$id,
                'body'=>$request['body'],
                'file'=>$request['file']
            ]);
            return $this->apiResponse(null,'post create successfully',201);
        }catch (AuthenticationException $authExp){
            return $this->apiResponse(null, $authExp->getMessage(), 401);
        }catch (\Exception $ex){
            return $this->apiResponse(null,$ex->getMessage(),$ex->getCode());
        }
    }

    public function delete($id){
        Post::find($id)->delete();
        return $this->apiResponse(null,'post deleted',200);
    }

    public function allPosts(){
        $posts =Post::all();
        return $this->apiResponse($posts,'all posts',200);
    }
}
