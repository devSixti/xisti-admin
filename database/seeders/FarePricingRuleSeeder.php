<?php

namespace Database\Seeders;

use App\Models\FarePricingRule;
use Illuminate\Database\Seeder;

class FarePricingRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Hora pico mañana',
                'rule_type' => 'peak',
                'multiplier' => 1.15,
                'conditions' => ['start' => '06:30', 'end' => '09:00'],
                'priority' => 10,
            ],
            [
                'name' => 'Hora pico tarde',
                'rule_type' => 'peak',
                'multiplier' => 1.20,
                'conditions' => ['start' => '17:00', 'end' => '20:00'],
                'priority' => 20,
            ],
            [
                'name' => 'Fin de semana',
                'rule_type' => 'weekday',
                'multiplier' => 1.05,
                'conditions' => ['days' => ['Saturday', 'Sunday']],
                'priority' => 30,
            ],
        ];

        foreach ($rules as $rule) {
            FarePricingRule::query()->updateOrCreate(
                ['name' => $rule['name']],
                array_merge($rule, ['status' => true])
            );
        }
    }
}
