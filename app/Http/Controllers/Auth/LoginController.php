<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailLoginRequest;
use App\Models\Admin;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\AdminMfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class LoginController extends Controller
{
    public function __construct(private readonly AdminMfaService $mfaService)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
        $this->middleware('guest:driver')->except('logout');
    }

    public function postSuperAdminLogin(EmailLoginRequest $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('driver')->logout();
        $roles = $request->get('roles');
        $super_admin = Admin::where('email', $request->get('email'))->first();

        if ($super_admin === null) {
            AdminAuditService::log('login_failed', metadata: ['email' => $request->get('email'), 'reason' => 'unknown_email']);

            return redirect()->back()->with('error', 'Your email and password was wrong. Please enter right credential.');
        }

        if ((int) ($super_admin->status ?? 1) === 0) {
            AdminAuditService::log('login_failed', $super_admin, metadata: ['reason' => 'suspended']);

            return redirect()->back()->with('error', 'Your account is suspended. Contact another super administrator.');
        }

        if (!($super_admin->roles == $roles || $super_admin->roles == '4')) {
            return redirect()->back()->with('error', 'Only Administration are Allowed.');
        }

        if (!Hash::check($request->get('password'), $super_admin->password)) {
            AdminAuditService::log('login_failed', $super_admin, metadata: ['reason' => 'bad_password']);

            return redirect()->back()->with('error', 'Your email and password was wrong. Please enter right credential.');
        }

        if (!in_array((int) $super_admin->roles, [1, 4], true)) {
            return redirect()->back()->with('error', 'Your email and password was wrong. Please enter right credential.');
        }

        Auth::logout();
        if (!Auth::guard('admin')->attempt(
            ['email' => $request->get('email'), 'password' => $request->get('password')],
            $request->boolean('remember')
        )) {
            return redirect()->back()->with('error', 'Your email and password was wrong. Please enter right credential.');
        }

        $super_admin = Auth::guard('admin')->user();
        if (Schema::hasColumn('super_admin', 'last_login_at')) {
            $super_admin->last_login_at = now();
        }
        if (Schema::hasColumn('super_admin', 'last_login_ip')) {
            $super_admin->last_login_ip = $request->ip();
        }
        if (Schema::hasColumn('super_admin', 'last_login_at') || Schema::hasColumn('super_admin', 'last_login_ip')) {
            $super_admin->save();
        }

        AdminAuditService::log('login_success', $super_admin);

        $this->mfaService->clearSessionVerification();

        if (config('admin.mfa_required')) {
            if (!$this->mfaService->isEnrolled($super_admin)) {
                return redirect()->route('get:admin:mfa.enroll')
                    ->with('success', 'Configure two-factor authentication to continue.');
            }

            return redirect()->route('get:admin:mfa.verify')
                ->with('success', 'Enter your authenticator code.');
        }

        return redirect()->intended(route('get:admin:dashboard'))
            ->with('success', $super_admin->loginSuccessMessage());
    }

    public function logout(Request $request, $guard)
    {
        if ($guard == 'admin') {
            $admin = Auth::guard('admin')->user();
            if ($admin) {
                AdminAuditService::log('logout', $admin);
            }
            $this->mfaService->clearSessionVerification();
            Auth::guard('admin')->logout();

            return redirect()->route('get:admin:login')->with('success', 'Admin Logout Successfully.');
        } elseif ($guard == 'user') {
            request()->session()->forget('login_previous_url');
            Auth::guard('user')->logout();

            return redirect()->route('get:homepage')->with('success', 'User Logout Successfully.');
        }

        Auth::logout();

        return redirect()->route('get:homepage')->with('success', 'User Logout Successfully.');
    }

    public function socialAuthLogin(Request $request, $guards, $provider)
    {
        try {
            if ($guards == 'user') {
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

    public function handleCallback(Request $request, $provider)
    {
        $guards = $request->get('state');
        if ($guards == 'user') {
            return $this->handleUserCallback($provider);
        }
        request()->session()->forget('login_previous_url');

        return redirect()->route('get:homepage');
    }

    public function handleUserCallback($provider)
    {
        $url = request()->session()->get('login_previous_url');
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
            $contact_number = $user->email . '';
            $full_name = $user->name . '';

            $user_details = User::query()->where('login_type', '=', $login_type)->where('login_id', '=', $login_id)->whereNull('deleted_at')->first();
            if ($user_details == null) {
                $user_details = new User();
                $user_details->status = 1;
                $user_details->is_register = 1;
                $user_details->login_type = $login_type;
                $user_details->login_id = $login_id;
                $user_details->verified_at = date('Y-m-d H:i:s');
                $user_details->language = 'en';

                if (trim($full_name) != null) {
                    $user_details->first_name = ucwords(strtolower(trim($full_name)));
                }
                if ($contact_number != null) {
                    if (is_numeric($contact_number)) {
                        $user_details->contact_number = $contact_number;
                    } else {
                        $user_details->email = $contact_number;
                    }
                }
                $user_details->save();
                if ($user_details->first_name != null) {
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
            if (Auth::guard('user')->check()) {
                Session::flash('success', 'User Login Successfully.');

                return redirect($url);
            }

            Auth::logout();
            Session::flash('error', 'Something went to wrong!');

            return redirect($url);
        } catch (\Exception $e) {
            Session::flash('error', 'Something went to wrong!');

            return redirect($url);
        }
    }
}
