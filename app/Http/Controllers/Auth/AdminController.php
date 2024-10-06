<?php

namespace App\Http\Controllers\Group\Group\Group\Auth;

use App\Http\Controllers\Group\Group\Group\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AdminController extends Controller
{


    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:admins,email'],
            'password' => ['required', 'string', 'min:8']
        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'data' => $validator->errors()
            ], 400);
        $info = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::guard('admins')->attempt($info)) {
            $user = Admin::where('email', $request->email)->first();
            $token = JWTAuth::fromUser($user);
            $data = $user;
            $data['token'] = $token;
            return response()->json([
                'status' => true,
                'message' => trans('messages.login'),
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
            ], 401);
        }
    }


}
