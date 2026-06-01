<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ChangeAdminPasswordRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    //use ResetsPasswords;

    protected $redirectTo = '/home';

    public function __construct() {
        //$this->middleware('guest');
    }

    //admin change password
    public function getAdminChangePassword(Request $request) {
        return view('admin.auth.change_password');
    }

    public function postAdminChangePassword(ChangeAdminPasswordRequest $request) {
        $old_password = $request->get('old_password');
        $new_password = $request->get('new_password');

        $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();
        if ($admin != Null) {
            if (Hash::check($old_password, $admin->password)) {
                $admin->password = Hash::make($new_password);
                $admin->save();
                Session::flash('success', 'Admin password change successfully!');
                return redirect()->route('get:admin:change_password');
            }
            Session::flash('error', 'old password enter wrong!');
            return redirect()->back();
        }
        Session::flash('error', 'Admin Details Not Found!');
        return redirect()->back();
    }
}
