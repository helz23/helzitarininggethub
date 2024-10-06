<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
//            UserGroup::create([
//                         'group_id' => 8,
//                         'user_id' =>$user->id,
//        ]);
        return response()->json([
            'message' => 'User successfully registered',
        ], 201);


    }

    public function userLogin(LoginRequest $request)
    {


        $info = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::guard('users')->attempt($info)) {
            $user = User::where('email', $request->email)->first();
            $token = JWTAuth::fromUser($user);

            $data = $user;
            $data['token'] = $token;

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('messages.password_error'),
                'data' => []
            ], 401);
        }
    }


}




