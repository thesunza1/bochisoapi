<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public  function  login(Request $request)
    {
        $user = User::where('username', $request->username)->count();
        if ($user == 1) {
            $user = User::where('username', $request->username)->where('password', md5($request->password))->get();
            $userCount = $user->count();
            if ($userCount == 1) {
                $token = $user[0]->createToken('plaintext')->plainTextToken;
                return response()->json([
                    'token' => $token,
                    'password' => md5($request->password),
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'statuscode' => 2,
                    'password' => md5($request->password),
                    'mess' => 'Sai pass'
                ]);
            }
        } else {
            return response()->json([
                'statuscode' => 0,
                'password' => md5($request->password),
                'mess' => 'user khong ton tai',
            ]);
        }
    }

    public function index(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
