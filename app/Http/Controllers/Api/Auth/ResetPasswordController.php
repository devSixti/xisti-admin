<?php

namespace App\Http\Controllers\Api\Auth;

use App\Classes\UserClassApi;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function __construct(private UserClassApi $userClassApi)
    {
    }

    public function postCustomerChangePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'access_token' => 'required',
            'old_password' => 'required',
            'new_password' => 'required|min:6|max:18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'message_code' => 9,
            ]);
        }

        $authorization = $this->userClassApi->checkUserAllow(
            $request->get('user_id'),
            $request->get('access_token')
        );

        if ($authorization instanceof JsonResponse) {
            return $authorization;
        }

        $user = User::query()
            ->where('id', $authorization->id)
            ->whereNull('deleted_at')
            ->first();

        if ($user === null || ! Hash::check($request->get('old_password'), (string) $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.11'),
                'message_code' => 11,
            ]);
        }

        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        return response()->json([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
        ]);
    }
}
