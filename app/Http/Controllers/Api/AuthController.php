<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $data= $request->validate( [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
        ]);

        $user = User::create($data);

        $token = $user->createToken($request->name);

        return response()->json([
            "user"=>$user,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }



    public function login(Request $request){
        $request->validate([
            'email'=>"required|email|exists:users|max:100",
            'password'=>"required|max:100",
        ]);


        $user=User::where("email",$request->email)->first();

        if(!$user ||  !Hash::check($request->password,$user->password)){

            return[
                "errors"=>[
                    "email"=>["the provided data is not correct"]
                ]
            ];

        }

        $token = $user->createToken($user->name);

        return [
            'user'=>$user,
            "token" =>$token->plainTextToken
        ];

    }


    public function logout(Request $request){

        $request->user()->tokens()->delete();

        return[
            "massage"=>"you are logged out"
        ];

    }


}
