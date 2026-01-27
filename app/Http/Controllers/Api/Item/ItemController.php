<?php

namespace App\Http\Controllers\Api\Item;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{
    public function index()
    {
        $item = Item::all();

        return ItemResource::collection($item);
    }

    public function show(Item $item)
    {

        return new ItemResource($item);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:items,code',
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'nullable|integer|min:0',
            'condition' => 'required|string',
            'description' => 'nullable|string',
            'umur_barang' => 'required|integer|min:0',
            'tanggal_perolehan' => 'required|date',
            'nilai_perolehan' => 'required',
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
            'code' => 'required|string|unique:items,code,'. $item->id,
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => 'nullable|integer|min:0',
            'condition' => 'required|string',
            'description' => 'nullable|string',
            'umur_barang' => 'required|integer|min:0',
            'tanggal_perolehan' => 'required|date',
            'nilai_perolehan' => 'required:string',
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

    public function destroy(Item $item)
    {
        DB::transaction(function () use ($item) {

            // Hapus file foto jika ada
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }

            // Ambil semua loan_id yang terkait item ini
            $loanIds = $item->loanItems()
                ->pluck('loan_id')
                ->unique();

            // Hapus loan yang terkait
            Loan::whereIn('id', $loanIds)->delete();

            // Hapus item
            $item->delete();
        });

        return response()->json([
            'message' => 'Item successfully deleted',
        ]);
    }
}
