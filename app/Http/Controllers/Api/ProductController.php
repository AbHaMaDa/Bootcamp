<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Use middleware for the store, update, and destroy methods


    // Fetch all products
    public function index(){
        $products = Product::all();
        return response()->json(["products" => $products], 200);
    }

    // Store a new product
    public function store(Request $request){
        // Validate incoming data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
        ]);

        // Handle the image upload
        $imagePath = '';
        if ($request->hasFile('img')) {
            // Get the image file
            $image = $request->file('img');

            // Generate a unique name for the image
            $imageName = time() . '.' . $image->extension();

            // Move the image to the 'storage/app/public/products_images' directory
            $image->move(public_path('storage/products_images'), $imageName);

            // Store the image path
            $imagePath = '/storage/products_images/' . $imageName;
        }

        // Create the product in the database and assign the image path
        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'], // Ensure category_id is assigned
            'img' => $imagePath,  // Store the image path in the product's image column
        ]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }



    public function show(Product $product){
        return response()->json(["product" => $product], 200);

    }


    public function update(Request $request, Product $product){
        // Validate incoming data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
        ]);

        // Handle the image upload
        $imagePath = $product->img;
        if ($request->hasFile('img')) {
            // Get the image file
            $image = $request->file('img');

            // Generate a unique name for the image
            $imageName = time() . '.' . $image->extension();

            // Move the image to the 'storage/app/public/products_images' directory
            $image->move(public_path('storage/products_images'), $imageName);

            // Store the image path
            $imagePath = '/storage/products_images/' . $imageName;
        }

        // Update the product in the database and assign the image path
        $product->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'], // Ensure category_id is assigned
            'img' => $imagePath,  // Store the image path in the product's image column
        ]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }


    public function destroy(Product $product){
        $product->delete();
        return response()->json(["message" => "Product deleted successfully"], 200);
    }
}
