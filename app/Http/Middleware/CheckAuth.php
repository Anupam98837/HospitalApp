<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Example use:
 *   Route::middleware('checkAuth:admin,doctor,user')->get(...);
 *
 * The middleware looks up a bearer token in personal_access_tokens.
 * It now accepts EITHER the 80-character plaintext tokens (admin / doctor)
 * OR the 64-character SHA-256 hashes (user or manually-hashed tokens).
 */
class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  string[] ...$roles  Allowed roles.  Empty list = accept any role.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /* 1️⃣  Extract token (Bearer preferred, two fall-backs) */
        $raw = $request->bearerToken()
              ?: $request->header('token')
              ?: $request->header('Authorization');

        if (!$raw) {
            return response()->json(['error' => 'Authorization token required'], 401);
        }

        // If someone sent "Bearer xxx" in Authorization header, strip the prefix
        if (str_starts_with($raw, 'Bearer ')) {
            $raw = substr($raw, 7);
        }

        /* 2️⃣  Canonicalise
         *     – 64-char hex  ➜ already hashed, use as-is
         *     – anything else ➜ hash once with SHA-256
         */
        $hashed = (strlen($raw) === 64 && ctype_xdigit($raw))
                  ? $raw
                  : hash('sha256', $raw);

        /* 3️⃣  Look up token */
        $query = DB::table('personal_access_tokens')->where('token', $hashed);

        if ($roles) {
            $query->whereIn('tokenable_type', $roles);
        }

        $tokenRow = $query->first();

        if (!$tokenRow) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        /* 4️⃣  Expose identity to downstream code (optional) */
        $request->attributes->set('auth_id',   $tokenRow->tokenable_id);
        $request->attributes->set('auth_role', $tokenRow->tokenable_type);

        return $next($request);
    }
}
