<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class XistiPurgeLegacyBrandingSeeder extends Seeder
{
    /**
     * Strip legacy white-label strings from seeded CMS and email content.
     */
    public function run(): void
    {
        $replacements = [
            'zimoapp@gmail.com' => 'soporte@xistiapp.com',
            'admin@zimo.com' => 'admin@xistiapp.com',
            'https://admin.appzimo.com' => 'https://admin.xistiapp.com',
            'Fox-jek' => 'XISTI',
            'Fox Jek' => 'XISTI',
            'Fox Drive' => 'XISTI',
            'WhiteLabelFox' => 'XISTI',
            'app-zimo' => 'xisti-app-ad901',
        ];

        $textColumnsByTable = [
            'page_settings' => ['name', 'description', 'page_title'],
            'email_templates' => ['title', 'content'],
        ];

        foreach ($textColumnsByTable as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $existingColumns = array_intersect($columns, Schema::getColumnListing($table));
            if ($existingColumns === []) {
                continue;
            }
            foreach (DB::table($table)->get() as $row) {
                $updates = [];
                foreach ($existingColumns as $column) {
                    $value = $row->{$column} ?? null;
                    if (!is_string($value) || $value === '') {
                        continue;
                    }
                    $replaced = $value;
                    foreach ($replacements as $from => $to) {
                        $replaced = str_replace($from, $to, $replaced);
                    }
                    if ($replaced !== $value) {
                        $updates[$column] = $replaced;
                    }
                }
                if ($updates !== []) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }

        if (Schema::hasTable('vehicle_services')) {
            DB::table('vehicle_services')->where('id', 5)->update(['status' => 0]);
        }
    }
}
