<?php

namespace App\Http\Controllers\Api\Loan;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Item;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * GET - List loan milik user login
     */
    public function index()
    {
        $loans = Loan::with('loanItems')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return LoanResource::collection($loans);
    }

    /**
     * POST - Buat loan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_date' => 'required|date',
            'note' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat loan
            $loan = Loan::create([
                'user_id' => Auth::id(),
                'loan_date' => $validated['loan_date'],
                'status' => 'menunggu',
                'note' => $validated['note'] ?? null,
            ]);

            // 2. Loop item
            foreach ($validated['items'] as $itemData) {
                // ambil item + lock
                $item = Item::where('id', $itemData['item_id'])
                    ->lockForUpdate()
                    ->first();

                // 3. Cek stok
                if ($item->stock < $itemData['qty']) {
                    throw new \Exception(
                        "Stok {$item->name} tidak mencukupi"
                    );
                }

                // 4. Simpan loan item
                LoanItem::create([
                    'loan_id' => $loan->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['qty'],
                ]);

                // 5. Kurangi stok
                $item->decrement('stock', $itemData['qty']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Loan berhasil dibuat',
                'data' => $loan->load('loanItems.item'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET - Detail loan
     */
    public function show(Loan $loan)
    {
        $loan->load([
            'user',
            'loanItems.item', // WAJIB
        ]);

        return new LoanResource($loan);
    }

    /**
     * PUT - Update status / catatan loan
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:dipinjam,dikembalikan,terlambat,menunggu',
            'return_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $loan->update($validated);

        return response()->json([
            'message' => 'Loan berhasil diperbarui',
            'data' => $loan,
        ]);
    }

    /**
     * DELETE - Hapus loan
     */
    public function destroy(Loan $loan)
    {
        $loan->loanItems()->delete();
        $loan->delete();

        return response()->json([
            'message' => 'Loan berhasil dihapus',
            'data' => $loan,
        ]);
    }
}
