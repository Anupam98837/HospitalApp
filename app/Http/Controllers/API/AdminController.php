<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /* ========== POST /api/admin/login ========== */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = DB::table('admins')->where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            Log::warning('Admin login failed', ['email' => $request->email]);
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        /** Sanctum-style personal access token */
        $plainText = bin2hex(random_bytes(40)); // 80-char plaintext token
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'admin',
            'tokenable_id'   => $admin->id,
            'name'           => 'auth_token',
            'token'          => hash('sha256', $plainText),
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json([
            'status'         => 'success',
            'message'        => 'Login successful',
            'access_token'   => $plainText,   // send ONLY plaintext back to client
            'token_type'     => 'Bearer',
            'tokenable_type' => 'admin',
        ], 200);
    }

    /* ========== POST /api/admin/logout ========== */
    public function logout(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json(['status' => 'error', 'message' => 'Token not provided'], 401);
        }

        $deleted = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $request->bearerToken()))
            ->where('tokenable_type', 'admin')
            ->delete();

        return response()->json([
            'status'  => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Logged out successfully' : 'Invalid token',
        ], $deleted ? 200 : 401);
    }
}
