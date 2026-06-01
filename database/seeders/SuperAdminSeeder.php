<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $super_admin_record =[
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@xistiapp.com',
                'password' =>Hash::make('w(0Gu(127Y}7$O50eMZD'),
                'roles' => 1,
                'area_id' => 0,
                'is_restrict_admin' => 0,
                'admin_type' => 's',
                'access_token' => '92455607201919047',
                'device_token' => 'ePm7ClDJlWcRrPd-KWEwr6:APA91bG1UPJol-KRVZLPEdMeNgW6peUnCUWkBPxNCocxl7iQPK2tB2x3_IMfVAMlfbDeFmcMk4d3e3Bqs7T71YB0j41ZsIBhbTGP56H0uKIb16Jhs7mM6X2VlMRGCu_QjRxWoOEr6m7s',
                'remember_token' => 'zU5vPJLFUGTG7zjTbsiyiY18Hwni6feKZjjnzYJ0u8T131DxX1KLKhdIVZEm',
            ]
           ];
        /*
       | upsert
       |--------------------------------------------------------------------------
       | We are using upsert here as it functions to either insert or update records efficiently.
       | If a record already exists, it updates it; if not, it inserts a new record.
       | This operation compares records using a unique key and supports handling multiple records in a single operation.
       */
        DB::table('super_admin')->upsert(
            $super_admin_record,
            ['id'], // Unique column to determine if a row exists
            ['name', 'email', 'password', 'roles', 'area_id', 'is_restrict_admin', 'admin_type', 'access_token', 'device_token', 'remember_token']
        );
    }
}
