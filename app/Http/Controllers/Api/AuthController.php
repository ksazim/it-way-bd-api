<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $user = User::where('email', $request->email)->first();
     
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Provided Credentials are Incorrect',
                'status'  => 400
            ]);
        }

        $device_name = 'mac';

        $token = $user->createToken($device_name)->plainTextToken;
     
        return response()->json([
            'token' => $token,
            'user'  => $user,
            'message' => 'Login Successfull !',
            'status'  => 200
        ]);
    }

    public function logout() 
    {
        return response()->json(Auth::user());
    }
}
