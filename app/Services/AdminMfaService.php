<?php

namespace App\Services;

use App\Models\Admin;
use App\Support\TotpVerifier;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminMfaService
{
    public function __construct(private readonly TotpVerifier $totp = new TotpVerifier())
    {
    }

    public function schemaReady(): bool
    {
        return Schema::hasColumn('super_admin', 'totp_secret')
            && Schema::hasColumn('super_admin', 'totp_enabled_at')
            && Schema::hasColumn('super_admin', 'totp_backup_codes');
    }

    public function isEnrolled(Admin $admin): bool
    {
        if (!$this->schemaReady()) {
            return false;
        }

        return $admin->totp_enabled_at !== null && $this->decryptSecret($admin) !== null;
    }

    public function beginEnrollment(Admin $admin): array
    {
        $secret = $this->totp->generateSecret();
        session([
            'admin_mfa_enroll_secret' => Crypt::encryptString($secret),
            'admin_mfa_enroll_admin_id' => $admin->id,
        ]);

        $uri = $this->totp->provisioningUri($secret, $admin->email);

        return [
            'secret' => $secret,
            'uri' => $uri,
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($uri),
        ];
    }

    public function completeEnrollment(Admin $admin, string $code): array
    {
        if (!$this->schemaReady()) {
            return ['ok' => false, 'message' => 'MFA database columns are missing. Run migrations (2026_05_28_100000_add_rbac_mfa_fields_to_super_admin).'];
        }

        $secret = $this->pullEnrollmentSecret($admin);
        if ($secret === null || !$this->totp->verify($secret, $code)) {
            return ['ok' => false, 'message' => 'Invalid verification code.'];
        }

        $backupCodes = $this->generateBackupCodes();
        $admin->totp_secret = Crypt::encryptString($secret);
        $admin->totp_enabled_at = now();
        $admin->totp_backup_codes = json_encode($this->hashBackupCodes($backupCodes));
        $admin->save();

        session()->forget(['admin_mfa_enroll_secret', 'admin_mfa_enroll_admin_id']);
        $this->markSessionVerified();

        return ['ok' => true, 'backup_codes' => $backupCodes];
    }

    public function verify(Admin $admin, string $code): bool
    {
        $secret = $this->decryptSecret($admin);
        if ($secret !== null && $this->totp->verify($secret, $code)) {
            $this->markSessionVerified();

            return true;
        }

        if ($this->consumeBackupCode($admin, $code)) {
            AdminAuditService::log('mfa_backup_code_used', $admin, metadata: ['admin_id' => $admin->id]);
            $this->markSessionVerified();

            return true;
        }

        return false;
    }

    public function markSessionVerified(): void
    {
        session(['admin_mfa_verified_at' => now()->toIso8601String()]);
    }

    public function sessionIsVerified(): bool
    {
        if (!config('admin.mfa_required')) {
            return true;
        }

        $verifiedAt = session('admin_mfa_verified_at');
        if (!$verifiedAt) {
            return false;
        }

        $hours = config('admin.mfa_session_hours', 8);

        return now()->diffInHours(\Carbon\Carbon::parse($verifiedAt)) < $hours;
    }

    public function clearSessionVerification(): void
    {
        session()->forget('admin_mfa_verified_at');
    }

    /** @return array<int, string> */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }

        return $codes;
    }

    /** @param array<int, string> $codes */
    private function hashBackupCodes(array $codes): array
    {
        return array_map(fn (string $code) => Hash::make(str_replace('-', '', strtoupper($code))), $codes);
    }

    private function consumeBackupCode(Admin $admin, string $code): bool
    {
        $normalized = str_replace([' ', '-'], '', strtoupper(trim($code)));
        if (strlen($normalized) < 8) {
            return false;
        }

        $stored = json_decode($admin->totp_backup_codes ?? '[]', true);
        if (!is_array($stored)) {
            return false;
        }

        foreach ($stored as $index => $hash) {
            if (Hash::check($normalized, $hash)) {
                unset($stored[$index]);
                $admin->totp_backup_codes = json_encode(array_values($stored));
                $admin->save();

                return true;
            }
        }

        return false;
    }

    private function pullEnrollmentSecret(Admin $admin): ?string
    {
        if ((int) session('admin_mfa_enroll_admin_id') !== (int) $admin->id) {
            return null;
        }
        $encrypted = session('admin_mfa_enroll_secret');
        if (!$encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    private function decryptSecret(Admin $admin): ?string
    {
        if (empty($admin->totp_secret)) {
            return null;
        }
        try {
            return Crypt::decryptString($admin->totp_secret);
        } catch (\Throwable) {
            return null;
        }
    }
}
