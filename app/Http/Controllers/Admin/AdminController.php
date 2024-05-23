<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Jobs\InviteEmployeeJob;
use App\Models\Employee;
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
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser(Request $request) {

            $user = User::where('id', $request->id)->first();
            if ($user) {
                $user->delete();
                return $this->apiResponse(null, 'User removed successfully', 200);
            }
            return $this->apiResponse(null, 'User not found', 404);

    }

    public function getUsers($type) {

        if($type == 'AllUsers') {
            $users = User::all();
            $users = $users->reject(function(User $user) {
                $roles = $user->roles_name;
                foreach ($roles as $value) {
                    return $value === 'owner';
                }
            });
            $result = UserResource::collection($users);
        }

        else if($type == 'JobSeekers' ) {
            $seekers = User::role('job_seeker')->latest()->get();
            $result = UserResource::collection($seekers);
        }

        else if($type == 'Companies' ) {
            $seekers = User::role('company')->latest()->get();
            $result = UserResource::collection($seekers);
        }

        else {
            return $this->apiResponse(null, 'Error User Type ', 403);
        }
        return $this->apiResponse($result , 'success' , 200);

    }

    public function searchByUsernameOrEmail($username){

        $users = User::whereAny(['user_name' , 'email'],'LIKE' , '%'.$username.'%')->get();
        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                return $value === 'owner';
            }
        });

        if($users->isEmpty()){
            return $this->apiResponse(null,'Not Found',404);

        } else{
            $result = UserResource::collection($users);
        }
        return $result;
    }
}
