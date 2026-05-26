<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\LogStatus;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    /**
     * Generate CAPTCHA and return it to client.
     */
    public function captcha(): JsonResponse
    {
        $code = strtoupper(Str::random(5));
        $key = Str::uuid()->toString();

        Cache::put("captcha:{$key}", $code, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'data' => ['captcha_key' => $key, 'captcha_code' => $code],
        ]);
    }

    /**
     * Authenticate user and issue Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Verify CAPTCHA
        $captchaKey = $request->input('captcha_key');
        $storedCode = Cache::pull("captcha:{$captchaKey}");
        if (! $storedCode || strtoupper($validated['captcha_code']) !== strtoupper($storedCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode CAPTCHA tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $user = User::where('email', $validated['email'])->active()->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Revoke existing tokens (single-session)
        $user->tokens()->delete();

        $role = $user->roles->first()?->name ?? 'User';
        $token = $user->createToken("docutrack-{$role}")->plainTextToken;

        $this->activityLog->log(
            $user->user_id, 'LOGIN', 'authentication',
            'User', $user->user_id, 'Login berhasil',
            request: $request
        );

        // Store login log in log_statuses
        LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'LOGIN',
            'status' => 'DIBACA',
            'konten_json' => [
                'judul' => 'Login Berhasil',
                'pesan' => "User {$user->nama} ({$role}) berhasil masuk ke sistem via API.",
                'link' => '#'
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new UserResource($user->load('roles')),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->activityLog->log(
            $request->user()->user_id, 'LOGOUT', 'authentication',
            request: $request
        );

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load('roles')),
        ]);
    }
}
