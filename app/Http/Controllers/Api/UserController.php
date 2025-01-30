<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{



    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }


    public function update(Request $request)
{
    $user = Auth::user();

    // Validate input data
    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);


    // Update user fields only if they are provided
    $user->update($request->only(['name', 'email', 'phone', 'address']));

    // Update password only if provided
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    // $user->save(); // This line is not needed as update() already saves the model

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ], 200);
}
}
