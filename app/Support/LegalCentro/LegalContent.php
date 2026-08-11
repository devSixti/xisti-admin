<?php

namespace App\Support\LegalCentro;

use App\Models\PageSettings;

class LegalContent
{
    /**
     * @return array{title: string, summary: string, body: string}|null
     */
    public static function resolve(string $slug, string $lang): ?array
    {
        $lang = strtolower(substr($lang, 0, 2));
        $content = XistiDocumentBodies::resolve($slug, $lang);

        if ($content === null) {
            $pageId = LegalHub::pageIdForSlug($slug);
            if ($pageId !== null) {
                $content = self::resolveFromDatabase($pageId, $lang);
            }
        }

        if ($content === null && in_array($lang, ['pt', 'fr', 'it'], true)) {
            $content = XistiDocumentBodies::resolve($slug, 'en');
            if ($content === null) {
                $pageId = LegalHub::pageIdForSlug($slug);
                if ($pageId !== null) {
                    $content = self::resolveFromDatabase($pageId, 'en');
                }
            }
        }

        if ($content === null && $lang !== 'es') {
            $content = XistiDocumentBodies::resolve($slug, 'es');
            if ($content === null) {
                $pageId = LegalHub::pageIdForSlug($slug);
                if ($pageId !== null) {
                    $content = self::resolveFromDatabase($pageId, 'es');
                }
            }
        }

        if ($content === null) {
            return null;
        }

        return self::applyTokens($content, $lang);
    }

    /**
     * @return array{title: string, summary: string, body: string}|null
     */
    private static function resolveFromDatabase(int $pageId, string $lang): ?array
    {
        try {
            $page = PageSettings::query()
                ->where('type', 1)
                ->where('id', $pageId)
                ->first();
        } catch (\Throwable) {
            return null;
        }

        if ($page === null) {
            return null;
        }

        $localized = $page->localized($lang);
        $description = trim((string) ($localized->description ?? ''));

        if (strlen($description) <= 800 || str_contains($description, 'Ver Términos Completos') || str_contains($description, 'Ver más información')) {
            return null;
        }

        return [
            'title' => trim((string) ($localized->name ?? '')),
            'summary' => self::extractSummary($description),
            'body' => $description,
        ];
    }

    private static function extractSummary(string $html): string
    {
        $text = trim(strip_tags($html));
        if ($text === '') {
            return '';
        }

        if (strlen($text) <= 280) {
            return $text;
        }

        $snippet = substr($text, 0, 277);
        $lastSpace = strrpos($snippet, ' ');

        if ($lastSpace !== false && $lastSpace > 200) {
            $snippet = substr($snippet, 0, $lastSpace);
        }

        return rtrim($snippet, '.,;:') . '…';
    }

    /**
     * @param  array{title: string, summary: string, body: string}  $content
     * @return array{title: string, summary: string, body: string}
     */
    private static function applyTokens(array $content, string $lang): array
    {
        $tokens = LegalHub::tokens($lang);

        return [
            'title' => str_replace(array_keys($tokens), array_values($tokens), $content['title']),
            'summary' => str_replace(array_keys($tokens), array_values($tokens), $content['summary']),
            'body' => str_replace(array_keys($tokens), array_values($tokens), $content['body']),
        ];
    }
}
