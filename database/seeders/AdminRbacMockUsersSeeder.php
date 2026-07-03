<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRbacMockUsersSeeder extends Seeder
{
    /** @return list<array{email: string, role: string}> */
    public static function mockUsers(string $domain = 'sixty.app'): array
    {
        return [
            ['email' => "admin@{$domain}", 'role' => 'admin_total'],
            ['email' => "contabilidad@{$domain}", 'role' => 'contabilidad'],
            ['email' => "socio@{$domain}", 'role' => 'socio'],
            ['email' => "desarrollador@{$domain}", 'role' => 'desarrollador'],
            ['email' => "aprobaciones@{$domain}", 'role' => 'aprobaciones'],
            ['email' => "soporte@{$domain}", 'role' => 'soporte'],
            ['email' => "marketing@{$domain}", 'role' => 'marketing'],
        ];
    }

    public function run(): void
    {
        if (! env('RBAC_SEED_MOCK_USERS', false)) {
            return;
        }

        $password = env('RBAC_MOCK_PASSWORD');
        if (! $password) {
            $this->command?->warn('RBAC_MOCK_PASSWORD not set; skipping mock admin users.');

            return;
        }

        $domain = env('RBAC_MOCK_EMAIL_DOMAIN', 'sixty.app');
        foreach (self::mockUsers($domain) as $entry) {
            $role = AdminRole::query()->where('slug', $entry['role'])->first();
            if ($role === null) {
                continue;
            }

            $admin = Admin::query()->firstOrNew(['email' => $entry['email']]);
            $admin->name = ucfirst(str_replace('@'.$domain, '', $entry['email']));
            $admin->password = Hash::make($password);
            $admin->roles = (int) ($role->legacy_role ?? 4);
            $admin->role_id = $role->id;
            $admin->area_id = 0;
            $admin->is_restrict_admin = $entry['role'] === 'admin_total' ? 0 : 1;
            $admin->admin_type = 's';
            $admin->status = 1;
            $admin->must_change_password = 1;
            $admin->save();
        }
    }
}
