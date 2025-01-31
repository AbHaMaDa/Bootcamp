<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="HTTP Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="Bearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     name="Authorization",
 *     in="header",
 * )
 *
 * @OA\Info(
 *     title="GDG Store API",
 *     version="1.0.0",
 *     description="API documentation for GDG Store API",
 *     @OA\Contact(
 *         name="Abdallah Hamada",
 *         email="AbdallahHamadar@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Developed by Osama Gasser",
 *         url="https://example.com"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="phone", type="string", example="099 2899 634 34"),
 *     @OA\Property(property="address", type="string", example="address of user"),
 *     @OA\Property(property="avatar", type="string", example="avatars/avatar.jpg"),
 * )
 * 
 */
class SwagerController extends Controller
{
    public function example()
    {
        return response()->json(['message' => 'Hello, world!']);
    }
}
