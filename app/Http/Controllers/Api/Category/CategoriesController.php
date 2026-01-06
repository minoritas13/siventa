<?php

namespace App\Http\Controllers\Api\Category;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;

class CategoriesController extends Controller
{
    public function index()
    {

        return CategoriesResource::collection(
            Categories::latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Categories::create($validated);

        return new CategoriesResource($category);
    }

    public function update(Request $request, Categories $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return new CategoriesResource($category);
    }

    public function destroy(Categories $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted',
            'cayegory' => new CategoriesResource($category),
        ]);
    }
}
