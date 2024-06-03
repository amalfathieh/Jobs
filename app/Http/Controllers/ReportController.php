<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Reason;
use App\Models\Report;
use App\Models\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    use responseTrait;
    public function reportUser($id, Request $request) {
        $user = User::find($id);
        $created_by = User::where('id', Auth::user()->id)->first();
        $reason_id = $request->reason_id;
        $report = Report::create([
            'created_by' => $created_by->id,
            'user_id' => $user->id,
            'reason_id' => $reason_id,
            'another_reason' => $request->another_reason,
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
