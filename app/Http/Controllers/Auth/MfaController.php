<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AdminUi;
use App\Http\Controllers\Controller;
use App\Http\Requests\MfaVerifyRequest;
use App\Models\Admin;
use App\Services\AdminAuditService;
use App\Services\AdminMfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
    public function __construct(private readonly AdminMfaService $mfaService)
    {
        $this->middleware('auth:admin');
    }

    public function showVerify()
    {
        $admin = Auth::guard('admin')->user();
        if (!$this->mfaService->isEnrolled($admin)) {
            return redirect()->route('get:admin:mfa.enroll');
        }
        if ($this->mfaService->sessionIsVerified()) {
            return redirect()->route('get:admin:dashboard');
        }

        return view('admin.auth.mfa_verify');
    }

    public function verify(MfaVerifyRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($this->mfaService->verify($admin, $request->validated('code'))) {
            AdminAuditService::log('mfa_verify_success', $admin);

            return redirect()->intended(route('get:admin:dashboard'))
                ->with('success', AdminUi::label('mfa.verify_success'));
        }

        AdminAuditService::log('mfa_verify_failed', $admin, metadata: ['email' => $admin->email]);

        return redirect()->back()->with('error', AdminUi::label('mfa.verify_failed'));
    }

    public function showEnroll()
    {
        $admin = Auth::guard('admin')->user();
        if (!$this->mfaService->schemaReady()) {
            return redirect()->route('get:admin:security')
                ->with('error', AdminUi::label('mfa.schema_missing'));
        }
        if ($this->mfaService->isEnrolled($admin) && $this->mfaService->sessionIsVerified()) {
            return redirect()->route('get:admin:dashboard');
        }

        $enrollment = $this->mfaService->beginEnrollment($admin);

        return view('admin.auth.mfa_enroll', $enrollment);
    }

    public function enroll(MfaVerifyRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        $result = $this->mfaService->completeEnrollment($admin, $request->validated('code'));
        if (!$result['ok']) {
            return redirect()->back()->with('error', $result['message']);
        }

        AdminAuditService::log('mfa_enroll_success', $admin);

        return view('admin.auth.mfa_backup_codes', [
            'backup_codes' => $result['backup_codes'],
        ]);
    }

    public function showSecurity()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.pages.super_admin.security.index', [
            'admin' => $admin,
            'mfa_enrolled' => $this->mfaService->isEnrolled($admin),
            'mfa_schema_ready' => $this->mfaService->schemaReady(),
        ]);
    }

    public function resetEnrollment(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ((int) $admin->roles !== 1) {
            abort(403);
        }

        if (!$this->mfaService->schemaReady()) {
            return redirect()->back()->with('error', AdminUi::label('mfa.schema_missing'));
        }

        $targetId = (int) $request->get('admin_id', $admin->id);
        $target = Admin::query()->findOrFail($targetId);

        $old = [
            'totp_enabled_at' => $target->totp_enabled_at,
        ];
        $target->totp_secret = null;
        $target->totp_enabled_at = null;
        $target->totp_backup_codes = null;
        $target->save();

        AdminAuditService::log('mfa_reset', $target, $old, ['totp_enabled_at' => null]);

        if ($target->id === $admin->id) {
            $this->mfaService->clearSessionVerification();

            return redirect()->route('get:admin:mfa.enroll')
                ->with('success', AdminUi::label('mfa.reset_self_success'));
        }

        return redirect()->back()->with('success', AdminUi::label('mfa.reset_other_success', null, ['email' => $target->email]));
    }
}
