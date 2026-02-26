<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    /** Database table that stores users */
    private const TABLE = 'user';

    /** Token roles this controller will accept */
    private const TOKEN_TYPES = ['user', 'doctor', 'admin'];

    /* ─────────────────────────  REGISTER  ───────────────────────── */
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:user,email',
            'phone'    => 'required|digits:10',
            'password' => 'required|string|min:8',
            'address'  => 'sometimes|nullable|string',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        try {
            $id = DB::table(self::TABLE)->insertGetId([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => Hash::make($request->password),
                'address'    => $request->address,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $hashedToken = $this->generateHashedToken($id);   // 64-char hash sent to client

            return response()->json([
                'success'        => true,
                'message'        => 'Registration successful',
                'access_token'   => $hashedToken,
                'token_type'     => 'Bearer',
                'tokenable_type' => 'user',
                'user_id'        => $id,
            ], 201);
        } catch (Throwable $e) {
            Log::error('User register error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Registration failed'], 500);
        }
    }

    /* ───────────────────────────  LOGIN  ────────────────────────── */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = DB::table(self::TABLE)->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $hashedToken = $this->generateHashedToken($user->id);

        return response()->json([
            'success'        => true,
            'message'        => 'Login successful',
            'access_token'   => $hashedToken,
            'token_type'     => 'Bearer',
            'tokenable_type' => 'user',
            'user_id'        => $user->id,
        ], 200);
    }

    /* ─────────────────────────── LOGOUT ─────────────────────────── */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
        }

        // Delete any row that matches the hashed token, regardless of role
        $deleted = DB::table('personal_access_tokens')
            ->where('token', $this->canonicalize($token))
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Logged out successfully' : 'Invalid token',
        ], $deleted ? 200 : 401);
    }

    /* ───────────────────────  GET OWN DETAILS  ──────────────────── */
    public function getUserDetails(Request $request)
    {
        $tokenRow = $this->resolveTokenRow($request);
        if (!$tokenRow) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $user = DB::table(self::TABLE)->where('id', $tokenRow->tokenable_id)->first();
        return $user
            ? response()->json(['success' => true, 'user' => $user], 200)
            : response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    /* ────────────────────────  UPDATE PROFILE  ──────────────────── */
    public function update(Request $request)
    {
        $tokenRow = $this->resolveTokenRow($request);
        if (!$tokenRow) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $id = $tokenRow->tokenable_id;

        $v = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:150',
            'email'    => "sometimes|email|unique:user,email,$id",
            'phone'    => 'sometimes|digits:10',
            'password' => 'sometimes|string|min:8',
            'address'  => 'sometimes|nullable|string',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $current = DB::table(self::TABLE)->where('id', $id)->first();
        if (!$current) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        DB::table(self::TABLE)->where('id', $id)->update([
            'name'       => $request->name     ?? $current->name,
            'email'      => $request->email    ?? $current->email,
            'phone'      => $request->phone    ?? $current->phone,
            'password'   => $request->password ? Hash::make($request->password) : $current->password,
            'address'    => $request->address  ?? $current->address,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Profile updated'], 200);
    }

    /* ──────────────────────────  DELETE  ────────────────────────── */
    public function delete(Request $request)
    {
        $tokenRow = $this->resolveTokenRow($request);
        if (!$tokenRow) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $id = $tokenRow->tokenable_id;

        DB::table('personal_access_tokens')
            ->where('tokenable_id', $id)
            ->delete();

        DB::table(self::TABLE)->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted'
        ], 200);
    }

    /* ─────────────────────────  HELPERS  ────────────────────────── */
    /** Generate and store a hashed token (64-char hex) for a user id */
    private function generateHashedToken(int $userId): string
    {
        $plain = bin2hex(random_bytes(40)); // never leaves the server
        $hash  = hash('sha256', $plain);

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'user',
            'tokenable_id'   => $userId,
            'name'           => 'auth_token',
            'token'          => $hash,
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $hash; // hashed token returned to client
    }

    /** Accepts either a 64-char SHA-256 hex string or legacy 80-char plain token */
    private function canonicalize(string $incoming): string
    {
        return strlen($incoming) === 64 && ctype_xdigit($incoming)
            ? $incoming                     // already hashed
            : hash('sha256', $incoming);    // legacy plain → hashed
    }

    /** Resolve the token row for any allowed role (user / doctor / admin) */
    private function resolveTokenRow(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        return DB::table('personal_access_tokens')
            ->whereIn('tokenable_type', self::TOKEN_TYPES)
            ->where('token', $this->canonicalize($token))
            ->first();
    }
}
