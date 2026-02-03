<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'divisi' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'divisi' => $validated['divisi'],
            'password' => Hash::make($validated['password']),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $html = "
        <h3>Verifikasi Email</h3>
        <p>Halo {$user->name},</p>
        <p>Silakan klik tombol di bawah untuk memverifikasi email Anda:</p>
        <a href='{$verificationUrl}'
           style='display:inline-block;padding:10px 16px;
           background:#2563eb;color:#fff;text-decoration:none;
           border-radius:6px'>
           Verifikasi Email
        </a>
        <p>Link berlaku 60 menit.</p>
        ";

        // Kirim email verifikasi
        BrevoMailService::send(
            $user->email,
            'Test Register',
            $html,
        );

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $user,
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * USER PROFILE (optional helper)
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return response()->json([
                'message' => 'Token tidak ditemukan',
            ], 400);
        }

        if (! Hash::check($request->token, $record->token)) {
            return response()->json([
                'message' => 'Token tidak valid',
            ], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Password berhasil direset',
        ]);
    }

    public function forgotPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = config('app.frontend_url').
            "/reset-password?email={$request->email}&token={$token}";

        $html = "
        <p>Anda menerima email ini karena ada permintaan reset password.</p>
        <p>
            <a href='{$resetUrl}'
               style='display:inline-block;padding:10px 16px;
               background:#2563eb;color:#fff;text-decoration:none;border-radius:4px'>
               Reset Password
            </a>
        </p>
        <p>Link ini berlaku selama 60 menit.</p>
    ";

        $response = BrevoMailService::send(
            $request->email,
            'Reset Password',
            $html
        );

        if ($response->failed()) {
            \Log::error('Brevo error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return response()->json([
                'message' => 'Gagal mengirim email reset password',
            ], 500);
        }

        return response()->json([
            'message' => 'Link reset password telah dikirim ke email',
        ]);
    }
}
