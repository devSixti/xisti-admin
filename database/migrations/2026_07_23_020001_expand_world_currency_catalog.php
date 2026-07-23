<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $records = [
            ['id' => 1, 'currency_name' => 'Colombian Peso', 'ratio' => 1.0000, 'currency_code' => 'COP', 'symbol' => 'COL$', 'status' => 1, 'default_currency' => 1],
            ['id' => 2, 'currency_name' => 'US Dollar', 'ratio' => 0.000278, 'currency_code' => 'USD', 'symbol' => '$', 'status' => 1, 'default_currency' => 0],
            ['id' => 3, 'currency_name' => 'Euro', 'ratio' => 0.000236, 'currency_code' => 'EUR', 'symbol' => '€', 'status' => 1, 'default_currency' => 0],
            ['id' => 4, 'currency_name' => 'Brazilian Real', 'ratio' => 0.00139, 'currency_code' => 'BRL', 'symbol' => 'R$', 'status' => 1, 'default_currency' => 0],
            ['id' => 5, 'currency_name' => 'Argentine Peso', 'ratio' => 0.3800, 'currency_code' => 'ARS', 'symbol' => 'AR$', 'status' => 1, 'default_currency' => 0],
            ['id' => 6, 'currency_name' => 'Mexican Peso', 'ratio' => 0.00472, 'currency_code' => 'MXN', 'symbol' => 'MX$', 'status' => 1, 'default_currency' => 0],
            ['id' => 7, 'currency_name' => 'Chilean Peso', 'ratio' => 0.2640, 'currency_code' => 'CLP', 'symbol' => 'CL$', 'status' => 1, 'default_currency' => 0],
            ['id' => 8, 'currency_name' => 'Peruvian Sol', 'ratio' => 0.00103, 'currency_code' => 'PEN', 'symbol' => 'S/', 'status' => 1, 'default_currency' => 0],
            ['id' => 9, 'currency_name' => 'British Pound', 'ratio' => 0.000220, 'currency_code' => 'GBP', 'symbol' => '£', 'status' => 1, 'default_currency' => 0],
            ['id' => 10, 'currency_name' => 'Canadian Dollar', 'ratio' => 0.000380, 'currency_code' => 'CAD', 'symbol' => 'CA$', 'status' => 1, 'default_currency' => 0],
            ['id' => 11, 'currency_name' => 'Uruguayan Peso', 'ratio' => 0.0110, 'currency_code' => 'UYU', 'symbol' => 'UY$', 'status' => 1, 'default_currency' => 0],
            ['id' => 12, 'currency_name' => 'Paraguayan Guaraní', 'ratio' => 2.0500, 'currency_code' => 'PYG', 'symbol' => '₲', 'status' => 1, 'default_currency' => 0],
            ['id' => 13, 'currency_name' => 'Bolivian Boliviano', 'ratio' => 0.00190, 'currency_code' => 'BOB', 'symbol' => 'Bs', 'status' => 1, 'default_currency' => 0],
            ['id' => 14, 'currency_name' => 'Costa Rican Colón', 'ratio' => 0.1750, 'currency_code' => 'CRC', 'symbol' => '₡', 'status' => 1, 'default_currency' => 0],
        ];

        DB::table('world_currency')->upsert(
            $records,
            ['id'],
            ['currency_name', 'ratio', 'currency_code', 'symbol', 'status', 'default_currency']
        );
    }

    public function down(): void
    {
        // Keep expanded catalog on rollback; ratios can be adjusted via admin.
    }
};
