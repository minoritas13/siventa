<?php

namespace App\Http\Controllers\Api\Loan;

use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use Illuminate\Support\Facades\Auth;

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
            'items.*.item_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $loan = Loan::create([
            'user_id' => Auth::id(),
            'loan_date' => $validated['loan_date'],
            'status' => 'dipinjam',
            'note' => $validated['note'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            LoanItem::create([
                'loan_id' => $loan->id,
                'item_id' => $item['item_id'],
                'quantity' => $item['qty'],
            ]);
        }

        return response()->json([
            'message' => 'Loan berhasil dibuat',
            'data' => $loan->load('loanItems')
        ], 201);
    }

    /**
     * GET - Detail loan
     */
    public function show(Loan $loan)
    {
        return response()->json([
            'data' => $loan->load('loanItems')
        ]);
    }

    /**
     * PUT - Update status / catatan loan
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:dipinjam,dikembalikan,terlambat',
            'return_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $loan->update($validated);

        return response()->json([
            'message' => 'Loan berhasil diperbarui',
            'data' => $loan
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
            'data' => $loan
        ]);
    }
}
