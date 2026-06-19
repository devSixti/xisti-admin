<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LogoutController extends Controller
{

    public function postCustomerLogout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "access_token" => "nullable|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()->first(),
                "message_code" => 9,
            ]);
        }

        $user = User::where('id', $request->get('user_id'))->first();
        if ($user != Null) {
            $user->access_token = Null;
            $user->device_token = Null;
            $user->driver_current_status = 0;
            $user->save();
        }
        return response()->json([
            "status" => 1,
            'message' => __('user_messages.1'),
            "message_code" => 1,
        ]);
    }
}
