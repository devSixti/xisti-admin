<?php

namespace App\Http\Middleware;

use App\Classes\UserClassApi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates user_id + access_token on every protected mobile API call.
 */
class EnsureMobileApiUser
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

        $check = app(UserClassApi::class)->checkUserAllow($userId, $accessToken);
        if ($check instanceof \Illuminate\Http\JsonResponse) {
            return $check;
        }

        $request->attributes->set('mobile_api_user', $check);

        return $next($request);
    }
}
