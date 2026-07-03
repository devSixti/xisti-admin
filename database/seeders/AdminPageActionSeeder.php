<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use App\Services\AdminRbacService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPageActionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            ['id' => 1, 'constant' => 'view', 'name' => 'View'],
            ['id' => 2, 'constant' => 'create', 'name' => 'Create'],
            ['id' => 3, 'constant' => 'edit', 'name' => 'Edit'],
            ['id' => 4, 'constant' => 'delete', 'name' => 'Delete'],
            ['id' => 5, 'constant' => 'approve', 'name' => 'Approve'],
            ['id' => 6, 'constant' => 'export', 'name' => 'Export'],
            ['id' => 7, 'constant' => 'configure', 'name' => 'Configure'],
        ];
        $now = now();
        foreach ($actions as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        DB::table('admin_pageaction')->upsert(
            $actions,
            ['id'],
            ['constant', 'name', 'updated_at']
        );
    }
}
