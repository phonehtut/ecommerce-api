<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAbout;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::paginate(10);

            if ($products->isEmpty()) {
                return response()->json([
                    'message' => 'No products yet',
                ], 200);
            } else {
                return response()->json([
                    'products' => $products,
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Could not retrieve products!",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required|max:20|unique:products',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'price' => 'required',
                'title' => 'nullable',
                'description' => 'nullable|max:300',
                'stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_images', 'public');
            }

            try {
                $product = Product::create([
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image' => $imagePath,
                    'price' => $request->price,
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'error' => "Could not create product!",
                    'message' => $th->getMessage()
                ], 500);
            }

            try {
                $productAbout = ProductAbout::create([
                    'product_id' => $product->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'stock' => $request->stock,
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'error' => "Could not create product detail!",
                    'message' => $th->getMessage()
                ], 500);
            }

            return response()->json([
                'product' => $product,
                'about' => $productAbout,
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($slug)
    {
        try {
            $product = Product::where('slug', $slug)->first();

            if ($product) {
                $product->about;
            }

            if ($product) {
                return response()->json([
                    'product' => $product,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No products yet',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => "Could not retrieve product!",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request,$slug)
    {
        try {
            // Get Product form slug
            $product = Product::where('slug', $slug)->first();

            // Check product not empty
            if ($product){
                //Attach ProductAbout with foregin key
                $product->about;
            }

            if ($product) {
                try {

                    // Validation Data
                    $validator = Validator::make($request->all(), [
                        'name' => 'max:50',
                        'slug' => 'max:20|unique:products',
                        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'price' => 'decimal:2',
                        'title' => 'nullable',
                        'description' => 'nullable|max:300',
                        'stock' => 'integer|min:0',
                    ]);

                    if ($validator->fails()) {
                        return response()->json($validator->errors(), 400); // Check Validate Data
                    }

                    // Store image on local folder
                    $imagePath = null;
                    if ($request->hasFile('image')) {
                        
                        // Delete the old image from storage if it exists
                        if ($product->image) {
                            Storage::disk('public')->delete($product->image);
                        }

                        $imagePath = $request->file('image')->store('product_images', 'public');
                    }

                    // Update Product
                    $product->update([
                        'name' => $request->name ?? $product->name,
                        'slug' => $request->slug ?? $product->slug,
                        'image' => $imagePath ?? $product->image,
                        'price' => $request->price ?? $product->price
                    ]);

                    // Update Product About
                    $product->about->update([
                        'title' => $request->title ?? $product->about->title,
                        'description' => $request->description ?? $product->about->description,
                        'stock' => $request->stock ?? $product->about->stock,
                    ]);

                    return response()->json([
                        'message' => 'Product updated successfully!',
                        'product' => $product,
                    ], 200);
                } catch (\Throwable $th) {
                    // Check Update error
                    return response()->json([
                        'error' => "Could not update product!",
                        'message' => $th->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'No products yet', // Show not found product on 404 status
                ], 404);
            }
        } catch (\Exception $e){
            // Check Any Exception on update process
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($slug)
    {
        try {
            // Find product by slug
            $product = Product::where('slug', $slug)->first();

            // Check if the product exists
            if ($product) {

                // Attach ProductAbout with foreign key (if needed)
                $product->about;

                // Check if the product has an image
                if ($product->image) {
                    // Delete the image from the 'public' disk
                    Storage::disk('public')->delete($product->image);
                }

                // Delete the product
                $product->delete();

                return response()->json([
                    'message' => 'Product deleted successfully!',
                    'product' => $product,
                ], 202);
            } else {
                // Product not found
                return response()->json([
                    'message' => 'No products yet',
                ], 404);
            }
        } catch (\Throwable $th) {
            // Catch any exception
            return response()->json([
                'error' => 'Could not delete product!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
