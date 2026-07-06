<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('general_settings')
            ->where('id', 1)
            ->whereIn('smtp_user_name', ['soporte@xistiapp.com', 'soportexisti@gmail.com', ''])
            ->update([
                'smtp_user_name' => 'noreply@xistiapp.com',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Non-destructive: keep noreply on rollback.
    }
};
