<?php

namespace Database\Factories;

use App\Models\PushNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PushNotification>
 */
class PushNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'notification_type' => $this->faker->randomElement([1, 2, 3]),
            'title' =>  $this->faker->sentence(3),
            'message' =>  $this->faker->paragraph(),
        ];
    }
}
