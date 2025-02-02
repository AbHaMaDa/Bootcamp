<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * @OA\Post(
     *     path="/cart/add",
     *     summary="Add a product to cart",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product added to cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product added to cart"),
     *             @OA\Property(property="cart", type="object")
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
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);
        $cartItem->quantity += $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Product added to cart', 'cart' => $cartItem]);
    }

    /**
     * @OA\Put(
     *     path="/cart/update/{id}",
     *     summary="Update cart item quantity",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Cart item ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cart updated"),
     *             @OA\Property(property="cart", type="object")
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
    public function updateCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Cart updated', 'cart' => $cartItem]);
    }

    /**
     * @OA\Delete(
     *     path="/cart/remove/{id}",
     *     summary="Remove an item from the cart",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Cart item ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product removed from cart")
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
    public function removeFromCart($id)
    {
        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();
        return response()->json(['message' => 'Product removed from cart']);
    }

    /**
     * @OA\Get(
     *     path="/cart",
     *     summary="Get all items in the cart",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart items retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="cart", type="array", @OA\Items(type="object"))
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
    public function getCart()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product')->get();
        return response()->json(['cart' => $cart]);
    }

    /**
     * @OA\Delete(
     *     path="/cart/clear",
     *     summary="Clear the entire cart",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cart cleared")
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
    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Cart cleared']);
    }

    /**
     * @OA\Post(
     *     path="/cart/checkout",
     *     summary="Checkout the cart",
     *     tags={"Cart"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Checkout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Checkout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cart is empty",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Cart is empty")
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
    public function checkout()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Payment processing logic (e.g., Stripe, PayPal)

        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Checkout successful']);
    }
}
