<?php

use App\Helpers\PushEventTemplateHelper;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (class_exists(PushEventTemplateHelper::class)) {
            PushEventTemplateHelper::syncCatalog();
        }
    }

    public function down(): void
    {
        // No rollback — templates are data, not schema.
    }
};
