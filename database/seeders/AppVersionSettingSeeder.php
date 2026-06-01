<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppVersionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $app_version_setting_record =[
            [
                'id' => 1,
                'app_type' => 0,
                'version_code' => 1,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 3,
            ],
            [
                'id' => 2,
                'app_type' => 1,
                'version_code' => 3,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 3,
            ],
            [
                'id' => 3,
                'app_type' => 2,
                'version_code' => 5,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 3,
            ],
            [
                'id' => 4,
                'app_type' => 3,
                'version_code' => 7,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 3,
            ],
            [
                'id' => 5,
                'app_type' => 0,
                'version_code' => 2,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 4,
            ],
            [
                'id' => 6,
                'app_type' => 1,
                'version_code' => 4,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 4,
            ],
            [
                'id' => 7,
                'app_type' => 2,
                'version_code' => 6,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 4,
            ],
            [
                'id' => 8,
                'app_type' => 3,
                'version_code' => 8,
                'version_name' => '1.0',
                'forcefully_type' =>0,
                'app_device_type' => 4,
            ],

        ];
        /*
        | upsert
        |--------------------------------------------------------------------------
        | We are using upsert here as it functions to either insert or update records efficiently.
        | If a record already exists, it updates it; if not, it inserts a new record.
        | This operation compares records using a unique key and supports handling multiple records in a single operation.
        */
        DB::table('app_version_setting')->upsert(
            $app_version_setting_record,
            ['id'], // Unique column to determine if a row exists
            ['app_type', 'version_code', 'version_name', 'forcefully_type', 'app_device_type']
        );
    }
}
