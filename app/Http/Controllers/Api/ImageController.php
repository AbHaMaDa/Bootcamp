<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ImageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/avatar",
     *     summary="Upload user avatar",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"avatar"},
     *                 @OA\Property(property="avatar", type="string", format="binary", description="User avatar image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Image uploaded successfully."),
     *             @OA\Property(property="user", type="object", description="User data with updated avatar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The avatar field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store image
        $imageName = time() . '.' . $request->file('avatar')->extension();
        $path = $request->file('avatar')->storeAs('avatars', $imageName, 'public');

        // Get authenticated user
        $user = Auth::user();

        // Update avatar path
        $user->avatar = '/storage/avatars/' . $imageName;
        $user->save();

        return response()->json([
            'success' => 'Image uploaded successfully.',
            'user' => $user
        ]);
    }
}
