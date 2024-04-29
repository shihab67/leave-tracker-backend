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
            'message'   => 'User status updated successfully',
        ], 200);
    }
}
