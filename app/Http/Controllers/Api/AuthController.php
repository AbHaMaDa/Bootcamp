<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

/**
 * @OA\Post(
 *     path="/register",
 *     summary="Register a new user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name", "email", "password"},
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                 @OA\Property(property="phone", type="string", example="01099634597"),
 *                 @OA\Property(property="password", type="string", format="password", example="password"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
 *                 @OA\Property(property="address", type="string", example="123 Main Street"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", ref="#/components/schemas/User"),
 *             @OA\Property(property="access_token", type="string", example="17|e6n7vBgUMKDFUzM5NAZoPE8QkJsp0G4K31DDoS40185d2895"),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal server error")
 *         )
 *     )
 * )
 */
    public function register(Request $request)
    {
        $data = $request->validate( [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15|unique:users',
            'address' => 'nullable|string|max:255',
        ]);

        $user = User::create($data);

        $token = $user->createToken($request->name);

        return response()->json([
            "user"=>$user,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ],Response::HTTP_CREATED);
    }


    /**
 * @OA\Post(
 *     path="/login",
 *     summary="User login",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="17|e6n7vBgUMKDFUzM5NAZoPE8QkJsp0G4K31DDoS40185d2895"),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Your credentials don't match our records")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal server error")
 *         )
 *     )
 * )
 */

    public function login(Request $request){
        $request->validate([
            'email'=>"required|email|max:100",
            'password'=>"required|max:100",
        ]);


        $user = User::where("email",$request->email)->first();

        if(!$user ||  !Hash::check($request->password,$user->password)){

            return response()->json([
                "mesage" => "your credintial doesn't match our records"
            ],Response::HTTP_UNAUTHORIZED);

        }

        $token = $user->createToken($user->name);

        return response()->json([
            'user'=>$user,
            "token" =>$token->plainTextToken,
            'token_type' => 'Bearer'
        ],Response::HTTP_OK);

    }

    /**
     * @OA\Delete(
     *     path="/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Auth"},
     *     security={{"BearerToken":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function logout(Request $request){

        $request->user()->tokens()->delete();

        return response()->json([
            "massage"=>"you are logged out"
        ],Response::HTTP_OK);

    }


}
