<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthPagesController extends Controller
{
    public function __construct() {
        $urlPrevious = url()->previous();
        $urlBase = url()->to('/');

        if(($urlPrevious != $urlBase) && (substr($urlPrevious, 0, strlen($urlBase)) === $urlBase)) {
            session()->put('url.intended', $urlPrevious);
        }
    }

    public function getAdminLogin() {
        Auth::guard('driver')->logout();
        if(Auth::guard('admin')->check())
        {
            return redirect()->route('get:admin:dashboard');
        }
        return view('admin.auth.super_admin.login');
    }

}
