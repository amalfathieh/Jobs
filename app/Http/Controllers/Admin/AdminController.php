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

use function Clue\StreamFilter\fun;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser($id) {

            $user = User::where('id', $id)->first();
            if ($user) {
                $user->delete();
                return $this->apiResponse(null, __('strings.user_removed_successfully'), 200);
            }
            return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getUsers($type) {

        if($type == 'AllUsers') {
            $users = User::all();
            $users = $users->reject(function(User $user) {
                $roles = $user->roles_name;
                foreach ($roles as $value) {
                    if ($value === 'owner' || $value === 'employee') {
                        return true;
                    }
                }
            });
            $result = UserResource::collection($users);
        }

        else if($type == 'JobSeekers') {
            $seekers = User::all();
            $seekers = $seekers->reject(function (User $user){
                return !array_search('job_seeker', $user->roles_name);
            });
            $result = UserResource::collection($seekers);
        }
        else if($type == 'Companies') {
            $companies = User::all();
            $companies = $companies->reject(function (User $user){
                return !array_search('company', $user->roles_name);
            });
            $result = UserResource::collection($companies);
        }
        else {
            return $this->apiResponse(null, 'Error user type', 400);
        }
        return $this->apiResponse($result , 'Success' , 200);
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
            return $this->apiResponse(null,__('strings.not_found'),404);

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
            return $this->apiResponse($ban, __('strings.banned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_banned'), 403);
        }
    }

    public function unBanUser($id) {
        $user = User::find($id);
        if ($user->isBanned()) {
            $user->syncRoles($user->roles_name);
            $user->unBan();
            return $this->apiResponse(null, __('strings.unbanned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_not_banned'), 403);
        }
    }

    public function isBan() {
        $user = User::where('id', Auth::user()->id)->first();
        return $user->isBanned();
    }

    public function getBans() {
        $users = User::onlyBanned()->get();
        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                return $value === 'owner';
            }
        });
        return $this->apiResponse($users, __('strings.all_users_banned'), 200);
        return $this->apiResponse(UserResource::collection($users), __('strings.all_users_banned'), 200);
    }

    public function countPOA() {
        $posts = Post::count();
        $opportunites = Opportunity::count();
        $applies = Apply::count();
        $counts = [
            'posts' => $posts,
            'opportunites' => $opportunites,
            'applies' => $applies
        ];
        return $this->apiResponse($counts, __('strings.count_posts_opportunities_applies'), 200);
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
        return $this->apiResponse($counts, __('strings.count_users'), 200);
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
