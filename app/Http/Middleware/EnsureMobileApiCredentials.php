<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates user_id + access_token without requiring verified_at (OTP / signup flows).
 */
class EnsureMobileApiCredentials
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->get('user_id');
        $accessToken = $request->get('access_token');

        if ($userId === null || $userId === '' || $accessToken === null || $accessToken === '') {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ], 401);
        }

        $user = User::query()
            ->select('id', 'access_token', 'status', 'verified_at')
            ->where('id', $userId)
            ->whereNull('deleted_at')
            ->first();

        if ($user === null) {
            return response()->json([
                'status' => 5,
                'message' => __('user_messages.5'),
                'message_code' => 5,
            ]);
        }

        if ((int) $user->status === 0) {
            return response()->json([
                'status' => 3,
                'message' => __('user_messages.3'),
                'message_code' => 3,
            ]);
        }

        $storedToken = (string) ($user->access_token ?? '');
        $providedToken = (string) $accessToken;
        if ($storedToken === '' || ! hash_equals($storedToken, $providedToken)) {
            return response()->json([
                'status' => 4,
                'message' => __('user_messages.4'),
                'message_code' => 4,
            ]);
        }

        $request->attributes->set('mobile_api_user', $user);

        return $next($request);
    }
}
