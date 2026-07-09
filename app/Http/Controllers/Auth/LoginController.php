<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailLoginRequest;
use App\Models\Admin;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class LoginController extends Controller
{

    //use AuthenticatesUsers;

    //protected $redirectTo = '/home';

    public function __construct() {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
        $this->middleware('guest:driver')->except('logout');
    }


    //post admin login
    public function postSuperAdminLogin(EmailLoginRequest $request){
        Auth::guard('admin')->logout();
        Auth::guard('driver')->logout();
        $roles = $request->get('roles');
        $super_admin = Admin::where('email', $request->get('email'))->first();

        if ($super_admin != Null) {
            if ($super_admin->roles == $roles || $super_admin->roles == "4") {
                if (Hash::check($request->get('password'), $super_admin->password)) {
                    if ($super_admin->roles == 1 || $super_admin->roles == 2 || $super_admin->roles == 3 || $super_admin->roles == 4) {
                        if ($super_admin->roles == 1 || $super_admin->roles == 4) {
                            Auth::logout();

                            if (Auth::guard('admin')->attempt(['email' => $request->get('email'), 'password' => $request->get('password')], $request->get('remember'))) {
                                $admin = Auth::guard('admin')->user();

                                return redirect()->intended(route('get:admin:dashboard'))->with('success', $admin->loginSuccessMessage());
                            } else {
                                Auth::logout();
                                return redirect()->back()->with("error", "Your email and password was wrong. Please enter right credential.");
                            }
                        }  else {
                            return redirect()->back()->with("error", "Your email and password was wrong. Please enter right credential.");
                        }
                    } else {
                        return redirect()->back()->with("error", "Your email and password was wrong. Please enter right credential.");
                    }
                } else {
                    Auth::logout();
                    return redirect()->back()->with("error", "Your email and password was wrong. Please enter right credential.");
                }
            } else {
                return redirect()->back()->with("error", "Only Administration are Allowed.");
            }
        } else {
            return redirect()->back()->with("error", "Your email and password was wrong. Please enter right credential.");
        }
    }

        //post logout
    public function logout(Request $request, $guard) {
        if ($guard == 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('get:admin:login')->with("success", "Admin Logout Successfully.");
        } elseif ($guard == "user") {
            request()->session()->forget('login_previous_url');
            Auth::guard('user')->logout();
            return redirect()->route('get:homepage')->with('success', "User Logout Successfully.");
        } else {
            Auth::logout();
            return redirect()->route('get:homepage')->with("success", "User Logout Successfully.");
        }
    }

    //social Login
    public function socialAuthLogin(Request $request, $guards, $provider) {
        try {
            if($guards == "user") {
                $previous_url = url()->previous();
                session()->put('login_previous_url', $previous_url);
            }
            return Socialite::driver($provider)->with(['state' => $guards])->redirect();
        } catch (\Exception $e) {
            Auth::logout();
            request()->session()->forget('login_previous_url');
            return redirect()->back();
        }
    }

    //social Login callback function
    public function handleCallback(Request $request, $provider) {
        $guards = $request->get("state");
        if($guards == "user") {
            return $this->handleUserCallback($provider);
        } else {
            request()->session()->forget('login_previous_url');
            return redirect()->route("get:homepage");
        }
    }

    public function handleUserCallback($provider)
    {
        $url = request()->session()->get("login_previous_url");
        request()->session()->forget('login_previous_url');
        try {
            try {
                try {
                    $user = Socialite::driver($provider)->user();
                } catch (InvalidStateException $e) {
                    $user = Socialite::driver($provider)->stateless()->user();
                }
            } catch (\Exception $e) {
                Session::flash('error', 'Something went to wrong!');
                return redirect($url);
            }

            $login_type = $provider;
            $login_id = $user->id;
            $contact_number = $user->email . "";
            $full_name = $user->name . "";
            $profile_image = "";
            $gender = "";

            $user_details = User::query()->where('login_type','=', $login_type)->where('login_id', '=', $login_id)->whereNull('deleted_at')->first();
            if ($user_details == Null) {
                $user_details = new User();
                $user_details->status = 1;
                $user_details->is_register = 1;
                $user_details->login_type = $login_type;
                $user_details->login_id = $login_id;
                $user_details->verified_at = date('Y-m-d H:i:s');
                $user_details->language = "en";

                if(trim($full_name) != Null) {
                    $user_details->first_name = ucwords(strtolower(trim($full_name)));
                }
                if ($contact_number != Null){
                    if (is_numeric($contact_number)) {
                        $user_details->contact_number = $contact_number;
                    } else {
                        $user_details->email = $contact_number;
                    }
                }
                $user_details->save();
                if ($user_details->first_name != Null){
                    $user_details->InviteCode($user_details->id, $user_details->first_name);
                }
                $user_details->save();
            } else {
                if ($user_details->status == 0) {
                    Session::flash('error', 'Your account is currently blocked, so not authorised to allow any activity!');
                    return redirect($url);
                }
            }

            Auth::guard('user')->login($user_details, true);
            if (Auth::guard("user")->check()) {
                Session::flash("success", "User Login Successfully.");
                return redirect($url);
            }
            else {
                Auth::logout();
                Session::flash('error', 'Something went to wrong!');
                return redirect($url);
            }
        }
        catch (\Exception $e) {
            Session::flash('error', 'Something went to wrong!');
            return redirect($url);
        }
    }
}
