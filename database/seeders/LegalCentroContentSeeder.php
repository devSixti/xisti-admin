<?php

namespace Database\Seeders;

use App\Models\PageSettings;
use App\Support\LegalCentro\LegalContent;
use Illuminate\Database\Seeder;

class LegalCentroContentSeeder extends Seeder
{
    private const SLUGS = [
        'contacto', 'faq', 'aviso-legal', 'privacidad', 'terminos', 'seguridad',
    ];

    public function run(): void
    {
        foreach (self::SLUGS as $slug) {
            $doc = LegalContent::resolve($slug, 'es');
            if ($doc === null) {
                continue;
            }

            $pageId = \App\Support\LegalCentro\LegalHub::pageIdForSlug($slug);
            if ($pageId === null) {
                continue;
            }

            $page = PageSettings::query()->find($pageId);
            if ($page === null) {
                continue;
            }

            $page->es_name = strip_tags($doc['title']);
            $page->name = $doc['title'];
            $page->es_description = $doc['body'];
            $page->description = $doc['body'];

            $docEn = LegalContent::resolve($slug, 'en');
            if ($docEn !== null) {
                $page->en_name = strip_tags($docEn['title']);
                if (property_exists($page, 'en_description') || array_key_exists('en_description', $page->getAttributes())) {
                    $page->en_description = $docEn['body'];
                }
            }

            $page->save();
        }
    }
}
