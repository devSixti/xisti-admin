<?php

namespace Database\Seeders;

use App\Models\PushNotification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PushNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! class_exists(\Faker\Factory::class)) {
            return;
        }

        PushNotification::factory()->count(100)->create();
    }
}
