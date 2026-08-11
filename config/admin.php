<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin RBAC / MFA (dev branch — enable after rollout)
    |--------------------------------------------------------------------------
    */
    'mfa_required' => env('ADMIN_MFA_REQUIRED', false),

    'mfa_session_hours' => (int) env('ADMIN_MFA_SESSION_HOURS', 8),

    'max_super_admins' => (int) env('ADMIN_MAX_SUPER_ADMINS', 5),

    'audit_retention_days' => (int) env('ADMIN_AUDIT_RETENTION_DAYS', 365),

    'login_throttle' => env('ADMIN_LOGIN_THROTTLE', '5,1'),

    'password_min_length' => (int) env('ADMIN_PASSWORD_MIN_LENGTH', 12),
];
