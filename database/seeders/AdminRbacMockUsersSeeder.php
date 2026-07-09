<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminRbacMockUsersSeeder extends Seeder
{
    public const DEFAULT_EMAIL_DOMAIN = 'xistiapp.com';

    public const DEFAULT_PASSWORD = 'PdP-Xisti-RBAC-2026!';

    /** @return list<array{email: string, role: string, name: string, credential_key?: string}> */
    public static function mockUsers(string $domain = self::DEFAULT_EMAIL_DOMAIN): array
    {
        return [
            ['email' => "admin@{$domain}", 'role' => 'admin_total', 'name' => 'Admin Total XISTI'],
            ['email' => "contabilidad@{$domain}", 'role' => 'contabilidad', 'name' => 'Contabilidad XISTI'],
            ['email' => 'daniel@xistiapp.com', 'role' => 'socio', 'name' => 'Daniel (Socio XISTI)', 'credential_key' => 'socio'],
            [
                'email' => 'santiago@xistiapp.com',
                'role' => 'socio',
                'name' => 'Santiago (Socio XISTI)',
                'credential_key' => 'socio_2',
            ],
            ['email' => "desarrollador@{$domain}", 'role' => 'desarrollador', 'name' => 'Desarrollador XISTI'],
            ['email' => "aprobaciones@{$domain}", 'role' => 'aprobaciones', 'name' => 'Aprobaciones XISTI'],
            ['email' => "soporte@{$domain}", 'role' => 'soporte', 'name' => 'Soporte XISTI'],
            ['email' => "marketing@{$domain}", 'role' => 'marketing', 'name' => 'Marketing XISTI'],
        ];
    }

    public function run(): void
    {
        if (filter_var(env('RBAC_SKIP_MOCK_USERS', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $this->migrateLegacySocioEmails([
            'socio@xistiapp.com' => 'daniel@xistiapp.com',
            'socio2@xistiapp.com' => 'santiago@xistiapp.com',
        ]);

        $password = (string) env('RBAC_MOCK_PASSWORD', self::DEFAULT_PASSWORD);
        if ($password === '') {
            $this->command?->warn('RBAC_MOCK_PASSWORD empty; skipping mock admin users.');

            return;
        }

        $domain = (string) env('RBAC_MOCK_EMAIL_DOMAIN', self::DEFAULT_EMAIL_DOMAIN);
        foreach (self::mockUsers($domain) as $entry) {
            $role = AdminRole::query()->where('slug', $entry['role'])->first();
            if ($role === null) {
                $this->command?->warn("Role {$entry['role']} missing; run AdminRbacMatrixSeeder first.");

                continue;
            }

            $admin = Admin::query()->firstOrNew(['email' => $entry['email']]);
            $isNew = ! $admin->exists;
            $admin->name = $entry['name'];
            if ($isNew || filter_var(env('RBAC_RESET_MOCK_PASSWORDS', false), FILTER_VALIDATE_BOOLEAN)) {
                $admin->password = Hash::make($password);
            }
            $admin->roles = 4;
            if ($entry['role'] === 'admin_total') {
                $admin->roles = 1;
            }
            $admin->role_id = $role->id;
            $admin->area_id = 0;
            $admin->is_restrict_admin = $entry['role'] === 'admin_total' ? 0 : 1;
            $admin->admin_type = 's';
            if (Schema::hasColumn('super_admin', 'status')) {
                $admin->status = 1;
            }
            if (Schema::hasColumn('super_admin', 'must_change_password')) {
                $admin->must_change_password = 0;
            }
            $admin->save();
        }

        $this->command?->info('XISTI RBAC mock admins seeded (domain '.$domain.').');
    }

    /** @param array<string, string> $map */
    private function migrateLegacySocioEmails(array $map): void
    {
        foreach ($map as $from => $to) {
            if ($from === $to) {
                continue;
            }
            $legacy = Admin::query()->where('email', $from)->first();
            if ($legacy === null) {
                continue;
            }
            $target = Admin::query()->where('email', $to)->first();
            if ($target !== null && $target->id !== $legacy->id) {
                $legacy->delete();
                continue;
            }
            $legacy->email = $to;
            $legacy->save();
            $this->command?->info("Migrated admin email {$from} → {$to}");
        }
    }
}
