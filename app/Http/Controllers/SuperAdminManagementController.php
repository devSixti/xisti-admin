<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuperAdminStoreRequest;
use App\Models\Admin;
use App\Services\AdminAuditService;
use App\Services\AdminMfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            if ((int) Auth::guard('admin')->user()->roles !== 1) {
                abort(403, 'Only super administrators can manage super admin accounts.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $max = config('admin.max_super_admins', 5);
        $admins = Admin::query()
            ->where('roles', 1)
            ->orderBy('id')
            ->get();

        return view('admin.pages.super_admin.super_admins.manage', [
            'admins' => $admins,
            'max_super_admins' => $max,
            'slots_remaining' => max(0, $max - $admins->where('status', 1)->count()),
        ]);
    }

    public function create()
    {
        if ($this->activeSuperAdminCount() >= config('admin.max_super_admins', 5)) {
            return redirect()->route('get:admin:super_admin_list')
                ->with('error', 'Maximum number of super administrators reached.');
        }

        return view('admin.pages.super_admin.super_admins.form', ['admin' => null]);
    }

    public function store(SuperAdminStoreRequest $request)
    {
        if ($this->activeSuperAdminCount() >= config('admin.max_super_admins', 5)) {
            return redirect()->route('get:admin:super_admin_list')
                ->with('error', 'Maximum number of super administrators reached.');
        }

        $actor = Auth::guard('admin')->user();
        $admin = new Admin();
        $admin->name = $request->validated('name');
        $admin->email = $request->validated('email');
        $admin->password = Hash::make($request->validated('password'));
        $admin->roles = '1';
        $admin->area_id = 0;
        $admin->is_restrict_admin = 0;
        $admin->admin_type = 's';
        $admin->status = 1;
        $admin->must_change_password = 1;
        $admin->created_by_admin_id = $actor->id;
        $admin->save();

        AdminAuditService::log('super_admin_created', $admin, null, [
            'name' => $admin->name,
            'email' => $admin->email,
            'status' => 1,
        ]);

        return redirect()->route('get:admin:super_admin_list')
            ->with('success', 'Super admin created. They must change password and enroll MFA on first login.');
    }

    public function edit(int $admin_id)
    {
        $admin = Admin::query()->where('roles', 1)->findOrFail($admin_id);

        return view('admin.pages.super_admin.super_admins.form', compact('admin'));
    }

    public function update(SuperAdminStoreRequest $request)
    {
        $admin = Admin::query()->where('roles', 1)->findOrFail($request->validated('admin_id'));
        $old = [
            'name' => $admin->name,
            'email' => $admin->email,
            'status' => $admin->status,
        ];

        $admin->name = $request->validated('name');
        $admin->email = $request->validated('email');
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->validated('password'));
            $admin->must_change_password = 1;
        }
        $admin->save();

        AdminAuditService::log('super_admin_updated', $admin, $old, [
            'name' => $admin->name,
            'email' => $admin->email,
            'status' => $admin->status,
        ]);

        return redirect()->route('get:admin:super_admin_list')->with('success', 'Super admin updated.');
    }

    public function suspend(int $admin_id)
    {
        $admin = Admin::query()->where('roles', 1)->findOrFail($admin_id);
        if ($this->activeSuperAdminCount() <= 1 && (int) $admin->status === 1) {
            return redirect()->back()->with('error', 'Cannot suspend the last active super administrator.');
        }

        $admin->status = (int) $admin->status === 1 ? 0 : 1;
        $admin->save();

        AdminAuditService::log(
            (int) $admin->status === 1 ? 'super_admin_activated' : 'super_admin_suspended',
            $admin,
            null,
            ['status' => $admin->status]
        );

        return redirect()->back()->with('success', 'Super admin status updated.');
    }

    private function activeSuperAdminCount(): int
    {
        return Admin::query()->where('roles', 1)->where('status', 1)->count();
    }
}
