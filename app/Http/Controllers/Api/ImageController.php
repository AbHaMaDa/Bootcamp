<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ImageController extends Controller
{
    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Validate and store image
        $imageName = time() . '.' . $request->file('avatar')->extension();
        $path = $request->file('avatar')->storeAs('avatars', $imageName, 'public'); // Store in storage/app/avatars

        // Get authenticated user
        $user = Auth::user();

        // Update user's avatar in the database
        $user->avatar = '/storage/avatars/' . $imageName;

    $user->save();
        return response()->json(['success' => 'Image uploaded successfully.',
    "user"=>$user]);
    }



}
