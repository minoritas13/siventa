<?php

namespace App\Http\Controllers\Api\Item;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;

class ItemController extends Controller
{
    public function index()
    {
        $item = Item::all();
        return ItemResource::collection($item);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|string|unique:items,code',
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'condition'   => 'required|string',
            'description' => 'nullable|string',
        ]);

        $item = Item::create($validated);

        return response()->json([
            'message' => 'Item successfully created',
            'data'    => $item,
        ], 201);
    }

    /**
     * UPDATE - Update item
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|string|unique:items,code,' . $item->id,
            'name'        => 'required|string|max:255',
            'photo'       => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'condition'   => 'required|string',
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Item successfully updated',
            'data'    => $item,
        ]);
    }

    /**
     * DELETE - Remove item
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json([
            'message' => 'Item successfully deleted',
        ]);
    }
}
