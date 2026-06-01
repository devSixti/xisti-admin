<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $presets = [
            ['id' => 1, 'name' => 'Recarga básica', 'value' => 13000.00],
            ['id' => 2, 'name' => 'Recarga estándar', 'value' => 26000.00],
            ['id' => 3, 'name' => 'Recarga premium', 'value' => 39000.00],
        ];

        foreach ($presets as $row) {
            DB::table('topup_wallet')->updateOrInsert(['id' => $row['id']], $row);
        }

        DB::table('topup_wallet')->where('id', '>', 3)->delete();
    }

    public function down(): void
    {
        // Non-destructive rollback.
    }
};
