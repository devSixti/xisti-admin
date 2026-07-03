<?php

namespace App\Support;

/**
 * Shared API request validation fragments.
 */
class ApiValidationRules
{
    /** Secure hex access tokens (64 chars) and legacy numeric tokens. */
    public const ACCESS_TOKEN = 'required|string|min:16';
}
