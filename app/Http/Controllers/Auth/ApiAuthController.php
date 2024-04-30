<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'name'      => 'required|string',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'role_id'   => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        try {
            $user                       = new User();
            $user->name                 = $request->name;
            $user->email                = $request->email;
            $user->password             = Hash::make($request->password);
            $user->remember_token       = Str::random(10);
            $user->role_id              = $request->role_id;
            $user->save();

            $token                      = $user->createToken('Laravel Password Grant Client')->accessToken;
            $response                   = ['token' => $token];

            return response($response, 200);
        } catch (\Throwable $th) {
            return response(['errors' => $th->getMessage()], 422);
        }
    }

    public function login(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::where('email', $request->email)
            ->with('role')
            ->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token      = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['user' => $user, 'token' => $token];

                return response($response, 200);
            } else {
                $response   = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response       = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }
}
