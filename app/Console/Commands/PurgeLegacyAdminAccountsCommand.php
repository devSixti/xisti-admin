<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Database\Seeders\AdminRbacMockUsersSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PurgeLegacyAdminAccountsCommand extends Command
{
    protected $signature = 'rbac:purge-legacy
        {--dry-run : List accounts that would be removed}
        {--force : Required to actually delete}';

    protected $description = 'Remove legacy admin accounts not in the RBAC mock or developer allowlist';

    /** @return list<string> */
    private function allowlistEmails(): array
    {
        $domain = (string) env('RBAC_MOCK_EMAIL_DOMAIN', AdminRbacMockUsersSeeder::DEFAULT_EMAIL_DOMAIN);
        $emails = array_column(AdminRbacMockUsersSeeder::mockUsers($domain), 'email');

        $developers = [
            'jeronimorestrepo48@gmail.com',
            'alvarezmaciasnicolas@gmail.com',
            'yhormangarcesballestas@gmail.com',
        ];

        $super = (string) env('SUPER_ADMIN_SEED_EMAIL', '');
        if ($super !== '') {
            $emails[] = $super;
        }

        return array_values(array_unique(array_merge($emails, $developers)));
    }

    public function handle(): int
    {
        $allowlist = $this->allowlistEmails();
        $legacy = Admin::query()
            ->whereNotIn('email', $allowlist)
            ->orderBy('id')
            ->get();

        if ($legacy->isEmpty()) {
            $this->info('No legacy admin accounts found.');

            return self::SUCCESS;
        }

        foreach ($legacy as $admin) {
            $this->line("  [{$admin->id}] {$admin->email} (roles={$admin->roles})");
        }

        if ($this->option('dry-run') || ! $this->option('force')) {
            $this->warn('Dry run — pass --force to delete '.count($legacy).' account(s).');

            return self::SUCCESS;
        }

        $deleted = Admin::query()->whereNotIn('email', $allowlist)->delete();
        $this->info("Deleted {$deleted} legacy admin account(s).");

        return self::SUCCESS;
    }
}
