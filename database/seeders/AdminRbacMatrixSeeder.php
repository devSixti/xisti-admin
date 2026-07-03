<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use App\Services\AdminRbacService;
use Illuminate\Database\Seeder;

class AdminRbacMatrixSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'admin_total', 'name' => 'Admin Total', 'legacy_role' => 1],
            ['slug' => 'contabilidad', 'name' => 'Contabilidad', 'legacy_role' => 3],
            ['slug' => 'socio', 'name' => 'Socio', 'legacy_role' => 4],
            ['slug' => 'desarrollador', 'name' => 'Desarrollador', 'legacy_role' => 1],
            ['slug' => 'aprobaciones', 'name' => 'Aprobaciones', 'legacy_role' => 4],
            ['slug' => 'soporte', 'name' => 'Soporte', 'legacy_role' => 4],
            ['slug' => 'marketing', 'name' => 'Marketing', 'legacy_role' => 4],
        ];

        foreach ($roles as $role) {
            AdminRole::query()->updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'legacy_role' => $role['legacy_role'],
                    'is_system' => true,
                ]
            );
        }

        (new AdminPageActionSeeder())->run();
        (new AdminRbacService())->seedRolePermissions();
    }
}
