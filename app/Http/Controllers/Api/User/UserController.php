<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();

        return UserResource::collection($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|min:6',
            'role' => 'required|string',
            'divisi' => 'nullable|string',
            'phone' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('profile', 'public');
        }

        $updated = $user->update($validated);

        return response()->json([
            'updated' => $updated,
            'data' => $user->fresh(),
        ]);
    }

    /**
     * DELETE USER
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // OPTIONAL: Cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun sendiri',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
        ], 200);
    }
}
