<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('general_settings', 'enable_encomiendas_mobile')) {
            return;
        }

        DB::table('general_settings')->where('id', 1)->update([
            'enable_encomiendas_mobile' => 1,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('general_settings', 'enable_encomiendas_mobile')) {
            return;
        }

        DB::table('general_settings')->where('id', 1)->update([
            'enable_encomiendas_mobile' => 0,
        ]);
    }
};
