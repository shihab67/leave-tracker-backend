<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator                  = Validator::make($request->all(), [
            'leave_type_id'         => 'required|exists:leave_types,id',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'reason'                => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $leaveRequest = new LeaveRequest();
        $leaveRequest->user_id = $request->user()->id;
        $leaveRequest->leave_type_id = $request->leave_type_id;
        $leaveRequest->start_date = date('Y-m-d', strtotime($request->start_date));
        $leaveRequest->end_date = date('Y-m-d', strtotime($request->end_date));
        $leaveRequest->reason = $request->reason;
        $leaveRequest->save();

        return response(
            ['message' => 'Leave request created successfully'],
            201
        );
    }

    public function approve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_request_id' => 'required|exists:leave_requests,id',
            'status'           => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $leaveRequest = LeaveRequest::find($request->leave_request_id);
        $leaveRequest->status = $request->status;
        $leaveRequest->approved_by = $request->user()->id;
        $leaveRequest->remarks = isset($request->remarks) ? $request->remarks : null;
        $leaveRequest->save();

        return response(['message' => 'Leave request updated successfully'], 200);
    }
}
