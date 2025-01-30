<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    //add to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::firstOrNew([//firstOrNew() method is used to retrieve the first record matching the attributes or create a new one if it doesn't exist.
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);
        $cartItem->quantity += $request->quantity;//incrementing the quantity of the product in the cart
        $cartItem->save();

        return response()->json(['message' => 'Product added to cart', 'cart' => $cartItem]);
    }

     // 2. Update product quantity in cart
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


      // 3. Remove product from cart
    public function removeFromCart($id)
    {
        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();
        return response()->json(['message' => 'Product removed from cart']);
    }


         // 4. Get all cart items
    public function getCart()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product')->get();
        return response()->json(['cart' => $cart]);
    }

// 5. Clear cart
    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Cart cleared']);
    }


    // 6. Checkout
    public function checkout()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Here, you can process payment (Stripe, PayPal, etc.)
        // For now, let's just assume checkout is successful

        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Checkout successful']);
    }

}
