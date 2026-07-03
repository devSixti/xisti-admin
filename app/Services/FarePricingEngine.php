<?php

namespace App\Services;

use App\Models\FarePricingRule;
use App\Support\FareNegotiationHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Applies admin-configured fare multipliers (peak, demand, weather, etc.).
 */
class FarePricingEngine
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function applyRules(float $baseFare, array $context = []): float
    {
        if (! Schema::hasTable('fare_pricing_rules')) {
            return $baseFare;
        }

        $rules = FarePricingRule::query()
            ->where('status', true)
            ->orderBy('priority')
            ->get();

        $multiplier = 1.0;
        $now = Carbon::parse($context['datetime'] ?? now());

        foreach ($rules as $rule) {
            if (! $this->matches($rule, $now, $context)) {
                continue;
            }
            $multiplier *= max(0.01, (float) $rule->multiplier);
        }

        return round($baseFare * $multiplier, 2);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function matches(FarePricingRule $rule, Carbon $now, array $context): bool
    {
        $conditions = $rule->conditions ?? [];

        return match ($rule->rule_type) {
            'peak' => $this->matchesTimeWindow($now, $conditions),
            'weekday' => $this->matchesWeekday($now, $conditions),
            'holiday' => $this->matchesHoliday($now, $conditions),
            'weather' => ($context['weather_factor'] ?? 1) > 1,
            'demand' => ($context['demand_factor'] ?? 1) >= (float) ($conditions['min_demand'] ?? 1.2),
            'driver_offer' => ($context['driver_supply_factor'] ?? 1) <= (float) ($conditions['max_supply'] ?? 0.8),
            'occupancy' => ($context['occupancy_factor'] ?? 1) >= (float) ($conditions['min_occupancy'] ?? 1.1),
            'special' => (bool) ($conditions['active'] ?? true),
            default => false,
        };
    }

    private function matchesTimeWindow(Carbon $now, array $conditions): bool
    {
        $start = $conditions['start'] ?? null;
        $end = $conditions['end'] ?? null;
        if (! $start || ! $end) {
            return false;
        }
        $time = $now->format('H:i');

        return $time >= $start && $time <= $end;
    }

    private function matchesWeekday(Carbon $now, array $conditions): bool
    {
        $days = $conditions['days'] ?? [];
        if (! is_array($days) || $days === []) {
            return false;
        }

        return in_array(strtolower($now->englishDayOfWeek), array_map('strtolower', $days), true);
    }

    private function matchesHoliday(Carbon $now, array $conditions): bool
    {
        $dates = $conditions['dates'] ?? [];
        if (! is_array($dates)) {
            return false;
        }

        return in_array($now->toDateString(), $dates, true);
    }

    /**
     * Computes recommended/min/max with rules + negotiation step snap.
     *
     * @return array{recommended: float, min: float, max: float}
     */
    public function priceRange(
        float $recommendedFare,
        float $minPrice,
        float $maxPrice,
        array $context = []
    ): array {
        $recommendedFare = $this->applyRules($recommendedFare, $context);
        $minPrice = $this->applyRules($minPrice, $context);
        $maxPrice = $this->applyRules($maxPrice, $context);

        $step = FareNegotiationHelper::step();

        return [
            'recommended' => FareNegotiationHelper::snap($recommendedFare, $step),
            'min' => FareNegotiationHelper::snap($minPrice, $step),
            'max' => FareNegotiationHelper::snap($maxPrice, $step),
        ];
    }
}
