<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateStatus(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'status'        => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user               = User::find($request->user_id);
        $user->status       = $request->status;
        $user->save();

        return response([
            'status' => 'success',
            'message'   => 'User status updated successfully',
        ], 200);
    }

    public function getAllUsers()
    {
        $users = User::orderBy('id', 'DESC')->with('role')->get();
        return response(['data' => $users], 200);
    }

    public function getUser(Request $request)
    {
        $user = User::where('id', $request->id)
            ->with('role', 'approvedLeaves', 'pendingLeaves', 'rejectedLeaves', 'allLeaves')
            ->first();
        return response(['status' => 'success', 'data' => $user], 200);
    }
}
