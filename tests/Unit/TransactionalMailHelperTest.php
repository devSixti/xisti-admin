<?php

namespace Tests\Unit;

use App\Helpers\TransactionalMailHelper;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TransactionalMailHelperTest extends TestCase
{
    public function test_default_from_is_noreply(): void
    {
        $this->assertSame('noreply@xistiapp.com', TransactionalMailHelper::defaultFromAddress());
        $this->assertSame('XISTI', TransactionalMailHelper::defaultFromName());
    }

    public function test_resend_path_uses_config_from_address(): void
    {
        Config::set('services.resend.key', 're_test_key');
        Config::set('mail.default', 'resend');
        Config::set('mail.from.address', 'noreply@xistiapp.com');

        $this->assertTrue(TransactionalMailHelper::resendEnabled());
        $this->assertTrue(TransactionalMailHelper::transportConfigured('', '', '', '', ''));
        $this->assertSame('noreply@xistiapp.com', TransactionalMailHelper::fromAddress('legacy@gmail.com'));
    }

    public function test_smtp_path_requires_credentials(): void
    {
        Config::set('services.resend.key', '');
        Config::set('mail.default', 'smtp');

        $this->assertFalse(TransactionalMailHelper::resendEnabled());
        $this->assertFalse(TransactionalMailHelper::transportConfigured('', '', '', '', ''));
        $this->assertTrue(TransactionalMailHelper::transportConfigured(
            'user@example.com',
            'secret',
            'smtp.example.com',
            '587',
            'tls'
        ));
        $this->assertSame('user@example.com', TransactionalMailHelper::fromAddress('user@example.com'));
    }
}
