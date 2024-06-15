<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
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

    public function getReports() {
        $reports = ReportResource::collection(Report::all());
        return $this->apiResponse($reports, 'These are all reports', 200);
    }
}
