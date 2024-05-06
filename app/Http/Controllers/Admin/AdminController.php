<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\responseTrait;
use App\Http\Resources\UserResource;
use App\Models\JobTitle;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

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

    public function addEmployee(Request $request) {

    }

    public function addPermission(Request $request) {
        $vaildate = Validator::make($request->all(), [
            'title' => 'required|string'
        ]);

        if ($vaildate->fails()) {
            return $this->apiResponse(null, $vaildate->errors(), 400);
        }

        Permission::create([
            'title' => $request->title
        ]);
        return $this->apiResponse(null, 'Added successfully', 200);
    }

    public function addJob(Request $request) {
        $vaildate = Validator::make($request->all(), [
            'title' => 'required|string|unique:job_titles',
            'permissions' => 'required|array'
        ]);

        if ($vaildate->fails()) {
            return $this->apiResponse(null, $vaildate->errors(), 400);
        }
        $permissions = $request->permissions;

        $job_title = JobTitle::create([
            'title' => $request->title,
        ]);

        for ($i = 0; $i < sizeof($permissions); $i++) {
            DB::table('permissions_jobs')->insert([
                'job_id' => $job_title->id,
                'permission_id' => $permissions[$i]
            ]);
        }
        return $this->apiResponse(null, 'Added successfully', 200);
    }

    public function allUsers() {
        return $this->apiResponse(UserResource::collection(User::all()), 'Success', 200);
    }

    public function allSeekers() {
        $seekers = User::where('role', 'job_seeker')->get();
        return $this->apiResponse(UserResource::collection($seekers), 'Success', 200);
    }

    public function allCompanies() {
        $seekers = User::where('role', 'company')->get();
        return $this->apiResponse(UserResource::collection($seekers), 'Success', 200);
    }

    public function allEmployees() {
        $seekers = User::where('role', 'employee')->get();
        return $this->apiResponse(UserResource::collection($seekers), 'Success', 200);
    }


}
