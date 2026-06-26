<?php

namespace Database\Seeders;

use App\Helpers\EmailBrandLayoutHelper;
use App\Models\EmailTemplates;
use Illuminate\Database\Seeder;

class BrandedEmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EmailBrandLayoutHelper::templateCatalog() as $index => $tpl) {
            EmailTemplates::query()->updateOrCreate(
                ['type' => $tpl['type']],
                [
                    'title' => $tpl['title'],
                    'content' => EmailBrandLayoutHelper::wrap($tpl['greeting'], $tpl['body']),
                    'status' => 1,
                ]
            );
        }
    }
}
