<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        $all_leaves = LeaveRequest::when(
            isset($user) && isset($user->role) && $user->role->id == 3,
            function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }
        )
            ->count();
        $pending_leaves = LeaveRequest::where('status', 'pending')->when(isset($user) &&
            isset($user->role) && $user->role->id == 3, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
            ->count();
        $approved_leaves = LeaveRequest::where('status', 'approved')->when(isset($user)
            && isset($user->role) && $user->role->id == 3, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
            ->count();
        $rejected_leaves = LeaveRequest::where('status', 'rejected')->when(isset($user)
            && isset($user->role) && $user->role->id == 3, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
            ->count();

        $data = [
            'all_leaves' => $all_leaves,
            'pending_leaves' => $pending_leaves,
            'approved_leaves' => $approved_leaves,
            'rejected_leaves' => $rejected_leaves
        ];

        return response(['status' => 'success', 'data' => $data], 200);
    }
}
