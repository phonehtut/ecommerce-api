<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();

            if (count($categories) == 0) {
                return response()->json([
                    'message' => 'No data found',
                ]);
            } else {
                return response()->json([
                    'categories' => $categories,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'max:300',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Category Could not created',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function detail(int $id)
    {
        $category = Category::with('products')->find($id);

        if (is_null($category)) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        } else {
            return response()->json([
                'category' => $category,
            ], 200);
        }
    }

    public function update(Request $request, int $id)
    {
        $category = Category::find($id);

        if ($category) {
            $validator = Validator::make($request->all(), [
                'name' => 'max:30',
                'description' => 'max:300',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            try {

                $category->update([
                    'name' => $request->name ?? $category->name,
                    'description' => $request->description ?? $category->description,
                ]);

                return response()->json([
                    'message' => 'Category updated successfully',
                    'category' => $category,
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'error' => 'Category Could not update',
                    'message' => $th->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        $category = Category::find($id);

        if ($category) {

            try {
                $category->delete();

                return response()->json([
                    'message' => 'Category deleted successfully',
                    'category' => $category,
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'error' => 'Category Could not delete',
                    'message' => $th->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }
    }
}
