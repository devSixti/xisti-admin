<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_event_templates')) {
            return;
        }

        Schema::create('push_event_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 80)->unique();
            $table->string('label', 160);
            $table->string('audience', 32)->comment('passenger|driver|broadcast');
            $table->string('category', 32)->default('ride');
            $table->unsignedTinyInteger('app_notification_type')->default(1);
            $table->unsignedSmallInteger('title_code')->default(91);
            $table->unsignedSmallInteger('message_code')->default(0);
            $table->string('title_es', 500);
            $table->text('message_es');
            $table->string('title_en', 500)->nullable();
            $table->text('message_en')->nullable();
            $table->string('sound_profile', 32)->default('default')->comment('default|new_request');
            $table->string('placeholders', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        if (class_exists(\App\Helpers\PushEventTemplateHelper::class)) {
            \App\Helpers\PushEventTemplateHelper::seedDefaults();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_event_templates');
    }
};
