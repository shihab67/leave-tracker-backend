<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\LeaveRequestMail;

class LeaveRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator                  = Validator::make($request->all(), [
            'leave_type'         => 'required|exists:leave_types,id',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'reason'                => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        try {
            $leaveRequest = new LeaveRequest();
            $leaveRequest->user_id = $request->user()->id;
            $leaveRequest->leave_type_id = $request->leave_type;
            $leaveRequest->start_date = date('Y-m-d', strtotime($request->start_date));
            $leaveRequest->end_date = date('Y-m-d', strtotime($request->end_date));
            $leaveRequest->reason = $request->reason;
            $leaveRequest->status = 'pending';
            $leaveRequest->save();

            //send mail starts
            $details = [
                'body' => 'Leave request created successfully. Wait for approval.',
                'reason' => $leaveRequest->reason,
                'type' => $leaveRequest->leaveType->name,
                'start_date' => date('d M Y', strtotime($leaveRequest->start_date)),
                'end_date' => date('d M Y', strtotime($leaveRequest->end_date)),
                'status' => ucfirst($leaveRequest->status),
            ];

            Mail::to($request->user()->email)->send(new LeaveRequestMail($details));
            //send mail ends

            return response(
                [
                    'status' => 'success',
                    'message' => 'Leave request created successfully'
                ],
                201
            );
        } catch (\Throwable $th) {
            return response(['errors' => $th->getMessage()], 422);
        }
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

        //send mail starts
        $body = "Your leave request have been " . ($leaveRequest->status == 'approved' ? "approved" : "rejected") . " by "  . $request->user()->name . ".";
        $details = [
            'body' => $body,
            'reason' => $leaveRequest->reason,
            'type' => $leaveRequest->leaveType->name,
            'start_date' => date('d M Y', strtotime($leaveRequest->start_date)),
            'end_date' => date('d M Y', strtotime($leaveRequest->end_date)),
            'status' => ucfirst($leaveRequest->status),
        ];

        Mail::to($request->user()->email)->send(new LeaveRequestMail($details));
        //send mail ends

        return response(['message' => 'Leave request updated successfully'], 200);
    }

    public function all(Request $request)
    {
        $user = $request->user();
        $leaves = LeaveRequest::when(isset($user) && isset($user->role) && $user->role->id == 3, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
            ->with('leaveType', 'user', 'approvedBy')
            ->orderBy('id', 'DESC')
            ->get();

        return response(['data' => $leaves], 200);
    }
}
