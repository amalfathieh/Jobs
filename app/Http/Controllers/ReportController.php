<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Reason;
use App\Models\Report;
use App\Models\User;
use App\Traits\responseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ReportController extends Controller
{
    use responseTrait;
    public function reportUser($id, Request $request) {
        $validate = Validator::make($request->all(), [
            'reason_id' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }
        $created_by = User::where('id', Auth::user()->id)->first();
        $reason_id = $request->reason_id;
        if ($reason_id == 2 && !$request->who) {
            return $this->apiResponse(null, 'You should tell as who? ', 400);
        }
        if ($reason_id == 6 && !$request->another_reason) {
            return $this->apiResponse(null, 'Hey, What is the reason? ', 400);
        }
        if ($id == $created_by->id) {
            return $this->apiResponse(null, 'Hey, You can\'t report yourself', 400);
        }
        if ($id == 1) {
            return $this->apiResponse(null, 'Hey, Are you crazy? this is the admin', 400);
        }

        $re = Report::where('created_by', $created_by->id)->where('user_id', $id)->where('reason_id', $reason_id)->first();
        if ($re) {
            return $this->apiResponse(null, 'You already reported this user', 400);
        }

        $report = Report::create([
            'created_by' => $created_by->id,
            'user_id' => $id,
            'reason_id' => $reason_id,
            'another_reason' => $reason_id == 6 ?  $request->another_reason : null,
            'notes' => $reason_id == 2 ? "Pretending to be " . $request->who : null
        ]);

        if ($report) {
            return $this->apiResponse($report, 'Created successfully', 201);
        }
        return $this->apiResponse(null, 'There is an error, please talk to the developer', 500);
    }

    public function reportPost($id, Request $request) {
        $validate = Validator::make($request->all(), [
            'reason_id' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }
        $created_by = User::where('id', Auth::user()->id)->first();
        $reason_id = $request->reason_id;

        if ($reason_id != 4 && $reason_id != 5 && $reason_id != 6) {
            return $this->apiResponse(null, 'To report user call api/report/reportUser', 404);
        }
        if ($reason_id == 6 && !$request->another_reason) {
            return $this->apiResponse(null, 'Hey, What is the reason? ', 400);
        }

        $post = Post::where('id', $id)->first();
        if (!$post) {
            return $this->apiResponse(null, __('strings.not_found'), 404);
        }
        $user = User::find($post->seeker->user_id);
        if ($user->id == $created_by->id) {
            return $this->apiResponse(null, 'Hey, You can\'t report yourself', 400);
        }

        $re = Report::where('created_by', $created_by->id)->where('user_id', $user->id)->where('reason_id', $reason_id)->first();
        if ($re) {
            return $this->apiResponse(null, 'You already reported this post', 400);
        }

        $report = Report::create([
            'created_by' => $created_by->id,
            'user_id' => $user->id,
            'reason_id' => $reason_id,
            'another_reason' => $reason_id == 6 ?  $request->another_reason : null,
            'notes' => 'Post\'s id is ' . $id
        ]);
        if ($report) {
            return $this->apiResponse($report, 'Created successfully', 201);
        }
        return $this->apiResponse(null, 'There is an error, please talk to the developer', 500);
    }

    public function reportOpportunity($id, Request $request) {
        $validate = Validator::make($request->all(), [
            'reason_id' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }
        $created_by = User::where('id', Auth::user()->id)->first();
        $reason_id = $request->reason_id;

        if ($reason_id != 4 && $reason_id != 5 && $reason_id != 6) {
            return $this->apiResponse(null, 'To report user call api/report/reportUser', 404);
        }
        if ($reason_id == 6 && !$request->another_reason) {
            return $this->apiResponse(null, 'Hey, What is the reason? ', 400);
        }

        $opp = Opportunity::where('id', $id)->first();
        if (!$opp) {
            return $this->apiResponse(null, __('strings.not_found'), 404);
        }
        $user = User::find($opp->company->user_id);
        if ($user->id == $created_by->id) {
            return $this->apiResponse(null, 'Hey, You can\'t report yourself', 400);
        }

        $re = Report::where('created_by', $created_by->id)->where('user_id', $user->id)->where('reason_id', $reason_id)->first();
        if ($re) {
            return $this->apiResponse(null, 'You already reported this opportunity', 400);
        }

        $report = Report::create([
            'created_by' => $created_by->id,
            'user_id' => $user->id,
            'reason_id' => $reason_id,
            'another_reason' => $reason_id == 6 ?  $request->another_reason : null,
            'notes' => 'Opportunity\'s id is ' . $id
        ]);
        if ($report) {
            return $this->apiResponse($report, 'Created successfully', 201);
        }
        return $this->apiResponse(null, 'There is an error, please talk to the developer', 500);

    }

    public function getReports() {
        $reports = ReportResource::collection(Report::all());
        return $this->apiResponse($reports, 'These are all reports', 200);
    }
}
