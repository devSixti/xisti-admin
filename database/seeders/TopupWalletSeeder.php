<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopupWalletSeeder extends Seeder
{
    public function run(): void
    {
        $topup_wallet_record = [
            ['id' => 1, 'name' => 'Recarga básica', 'value' => 13000.00],
            ['id' => 2, 'name' => 'Recarga estándar', 'value' => 26000.00],
            ['id' => 3, 'name' => 'Recarga premium', 'value' => 39000.00],
        ];

        DB::table('topup_wallet')->upsert(
            $topup_wallet_record,
            ['id'],
            ['name', 'value']
        );
    }
}
