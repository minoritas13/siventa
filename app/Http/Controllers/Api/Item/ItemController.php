<?php

namespace App\Http\Controllers\Api\Item;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

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
            'code' => 'required|string|unique:items,code',
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request
                ->file('photo')
                ->store('items', 'public');
        }

        $item = Item::create($validated);

        return response()->json([
            'message' => 'Item successfully created',
            'data' => new ItemResource($item),
        ], 201);
    }

    /**
     * UPDATE - Update item
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:items,code,'.$item->id,
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {

            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('items', 'public');
        }

        $item->update($validated);

        return response()->json([
            'message' => 'Item successfully updated',
            'data' => $item,
        ]);
    }

    /**
     * DELETE - Remove item
     */
    public function destroy(Item $item)
    {
        if ($item->photo) {
            Storage::disk('public/item')->delete($item->photo);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item successfully deleted',
        ]);
    }
}
