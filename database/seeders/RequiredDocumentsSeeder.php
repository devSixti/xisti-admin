<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequiredDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $required_documents_record =[
            [
                'id' => 1,
                'name' => 'Driver’s License',
                'status' => 1,
                'contains_expiry' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Car Insurance',
                'status' => 1,
                'contains_expiry' => 1,
            ]
            ];
        /*
        | upsert
        |--------------------------------------------------------------------------
        | We are using upsert here as it functions to either insert or update records efficiently.
        | If a record already exists, it updates it; if not, it inserts a new record.
        | This operation compares records using a unique key and supports handling multiple records in a single operation.
        */
        DB::table('required_documents')->upsert(
            $required_documents_record,
            ['id'], // Unique column to determine if a row exists
            ['name', 'status', 'contains_expiry']
        );
    }
}
