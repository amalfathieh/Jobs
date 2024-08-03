<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BansResource;
use App\Http\Resources\LogsResource;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

use function Clue\StreamFilter\fun;

class AdminController extends Controller
{
    use responseTrait;
    public function removeUser($id) {
        $user = User::where('id', $id)->first();
        if ($user) {
            if ($user->hasRole('owner')) {
                return $this->apiResponse(null, 'Are you serious? this account for admin', 403);
            }
            $user->delete();
            return $this->apiResponse(null, __('strings.user_removed_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getUsers($type, Request $request) {
        if($type == 'AllUsers') {
            if ($request->startDate && $request->endDate) {
                $users = User::whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $users = User::whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $users = User::all();
            }
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
            if ($request->startDate && $request->endDate) {
                $seekers = User::Role('job_seeker')->whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $seekers = User::Role('job_seeker')->whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $seekers = User::Role('job_seeker')->get();
            }
            // $seekers = $seekers->reject(function (User $user){
            //     return !array_search('job_seeker', $user->roles_name);
            // });
            $result = UserResource::collection($seekers);
        }
        else if($type == 'Companies') {
            if ($request->startDate && $request->endDate) {
                $companies = User::Role('company')->whereBetween('created_at', [$request->startDate, $request->endDate])->get();
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = User::max('created_at');
                $companies = User::Role('company')->whereBetween('created_at', [$request->startDate, $dateMax])->get();
            }
            else {
                $companies = User::Role('company')->get();
            }
            // $companies = $companies->reject(function (User $user){
            //     return !array_search('company', $user->roles_name);
            // });
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
        $auth = User::find(Auth::user()->id);
        if ($user->isNotBanned()) {
            $comment = $request->reason;
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
            if ($auth->hasRole('employee'))
                activity('User')->causedBy($auth)->event('block')->withProperties(['blocked_info' => $ban])->log('block user');
            return $this->apiResponse($ban, __('strings.banned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_banned'), 403);
        }
    }

    public function unBanUser($id) {
        $user = User::find($id);
        $auth = User::find(Auth::user()->id);
        if ($user->isBanned()) {
            $user->syncRoles($user->roles_name);
            $user->unBan();
            if ($auth->hasRole('employee'))
                activity('User')->causedBy($auth)->event('unblock')->withProperties(['unblocked_info' => $user])->log('unblock user');
            return $this->apiResponse(null, __('strings.unbanned_successfully'), 200);
        } else {
            return $this->apiResponse(null, __('strings.user_already_not_banned'), 403);
        }
    }

    public function isBan() {
        $user = User::where('id', Auth::user()->id)->first();
        return $user->isBanned();
    }

    public function getBans($type, Request $request) {
        $bans = [];
        if ($type === 'all') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->get());
            }
        } else if ($type === 'expired') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->where('deleted_at', '!=', null)->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->where('deleted_at', '!=', null)->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->where('deleted_at', '!=', null)->get());
            }
        } else if ($type === 'active') {
            if ($request->startDate && $request->endDate) {
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $request->endDate])->where('deleted_at', null)->get());
            }
            else if ($request->startDate && !$request->endDate) {
                $dateMax = DB::table('bans')->max('created_at');
                $bans = BansResource::collection(DB::table('bans')->whereBetween('created_at', [$request->startDate, $dateMax])->where('deleted_at', null)->get());
            }
            else {
                $bans = BansResource::collection(DB::table('bans')->where('deleted_at', null)->get());
            }
        } else {
            return $this->apiResponse(null, 'Error type', 400);
        }
        return $this->apiResponse($bans, __('strings.all_users_banned'), 200);
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
        return LogsResource::collection($logs);
    }

    public function getLogsEmployees() {
        $employees = Employee::all()->pluck('user_id')->toArray();
        $logs = Activity::whereIn('causer_id', $employees)->get();
        return LogsResource::collection($logs);
    }

    public function lineChartByDay() {
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

    public function lineChartByWeek()
    {
        $minDate = Activity::min('created_at');
        $maxDate = Activity::max('created_at');

        if (!$minDate || !$maxDate) {
            return response()->json(['message' => 'No data available'], 404);
        }

        $startDate = Carbon::parse($minDate)->startOfWeek();
        $endDate = Carbon::parse($maxDate)->endOfWeek();

        $weeklyData = [];

        while ($startDate->lte($endDate)) {
            $weekStart = $startDate->copy()->startOfWeek()->toDateTimeString();
            $weekEnd = $startDate->copy()->endOfWeek()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $weekNumber = $startDate->weekOfYear;
            $weeklyData[] = [
                'week' => $weekNumber,
                'count' => $count,
                'amt' => 2000
            ];

            $startDate->addWeek();
        }

        return response()->json($weeklyData);
    }
    public function lineChartByMonth()
    {
        $minDate = Activity::min('created_at');
        $maxDate = Activity::max('created_at');

        if (!$minDate || !$maxDate) {
            return response()->json(['message' => 'No data available'], 404);
        }

        $startDate = Carbon::parse($minDate)->startOfMonth(); // Start from the beginning of the month
        $endDate = Carbon::parse($maxDate)->endOfMonth(); // End at the end of the month

        $monthlyData = [];

        while ($startDate->lte($endDate)) {
            $monthStart = $startDate->copy()->startOfMonth()->toDateTimeString();
            $monthEnd = $startDate->copy()->endOfMonth()->toDateTimeString();

            $count = Activity::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthName = $startDate->copy()->format('F'); // Get the full month name
            $monthlyData[] = [
                'month' => $monthName,
                'count' => $count,
                'amt' => 2000
            ];

            $startDate->addMonth(); // Move to the next month
        }

        return response()->json($monthlyData);
    }
}
