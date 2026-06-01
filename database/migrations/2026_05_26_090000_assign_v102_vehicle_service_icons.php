<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicle_services')) {
            return;
        }

        $map = [
            'expreso' => 'expreso_bus.png',
            'encomiendas' => 'encomiendas_delivery.png',
        ];

        foreach ($map as $mode => $icon) {
            DB::table('vehicle_services')
                ->where('service_mode', $mode)
                ->where(function ($q) {
                    $q->whereNull('icon_name')->orWhere('icon_name', '');
                })
                ->update(['icon_name' => $icon, 'updated_at' => now()]);
        }

        // Delivery vehicle options (bici / moto carguero) when named in DB
        $byName = [
            'bicicleta' => 'bicycle.png',
            'bicycle' => 'bicycle.png',
            'moto carguero' => 'cargo_moto.png',
            'cargo' => 'cargo_moto.png',
        ];

        foreach ($byName as $needle => $icon) {
            DB::table('vehicle_services')
                ->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($needle) . '%']);
                    if (Schema::hasColumn('vehicle_services', 'es_name')) {
                        $q->orWhereRaw('LOWER(es_name) LIKE ?', ['%' . strtolower($needle) . '%']);
                    }
                })
                ->where(function ($q) {
                    $q->whereNull('icon_name')->orWhere('icon_name', '');
                })
                ->update(['icon_name' => $icon, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Icons can remain; no destructive rollback.
    }
};
