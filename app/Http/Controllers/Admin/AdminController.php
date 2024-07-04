<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\responseTrait;
use App\Http\Resources\UserResource;
use App\Models\Apply;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser($id) {

            $user = User::where('id', $id)->first();
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

        else if($type == 'Companies') {
            $companies = User::role('company')->latest()->get();
            $result = UserResource::collection($companies);
        }
        else {
            return $this->apiResponse(null, 'Error User Type ', 403);
        }
        return $this->apiResponse($result , 'success' , 200);
    }

    public function search($search){
        $users = User::where(function ($query) use ($search){
            $query->where('user_name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');

        })->orWhereHas('seeker', function ($query) use ($search) {
            $query->where('first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search . '%');

        })->orWhereHas('company', function ($query) use ($search) {
            $query->where('company_name', 'LIKE', '%' . $search . '%');
        })->get();

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
        return $this->apiResponse($result,'Found it',200);
    }

    public function banUser(Request $request, $id){
        $vaildate = Validator::make($request->all(), [
            'reason' => 'required',
            'type' => 'required'
        ]);
        if ($vaildate->fails()) {
            return $this->apiResponse(null, $vaildate->errors(), 400);
        }
        $user = User::find($id);
        if ($user->isNotBanned()) {
            $comment = $request->comment;
            $type = $request->type;
            $expired_at = $request->expired_at;
            if ($type === 'forever') {
                $expired_at = null;
            } else if (!$expired_at) {
                return $this->apiResponse(null, 'Date is required', 400);
            }

            $user->roles()->detach();
            $ban = $user->ban([
                'comment' => $comment,
                'expired_at' => $expired_at
            ]);
            return $this->apiResponse($ban, "Banned successfully", 200);
        } else {
            return $this->apiResponse(null, "User is already banned", 400);
        }
    }

    public function unBanUser($id) {
        $user = User::find($id);
        if ($user->isBanned()) {
            $user->syncRoles($user->roles_name);
            $user->unBan();
            return $this->apiResponse(null, "Unbanned successfully", 200);
        } else {
            return $this->apiResponse(null, "User is already not banned", 400);
        }
    }

    public function isBan() {
        $user = User::where('id', Auth::user()->id)->first();
        return $user->isBanned();
    }

    public function getBans() {
        $users = User::onlyBanned()->get();
        return $this->apiResponse(UserResource::collection($users), "These are all users banned", 200);
    }

    // public function deleteExpiredBanned() {
    //     $users = User::onlyBanned()->get();
    //     $user = User::find(6);
    //     return $user->bans;
    //     foreach ($users as $user) {
    //         if ($user->bans[0]->expired_at < Carbon::now()) {
    //             $user->syncRoles($user->roles_name);
    //             $user->unBan();
    //         }
    //     }
    // }

    public function countPOA() {
        $posts = Post::count();
        $opportunites = Opportunity::count();
        $applies = Apply::count();
        $counts = [
            'posts' => $posts,
            'opportunites' => $opportunites,
            'applies' => $applies
        ];
        return $this->apiResponse($counts, 'These are count for posts and opportunites and applies', 200);
    }

    public function countUsers() {
        $users = User::count();
        $seekers = Seeker::count();
        $companies = Company::count();
        $employees = Employee::count();
        $counts = [
            'users' => $users,
            'seekers' => $seekers,
            'companies' => $companies,
            'employees' => $employees,
        ];
        return $this->apiResponse($counts, 'These are count of users', 200);
    }

    public function logs() {
        $logs = Activity::all();
        return $logs;
    }

    public function lineChart() {
        $minDate = Activity::min('created_at');
        $maxDate = Activity::max('created_at');

        if (!$minDate || !$maxDate) {
            return response()->json(['message' => 'No data available'], 404);
        }

        $startDate = Carbon::parse($minDate)->startOfDay();
        $endDate = Carbon::parse($maxDate)->endOfDay();

        $dailyData = [];

        while ($startDate->lte($endDate)) {
            $dayStart = $startDate->copy()->startOfDay()->toDateTimeString();
            $dayEnd = $startDate->copy()->endOfDay()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $dayFormat = $startDate->copy()->startOfDay()->format('M-d');
            $dailyData[] = [
                'day' => $dayFormat,
                'count' => $count,
                'amt' => 2000
            ];

            $startDate->addDay();
        }

        return response()->json($dailyData);
    }
}
