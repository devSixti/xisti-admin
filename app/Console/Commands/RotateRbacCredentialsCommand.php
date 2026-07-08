<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Database\Seeders\AdminRbacMockUsersSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RotateRbacCredentialsCommand extends Command
{
    protected $signature = 'rbac:rotate-credentials
        {--dry-run : Show generated passwords without saving}
        {--shared-password : Use one password for all RBAC mock users (easier QA)}
        {--only= : Comma-separated credential keys or emails to rotate (default: all mock users)}';

    protected $description = 'Rotate RBAC mock admin passwords and export credentials to docs/';

    public function handle(): int
    {
        $domain = (string) env('RBAC_MOCK_EMAIL_DOMAIN', AdminRbacMockUsersSeeder::DEFAULT_EMAIL_DOMAIN);
        $product = str_contains($domain, 'xisti') ? 'xisti' : 'zimo';
        $docsRoot = base_path('docs');
        $pdpDocs = dirname(base_path(), 2).'/docs';
        $credentialsPath = is_dir($pdpDocs)
            ? $pdpDocs.'/rbac-credentials.json'
            : $docsRoot.'/rbac-credentials.json';

        $existing = [];
        if (is_file($credentialsPath)) {
            $decoded = json_decode((string) file_get_contents($credentialsPath), true);
            if (is_array($decoded)) {
                $existing = $decoded;
            }
        }

        $onlyFilter = $this->option('only')
            ? array_map('trim', explode(',', (string) $this->option('only')))
            : null;

        $productCreds = [];
        $shared = $this->option('shared-password')
            ? Str::password(24, symbols: true)
            : null;

        foreach (AdminRbacMockUsersSeeder::mockUsers($domain) as $entry) {
            $credKey = $entry['credential_key'] ?? $entry['role'];
            if ($onlyFilter !== null
                && ! in_array($credKey, $onlyFilter, true)
                && ! in_array($entry['email'], $onlyFilter, true)) {
                continue;
            }

            $password = $shared ?? Str::password(24, symbols: true);
            $productCreds[$credKey] = [
                'email' => $entry['email'],
                'password' => $password,
                'name' => $entry['name'],
                'role' => $entry['role'],
            ];

            if ($this->option('dry-run')) {
                $this->line("{$entry['email']} ({$entry['role']}): {$password}");

                continue;
            }

            $admin = Admin::query()->where('email', $entry['email'])->first();
            if ($admin === null) {
                $this->warn("Admin not found: {$entry['email']} — run AdminRbacMockUsersSeeder first.");

                continue;
            }

            $admin->password = Hash::make($password);
            if (\Illuminate\Support\Facades\Schema::hasColumn('super_admin', 'must_change_password')) {
                $admin->must_change_password = 0;
            }
            $admin->save();
            $this->info("Rotated: {$entry['email']}");
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $existing[$product] = array_merge($existing[$product] ?? [], $productCreds);
        $existing[$product]['rotated_at'] = now()->toIso8601String();

        if (! is_dir(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0755, true);
        }
        file_put_contents(
            $credentialsPath,
            json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n"
        );

        $this->exportCsv($docsRoot, $product, $productCreds);
        $this->info("Credentials written to {$credentialsPath}");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, array{email: string, password: string, name: string}>  $productCreds
     */
    private function exportCsv(string $docsRoot, string $product, array $productCreds): void
    {
        if (! is_dir($docsRoot)) {
            return;
        }

        $csvPath = $docsRoot.'/RBAC-CREDENTIALS-'.strtoupper($product).'.csv';
        $fh = fopen($csvPath, 'w');
        if ($fh === false) {
            return;
        }

        fputcsv($fh, ['role', 'email', 'password', 'rotated_at']);
        foreach ($productCreds as $role => $row) {
            if (! is_array($row) || ! isset($row['email'], $row['password'])) {
                continue;
            }
            fputcsv($fh, [$role, $row['email'], $row['password'], now()->toIso8601String()]);
        }
        fclose($fh);
        $this->info("CSV exported: {$csvPath}");
    }
}
