<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminAuditService
{
    /** @var array<int, string> */
    private static array $redactKeys = [
        'password',
        'password_confirmation',
        'totp_secret',
        'totp_backup_codes',
        'access_token',
        'device_token',
        'remember_token',
    ];

    public static function log(
        string $action,
        ?object $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?Request $request = null,
        ?int $adminId = null,
        ?string $adminEmail = null,
    ): void {
        if (!Schema::hasTable('admin_audit_logs')) {
            return;
        }

        $request ??= request();
        $admin = Auth::guard('admin')->user();

        AdminAuditLog::query()->create([
            'admin_id' => $adminId ?? ($admin?->id),
            'admin_email' => $adminEmail ?? ($admin?->email),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject->id ?? null,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'request_method' => $request->method(),
            'request_path' => substr($request->path(), 0, 255),
            'old_values' => self::sanitize($oldValues),
            'new_values' => self::sanitize($newValues),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public static function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $clean = [];
        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), self::$redactKeys, true)) {
                $clean[$key] = '[REDACTED]';
                continue;
            }
            if (is_array($value)) {
                $clean[$key] = self::sanitize($value);
                continue;
            }
            $clean[$key] = $value;
        }

        return $clean;
    }
}
