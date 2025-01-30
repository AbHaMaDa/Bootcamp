<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    public function index()
    {
        $categories = Category::all();
        return response()->json(["categories" => $categories], 200);
    }

    // Store a new product
    public function store(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
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
            $image->move(public_path('storage/categories_images'), $imageName);

            // Store the image path
            $imagePath = '/storage/categories_images/' . $imageName;
        }

        // Create the product in the database and assign the image path
        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'img' => $imagePath,  // Store the image path in the category's image column
        ]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'category created successfully',
            'category' => $category
        ], 201);
    }


    public function show(Category $category)
    {
        return response()->json(["category" => $category], 200);

    }


    public function update(Request $request, Category $category)
    {
        // Validate incoming request
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Image handling if uploaded
        $imagePath = '';
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('storage/categories_images'), $imageName);
            $imagePath = '/storage/categories_images/' . $imageName;
        }

        // Update category data
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'img' => $imagePath, // If no image, this will remain empty
        ]);

        // Return updated category data with success message
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ], 200);
    }



}
